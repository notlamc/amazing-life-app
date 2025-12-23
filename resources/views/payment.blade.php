<form action="{{ route('paypal.create') }}" method="POST">
    @csrf
    <input type="number" name="amount" min="1" required placeholder="Enter Amount ">
    <input type="number" name="user_id" min="1" required placeholder="Enter User Id">
    <input type="number" name="subscription_id" min="1" required placeholder="Enter SubscriptionId">
    
    <button type="submit">Pay With PayPal</button>
</form>
