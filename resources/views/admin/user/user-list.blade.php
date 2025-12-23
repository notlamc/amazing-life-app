@include('admin.layout.header')

<div class="main-wrapper">
	@include('admin.layout.navbar')
	@include('admin.layout.sidebar')

	<div class="page-wrapper">
		<div class="content container-fluid">
			<div class="page-header">
				<div class="row">
					<div class="col-sm-12">
						<h3 class="page-title">List of Users</h3>
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
											<th>Wallet Balance</th>
											<th>Referral Code</th>
											<th>Refferal Count</th>
											<th>Transaction</th>
											<th>Subscription</th>
											<th>Extends Date</th>
											<th>Status</th>
											<th>Action</th>
										</tr>
									</thead>
								</table>
							</div>
						</div>
					</div>
				</div>			
			</div>
		</div>			
	</div>
</div>


@include('admin.layout.footer')	

<!--  DataTables JS & CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
	$(document).ready(function(){

		let table = $('#userTable').DataTable({
			ajax: "{{ route('superadmin.user.list') }}",
			columns: [
				{ data: null, render: (data, type, row, meta) => meta.row + 1 },
				{ data: 'name', defaultContent: '-' },
				{ 
					data: 'phone_number', 
					defaultContent: '-', 
					render: function (data, type, row) {
						// Add '$' sign only if data exists and is numeric
						if (data && !isNaN(data)) {
							return '$ ' + data;
						} else {
							return data || '$ 0';
						}
					}
				},
				{ data: 'referral_code', defaultContent: '-' },
				{ data: 'affiliate', name: 'affiliate', orderable: false, searchable: false },
				{
					data: 'id',
					render: function(id) {
						let url = "{{ route('superadmin.user.transactions', ':id') }}";
						url = url.replace(':id', id);

						return `
							<a href="${url}" class="btn btn-warning btn-xs">
								Transaction History
							</a>

						`;
					}
				},
				{
					data: 'id',
					render: function(id) {
						let url = "{{ route('superadmin.user.subscriptions', ':id') }}";
						url = url.replace(':id', id);

						return `
							<a href="${url}" class="btn btn-success btn-xs">
								Subscription History
							</a>

						`;
					}
				},

				
				{
					data: null,
					orderable: false,
					searchable: false,
					render: function(data, type, row) {
						let extendDate = row.extend_date || row.end_date || null;
						let displayDate = extendDate ? extendDate.split(' ')[0] : 'N/A';
						let btnLabel = `Extend (${displayDate})`;
						let subId = row.subscription_record_id || row.id;
						return `<button class="btn btn-primary btn-xs extend-btn" data-subid="${subId}" data-name="${row.name || ''}" data-enddate="${row.end_date || ''}" data-extenddate="${row.extend_date || ''}">${btnLabel}</button>`;
					}
				},



				{ 
					data: 'status',
					render: function(data, type, row){
						let checked = (data === 'active') ? 'checked' : '';
						return `
							<label class="switch">
								<input type="checkbox" class="toggle-status" data-id="${row.id}" ${checked}>
								<span class="slider round"></span>
							</label>`;
					}
				},
				{
					data: 'id',
					render: function(id){
						return `
							<a href="/superadmin/user/view/${id}" 
							class="text-info me-2" 
							title="View">
								<i class="fa-solid fa-eye fs-5"></i>
							</a>

							<a href="javascript:void(0)" 
							class="text-danger deleteUser" 
							data-id="${id}" 
							title="Delete">
								<i class="fa-solid fa-trash fs-5"></i>
							</a>
						`;
					}
				}

			]
		});

		//  Status toggle
		$(document).on('change', '.toggle-status', function(){
			let id = $(this).data('id');
			$.ajax({ 
				url: "{{ route('superadmin.user.status') }}",
				type: "POST",
				data: { id: id, _token: "{{ csrf_token() }}" },
				success: function(res){
					if(res.success){
						table.ajax.reload(null, false);

						$('#statusMsg')
							.removeClass('alert-danger')
							.addClass('alert-success')
							.text(res.message || 'User status updated successfully!')
							.fadeIn();

						setTimeout(() => { $('#statusMsg').fadeOut(); }, 3000);
					} else {
						$('#statusMsg')
							.removeClass('alert-success')
							.addClass('alert-danger')
							.text(res.message || 'Something went wrong!')
							.fadeIn();

						setTimeout(() => { $('#statusMsg').fadeOut(); }, 3000);
					}
				}
			});
		});

		//  Delete user
		$(document).on('click', '.deleteUser', function(){
			let id = $(this).data('id');

			Swal.fire({
				title: 'Delete User?',
				text: "Are you sure you want to delete this user?",
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
						url: "{{ route('superadmin.user.delete') }}",
						type: "POST",
						data: { id: id, _token: "{{ csrf_token() }}" },
						success: function(res){
							if(res.success){
								Swal.fire({
									title: 'Deleted!',
									text: 'User has been deleted successfully.',
									showConfirmButton: false,
									timer: 1200
								});
								table.ajax.reload(null, false);
							}
						}
					});
				}
			});
		});


		// ---------- Extend button click handler ----------
		$(document).on('click', '.extend-btn', function(){
			let subId = $(this).data('subid');
			let name = $(this).data('name') || 'User';
			let currentExtend = $(this).data('extenddate') || $(this).data('enddate') || null;
			let displayDate = currentExtend ? currentExtend.split(' ')[0] : 'N/A';

			Swal.fire({
				title: `<span style="font-size:20px;">Extend subscription for ${name}?</span>`,
				html: `
					<p style="font-size:15px; margin-bottom:10px;">
						Current base date: <strong>${displayDate}</strong>
					</p>
					<p style="font-size:15px; margin:0;">
						Are you sure you want to extend this subscription?
					</p>
				`,
				showCancelButton: true,
				confirmButtonText: 'Yes, Extend',
				cancelButtonText: 'Cancel',
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				width: 400,
				padding: '10px',
				customClass: {
					confirmButton: 'swal-btn-sm',
					cancelButton: 'swal-btn-sm'
				}
			}).then((result) => {
				if (result.isConfirmed) {
					$.ajax({
						url: "{{ route('superadmin.user.extend') }}",
						type: "POST",
						data: {
							subscription_id: subId,
							_token: "{{ csrf_token() }}"
						},
						success: function(res){
							if (res.success) {

								Swal.fire({
									title: `<span style="font-size:20px;">Extended!</span>`,
									html: `
										<p style="font-size:16px; margin-bottom:0;">
											Subscription Extended Successfully!
										</p>
									`,
									icon: 'success',
									width: 400, 
									padding: '10px 10px',
									showConfirmButton: false,
									timer: 1500,
									customClass: {
										icon: 'swal-small-icon'
									}
								});

								if (typeof table !== 'undefined' && table.ajax) {
									table.ajax.reload(null, false);
								} else {
									location.reload();
								}

							} else {
								Swal.fire('Error', res.message || 'Could not extend subscription', 'error');
							}
						},


						error: function(xhr){
							let msg = 'Something went wrong';
							if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
							Swal.fire('Error', msg, 'error');
						}
					});
				}
			});
		});

	});
</script>

<!-- Toggle Switch CSS -->


<style>
    .swal-small-icon .swal2-icon {
        font-size: 1rem !important;
        width: 40px !important;
        height: 40px !important;
        margin: 6px auto !important;
    }
    .swal2-title {
        margin: 5px 0 !important;
    }
    .swal2-html-container {
        margin: 0 !important;
    }
	.swal-btn-sm {
		padding: 4px 12px !important;
		font-size: 16px !important;
	}
	.swal-small-icon {
		width: 80px !important;
		height: 80px !important;
		margin: 4px auto !important;
	}

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

	.deleteUser i {
		color: #e74c3c;
		transition: all 0.2s ease;
	}
	.deleteUser i:hover {
		color: #c0392b;
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

