<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\WalletPaymentTransaction;
use Illuminate\Http\Request;

class WalletTransactionController extends Controller
{
    // List view
    public function index()
    {
        return view('admin.wallet_requests.index');
    }

    // Return JSON for DataTables
    public function list()
    {
        $subscriptions = WalletPaymentTransaction::select(
            'wallet_payment_transactions.*',
            'users.name as username'
        )
        ->leftJoin('users', 'users.id', '=', 'wallet_payment_transactions.user_id')
        ->latest()
        ->get();
        return response()->json(['data' => $subscriptions]);
    }

    // Create form
    public function create()
    {
        return view('admin.subscriptions.create');
    }

    // Store new subscription
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'duration_days' => 'required|integer',
            'commission_percentage' => 'required|numeric|min:0|max:100',
        ]);

        Subscription::create($request->all());

        return redirect()->route('admin.subscriptions.index')
                         ->with('success', 'Subscription added successfully!');
    }

    // Edit form
    public function edit($id)
    {
        $subscription = Subscription::findOrFail($id);
        return view('admin.subscriptions.subscription-edit', compact('subscription'));
    }

    // Update subscription
    public function update(Request $request, $id)
    {
        $video = Subscription::findOrFail($id);

       $request->validate([
            'name' => 'required|string',
            'description' => 'required',
            'price' => 'required|numeric',
            'duration_days' => 'required|integer',
            'commission_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $updateData = [
            'name' => $request->name,
            'price' => $request->price,
            'duration_days' => $request->duration_days,
            'commission_percentage' => $request->commission_percentage,
            'description' => $request->description,
        ];      

        // 🔹 Update only provided fields
        $video->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Video updated successfully!',
            'redirect' => route('superadmin.subscriptions.index')
        ]);
    }

    // Delete subscription
    public function destroy(Request $request)
    {
        $subscription = Subscription::findOrFail($request->id);
        $subscription->delete();

        return response()->json(['success' => true]);
    }

    // Toggle status
    public function toggleStatus(Request $request)
    {
        $subscription = Subscription::findOrFail($request->id);
        $subscription->status = ($subscription->status === 'active') ? 'inactive' : 'active';
        $subscription->save();

        return response()->json(['success' => true, 'status' => $subscription->status]);
    }

    // View subscription details
    public function details(Request $request)
    {
        $subscription = Subscription::findOrFail($request->id);
        return response()->json(['success' => true, 'data' => $subscription]);
    }
}
