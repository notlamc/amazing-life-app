<?php echo $__env->make('admin.layout.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="main-wrapper">
	<?php echo $__env->make('admin.layout.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	<?php echo $__env->make('admin.layout.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="page-wrapper">
        <div class="content container-fluid">

            <div class="page-header">
                <h3 class="page-title mb-0">Site Settings</h3>
            </div>

            <div class="card">
                <div class="card-body">

                    <form id="siteSettingForm" action="<?php echo e(route('superadmin.site.setting.update')); ?>" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Site Title <span class="text-danger">*</span></label>
                                <input type="text" name="site_title" class="form-control"
                                       value="<?php echo e($setting->site_title); ?>" required>
                            </div>

                            <div class="col-md-6">
								<label class="form-label">Referral Commission (%)</label>
								<input type="text" name="referral_commission"
									class="form-control only-number"
									placeholder="e.g. 10"
									
									value="<?php echo e((isset($setting->referral_commission) && (int)$setting->referral_commission !== 0) ? $setting->referral_commission : ''); ?>">
							</div>

							<div class="col-md-6">
								<label class="form-label">Extend Duration (days)</label>
								<input type="text" name="extend_duration_days"
									class="form-control only-number"
									placeholder="e.g. 30"
									value="<?php echo e((isset($setting->extend_duration_days) && (int)$setting->extend_duration_days !== 0) ? $setting->extend_duration_days : ''); ?>">
							</div>


                            <div class="col-md-6">
                                <label class="form-label">Logo</label>
                                <input type="file" name="logo" id="logo" accept="image/*" class="form-control">

                                <div class="mt-2">
                                    <img id="logoPreview"
										src="<?php echo e($setting->logo ? asset($setting->logo) : asset('assets/img/placeholder.png')); ?>"
										width="120" class="rounded">
                                </div>
                            </div>

							<div class="col-md-6">
                                <label class="form-label">Small Logo</label>
                                <input type="file" name="small_logo" id="small_logo" accept="image/*" class="form-control">

                                <div class="mt-2">
                                    <img id="small_logoPreview"
										src="<?php echo e($setting->small_logo ? asset($setting->small_logo) : asset('assets/img/placeholder.png')); ?>"
										width="40" class="rounded">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Favicon</label>
                                <input type="file" name="favicon" id="favicon" accept="image/*" class="form-control">

                                <div class="mt-2">
                                    <img id="faviconPreview"
										src="<?php echo e($setting->favicon ? asset($setting->favicon) : asset('assets/img/placeholder.png')); ?>"
										width="40" class="rounded">
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Save Settings</button>
                            </div>

                        </di	v>
                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

<?php echo $__env->make('admin.layout.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<!-- include scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
	$(document).ready(function(){

		$('#logo').change(function(){
			let file = this.files[0];
			if(file){
				let reader = new FileReader();
				reader.onload = e => $('#logoPreview').attr('src', e.target.result);
				reader.readAsDataURL(file);
			}
		});

		$('#small_logoPreview').change(function(){
			let file = this.files[0];
			if(file){
				let reader = new FileReader();
				reader.onload = e => $('#small_logoPreviewPreview').attr('src', e.target.result);
				reader.readAsDataURL(file);
			}
		});

		$('#favicon').change(function(){
			let file = this.files[0];
			if(file){
				let reader = new FileReader();
				reader.onload = e => $('#faviconPreview').attr('src', e.target.result);
				reader.readAsDataURL(file);
			}
		});

		const logoRequired = <?php echo json_encode(!$setting->logo, 15, 512) ?>; 
		const faviconRequired = <?php echo json_encode(!$setting->favicon, 15, 512) ?>; 

		// validation + AJAX
		$("#siteSettingForm").validate({
			rules: {
				site_title: { required: true },
				referral_commission: { required: true, number: true, min: 0 },
				extend_duration_days: { required: true, number: true, min: 0 },

				logo: { required: logoRequired, extension: "jpg|jpeg|png|webp" },
				favicon: { required: faviconRequired, extension: "jpg|jpeg|png|ico|webp" }
			},
			messages: {
				logo: { extension: "Allowed: jpg, jpeg, png, webp" },
				favicon: { extension: "Allowed: jpg, jpeg, png, ico, webp" }
			},
			errorElement: 'span',
			errorClass: 'text-danger d-block mt-1',

			submitHandler: function(form){
				let formData = new FormData(form);

				$.ajax({
					url: form.action,
					type: "POST",
					data: formData,
					contentType: false,
					processData: false,

					beforeSend: function(){
						$("button[type='submit']").prop("disabled", true).text("Saving...");
						$('.text-danger').remove();
					},

					success: function(res){
						$("button[type='submit']").prop("disabled", false).text("Save Settings");

						Swal.fire({
							icon: "success",
							title: res.message,
							width: '400px',
							showConfirmButton: false,
							timer: 1400,
							customClass: {
								popup: 'swal-small-popup',
								icon: 'swal-small-icon',
								title: 'swal-small-title'
							}
						});


						setTimeout(() => {
							window.location.href = res.redirect;
						}, 1500);
					},

					error: function(xhr){
						$("button[type='submit']").prop("disabled", false).text("Save Settings");

						if(xhr.status === 422){
							let errors = xhr.responseJSON.errors;
							$.each(errors, (field, msg) => {
								// for safety, escape field name when used as selector
								const selector = "[name='"+field+"']";
								$(selector).after(`<span class="text-danger">${msg[0]}</span>`);
							});
						} else {
							Swal.fire("Error!", "Something went wrong.", "error");
						}
					}

				});

				return false;
			}
		});

	});
</script>
<style>
	.swal-small-popup {
		padding: 12px !important;
		display: flex !important;
		flex-direction: column !important;
		align-items: center !important;
	}

	.swal-small-icon {
		transform: scale(0.65) !important;
		margin: 0 auto !important; 
	}

	.swal-small-title {
		font-size: 20px !important;
		text-align: center !important;
		margin-top: 8px !important;
	}

</style><?php /**PATH F:\xampp\htdocs\amazinglifeapp\resources\views/admin/setting/setting-add.blade.php ENDPATH**/ ?>