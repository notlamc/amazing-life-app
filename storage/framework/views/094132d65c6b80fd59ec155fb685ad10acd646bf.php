<?php echo $__env->make('admin.layout.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="main-wrapper">
    <?php echo $__env->make('admin.layout.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('admin.layout.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">Withdrawal Requests</h3>
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




<!-- ============= -->
 <div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="approveForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Approve Transaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="approveId">

                    <label>Transaction ID (optional)</label>
                    <input type="text" id="transaction_id" class="form-control">

                    <label class="mt-2">Admin Note (optional)</label>
                    <textarea id="admin_note" class="form-control"></textarea>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-success" id="approveSubmitBtn">Approve</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="rejectForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5>Reject Transaction</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="rejectId">
          <div class="mb-2">
            <label>Admin Note (optional)</label>
            <textarea id="reject_admin_note" class="form-control" rows="3"></textarea>
          </div>
          <div class="text-danger">
            Reject karne se user ko request reject dikhai degi. Wallet balance change nahi hoga.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-danger" id="rejectSubmitBtn">Reject</button>
        </div>
      </div>
    </form>
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
        ajax: "<?php echo e(route('superadmin.wallet.request.list')); ?>",
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

                    if (data === 'credit') labelClass = 'badge bg-success';
                    else if (data === 'debit') labelClass = 'badge bg-danger';
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
                           <a href="<?php echo e(route('superadmin.wallet-payment.view', ':id')); ?>"
                                class="text-primary viewUser me-2" 
                                title="View" 
                                target="_blank">
                                    <i class="fa-solid fa-eye fs-5"></i>
                                </a>
                         
                        ${approveBtn}
                        ${rejectBtn}
                    `.replace(':id', id);
                }
            }

        ]
    });
// ========================================
    // open approve modal (already included previously)
    $(document).on('click', '.approveRequest', function () {
        let id = $(this).data('id');
        $('#approveId').val(id);
        // clear fields if needed
        $('#transaction_id').val('');
        $('#admin_note').val('');
        $('#approveModal').modal('show');
    });

    // open reject modal
    $(document).on('click', '.rejectRequest', function () {
        let id = $(this).data('id');
        $('#rejectId').val(id);
        $('#reject_admin_note').val('');
        $('#rejectModal').modal('show');
    });
    // single ajax setup (put once on page)
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    });

 
    // Approve handler (uses Swal with your size/settings)
    $('#approveForm').on('submit', function(e){
    e.preventDefault();
    const id = $('#approveId').val();
    const approveUrl = "<?php echo e(route('superadmin.wallet-payments.approve', ':id')); ?>".replace(':id', id);

    $('#approveSubmitBtn').prop('disabled', true).text('Approving...');

    $.ajax({
        url: approveUrl,
        type: 'POST',
        data: {
        transaction_id: $('#transaction_id').val(),
        admin_note: $('#admin_note').val(),
        _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(res){
        const ok = (typeof res.success !== 'undefined') ? !!res.success : (res.status === 'success');

        if(ok){
            $('#approveModal').modal('hide');

            try {
            $(`[data-id="${id}"]`).closest('tr').find('.statusCell').text('approved');
            $(`[data-id="${id}"]`).remove();
            } catch (e) {
            }

            Swal.fire({
            title: res.message || 'Approved',
            icon: 'success',
            showConfirmButton: false,
            timer: 1500,
            width: '400px',
            padding: '0.8rem',
            customClass: {
                title: 'swal-title',
                popup: 'swal-popup-sm'
            }
            }).then(function() {
            const redirect = res.redirect || res.redirect_url || res.url || null;
            if (redirect) {
                window.location.href = redirect;
            } else {
                window.location.reload();
            }
            });

        } else {
            // show error swal with confirm button
            Swal.fire({
            title: 'Error!',
            text: res.message || 'Something went wrong',
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
        const msg = (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.error)) || 'Server error';
        Swal.fire({
            title: 'Server Error',
            text: msg,
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
        },
        complete: function(){
        $('#approveSubmitBtn').prop('disabled', false).text('Approve');
        }
    });
    });

    // Reject 
    $('#rejectForm').on('submit', function(e){
    e.preventDefault();
    const id = $('#rejectId').val();
    const rejectUrl = "<?php echo e(route('superadmin.wallet-payments.reject', ':id')); ?>".replace(':id', id);

    $('#rejectSubmitBtn').prop('disabled', true).text('Rejecting...');

    $.ajax({
        url: rejectUrl,
        type: 'POST',
        data: {
        admin_note: $('#reject_admin_note').val(),
        _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(res){
        const ok = (typeof res.success !== 'undefined') ? !!res.success : (res.status === 'success');

        if(ok){
            $('#rejectModal').modal('hide');

            setTimeout(function(){
            const redirect = res.redirect || res.redirect_url || res.url || null;
            if (redirect) {
                window.location.href = redirect;
            } else {
                location.reload();
            }
            }, 300);

        } else {
            // show error to user
            Swal.fire({
            title: 'Error!',
            text: res.message || 'Something went wrong',
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
        const msg = (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.error)) || 'Server error';
        Swal.fire({
            title: 'Server Error',
            text: msg,
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
        },
        complete: function(){
        $('#rejectSubmitBtn').prop('disabled', false).text('Reject');
        }
    });
    });




// ============================================
    // Toggle status
    $(document).on('change', '.toggle-status', function(){
        let id = $(this).data('id');
        $.post("<?php echo e(route('superadmin.subscriptions.toggleStatus')); ?>", {id:id,_token:"<?php echo e(csrf_token()); ?>"}, function(res){
            table.ajax.reload(null,false);
        });
    });

    // View subscription modal
    $(document).on('click', '.viewSubscription', function(){
        let id = $(this).data('id');
        $.post("<?php echo e(route('superadmin.subscriptions.details')); ?>", {id:id,_token:"<?php echo e(csrf_token()); ?>"}, function(res){
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
/* --- SweetAlert2 small popup + smaller icon styles --- */
.swal-popup-sm {           /* popup size & padding (used as customClass.popup) */
  width: 400px !important;
  padding: 0.8rem !important;
  box-shadow: 0 6px 24px rgba(0,0,0,0.12);
  font-size: 0.95rem;
}

.swal-title {              /* custom title styling (used as customClass.title) */
  font-size: 1rem !important;
  margin: 0;
  padding-bottom: 0.2rem;
  color: #222;
}

/* smaller icons (success / error / warning) */
.swal2-icon {
  transform: scale(0.75); /* icon chhota karne ke liye */
}

/* custom confirm button class */
.swal-btn-confirm {
  padding: 6px 14px !important;
  font-size: 0.9rem !important;
}


</style>
<?php /**PATH F:\xampp\htdocs\amazinglifeapp\resources\views/admin/wallet_requests/index.blade.php ENDPATH**/ ?>