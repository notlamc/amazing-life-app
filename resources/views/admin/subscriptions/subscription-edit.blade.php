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
								<h3 class="page-title mb-0">Edit subscription</h3>
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
											<div class="col-md-6">
												<label class="form-label">Title <span class="text-danger">*</span></label>
												<input type="text" name="name" class="form-control" placeholder="Enter title" value="{{ old('name', $subscription->name ?? '') }}" required>
											</div>
											<div class="col-md-6">
												<label class="form-label">Price <span class="text-danger">*</span></label>
												<input type="text" name="price" class="form-control" placeholder="Enter Price" value="{{ old('name', $subscription->price ?? '') }}" required>
											</div>
											<div class="col-md-6">
												<label class="form-label">Duration (In Days) <span class="text-danger">*</span></label>
												<input type="text" name="duration_days" class="form-control" placeholder="Enter Duration" value="{{ old('name', $subscription->duration_days ?? '') }}" required>
											</div>
											<div class="col-md-6">
												<label class="form-label">Commission Percentage <span class="text-danger">*</span></label>
												<input type="text" name="commission_percentage" class="form-control" placeholder="Enter commission percentage" value="{{ old('name', $subscription->commission_percentage ?? '') }}" required>
											</div>											

											<div class="col-md-12">
												<label class="form-label">Description <span class="text-danger">*</span></label>
												<textarea type="description" name="description" class="form-control" placeholder="Enter Description" required>{{ old('email', $subscription->description ?? '') }}</textarea>
											</div>

											<div class="col-12 d-flex justify-content-end">
												<button type="submit" class="btn btn-primary" >Update subscription</button>
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
						name: { required: true },
						price: { required: true },
						duration_days: { required: true },
						commission_percentage: { required: true },
						description: { required: true },						
					},
					messages: {
						name: "Please enter title",
						price: "Please enter price",
						duration_days: "Please enter duration",
						commission_percentage: "Please enter commission percentage",
						description: {
							required: "Please enter description",
							description: "Please enter valid description"
						},
						
					},
					errorElement: 'span',
					errorClass: 'text-danger d-block mt-1',
					errorPlacement: function(error, element){
						error.insertAfter(element);
					},
					submitHandler: function(form){
						let formData = new FormData(form);

						$.ajax({
							url: "{{ route('superadmin.subscriptions.update', $subscription->id) }}",
							type: "POST",
							data: formData,
							processData: false,
							contentType: false,
							beforeSend: function(){
								$('button[type="submit"]').prop('disabled', true).text('Updating...');
							},
							success: function(res){
								$('button[type="submit"]').prop('disabled', false).text('Update subscription');

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
		
