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
								<h3 class="page-title mb-0">Create Notification Template</h3>
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
												<label class="form-label">Template Name <span class="text-danger">*</span></label>
												<input type="text" name="title" class="form-control" placeholder="Enter Title" required>
											</div>										

											{{-- Type --}}
											<div class="col-md-6">
												<label class="form-label">Notification Type</label>
												<select name="type" id="type" class="form-control" required>
													<option value="">Select Type</option>
													<option value="email">EMAIL</option>
													<option value="push">PUSH</option>
												</select>
											</div>

											{{-- Subject (only for email) --}}
											<div class="col-md-6" id="subject_box" style="display: {{ old('type') == 'email' ? 'block' : 'none' }};">
												<label class="form-label">Email Subject</label>
												<input type="text" name="subject" class="form-control"
													value="{{ old('subject') }}">
											</div>
																					
											<div class="col-md-12">
												<label class="form-label">Message Body <span class="text-danger">*</span></label>
												<textarea type="description" name="description" class="form-control" placeholder="Enter Description" required></textarea>
											</div>

											{{-- Variables --}}
											<div class="col-md-12">
												<label class="form-label">Variables (comma separated)</label>
												<input type="text" name="variables" class="form-control"
													value="{{ old('variables') }}">
												<small class="text-muted">Example: name, otp, date</small>
											</div>

											<div class="col-12">
												<button type="submit" class="btn btn-primary">Save Notification</button>
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

				// 🚦 Form Validation + Submit
				$("#videoForm").validate({
					rules: {
						title: { required: true },
						description: { required: true },						
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
							text: "Do you want to save this Notification?",
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
									url: "{{ route('superadmin.notification.store') }}",
									type: "POST",
									data: formData,
									processData: false,
									contentType: false,
									beforeSend: function(){
										$('button[type="submit"]').prop('disabled', true).text('Saving...');
										$('.text-danger').remove();
									},
									success: function(res){
										$('button[type="submit"]').prop('disabled', false).text('Save Notification');

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
<script>
    // Show/hide subject for email type
    document.getElementById('type').addEventListener('change', function () {
        if (this.value === 'email') {
            document.getElementById('subject_box').style.display = 'block';
        } else {
            document.getElementById('subject_box').style.display = 'none';
        }
    });
</script>
		<!-- SweetAlert2 -->
		
