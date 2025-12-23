<!DOCTYPE html>
<html lang="en">
    
<!-- Mirrored from doccure.dreamstechnologies.com/html/template/admin/forgot-password.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 22 May 2025 11:00:21 GMT -->
<head>
	@php
		$setting = \App\Models\Admin\SiteSetting::first();
	@endphp
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Amazing Life App">
	<meta name="keywords" content="Amazing Life App">
	<meta name="author" content="Amazing Life App">
	<meta property="og:url" content="https://doccure.dreamstechnologies.com/html/">
	<meta property="og:type" content="Amazing Life App">
	<meta property="og:title" content="Amazing Life App">
	<meta property="og:description" content="Amazing Life App">
	<meta property="og:image" content="assets/img/preview-banner.html">
	<meta name="twitter:card" content="summary_large_image">
	<meta property="twitter:domain" content="https://doccure.dreamstechnologies.com/html/">
	<meta property="twitter:url" content="https://doccure.dreamstechnologies.com/html/">
	<meta name="twitter:title" content="Amazing Life App">
	<meta name="twitter:description" content="Amazing Life App">
	<meta name="twitter:image" content="assets/img/preview-banner.html">	
	<title>Amazing Life App - Forgot Password</title>
	
	<!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon"
		href="{{ $setting && $setting->favicon ? asset($setting->favicon) : asset('assets/img/favicon.png') }}">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
	
	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
	
	<!-- Main CSS -->
	<link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
	
</head>
<body>

	<!-- Main Wrapper -->
	<div class="main-wrapper login-body">
		<div class="login-wrapper">
			<div class="container">
				<div class="loginbox">
					
					<div class="login-right">
						<div class="login-right-wrap">
							<h1>Forgot Password?</h1>
							<p class="account-subtitle">Enter your email to get a password reset link</p>
							
							<!-- Form -->
							<form id="forgotPasswordForm">
								<div class="mb-3">
									<input class="form-control" type="text" name="email" id="email" placeholder="Email">
									<small class="text-danger" id="emailError"></small>
								</div>
								<div class="mb-0">
									<button class="btn btn-primary w-100" type="submit">Reset Password</button>
								</div>
							</form>
							<!-- /Form -->
							
							<div class="text-center dont-have">Remember your password? <a href="{{route('admin.login')}}">Login</a></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /Main Wrapper -->
	
	<!-- jQuery -->
	<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}" type="f7dd67b7fe468ed45154472c-text/javascript"></script>
	
	<!-- Bootstrap Core JS -->
	<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" type="f7dd67b7fe468ed45154472c-text/javascript"></script>
	
	<!-- Custom JS -->
	<script src="{{ asset('assets/js/script.js') }}" type="f7dd67b7fe468ed45154472c-text/javascript"></script>
	
<script src="{{ asset('assets/cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js') }}" data-cf-settings="f7dd67b7fe468ed45154472c-|49" defer></script><script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon='{"rayId":"943bce3d09128e16","version":"2025.4.0-1-g37f21b1","serverTiming":{"name":{"cfExtPri":true,"cfL4":true,"cfSpeedBrain":true,"cfCacheStatus":true}},"token":"3ca157e612a14eccbb30cf6db6691c29","b":1}' crossorigin="anonymous"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
	$(document).ready(function(){
		$('#forgotPasswordForm').on('submit', function(e){
			e.preventDefault();

			let email = $('#email').val().trim();
			$('#emailError').text('');

			// Step 1: Validation
			if(email === ''){
				$('#emailError').text('Email is required');
				return;
			}
			if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){
				$('#emailError').text('Enter a valid email');
				return;
			}

			$.ajax({
				url: "{{ route('admin.checkEmail') }}",
				type: 'POST',
				data: { email: email, _token: "{{ csrf_token() }}" },
				success: function(res){
					if(res.exists){
						// Store email in session
						$.post("{{ route('admin.setResetEmail') }}", {
							email: email,
							_token: "{{ csrf_token() }}"
						}, function(){
							// Redirect clean URL
							window.location.href = "{{ route('admin.resetPassword') }}";
						});
					} else {
						$('#emailError').text('This email is not registered');
					}
				}
			});
		});
	});
</script>


</body>
</html>