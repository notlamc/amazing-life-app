<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PayPalService;
use App\Models\Admin\UserSubscription;
use App\Models\Admin\Subscription;
use App\Models\Admin\SubscriptionPaymentRequest;
use App\Models\Admin\WalletTransaction;

class PayPalController extends Controller
{
    protected $paypal;

    public function __construct(PayPalService $paypal)
    {
        $this->paypal = $paypal;
    }

    public function index()
    {
        return view('payment');
    }

    public function createOrder(Request $request)
    {
        $amount = $request->amount;
        $subscription_id = $request->subscription_id;
        $user_id = $request->user_id;
        $referenceId = 'REF' . strtoupper(uniqid());

        // Save payment in DB
        $payment = SubscriptionPaymentRequest::create([
            'payment_reference' => $referenceId,
            'user_id'           => $user_id,
            'subscription_id'   => $subscription_id,
            'amount'            => $amount,
            'status'            => 'pending',
        ]);

        // Add any custom params you want
        $extraParams = [
            'subscription_id' => $subscription_id,
            'user_id' => $user_id,
        ];
        
        // Create PayPal order
        $order = $this->paypal->createOrder(
            $amount,
            $referenceId,
            route('paypal.success', $referenceId),
            route('paypal.cancel', $referenceId),
            $extraParams
        );

        // Save PayPal order ID
        $payment->update([
            'paypal_order_id' => $order['id'] ?? null,
            'request_payload' => $order ?? null,
        ]);

        // Get approval link
        $approvalUrl = collect($order['links'] ?? [])->firstWhere('rel', 'approve')['href'] ?? null;

        return redirect()->away($approvalUrl);
        return response()->json([
            'status' => true,
            'redirect_url' => $approvalUrl,
            'reference_id' => $referenceId
        ]);
    }

    public function success(Request $request, $referenceId)
    {
        $payment = SubscriptionPaymentRequest::where('payment_reference', $referenceId)->firstOrFail();

        $orderId = $payment->paypal_order_id;
        $capture = $this->paypal->captureOrder($orderId);        
        
        // Extract custom_id (JSON with subscription_id and user_id)
        $customIdJson = $capture['purchase_units'][0]['payments']['captures'][0]['custom_id'] ?? null;
        $custom = json_decode($customIdJson, true);

        $subscriptionId = $custom['subscription_id'] ?? $payment->subscription_id;
        $userId         = $custom['user_id'] ?? $payment->user_id;

        $transactionId  = $capture['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;
        $amountPaid     = $capture['purchase_units'][0]['payments']['captures'][0]['amount']['value'] ?? $payment->amount;
        $paymentTime    = $capture['purchase_units'][0]['payments']['captures'][0]['create_time'] ?? now();

        // Update subscription payment request
        $payment->update([
            'status'           => 'completed',
            'transaction_id'   => $transactionId,
            'response_payload' => json_encode($capture),
            'payment_time'     => $paymentTime,
        ]);
        // Get subscription details
        $subscription = Subscription::find($subscriptionId);
        // Get user's latest subscription (active or last expired)
        $lastSubscription = UserSubscription::where('user_id', $userId)
            ->orderBy('end_date', 'desc')
            ->first();

        // Determine start date
        if ($lastSubscription && Carbon::parse($lastSubscription->end_date)->isFuture()) {
            // Active subscription exists → extend from last end date
            $startDate = Carbon::parse($lastSubscription->end_date)->addDay(); 
        } else {
            // No subscription OR last one expired → start from today
            $startDate = Carbon::now();
        }

        // Calculate end date using duration_days
        $endDate = $startDate->copy()->addDays($subscription->duration_days);

        if (!$subscription) {
            throw new \Exception('Subscription not found');
        }
        // Create user subscription using Eloquent
        $userSubscription = UserSubscription::create([
            'user_id'          => $userId,
            'subscription_id'  => $subscriptionId,
            'start_date'       => $startDate->toDateString(),
            'end_date'         => $endDate->toDateString(),
            'status'           => 'success',
            'payment_reference'=> $referenceId,
            'transaction_id'   => $transactionId,
            'payment_id'       => $payment->id,
            'is_active'        => 1,
            'amount'           => $amountPaid,
        ]);

        $user = User::find($userId);
        $referredBy = $user->referred_by ?? null;

        if (!empty($referredBy)) {
            $referrerActiveSub = UserSubscription::where('user_id', $referredBy)
            ->where('is_active', 1)
            ->whereDate('end_date', '>=', Carbon::today())
            ->first();
            
            
            if ($referrerActiveSub) {
                $wallet = Wallet::where('user_id', $referredBy)->first();
                
                $commissionPercentage  = 20; // example 20%
                $commissionAmount = ($amountPaid * $commissionPercentage) / 100;
                $walletAmount = $wallet->balance ; 
                $balanceAmount = $walletAmount + $commissionAmount;

                // Add amount
                $wallet->balance = $$balanceAmount;
                $wallet->save();
                
               
                $walletTransaction = WalletTransaction::create([
                    'user_id'               => $userId,
                    'purchased_id'          => $payment->id,    // change if needed
                    'purchased_by'          => $payment->id,    // change if needed
                    'subscription_id'       => $subscriptionId,
                    'wallet_id'             => $wallet->id ,  // you must have wallet ID
                    'type'                  => 'credit',   // credit to admin/platform
                    'subscription_price'    => $amountPaid,
                    'commission_percentage' => $commissionPercentage,
                    'balance_amount'        => $balanceAmount,
                    'commission_amount'     => $commissionAmount,
                    'description'           => 'Subscription purchased by user #' . $userId,
                    'status'                => 'approved',
                ]);
            }
        }

        return view('payment-success', compact('payment'));
    }

    public function cancel($referenceId)
    {
        $payment = SubscriptionPaymentRequest::where('payment_reference', $referenceId)->first();
        if ($payment) {
            $payment->update(['status' => 'cancelled']);
        }

        return view('payment-cancel');
    }
}
