@include('admin.layout.header')

<div class="main-wrapper">
	@include('admin.layout.navbar')
	@include('admin.layout.sidebar')


			<!-- Page Wrapper -->
            <div class="page-wrapper">
                <div class="content container-fluid">
				
					<!-- Page Header -->
					<div class="page-header">
						<div class="row align-items-center">
							<div class="col-sm-6">
								<h3 class="page-title mb-0">Create Video</h3>
							</div>
						</div>
					</div>
					<!-- /Page Header -->
					
					<div class="row">
						<div class="col-sm-12">
							<div class="card">
								<div class="card-body">
									<form id="videoForm" enctype="multipart/form-data">
										@csrf
										<div class="row g-3">
											<div class="col-md-12">
												<label class="form-label">Title <span class="text-danger">*</span></label>
												<input type="text" name="title" class="form-control" placeholder="Enter Title" required>
											</div>										
<input type="hidden" id="videoDuration" name="duration">

											<div class="col-md-6">
												<label class="form-label">Thumbnail Image <span class="text-danger">*</span></label>
												<input type="file" name="profile_image" id="profile_image" accept="image/*" class="form-control" required>
												<div class="mt-2">
													<img id="photoPreview" src="{{ asset('assets/img/profiles/default-avatar.png') }}" 
														width="100" class="rounded" alt="">
												</div>
											</div>
											<div class="col-md-6">
												<label class="form-label">Video File <span class="text-danger">*</span></label>
												<input type="file" name="video_file" id="video_file" accept="video/*" class="form-control" required>
												<div class="mt-2">
													<img id="videoPreview" src="{{ asset('assets/img/profiles/default-avatar.png') }}" 
														width="100" class="rounded" alt="">
												</div>
											</div>
											
											<div class="col-md-12">
												<label class="form-label">Description <span class="text-danger">*</span></label>
												<textarea type="description" name="description" class="form-control" placeholder="Enter Description" required></textarea>
											</div>

											<div class="col-12">
												<button type="submit" class="btn btn-primary">Save Video</button>
											</div>
										</div>
									</form>

								

								</div>
							</div>
						</div>			
					</div>
					
				</div>			
			</div>
			<!-- /Page Wrapper -->
		
        </div>
		<!-- /Main Wrapper -->
		
		@include('admin.layout.footer')


		<!-- jQuery Validation + AJAX -->
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


		<script>
			$(document).ready(function(){

				// 🖼️ Thumbnail Preview
				$('#profile_image').change(function(){
					const file = this.files[0];
					if (file){
						let reader = new FileReader();
						reader.onload = function(e){
							$('#photoPreview').attr('src', e.target.result);
						}
						reader.readAsDataURL(file);
					}
				});

				// 🎥 Video Preview (optional - you can remove if not needed)
				// $('#video_file').change(function(){
				// 	const file = this.files[0];
				// 	if (file){
				// 		let reader = new FileReader();
				// 		reader.onload = function(e){
				// 			$('#videoPreview').attr('src', "{{ asset('assets/img/icons/video-file.png') }}"); // placeholder icon
				// 		}
				// 		reader.readAsDataURL(file);
				// 	}
				// });

				// 🎥 Video Preview + Duration Calculation
				$('#video_file').change(function(){
					const file = this.files[0];
					if (file){
						// Remove existing preview if any
						$('#videoPreview').remove();

						// Create a new video element
						const video = document.createElement('video');
						video.id = 'videoPreview';
						video.width = 180; // adjust size as needed
						video.controls = true; // show play/pause controls
						video.className = 'rounded mt-2';

						// Set video source
						video.src = URL.createObjectURL(file);

						// Append video after input
						$(this).after(video);

						// Calculate video duration
						const tempVideo = document.createElement('video');
						tempVideo.preload = 'metadata';
						tempVideo.onloadedmetadata = () => {
							window.URL.revokeObjectURL(tempVideo.src);
							const duration = tempVideo.duration; // seconds
							$('#videoDuration').val(duration.toFixed(2));
							console.log('Video duration:', duration.toFixed(2));
						};
						tempVideo.src = URL.createObjectURL(file);
					}
				});


				// 🚦 Form Validation + Submit
				$("#videoForm").validate({
					rules: {
						title: { required: true },
						description: { required: true },
						profile_image: { required: true, extension: "jpg|jpeg|png|webp" },
						video_file: { required: true, extension: "mp4|mov|avi|mkv|webm" }
					},
					messages: {
						title: "Please enter title",
						description: "Please enter description",
						profile_image: "Only JPG, JPEG, PNG, or WEBP files are allowed",
						video_file: "Please upload a valid video (MP4, MOV, AVI, MKV, WEBM)"
					},
					errorElement: 'span',
					errorClass: 'text-danger d-block mt-1',
					errorPlacement: function(error, element){
						error.insertAfter(element);
					},

					// 🚀 When form is valid and ready to submit
					submitHandler: function(form){
						Swal.fire({
							title: 'Are you sure?',
							text: "Do you want to save this video?",
							icon: 'question',
							showCancelButton: true,
							confirmButtonText: 'Yes, Save it!',
							cancelButtonText: 'Cancel',
							reverseButtons: true,
							customClass: {
								popup: 'swal-popup-sm',
								title: 'swal-title'
							}
						}).then((result) => {
							if (result.isConfirmed) {
								let formData = new FormData(form);

								$.ajax({
									url: "{{ route('superadmin.video.store') }}",
									type: "POST",
									data: formData,
									processData: false,
									contentType: false,
									beforeSend: function(){
										$('button[type="submit"]').prop('disabled', true).text('Saving...');
										$('.text-danger').remove();
									},
									success: function(res){
										$('button[type="submit"]').prop('disabled', false).text('Save Video');

										if (res.success) {
											Swal.fire({
												title: res.message,
												icon: 'success',
												showConfirmButton: false,
												timer: 1500
											}).then(() => {
												window.location.href = res.redirect;
											});
										} else {
											Swal.fire({
												title: 'Error!',
												text: res.message,
												icon: 'error',
												showConfirmButton: true
											});
										}
									},
									error: function(xhr){
										$('button[type="submit"]').prop('disabled', false).text('Save Video');
										$('.text-danger').remove();

										if (xhr.responseJSON && xhr.responseJSON.errors) {
											let errors = xhr.responseJSON.errors;
											$.each(errors, function(field, messages){
												let input = $('[name="'+field+'"]');
												if (input.length) {
													input.after('<span class="text-danger d-block mt-1">'+messages[0]+'</span>');
												}
											});
										} else {
											Swal.fire({
												title: 'Error!',
												text: 'Something went wrong. Please try again later.',
												icon: 'error'
											});
										}
									}
								});
							}
						});

						return false;
					}
				});
			});
		</script>



<style>
	.swal-popup-sm {
		border-radius: 10px !important;
		width: 400px !important;
		padding: 0.8rem !important;
	}

	.swal-title {
		font-size: 18px !important;
		font-weight: 600 !important;
		color: #333 !important;
	}

	.swal-btn-confirm {
		padding: 4px 16px !important;
		font-size: 14px !important;
	}

	.swal2-icon {
		transform: scale(0.8);
		margin-top: 0 !important;
	}

</style>
		<!-- SweetAlert2 -->
		
