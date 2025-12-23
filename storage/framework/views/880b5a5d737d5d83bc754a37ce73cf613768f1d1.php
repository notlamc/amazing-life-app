<?php echo $__env->make('admin.layout.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="main-wrapper">
	<?php echo $__env->make('admin.layout.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	<?php echo $__env->make('admin.layout.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

	<div class="page-wrapper">
		<div class="content container-fluid">
            <div class="breadcrumb-box">
				<ol>
					<li ><a href="<?php echo e(route('superadmin.user.list.page')); ?>">Users List</a></li>
					<li class="active">Transaction History</li>
				</ol>
			</div>
			<div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">Transaction History</h3>
                    </div>

                    <div class="col-sm-11 d-flex justify-content-end">
                         <h6 class="page-title">
                            Wallet Current Balance :   <?php echo e($currentBalence->balance ?? 0); ?> ₹ 
                        </h6>
                    </div>
                </div>
            </div>

           
			<div id="statusMsg" style="display:none;" class="alert text-center mx-auto"></div>

			<div class="row">
				<div class="col-sm-12">
					<div class="card">
						<div class="card-body">
							<div class="table-responsive">
								<table id="transactionsTable" class="table table-hover table-striped table-bordered text-center align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Sr</th>
                                            <th>Transaction ID</th>
                                            <th>Type</th>
                                            <th>Amount ($)</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Balance After ($)</th>
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

<!--  DataTables JS & CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {

    let userId = "<?php echo e($id); ?>";

    let url = "<?php echo e(route('superadmin.user.transactions.data', ':id')); ?>";
    url = url.replace(':id', userId);

    $('#transactionsTable').DataTable({
        ajax: {
            url: url,
            dataSrc: "data"
        },
        columns: [
            { 
                data: null,
                render: (d, t, r, meta) => meta.row + 1
            },
            { data: "transaction_id" },

            { 
                data: "type",
                render: function(type){
                    if(type === 'credit'){
                        return `<span class="badge bg-success">Credit</span>`;
                    }
                    return `<span class="badge bg-danger">Debit</span>`;
                }
            },

            { 
                data: "commission_amount",
                render: amount => "$ " + parseFloat(amount).toFixed(2)
            },

            {
                data: "created_at",
                render: function(dateStr) {

                    if (!dateStr) return '-';
                    dateStr = dateStr.replace(/\.?\d+Z$/, '');
                    dateStr = dateStr.replace('T', ' ');
                    return dateStr.substring(0, 19);
                }
            },

            { 
                data: "status",
                render: function(status){
                    if(status === 'approved') return `<span class="badge bg-success">Success</span>`;
                    if(status === 'pending') return `<span class="badge bg-warning text-dark">Pending</span>`;
                    return `<span class="badge bg-danger">Failed</span>`;
                }
            },

            { 
                data: "balance_amount",
                render: bal => "$ " + parseFloat(bal).toFixed(2)
            },
        ]
    });

});
</script>
<style>
	.breadcrumb-box {
		margin: 5px 0 20px 0; 
		padding: 0;
	}

	.breadcrumb-box ol {
		margin: 0;
		padding: 0;
		list-style: none;
		display: flex;
		align-items: center;
		font-size: 17px; /* professional size */
		font-weight: 500;
		color: #6c757d;
	}

	.breadcrumb-box li a {
		color: #ed892b;
		text-decoration: none;
		transition: 0.2s;
	}

	.breadcrumb-box li a:hover {
		color: #ea7305ff;
	}

	.breadcrumb-box li + li:before {
		content: "/";
		padding: 0 10px;
		color: #b4b4b4;
		font-weight: 400;
	}

	.breadcrumb-box li.active {
		color: #343a40;
		font-weight: 600;
	}
</style>







<?php /**PATH F:\xampp\htdocs\amazinglifeapp\resources\views/admin/user/user-transactions.blade.php ENDPATH**/ ?>