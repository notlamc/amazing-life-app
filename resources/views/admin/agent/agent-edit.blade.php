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
								<h3 class="page-title mb-0">Create Agents</h3>
							</div>
						</div>
					</div>
					<!-- /Page Header -->
					
					<div class="row">
						<div class="col-sm-12">
							<div class="card">
								<div class="card-body">
									<form id="agentForm" enctype="multipart/form-data">
										@csrf
										<div class="row g-3">
											<div class="col-md-6">
												<label class="form-label">Name <span class="text-danger">*</span></label>
												<input type="text" name="name" class="form-control" placeholder="Enter name" value="{{ old('name', $agent->name ?? '') }}" required>
											</div>

											<div class="col-md-6">
												<label class="form-label">Email <span class="text-danger">*</span></label>
												<input type="email" name="email" class="form-control" placeholder="Enter email" value="{{ old('email', $agent->email ?? '') }}" required>
											</div>

											<div class="col-md-6">
												<label class="form-label">Profile Photo</label>
												<input type="file" name="profile_image" id="profile_image" accept="image/*" class="form-control">

												<div class="mt-2">
													<img id="photoPreview"
														src="{{ isset($agent->profile_image) && $agent->profile_image
																? asset($agent->profile_image)
																: asset('assets/img/profiles/default-avatar.png') }}"
														width="100"
														class="rounded border"
														alt="Preview">
												</div>
											</div>

											<div class="col-12 d-flex justify-content-end">
												<button type="submit" class="btn btn-primary" >Update Agent</button>
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

				// Image Preview
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

				$("#agentForm").validate({
					rules: {
						name: { required: true },
						email: { required: true, email: true },
						profile_image: { extension: "jpg|jpeg|png|webp" }
					},
					messages: {
						name: "Please enter name",
						email: {
							required: "Please enter email",
							email: "Please enter valid email"
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
							url: "{{ route('admin.agent.update', $agent->id) }}",
							type: "POST",
							data: formData,
							processData: false,
							contentType: false,
							beforeSend: function(){
								$('button[type="submit"]').prop('disabled', true).text('Updating...');
							},
							success: function(res){
								$('button[type="submit"]').prop('disabled', false).text('Update Agent');

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
								$('button[type="submit"]').prop('disabled', false).text('Update Agent');
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
		
