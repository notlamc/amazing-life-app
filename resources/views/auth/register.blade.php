<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>

<h2>Create Account</h2>

@if ($errors->any())
    <p style="color:red;">{{ $errors->first() }}</p>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf

    <!-- Hidden referral code -->
    <input type="hidden" name="referral_code" value="{{ $referralCode }}">

    <label>Name</label><br>
    <input type="text" name="name" value="{{ old('name') }}" required><br><br>

    <label>Email</label><br>
    <input type="email" name="email" value="{{ old('email') }}" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Register</button>
</form>

<hr>

<h3>Or sign up with:</h3>

<a href="{{ route('google.redirect', ['referral' => $referralCode]) }}">
    <button>Google Login</button>
</a>

<!-- Social login buttons 
    <a href="/auth/google?referral={{ $referralCode }}">Continue with Google</a>
    <a href="/auth/facebook?referral={{ $referralCode }}">Continue with Facebook</a>-->

</body>
</html>
