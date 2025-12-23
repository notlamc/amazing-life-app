@include('admin.layout.header')

<div class="main-wrapper">
    @include('admin.layout.navbar')
    @include('admin.layout.sidebar')

    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">Withdrawal Requests</h3>
                        <!-- <a href="{{ route('superadmin.subscriptions.create') }}" class="btn btn-primary float-end">Add Subscription</a> -->
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
                                            <th>User</th>
                                            <th>Amount</th>
                                            <th>Type</th>
                                            <th>Txn ID</th>
                                            <th>Requested</th>
                                            <th>Status</th>
                                            <th>Action Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>          
            </div>

            <!-- Modal -->
            <div class="modal fade" id="subscriptionModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Subscription Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <ul class="list-group" id="subscriptionDetails"></ul>
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

@include('admin.layout.footer')

<!-- DataTables JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function(){

    let table = $('#subscriptionTable').DataTable({
        ajax: "{{ route('superadmin.wallet.request.list') }}",
        columns: [
            { data: null, render: (data, type, row, meta) => meta.row + 1 },
           {
                data: 'username',
                defaultContent: '-',
                render: function(data) {
                    if (!data) return '-';
                    return data.charAt(0).toUpperCase() + data.slice(1);
                }
            },

            { data: 'amount', render: data => '$ ' + data },            
            {
                data: 'type',
                render: function(data) {
                    let labelClass = '';

                    if (data === 'approved') labelClass = 'badge bg-success';
                    else if (data === 'pending') labelClass = 'badge bg-warning';
                    else labelClass = 'badge bg-danger';

                    return `<span class="${labelClass}">${data.toUpperCase()}</span>`;
                }
            },            
            { data: 'transaction_id', defaultContent: '-' },
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
                data: 'status',
                render: function(data) {
                    let labelClass = '';

                    if (data === 'approved') labelClass = 'badge bg-success';
                    else if (data === 'pending') labelClass = 'badge bg-warning';
                    else if (data === 'rejected') labelClass = 'badge bg-danger';

                    return `<span class="${labelClass}">${data.toUpperCase()}</span>`;
                }
            },
            // Approved/Rejected date column
            {
                data: null,
                render: function(row) {
                    let dateStr = row.approved_at || row.rejected_at;
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
                data: 'id',
                render: function(id, type, row) {

                    let approveBtn = '';
                    let rejectBtn = '';

                    // Only show approve/reject if status is pending
                    if (row.status === 'pending') {
                        approveBtn = `
                            <a href="javascript:void(0)" class="text-success approveRequest me-2" data-id="${id}" title="Approve">
                                <i class="fa-solid fa-check fs-5"></i>
                            </a>
                        `;

                        rejectBtn = `
                            <a href="javascript:void(0)" class="text-danger rejectRequest me-2" data-id="${id}" title="Reject">
                                <i class="fa-solid fa-xmark fs-5"></i>
                            </a>
                        `;
                    }

                    return `
                        <a href="/admin/video/view/${id}" class="text-primary viewUser me-2" title="View">
                            <i class="fa-solid fa-eye fs-5"></i>
                        </a>
                        ${approveBtn}
                        ${rejectBtn}
                    `;
                }
            }

        ]
    });

    // Toggle status
    $(document).on('change', '.toggle-status', function(){
        let id = $(this).data('id');
        $.post("{{ route('superadmin.subscriptions.toggleStatus') }}", {id:id,_token:"{{ csrf_token() }}"}, function(res){
            table.ajax.reload(null,false);
        });
    });

    // View subscription modal
    $(document).on('click', '.viewSubscription', function(){
        let id = $(this).data('id');
        $.post("{{ route('superadmin.subscriptions.details') }}", {id:id,_token:"{{ csrf_token() }}"}, function(res){
            if(res.success){
                let s = res.data;
                let html = `
                    <li class="list-group-item"><strong>Name:</strong> ${s.name}</li>
                    <li class="list-group-item"><strong>Price:</strong> $ ${s.price}</li>
                    <li class="list-group-item"><strong>Duration:</strong> ${s.duration_days} days</li>
                    <li class="list-group-item"><strong>Commission:</strong> ${s.commission_percentage}%</li>
                    <li class="list-group-item"><strong>Status:</strong> ${s.status}</li>
                `;
                $('#subscriptionDetails').html(html);
                $('#subscriptionModal').modal('show');
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
                $.post("{{ route('superadmin.subscriptions.destroy') }}",{id:id,_token:"{{ csrf_token() }}"},function(res){
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
</style>
