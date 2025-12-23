<?php echo $__env->make('admin.layout.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="main-wrapper">
    <?php echo $__env->make('admin.layout.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('admin.layout.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">Subscriptions</h3>
                        <!-- <a href="<?php echo e(route('superadmin.subscriptions.create')); ?>" class="btn btn-primary float-end">Add Subscription</a> -->
                    </div>
                </div>
            </div>

            <div id="statusMsg" style="display:none;" class="alert text-center mx-auto"></div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="subscriptionTable" class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Name</th>
                                            <th>Price</th>
                                            <th>Duration (Days)</th>
                                            <th>Commission (%)</th>
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

<?php echo $__env->make('admin.layout.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<!-- DataTables JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function(){

    let table = $('#subscriptionTable').DataTable({
        ajax: "<?php echo e(route('superadmin.subscriptions.list')); ?>",
        columns: [
            { data: null, render: (data, type, row, meta) => meta.row + 1 },
            { data: 'name', defaultContent: '-' },
            { data: 'price', render: data => '$ ' + data },
            { data: 'duration_days', defaultContent: '-' },
            { data: 'commission_percentage', defaultContent: '-' },
            {
                data: 'status',
                render: function(data,type,row){
                    let checked = data === 'active' ? 'checked' : '';
                    return `<label class="switch">
                                <input type="checkbox" class="toggle-status" data-id="${row.id}" ${checked}>
                                <span class="slider round"></span>
                            </label>`;
                }
            },
            {
                data: 'id',
                render: function(id){
                    return `
                        <a href="<?php echo e(url('superadmin/subscriptions')); ?>/${id}/edit" class="text-primary viewUser me-2" title="View">
                            
                                <i class="fa-solid fa-pen fs-5"></i>                       
                            </a>
                        <a href="<?php echo e(url('superadmin/subscriptions')); ?>/${id}/view" class="text-info me-2" title="View">
                            <i class="fa-solid fa-eye fs-5"></i>
                        </a>

                      
                        <a href="javascript:void(0)" class="text-danger deleteSubscription" data-id="${id}" title="Delete">
                            <i class="fa-solid fa-trash fs-5"></i>
                        </a>`;                   
                       
                }
            }
        ]
    });



    //  Status toggle
		$(document).on('change', '.toggle-status', function(){
			let id = $(this).data('id');
            
			$.ajax({ 
				url: "<?php echo e(route('superadmin.subscriptions.toggleStatus')); ?>",
				type: "POST",
				data: { id: id, _token: "<?php echo e(csrf_token()); ?>" },
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




    // Delete subscription
    $(document).on('click', '.deleteSubscription', function(){
        let id = $(this).data('id');
        Swal.fire({
            title:'Delete Subscription?',
            showCancelButton:true,
            confirmButtonText:'Yes',
            cancelButtonText:'No'
        }).then((result)=>{
            if(result.isConfirmed){
                $.post("<?php echo e(route('superadmin.subscriptions.destroy')); ?>",{id:id,_token:"<?php echo e(csrf_token()); ?>"},function(res){
                    if(res.success){
                        Swal.fire('Deleted!','','success');
                        table.ajax.reload(null,false);
                    }
                });
            }
        });
    });

});
</script>

<style>
    /* Toggle switch CSS */
    .switch {position: relative; display: inline-block; width:36px; height:18px;}
    .switch input {display:none;}
    .slider {position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background:#dc3545; transition:.3s; border-radius:20px;}
    .slider:before {position:absolute; content:""; height:12px; width:12px; left:3px; bottom:3px; background:#fff; transition:.3s; border-radius:50%;}
    input:checked + .slider {background:#28a745;}
    input:checked + .slider:before {transform:translateX(18px);}
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
</style>
<?php /**PATH F:\xampp\htdocs\amazinglifeapp\resources\views/admin/subscriptions/subscription-list.blade.php ENDPATH**/ ?>