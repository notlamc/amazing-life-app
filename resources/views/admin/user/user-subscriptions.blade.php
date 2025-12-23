@include('admin.layout.header')

<div class="main-wrapper">
	@include('admin.layout.navbar')
	@include('admin.layout.sidebar')

	<div class="page-wrapper">
		<div class="content container-fluid">
            <div class="breadcrumb-box">
				<ol>
					<li ><a href="{{ route('superadmin.user.list.page') }}">Users List</a></li>
					<li class="active">Subscription History</li>
				</ol>
			</div>
			<div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">Subscription History</h3>
                    </div>

                    <!-- <div class="col-sm-11 d-flex justify-content-end">
                         <h6 class="page-title">
                            Wallet Current Balance :   {{ $currentBalence->balance ?? 0 }} ₹ 
                        </h6>
                    </div> -->
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
                                        <tr class="text-center">
                                            <th>Sr</th>
                                            <th class="text-center">Transaction ID</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th class="text-center">Amount ($)</th>
                                            <th class="text-center">Payment Date</th>
                                            <th class="text-center">Status</th>                                           
                                                                                       
                                                                                        
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
$(document).ready(function () {

    let userId = "{{ $id }}";

    let url = "{{ route('superadmin.user.subscriptions.data', ':id') }}";
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
                data: 'start_date',
                render: function(data) {
                    let dateStr = data ;
                    if (!dateStr) return '-';

                    // Format date like "24 DEC 2025"
                    let dateObj = new Date(dateStr);
                    let day = dateObj.getDate();
                    let month = dateObj.toLocaleString('en-US', { month: 'short' }).toUpperCase();
                    let year = dateObj.getFullYear();

                    return `${day} ${month} ${year}`;
                }
            },
            {
                data: 'end_date',
                render: function(data) {
                    let dateStr = data ;
                    if (!dateStr) return '-';

                    // Format date like "24 DEC 2025"
                    let dateObj = new Date(dateStr);
                    let day = dateObj.getDate();
                    let month = dateObj.toLocaleString('en-US', { month: 'short' }).toUpperCase();
                    let year = dateObj.getFullYear();

                    return `${day} ${month} ${year}`;
                }
            },

            // { 
            //     data: "type",
            //     render: function(type){
            //         if(type === 'credit'){
            //             return `<span class="badge bg-success">Credit</span>`;
            //         }
            //         return `<span class="badge bg-danger">Debit</span>`;
            //     }
            // },

            { 
                data: "amount",
                render: amount => "$ " + parseFloat(amount).toFixed(2)
            },
            {
                data: 'created_at',
                render: function(data) {
                    let dateStr = data ;
                    if (!dateStr) return '-';

                    // Format date like "24 DEC 2025"
                    let dateObj = new Date(dateStr);
                    let day = dateObj.getDate();
                    let month = dateObj.toLocaleString('en-US', { month: 'short' }).toUpperCase();
                    let year = dateObj.getFullYear();

                    return `${day} ${month} ${year}`;
                }
            },
            
            {
                data: null,
                render: function(row) {
                    let statusBadge = '';
                    
                    // Original status display
                    if(row.status === 'success') {
                        statusBadge = `<span class="badge bg-success">Paid</span>`;
                    } 
                    else if(row.status === 'pending') {
                        statusBadge = `<span class="badge bg-warning text-dark">Pending</span>`;
                    } 
                    else {
                        statusBadge = `<span class="badge bg-danger">Failed</span>`;
                    }

                    // Check expired or running
                    let today = new Date().toISOString().split('T')[0];
                    let endDate = row.end_date; // must exist in row
                    
                    let planStatus = (endDate < today) 
                        ? `<span class="badge bg-danger ms-2">Expired</span>` 
                        : `<span class="badge bg-primary ms-2">Running</span>`;

                    return statusBadge + " " + planStatus;
                }
            }

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







