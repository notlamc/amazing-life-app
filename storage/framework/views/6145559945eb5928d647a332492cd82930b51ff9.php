<?php echo $__env->make('admin.layout.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="main-wrapper">
	<?php echo $__env->make('admin.layout.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	<?php echo $__env->make('admin.layout.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

	<div class="page-wrapper">
		<div class="content container-fluid">
			<div class="page-header">
				<div class="row align-items-center">
					<div class="col-sm-6">
						<h3 class="page-title mb-0">List of Notification Template</h3>
					</div>
					<div class="col-sm-6 text-end">
						<a href="<?php echo e(route('superadmin.notification.create')); ?>" class="btn btn-primary">
							<i class="fa fa-plus me-1"></i> Add Notification Template
						</a>
					</div>
				</div>
			</div>
			<div id="statusMsg" style="display:none;" class="alert text-center mx-auto"></div>

			<div class="row">
				<div class="col-sm-12">
					<div class="card">
						<div class="card-body">
							<div class="table-responsive">
								<table id="userTable" class="table table-hover table-center mb-0">
									<thead>
										<tr>
											<th>Sr.</th>
											<th>Name</th>
											<th>Type</th>
											<th>Action</th>
										</tr>
									</thead>
								</table>
							</div>
						</div>
					</div>
				</div>			
			</div>


			<!-- Modal -->
			<div class="modal fade" id="userModal" tabindex="-1">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-header bg-primary text-white">
							<h5 class="modal-title">User Details</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
						</div>
						<div class="modal-body">
							<ul class="list-group" id="userDetails"></ul>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>
		</div>			
	</div>
</div>


<?php echo $__env->make('admin.layout.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>	

<!--  DataTables JS & CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
	$(document).ready(function(){

		let table = $('#userTable').DataTable({
			ajax: "<?php echo e(route('superadmin.notification.list')); ?>",
			columns: [
				{ data: null, render: (data, type, row, meta) => meta.row + 1 },
				{ data: 'name', defaultContent: '-' },
				{
					data: 'type',
					defaultContent: '-',
					render: function (data) {
						if (!data) return '-';

						data = data.toUpperCase();

						if (data === 'EMAIL') {
							return '<span class="badge bg-primary">' + data + ' NOTIFICATION </span>';
						}

						if (data === 'PUSH') {
							return '<span class="badge bg-info">' + data + ' NOTIFICATION </span>';
						}

						return data;
					}
				},		
				{ 
					data: 'id',
					render: function(id){
						return `
							<a href="javascript:void(0)" class="text-info editVideo me-2" data-id="${id}" title="Edit">
								<i class="fa-solid fa-pen fs-5"></i>
							</a>
							<a href="<?php echo e(url('superadmin/notification')); ?>/${id}/view" class="text-info me-2" title="View">
								<i class="fa-solid fa-eye fs-5"></i>
							</a>`;
					}
				}
			]
		});

		// Status toggle
		$(document).on('change', '.toggle-status', function(){
			let id = $(this).data('id');
			$.ajax({ 
				url: "<?php echo e(route('superadmin.notification.status')); ?>",
				type: "POST",
				data: { id: id, _token: "<?php echo e(csrf_token()); ?>" },
				success: function(res){
					let msg = $('#statusMsg');
					if(res.success){
						table.ajax.reload(null, false);
						msg.removeClass('alert-danger').addClass('alert-success')
						.text(res.message || 'Video status updated successfully!').fadeIn();
					} else {
						msg.removeClass('alert-success').addClass('alert-danger')
						.text(res.message || 'Something went wrong!').fadeIn();
					}
					setTimeout(() => { msg.fadeOut(); }, 3000);
				}
			});
		});

		// Delete agent
		$(document).on('click', '.deleteVideo', function(){
			let id = $(this).data('id');

			Swal.fire({
				title: 'Delete Video?',
				text: "Are you sure you want to delete this video?",
				showCancelButton: true,
				confirmButtonText: 'Yes',
				cancelButtonText: 'No',
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				icon: null,
				width: '400px',
				padding: '0.8rem',
				customClass: {
					title: 'swal-title',
					popup: 'swal-popup-sm',
					confirmButton: 'swal-btn-confirm',
					cancelButton: 'swal-btn-cancel'
				}
			}).then((result) => {
				if (result.isConfirmed) {
					$.ajax({
						url: "<?php echo e(route('superadmin.notification.delete')); ?>",
						type: "POST",
						data: { id: id, _token: "<?php echo e(csrf_token()); ?>" },
						success: function(res){
							
							if (res.success) {
									Swal.fire({
										title: 'Deleted successful!',
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
										table.ajax.reload(null, false);
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
						}
					});
				}
			});
		});

		// View user details
		$(document).on('click', '.viewUser', function(){
			let id = $(this).data('id');
			$.ajax({
				url: "<?php echo e(route('superadmin.notification.details')); ?>",
				type: "POST",
				data: { id: id, _token: "<?php echo e(csrf_token()); ?>" },
				success: function(res){
					if (res.success) {
						let u = res.data;

						let statusBtn = '';
						if (u.status?.toLowerCase() === 'active') {
							statusBtn = `<button class="btn btn-success btn-xs px-2 py-0" disabled>Active</button>`;
						} else {
							statusBtn = `<button class="btn btn-danger btn-xs px-2 py-0" disabled>Inactive</button>`;
						}

						let html = `
							<li class="list-group-item"><strong>Name:</strong> ${u.name ?? '-'}</li>
							<li class="list-group-item"><strong>Email:</strong> ${u.email ?? '-'}</li>
							<li class="list-group-item"><strong>Gender:</strong> ${u.gender ?? '-'}</li>
							<li class="list-group-item"><strong>Phone:</strong> ${u.phone_number ?? '-'}</li>
							<li class="list-group-item"><strong>Age:</strong> ${u.age ?? '-'}</li>
							<li class="list-group-item"><strong>Status:</strong> ${statusBtn}</li>
						`;

						$('#userDetails').html(html);
						$('#userModal').modal('show');
					}

				}
			});
		});

		// get edit id
		$(document).on('click', '.editVideo', function() {
			let id = $(this).data('id');
			
			let url = "<?php echo e(route('superadmin.notification.edit', ':id')); ?>";
			url = url.replace(':id', id);

			window.location.href = url;
		});
	});

</script>

<!-- Toggle Switch CSS -->
<style>
	.switch {
		position: relative;
		display: inline-block;
		width: 36px; 
		height: 18px;
		vertical-align: middle;
	}
	.switch input { display: none; }

	.slider {
		position: absolute;
		cursor: pointer;
		top: 0; left: 0; right: 0; bottom: 0;
		background-color: #dc3545; 
		transition: .3s;
		border-radius: 20px;
	}

	.slider:before {
		position: absolute;
		content: "";
		height: 12px;
		width: 12px;
		left: 3px;
		bottom: 3px;
		background-color: white;
		transition: .3s;
		border-radius: 50%;
	}

	input:checked + .slider {
		background-color: #28a745; /* green when active */
	}

	input:checked + .slider:before {
		transform: translateX(18px);
	}
	.viewUser i {
		color: #0dcaf0;
		transition: all 0.2s ease;
	}
	.viewUser i:hover {
		color: #0bbcd6;
		transform: scale(1.15);
	}

	.deleteAgent i {
		color: #e74c3c;
		transition: all 0.2s ease;
	}
	.deleteAgent i:hover {
		color: #c0392b;
		transform: scale(1.15);
	}
	.editAgent i {
		color: #4b61d2;
		transition: all 0.2s ease;
	}
	.editAgent i:hover {
		color: #4b61d2;
		transform: scale(1.15);
	}

	.btn-xs {
		font-size: 11px;
		padding: 2px 6px;
		border-radius: 4px;
		line-height: 1.2;
	}

	.swal-popup-sm {
		font-size: 15px;
		border-radius: 12px;
		padding: 1rem !important;
	}
	.swal-title {
		font-size: 18px;
		font-weight: 600;
	}
	.swal-btn-confirm, .swal-btn-cancel {
		padding: 6px 16px !important;
		font-size: 14px !important;
		border-radius: 8px !important;
	}

	
	#statusMsg {
		width: auto;
		display: inline-block;
		padding: 6px 14px;
		border-radius: 20px;
		font-size: 13px;
		font-weight: 500;
		margin-bottom: 10px;
		position: relative;
		top: 5px;
	}
	.alert-success {
		background-color: #d1e7dd !important;
		color: #0f5132 !important;
		border: 1px solid #badbcc !important;
	}
	.alert-danger {
		background-color: #f8d7da !important;
		color: #842029 !important;
		border: 1px solid #f5c2c7 !important;
	}
	


</style>

<?php /**PATH F:\xampp\htdocs\amazinglifeapp\resources\views/admin/notification/notification-list.blade.php ENDPATH**/ ?>