@include('admin.layout.header')
		<!-- Main Wrapper -->
        <div class="main-wrapper">
		
			@include('admin.layout.navbar')
			@include('admin.layout.sidebar')
			
			<!-- Page Wrapper -->
            <div class="page-wrapper">
                <div class="content container-fluid">
					<!-- Page Header -->
			<div class="page-header">
				<h3 class="page-title">Category Details</h3>
			</div>

			<!-- Agent Details Card -->
			<div class="card">
				<div class="card-body">
					<div class="row align-items-center">
						<div class="col-md-3 text-center">
							<img src="{{ $agent->profile_image ? asset($agent->profile_image) : 'https://via.placeholder.com/150' }}" 
								class="img-fluid  shadow-sm" alt="{{ $agent->name }}" width="300" height="300">
						</div>

						<div class="col-md-9">
							<h4 class="fw-bold mb-3">{{ $agent->name }}</h4>
							<p><strong>Email:</strong> {{ $agent->email }}</p>
							<p><strong>Status:</strong> 
								<span class="badge {{ $agent->status == 'active' ? 'bg-success' : 'bg-danger' }}">
									{{ ucfirst($agent->status) }}
								</span>
							</p>

							<a href="{{ route('superadmin.category.list.page') }}" class="btn btn-secondary mt-3">
								<i class="fa fa-arrow-left me-1"></i> Back to List
							</a>
						</div>
					</div>
				</div>
			</div>
			<!-- /Agent Details Card -->
					
					
				</div>			
			</div>
			<!-- /Page Wrapper -->
		
        </div>
		<!-- /Main Wrapper -->
		 
		@include('admin.layout.footer')