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
								<h3 class="page-title mb-0">Edit Video</h3>
							</div>
						</div>
					</div>
					<!-- /Page Header -->
					
					<div class="row">
						<div class="col-sm-12">
							<div class="card">
								<div class="card-body">
									@php
										$categories = \App\Models\Admin\Category::all();

										// Expecting $video->categories to be JSON array string like '["1","2"]'
										$savedCategories = isset($video->categories) ? json_decode($video->categories, true) : [];
										$savedCategories = array_map('strval', $savedCategories ?: []);
										$totalCategories = $categories->count();
										$selectedCount = count($savedCategories);
										$allSelected = ($totalCategories > 0 && $selectedCount === $totalCategories);
									@endphp

									<form id="videoForm" enctype="multipart/form-data">
										@csrf
										<div class="row g-3">

											<div class="col-md-6">
												<label class="form-label">Categories <span class="text-danger">*</span></label>

												<div class="multiselect-dropdown" id="categoryMultiselect">
													<button type="button" class="multiselect-btn" id="multiBtn" aria-haspopup="listbox" aria-expanded="false">
														<span class="selected-list" id="selectedText">Select categories</span>
														<span id="caret">▾</span>
													</button>

													<!-- dropdown menu with checkboxes -->
													<div class="multiselect-menu" id="multiMenu" role="listbox" aria-label="Categories">
														<div class="multiselect-row">
															<input class="form-check-input custom-check" type="checkbox" id="selectAll"
																@if($allSelected) checked @endif>
															<label for="selectAll" style="margin:0;cursor:pointer;">Select All</label>
														</div>

														<hr style="margin:6px 0;">

														@foreach($categories as $cat)
															<div class="multiselect-row">
																<input class="form-check-input custom-check category-checkbox"
																	type="checkbox"
																	name="categories[]"
																	value="{{ $cat->id }}"
																	id="cat{{ $cat->id }}"
																	@if(in_array((string)$cat->id, $savedCategories)) checked @endif>
																<label for="cat{{ $cat->id }}" style="margin:0;cursor:pointer;">{{ $cat->name }}</label>
															</div>
														@endforeach
													</div>
												</div>
											</div>

											<div class="col-md-12">
												<label class="form-label">Title <span class="text-danger">*</span></label>
												<input type="text" name="title" class="form-control" placeholder="Enter title" value="{{ old('name', $video->title ?? '') }}" required>
											</div>

											<div class="col-md-6">
												<label class="form-label">Thumbnail</label>
												<input type="file" name="profile_image" id="profile_image" accept="image/*" class="form-control">

												<div class="mt-2">
													<img id="photoPreview"
														src="{{ isset($video->thumbnail_path) && $video->thumbnail_path ? asset($video->thumbnail_path) : asset('assets/img/profiles/default-avatar.png') }}"
														width="100"
														class="rounded border"
														alt="Preview">
												</div>
											</div>

											<div class="col-md-6">
												<label class="form-label">Video File <span class="text-danger">*</span></label>
												<input type="file" name="video_file" id="video_file" accept="video/*" class="form-control">
												<div class="mt-2">
													<!-- initially an <img>, but JS will replace with <video> if file chosen -->
													<img id="videoPreview" src="{{ asset('assets/img/profiles/default-avatar.png') }}" 
														width="100" class="rounded" alt="">
												</div>
											</div>

											<div class="col-md-12">
												<label class="form-label">Description <span class="text-danger">*</span></label>
												<textarea type="description" name="description" class="form-control" placeholder="Enter Description" required>{{ old('email', $video->description ?? '') }}</textarea>
											</div>

											<div class="col-md-6">
												<label class="form-label">Tags <span class="text-danger">*</span></label>
												<textarea type="tags" name="tags" class="form-control" placeholder="Enter tags">{{ old('email', $video->tags ?? '') }}</textarea>
											</div>

											<div class="col-md-6">
												<label class="form-label">Mata Tags <span class="text-danger">*</span></label>
												<textarea type="metatags" name="metatags" class="form-control" placeholder="Enter metatags">{{ old('email', $video->metatags ?? '') }}</textarea>
											</div>

											<div class="col-12 d-flex justify-content-end">
												<button type="submit" class="btn btn-primary" >Update Video</button>
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

				// ----- Multiselect UI (simple) -----
				var $dropdown = $('#categoryMultiselect');
				var $btn = $('#multiBtn');
				var $menu = $('#multiMenu');
				var $selectAll = $('#selectAll');
				var $checkboxes = $dropdown.find('.category-checkbox');
				var $selectedText = $('#selectedText');

				// toggle menu
				$btn.on('click', function(e){
					e.preventDefault();
					$menu.toggle();
					$btn.attr('aria-expanded', $menu.is(':visible') ? 'true' : 'false');
				});

				// close on outside click
				$(document).on('click', function(e){
					if (!$dropdown.is(e.target) && $dropdown.has(e.target).length === 0) {
						$menu.hide();
						$btn.attr('aria-expanded', 'false');
					}
				});

				// select all checkbox
				$selectAll.on('change', function(){
					var checked = $(this).is(':checked');
					$checkboxes.prop('checked', checked).trigger('change');
					updateSelectedText();
				});

				// individual checkbox changes
				$checkboxes.on('change', function(){
					var total = $checkboxes.length;
					var checkedCount = $checkboxes.filter(':checked').length;
					$selectAll.prop('checked', checkedCount === total);
					$selectAll.prop('indeterminate', checkedCount > 0 && checkedCount < total);
					updateSelectedText();
				});

				function updateSelectedText(){
					// show category NAMES instead of IDs for better UX
					var selectedLabels = $checkboxes.filter(':checked').map(function(){
						var id = $(this).attr('id');
						var label = $dropdown.find('label[for="' + id + '"]').text().trim();
						return label || this.value;
					}).get();

					if (selectedLabels.length === 0) {
						$selectedText.text('Select categories');
					} else if (selectedLabels.length <= 3) {
						$selectedText.text(selectedLabels.join(', '));
					} else {
						$selectedText.text(selectedLabels.length + ' selected');
					}
				}

				// initialize label (server already set checked attributes where needed)
				updateSelectedText();

				// ensure selectAll indeterminate/checked correct at load
				(function initSelectAll(){
					var total = $checkboxes.length;
					var checkedCount = $checkboxes.filter(':checked').length;
					$selectAll.prop('checked', checkedCount === total);
					$selectAll.prop('indeterminate', checkedCount > 0 && checkedCount < total);
				})();

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

				$('#video_file').change(function(){
					const file = this.files[0];
					if (file){
						// Remove existing preview element (could be img or video)
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
							// if you have an input to store duration, set it here (optional)
							// $('#videoDuration').val(duration.toFixed(2));
							console.log('Video duration:', duration.toFixed(2));
						};
						tempVideo.src = URL.createObjectURL(file);
					}
				});

				// Form validation + submit (kept as-is; adjust route if needed)
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
							url: "{{ route('superadmin.video.update', $video->id) }}",
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
		
<style>
/* dropdown container */
.multiselect-dropdown {
  position: relative;
  display: inline-block;
  width: 100%;
}

/* the button that shows selected values */
.multiselect-btn {
  width: 100%;
  text-align: left;
  padding: 8px 12px;
  border: 1px solid #ced4da;
  border-radius: 4px;
  background: #fff;
  cursor: pointer;
  display:flex;
  align-items:center;
  justify-content:space-between;
}

/* the dropdown menu */
.multiselect-menu {
  position: absolute;
  top: calc(100% + 6px);
  left: 0;
  right: 0;
  background: #fff;
  border: 1px solid #ced4da;
  border-radius: 6px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.08);
  max-height: 250px;
  overflow:auto;
  z-index: 999;
  padding: 8px;
  display: none; /* toggled via .show */
}

/* show menu */
.multiselect-menu.show {
  display: block;
}

/* each option row */
.multiselect-row {
  display:flex;
  align-items:center;
  gap:8px;
  padding:6px 4px;
}

/* make checkbox box clearer */
.custom-check {
  width: 18px !important;
  height: 18px !important;
  border: 2px solid #555 !important;
  border-radius: 4px;
  cursor: pointer;
}

/* small text wrap */
.selected-list {
  display: inline-block;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: calc(100% - 50px);
}
</style>