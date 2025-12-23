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
									
									<div class="col-md-6">
										<label class="form-label">Categories <span class="text-danger">*</span></label>

										@php
											$categories = \App\Models\Admin\Category::all();
										@endphp

										<div class="multiselect-dropdown" id="categoryMultiselect">
											<button type="button" class="multiselect-btn" id="multiBtn" aria-haspopup="listbox" aria-expanded="false">
											<span class="selected-list" id="selectedText">Select categories</span>
											<span id="caret">▾</span>
											</button>

											<!-- dropdown menu with checkboxes -->
											<div class="multiselect-menu" id="multiMenu" role="listbox" aria-label="Categories">
											<div class="multiselect-row">
												<input class="form-check-input custom-check" type="checkbox" id="selectAll">
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
														data-name="{{ $cat->name }}">
												<label for="cat{{ $cat->id }}" style="margin:0;cursor:pointer;">{{ $cat->name }}</label>
												</div>
											@endforeach
											</div>
										</div>
									</div>

									<div class="col-md-12">
										<label class="form-label">Title <span class="text-danger">*</span></label>
										<input type="text" name="title" class="form-control" placeholder="Enter Title" required>
									</div>										
									<input type="hidden" id="videoDuration" name="duration">

									<div class="col-md-6">
										<label class="form-label">Thumbnail Image <span class="text-danger">*</span></label>
										<input type="file" name="profile_image" id="profile_image" accept="image/*" class="form-control" >
										<div class="mt-2">
											<img id="photoPreview" src="{{ asset('assets/img/profiles/default-avatar.png') }}" 
												width="100" class="rounded" alt="">
										</div>
									</div>
									<div class="col-md-6">
										<label class="form-label">Video File <span class="text-danger">*</span></label>
										<input type="file" name="video_file" id="video_file" accept="video/*" class="form-control" >
										<div class="mt-2">
											<img id="videoPreview" src="{{ asset('assets/img/profiles/default-avatar.png') }}" 
												width="100" class="rounded" alt="">
										</div>
									</div>
									
									<div class="col-md-12">
										<label class="form-label">Description <span class="text-danger">*</span></label>
										<textarea type="description" name="description" class="form-control" placeholder="Enter Description" required></textarea>
									</div>

									<div class="col-md-6">
										<label class="form-label">Tags <span class="text-danger">*</span></label>
										<textarea type="tags" name="tags" class="form-control" placeholder="Enter tags"></textarea>
									</div>

									<div class="col-md-6">
										<label class="form-label">Mata Tags <span class="text-danger">*</span></label>
										<textarea type="metatags" name="metatags" class="form-control" placeholder="Enter metatags"></textarea>
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


<!-- <script>
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
			ignore: [], // don't ignore hidden so validation can check hidden inputs
			rules: {
				title: { required: true },
				description: { required: true },
				profile_image: { required: true, extension: "jpg|jpeg|png|webp" },
				video_file: { required: true, extension: "mp4|mov|avi|mkv|webm" },
				
			},
			messages: {
				title: "Please enter title",
				description: "Please enter description",
				profile_image: "Only JPG, JPEG, PNG, or WEBP files are allowed",
				video_file: "Please upload a valid video (MP4, MOV, AVI, MKV, WEBM)",
				
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

<script>
	$(function(){
	var $dropdown = $('#categoryMultiselect');
	var $btn = $('#multiBtn');
	var $menu = $('#multiMenu');
	var $selectAll = $('#selectAll');
	var $checkboxes = $dropdown.find('.category-checkbox');
	var $selectedText = $('#selectedText');

	// toggle menu
	$btn.on('click', function(e){
		e.preventDefault();
		$menu.toggleClass('show');
		$btn.attr('aria-expanded', $menu.hasClass('show') ? 'true' : 'false');
	});

	// close when clicking outside
	$(document).on('click', function(e){
		if (!$dropdown.is(e.target) && $dropdown.has(e.target).length === 0) {
		$menu.removeClass('show');
		$btn.attr('aria-expanded', 'false');
		}
	});

	// Select All logic
	$selectAll.on('change', function(){
		var checked = $(this).is(':checked');
		$checkboxes.prop('checked', checked).trigger('change'); // trigger so UI updates
		updateSelectedText();
	});

	// individual checkbox change
	$checkboxes.on('change', function(){
		var allChecked = $checkboxes.length === $checkboxes.filter(':checked').length;
		var anyChecked = $checkboxes.filter(':checked').length > 0;

		$selectAll.prop('checked', allChecked);
		// set indeterminate via DOM property (jQuery prop supports it)
		$selectAll.prop('indeterminate', (!allChecked && anyChecked));

		updateSelectedText();
	});

	// update button label with selected items
	function updateSelectedText(){
		var selected = $checkboxes.filter(':checked').map(function(){ return this.value; }).get();
		if(selected.length === 0){
		$selectedText.text('Select categories');
		} else if(selected.length <= 3){
		$selectedText.text(selected.join(', '));
		} else {
		$selectedText.text(selected.length + ' selected');
		}
	}

	// initialize (if checkboxes might be pre-checked server-side)
	updateSelectedText();
	});
</script> -->

<!-- Simple combined script: thumbnail, video preview+duration, multiselect, validation + AJAX -->
<script>
	$(document).ready(function(){

		// ----- Thumbnail preview -----
		$('#profile_image').on('change', function(){
			const file = this.files && this.files[0];
			if (!file) return;
			const reader = new FileReader();
			reader.onload = function(e){
				$('#photoPreview').attr('src', e.target.result);
			};
			reader.readAsDataURL(file);
		});

		// ----- Video preview + duration -----
		$('#video_file').on('change', function(){
			const file = this.files && this.files[0];
			if (!file) return;

			// remove any previous preview
			$('#videoPreview').remove();
			$('#videoPreviewWrap').empty();

			// create preview element
			const previewVideo = document.createElement('video');
			previewVideo.id = 'videoPreview';
			previewVideo.controls = true;
			previewVideo.className = 'rounded mt-2';
			previewVideo.style.maxWidth = '100%';
			previewVideo.src = URL.createObjectURL(file);
			$('#videoPreviewWrap').append(previewVideo);

			// get duration
			const temp = document.createElement('video');
			temp.preload = 'metadata';
			temp.src = URL.createObjectURL(file);
			temp.onloadedmetadata = function() {
				URL.revokeObjectURL(temp.src);
				const dur = (temp.duration || 0);
				$('#videoDuration').val(dur.toFixed(2));
				// optional console log
				console.log('Video duration (s):', dur.toFixed(2));
			};
		});

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
			var selected = $checkboxes.filter(':checked').map(function(){ return this.value; }).get();
			if (selected.length === 0) {
				$selectedText.text('Select categories');
			} else if (selected.length <= 3) {
				$selectedText.text(selected.join(', '));
			} else {
				$selectedText.text(selected.length + ' selected');
			}
		}

		// function updateSelectedText(){
		// 	var names = $checkboxes.filter(':checked').map(function(){
		// 		return $(this).closest('.multiselect-row').find('label').first().text().trim();
		// 	}).get();

		// 	if (names.length === 0) {
		// 		$selectedText.text('Select categories');
		// 	} else if (names.length <= 3) {
		// 		$selectedText.text(names.join(', '));
		// 	} else {
		// 		var firstThree = names.slice(0, 3).join(', ');
		// 		$selectedText.text(firstThree + ' and ' + (names.length - 3) + ' more');
		// 	}
		// }

		// initialize label (in case some checked by server)
		updateSelectedText();

		// ----- Validation + AJAX submit -----
		// make sure jQuery.validate is loaded
		if (typeof $.fn.validate !== 'function') {
			console.error('jquery.validate not loaded.');
			return;
		}

		// custom rule: at least one category
		$.validator.addMethod('requireCategories', function(value, element) {
			return $('input[name="categories[]"]:checked').length > 0;
		}, 'Please select at least one category.');

		$("#videoForm").validate({
			ignore: [], // important, so hidden inputs are validated
			rules: {
				title: { required: true },
				description: { required: true },
				// profile_image: { required: true, extension: "jpg|jpeg|png|webp" },
				// video_file: { required: true, extension: "mp4|mov|avi|mkv|webm" },
				'categories[]': { requireCategories: true }
			},
			messages: {
				title: "Please enter title",
				description: "Please enter description",
				profile_image: "Only JPG, JPEG, PNG, or WEBP files are allowed",
				video_file: "Please upload a valid video (MP4, MOV, AVI, MKV, WEBM)",
				'categories[]': "Please select at least one category"
			},
			errorElement: 'span',
			errorClass: 'text-danger d-block mt-1',
			errorPlacement: function(error, element) {
				// show categories errors after the multiselect button
				if (element.attr('name') === 'categories[]') {
					$('#multiBtn').nextAll('.text-danger').remove();
					error.insertAfter($('#multiBtn'));
				} else {
					error.insertAfter(element);
				}
			},

			submitHandler: function(form) {
			
				var formData = new FormData(form);

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
						$('button[type="submit"]').prop('disabled', false).text('Save Video');
						$('.text-danger').remove();

						if (xhr.responseJSON && xhr.responseJSON.errors) {
							var errors = xhr.responseJSON.errors;
							$.each(errors, function(field, msgs){
								if (field.indexOf('categories') === 0) {
									$('#multiBtn').after('<span class="text-danger d-block mt-1">'+msgs[0]+'</span>');
								} else {
									var $el = $('[name="'+field+'"]');
									if ($el.length) $el.after('<span class="text-danger d-block mt-1">'+msgs[0]+'</span>');
									else $('#videoForm').prepend('<div class="text-danger mb-2">'+msgs[0]+'</div>');
								}
							});
						} else {
							Swal.fire({ title: 'Error', text: 'Something went wrong. Try again later.', icon: 'error' });
						}
					}
				});
			return false; 
			}
		});

		// remove category error when user toggles any checkbox
		$(document).on('change', 'input[name="categories[]"]', function(){
			$('#multiBtn').nextAll('.text-danger').remove();
			$("#videoForm").validate().element($('input[name="categories[]"]').first());
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

<!-- SweetAlert2 -->
		
