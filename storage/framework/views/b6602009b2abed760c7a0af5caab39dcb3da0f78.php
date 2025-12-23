<!DOCTYPE html>
<html lang="en">
    
<!-- Mirrored from doccure.dreamstechnologies.com/html/template/admin/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 22 May 2025 11:00:06 GMT -->
<head>
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
	<title>Amazing Life App - Login</title>
	
	<!-- Favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo e(asset('assets/img/favicon.png')); ?>">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap.min.css')); ?>">
	
	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="<?php echo e(asset('assets/plugins/fontawesome/css/fontawesome.min.css')); ?>">
	<link rel="stylesheet" href="<?php echo e(asset('assets/plugins/fontawesome/css/all.min.css')); ?>">
	
	<!-- Main CSS -->
	<link rel="stylesheet" href="<?php echo e(asset('assets/css/custom.css')); ?>">
	

	
</head>
<body>
	
	<!-- Main Wrapper -->
	<div class="main-wrapper login-body d-flex align-items-center justify-content-center" style="min-height:100vh; background:#fff;">
		<div class="login-wrapper w-100" style="max-width:420px;">
			<div class="loginbox bg-white p-4 rounded-4 shadow-lg border-0">
				<div class="login-right">
					<div class="login-right-wrap">
						<!-- <img src="<?php echo e(asset('assets/img/logo.png')); ?>" alt="Amazing Life App"  width="250" height="80"> -->

						<h1 class="text-center mb-1" style="font-weight:600;">Admin Login</h1>
						<p class="account-subtitle text-center text-muted mb-4">Access to your dashboard</p>

						<!-- Login Form -->
						<form id="adminLoginForm" method="POST">
							<?php echo csrf_field(); ?>
							<div id="formAlert"></div>

							<div class="mb-3">
								<label class="form-label fw-semibold">Email Address</label>
								<input class="form-control form-control-lg rounded-3" 
									type="email" name="email" placeholder="Enter your email">
							</div>

							<div class="mb-3">
								<label class="form-label fw-semibold">Password</label>
								<input class="form-control form-control-lg rounded-3" 
									type="password" name="password" placeholder="Enter your password">
							</div>

							<div class="mb-3">
								<button class="btn btn-primary w-100 py-2 rounded-3" type="submit">Login</button>
							</div>
						</form>
						<!-- /Login Form -->

						<div class="text-center forgotpass mt-3">
							<a href="<?php echo e(route('admin.forgot-password')); ?>" class="text-decoration-none text-primary small">Forgot Password?</a>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
	

	<!-- jQuery + Validation -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>

	<script>
		$(document).ready(function() {

			// Loader
			$('body').append(`
				<div id="loader" style="
					display:none;
					position:fixed;
					top:0;
					left:0;
					width:100%;
					height:100%;
					background:rgba(255,255,255,0.8);
					z-index:9999;
					text-align:center;
					padding-top:20%;
					font-size:18px;
					color:#333;">
					<div class="spinner-border text-primary" role="status"></div>
					<br>Loading...
				</div>
			`);

			// Validation
			$("#adminLoginForm").validate({
				rules: {
					email: { required: true, email: true },
					password: { required: true }
				},
				messages: {
					email: {
						required: "Email is required",
						email: "Please enter a valid email (e.g., example@domain.com)"
					},
					password: { required: "Password is required" }
				},
				errorElement: 'span',
				errorPlacement: function(error, element) {
					error.addClass('text-danger');
					element.closest('.mb-3').append(error);
				},
				highlight: function(element) {
					$(element).addClass('is-invalid');
				},
				unhighlight: function(element) {
					$(element).removeClass('is-invalid');
				},
				success: function(label, element) {
					$(label).remove();
				},
				submitHandler: function(form) {
					$("#formAlert").html(""); 
					$.ajax({
						url: "<?php echo e(route('admin.auth')); ?>",
						type: "POST",
						data: $(form).serialize(),
						beforeSend: function() {
							$("#loader").show();
						},
						success: function(response) {
							$("#loader").hide();
							if (response.status) {
								$("#formAlert").html(`
									<div class="alert alert-success text-center">${response.message}</div>
								`);
								setTimeout(() => {
									window.location.href = "<?php echo e(route('superadmin.dashboard')); ?>";
								}, 800);
							} else {
								$("#formAlert").html(`
									<div class="alert alert-danger text-center">${response.message}</div>
								`);
							}
						},
						error: function() {
							$("#loader").hide();
							$("#formAlert").html(`
								<div class="alert alert-danger text-center">Something went wrong, please try again!</div>
							`);
						}
					});
				}
			});

		});
		$(document).ready(function(){
			let msg = localStorage.getItem('resetSuccessMessage');
			if(msg){
				$('#formAlert').html(`
					<div id="successAlert" class="alert alert-success alert-dismissible fade show" role="alert">
						${msg}
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>
				`);
				localStorage.removeItem('resetSuccessMessage');

				setTimeout(() => {
					$('#successAlert').fadeOut('slow', function(){
						$(this).remove();
					});
				}, 3000);
			}
		});


	</script>
	
	<!-- /Main Wrapper -->
	<!-- jQuery -->
	<script src="<?php echo e(asset('assets/js/jquery-3.7.1.min.js')); ?>" type="ff9f2b1bfd9653bfa488e43b-text/javascript"></script>		
	<!-- Bootstrap Core JS -->
	<script src="<?php echo e(asset('assets/js/bootstrap.bundle.min.js')); ?>" type="ff9f2b1bfd9653bfa488e43b-text/javascript"></script>
	<!-- Custom JS -->
	<script src="<?php echo e(asset('assets/js/script.js')); ?>" type="ff9f2b1bfd9653bfa488e43b-text/javascript"></script>
	<script src="<?php echo e(asset('assets/cdn-cgi/scripts/7d0fa10a/cloudflare-static/rocket-loader.min.js')); ?>" data-cf-settings="ff9f2b1bfd9653bfa488e43b-|49" defer></script><script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon='{"rayId":"943bce2eafe08575","version":"2025.4.0-1-g37f21b1","serverTiming":{"name":{"cfExtPri":true,"cfL4":true,"cfSpeedBrain":true,"cfCacheStatus":true}},"token":"3ca157e612a14eccbb30cf6db6691c29","b":1}' crossorigin="anonymous"></script>
</body>
</html>

<?php /**PATH F:\xampp\htdocs\amazinglifeapp\resources\views/admin/login.blade.php ENDPATH**/ ?>