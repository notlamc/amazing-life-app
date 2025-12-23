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
				<h3 class="page-title">Video Details</h3>
			</div>

			<!-- Agent Details Card -->
			<div class="card">
				<div class="card-body">
					<div class="row align-items-center">
						<!-- <div class="col-md-3 text-center">
							<img src="{{ $video->thumbnail_path ? asset($video->thumbnail_path) : 'https://via.placeholder.com/150' }}" 
								class="img-fluid  shadow-sm" alt="{{ $video->title }}" width="300" height="300">
						</div>
						<div class="col-md-3 text-center">
							<img src="{{ $video->video_path ? asset($video->video_path) : 'https://via.placeholder.com/150' }}" 
								class="img-fluid  shadow-sm" alt="{{ $video->title }}" width="300" height="300">
						</div> -->

						<div class="col-md-9">
							<h4 class="fw-bold mb-3">{{ $video->title }}</h4>
							<p><strong>Tags:</strong> {{ $video->tags }}</p>
							<p><strong>Metatags:</strong> {{ $video->metatags }}</p>
							<p><strong>Description:</strong> {{ $video->description }}</p>
							<p><strong>Duration:</strong> {{ $video->duration }}</p>
							<p><strong>Views:</strong> {{ $video->views }}</p>
							<p><strong>Likes:</strong> {{ $video->likes }}</p>
							<p><strong>Categories:</strong>
								@if($video->videoCategories->count() > 0)
									@foreach($video->videoCategories as $cat)
										<span style="background:#6577eb; color:#fff; padding:4px 8px; border-radius:4px; font-size:12px; margin-left:5px;">
											{{ $cat->name }}
										</span>
									@endforeach
								@else
									<span class="text-muted">No categories selected</span>
								@endif
							</p>

							<p><strong>Status:</strong> 
								<span class="badge {{ $video->status == 'active' ? 'bg-success' : 'bg-danger' }}">
									{{ ucfirst($video->status) }}
								</span>
							</p>

							<a href="{{ route('superadmin.video.list.page') }}" class="btn btn-secondary mt-3">
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