<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\SubscriptionPaymentRequest;
use App\Models\Admin\User;
use App\Models\Admin\Subscription;
use Illuminate\Support\Str;
class SubscriptionPaymentRequestSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $subscriptions = Subscription::all();

        foreach ($users as $user) {
            $plan = $subscriptions->random();

            SubscriptionPaymentRequest::create([
                'user_id' => $user->id,
                'subscription_id' => $plan->id,
                'amount' => $plan->price,
                'status' => 'completed',
                'payment_reference' => 'PAY-' . strtoupper(uniqid()),
                'request_payload' => [
                    'method' => 'razorpay',
                    'transaction_id' => strtoupper(Str::random(10)),
                    'details' => 'Auto test payment',
                ],
            ]);
        }
    }
}
