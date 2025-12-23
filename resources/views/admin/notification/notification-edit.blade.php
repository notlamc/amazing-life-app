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
								<h3 class="page-title mb-0">Edit Notification Template</h3>
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
												<input type="text" name="title" class="form-control" placeholder="Enter title" value="{{ old('name', $video->name ?? '') }}" required>
											</div>

											

											{{-- Type --}}
											<div class="mb-3">
												<label class="form-label">Notification Type</label>
												<select name="type" id="type" class="form-control" required>
													<option value="">Select Type</option>
													<option value="email" {{ old('type',$video->type) == 'email' ? 'selected' : '' }}>EMAIL</option>
													<option value="push" {{ old('type',$video->type) == 'push' ? 'selected' : '' }}>PUSH</option>
												</select>
											</div>

											{{-- Subject (only for email) --}}
											<div class="mb-3" id="subject_box" style="display: {{ old('type',$video->type) == 'email' ? 'block' : 'none' }};">
												<label class="form-label">Email Subject</label>
												<input type="text" name="subject" class="form-control"
													value="{{ old('subject',$video->subject) }}">
											</div>

											{{-- Body --}}
											<div class="mb-3">
												<label class="form-label">Message Body</label>
												<textarea name="description" class="form-control" rows="6" required>{{ old('body',$video->body) }}</textarea>
											</div>

											{{-- Variables --}}
											<div class="mb-3">
												<label class="form-label">Variables (comma separated)</label>
												<input type="text" name="variables" class="form-control"
													value="{{ old('variables',$video->variables ? implode(', ', json_decode($video->variables, true)) : '') }}">
												<small class="text-muted">Example: name, otp, date</small>
											</div>

											<div class="col-12 d-flex justify-content-end">
												<button type="submit" class="btn btn-primary" >Update Notification</button>
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
				$("#videoForm").validate({
					rules: {
						title: { required: true },
						description: { required: true },
						profile_image: { extension: "jpg|jpeg|png|webp" }
					},
					messages: {
						name: "Please enter title",
						description: {
							required: "Please enter description",
							description: "Please enter valid description"
						},
						profile_image: "Only JPG, JPEG, PNG, or WEBP allowed"
					},
					errorElement: 'span',
					errorClass: 'text-danger d-block mt-1',
					errorPlacement: function(error, element){
						error.insertAfter(element);
					},
					submitHandler: function(form){
						let formData = new FormData(form);

						$.ajax({
							url: "{{ route('superadmin.notification.update', $video->id) }}",
							type: "POST",
							data: formData,
							processData: false,
							contentType: false,
							beforeSend: function(){
								$('button[type="submit"]').prop('disabled', true).text('Updating...');
							},
							success: function(res){
								$('button[type="submit"]').prop('disabled', false).text('Update Video');

								if (res.success) {
									Swal.fire({
										title: res.message,
										icon: 'success',
										showConfirmButton: false,
										timer: 1500,
										width: '400px',
										padding: '0.8rem',
										customClass: {
											title: 'swal-title',
											popup: 'swal-popup-sm'
										}
									}).then(() => {
										window.location.href = res.redirect;
									});
								} else {
									Swal.fire({
										title: 'Error!',
										text: res.message,
										icon: 'error',
										showConfirmButton: true,
										width: '400px',
										padding: '0.8rem',
										customClass: {
											title: 'swal-title',
											popup: 'swal-popup-sm',
											confirmButton: 'swal-btn-confirm'
										}
									});
								}
							},
							error: function(xhr){
								$('button[type="submit"]').prop('disabled', false).text('Update Video');
								alert('Something went wrong');
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
		
