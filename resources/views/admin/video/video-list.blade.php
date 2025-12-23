@include('admin.layout.header')

<div class="main-wrapper">
	@include('admin.layout.navbar')
	@include('admin.layout.sidebar')

	<div class="page-wrapper">
		<div class="content container-fluid">
			<div class="page-header">
				<div class="row align-items-center">
					<div class="col-sm-6">
						<h3 class="page-title mb-0">List of Videos</h3>
					</div>
					<div class="col-sm-6 text-end">
						<a href="{{ route('superadmin.videos.create') }}" class="btn btn-primary">
							<i class="fa fa-plus me-1"></i> Add Video
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
											<th>Thumbnail</th>
											<th>Video Title</th>
											<th>Categories</th>
											<th>Views</th>
											<th>Likes </th>
											<th>Duration</th>
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


			<!-- Modal -->
			<!-- <div class="modal fade" id="userModal" tabindex="-1">
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
			</div> -->
		</div>			
	</div>
</div>

<!-- Categories Modal (put near end of <body>) -->
<div class="modal fade" id="catModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Categories</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="catBody">
        <!-- categories yaha show hongi -->
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
			ajax: "{{ route('superadmin.video.list') }}",
			columns: [
			{ data: null, render: (data, type, row, meta) => meta.row + 1 },
			{
				data: 'thumbnail_path',
				render: function(data, type, row){
				let imgUrl = data ? data : 'https://via.placeholder.com/50';
				return `<img src="${imgUrl}" alt="${row.name || ''}" class="rounded-circle" width="50" height="50">`;
				},
				orderable: false,
				searchable: false
			},
			{ data: 'title', defaultContent: '-' },

			{
				data: 'categories_name',
				render: function(data){
					return `<button class="btn btn-xs  btn-secondary viewCats" data-cats="${data}">
								View Categories
							</button>`;
				}
			},


			{ data: 'views', defaultContent: '-' },
			{ data: 'likes', defaultContent: '-' },
			{
				data: 'duration',
				defaultContent: '-',
				render: function(data){
				if (!data || isNaN(data)) return '-';
				let minutes = Math.floor(data / 60);
				let seconds = data % 60;
				let formatted = `${minutes}:${seconds.toString().padStart(2, '0')} Min`;
				return formatted;
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
					<a href="javascript:void(0)" class="text-info editVideo me-2" data-id="${id}" title="Edit">
					<i class="fa-solid fa-pen fs-5"></i>
					</a>
					<a href="/superadmin/video/view/${id}" class="text-primary viewUser me-2" title="View">
					<i class="fa-solid fa-eye fs-5"></i>
					</a>
					<a href="javascript:void(0)" class="text-danger deleteVideo" data-id="${id}" title="Delete">
					<i class="fa-solid fa-trash fs-5"></i>
					</a>`;
				}
			}
			]
		});

		$(document).on('click', '.viewCats', function () {
			let cats = $(this).data('cats') || "";

			let items = cats.split(',').map(c => c.trim());

			if (!items.length) {
				$("#catBody").html("No categories");
				$("#catModal").modal("show");
				return;
			}

			let html = "<ol style='padding-left: 20px;'>";

			items.forEach(c => {
				html += `
					<li>${c}</li>
					<hr style="margin: 4px 0;">
				`;
			});

			html += "</ol>";

			$("#catBody").html(html);
			$("#catModal").modal("show");
		});




		// Status toggle
		$(document).on('change', '.toggle-status', function(){
			let id = $(this).data('id');
			$.ajax({ 
				url: "{{ route('superadmin.video.status') }}",
				type: "POST",
				data: { id: id, _token: "{{ csrf_token() }}" },
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
						url: "{{ route('superadmin.video.delete') }}",
						type: "POST",
						data: { id: id, _token: "{{ csrf_token() }}" },
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

		// get edit id
		$(document).on('click', '.editVideo', function() {
			let id = $(this).data('id');
			
			let url = "{{ route('superadmin.video.edit', ':id') }}";
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

