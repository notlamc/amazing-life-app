<?php
    $user = auth()->user();
    $role = $user->role ?? null;
?>
<!-- Header -->
<div class="header">

	<!-- Logo -->
	<div class="header-left">
		<a href="index-2.html" class="logo">
			<img src="<?php echo e(asset('assets/img/logo.png')); ?>" alt="Amazing Life App">
		</a>
		<a href="index-2.html" class="logo logo-small">
			<img src="<?php echo e(asset('assets/img/logo-small.png')); ?>" alt="Amazing Life App" >
		</a>
	</div>
	<!-- /Logo -->
	
	<a href="javascript:void(0);" id="toggle_btn">
		<i class="fe fe-text-align-left"></i>
	</a>
	
	<!-- <div class="top-nav-search">
		<form>
			<input type="text" class="form-control" placeholder="Search here">
			<button class="btn" type="submit"><i class="fa fa-search"></i></button>
		</form>
	</div> -->
	
	<!-- Mobile Menu Toggle -->
	<a class="mobile_btn" id="mobile_btn">
		<i class="fa fa-bars"></i>
	</a>
	<!-- /Mobile Menu Toggle -->
	
	<!-- Header Right Menu -->
	<ul class="nav user-menu">


		
		<!-- User Menu -->
		<li class="nav-item dropdown has-arrow">
			<a href="#" class="dropdown-toggle nav-link d-flex align-items-center" data-bs-toggle="dropdown">
				<div class="rounded-circle bg-light d-flex justify-content-center align-items-center" 
					style="width: 35px; height: 35px;">
					<i class="fa-solid fa-user text-secondary"></i>
				</div>
				<span class="ms-2"><?php echo e(Auth::user()->name ?? 'User'); ?></span>
			</a>


			<div class="dropdown-menu">
				<a class="dropdown-item" href="">My Profile</a>
				<a class="dropdown-item" href="">Settings</a>

				<!-- Logout Button -->
				<form method="GET" action="<?php echo e(route('superadmin.logout')); ?>">
					<?php echo csrf_field(); ?>
					<button type="submit" class="dropdown-item text-danger">Logout</button>
				</form>
			</div>
		</li>
		<!-- /User Menu -->

		
	</ul>
	<!-- /Header Right Menu -->
	
</div>
<!-- /Header --><?php /**PATH F:\xampp\htdocs\amazinglifeapp\resources\views/admin/layout/navbar.blade.php ENDPATH**/ ?>