<?php $__env->startSection('title', 'Query (Task)'); ?>

<?php $__env->startSection('style'); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<style>
    .inquiry-filter-btn {
        border-radius: 50px;
        font-size: 0.87rem;
        padding: 6px 16px;
    }
    .inquiry-filter-btn.active {
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Query (Task)</h4>
                <div>
                    <button id="exportBtn" class="btn btn-success btn-sm">
                        <i class="fa fa-file-excel"></i> Export
                    </button>
                    <button id="addInquiryBtn" class="btn btn-light btn-sm text-dark ms-1">
                        <i class="fa fa-plus"></i> Add Inquiry
                    </button>
                </div>
            </div>

            <div class="card-body">
                <!-- Status Filters -->
                <div class="d-flex flex-wrap mb-3">
                    <button class="btn btn-outline-primary inquiry-filter-btn me-2 active" data-status="">
                        All <span class="badge bg-primary ms-1" id="countAll"><?php echo e($counts['all']); ?></span>
                    </button>
                    <button class="btn btn-outline-warning inquiry-filter-btn me-2" data-status="Pending">
                        Pending <span class="badge bg-warning text-dark ms-1" id="countPending"><?php echo e($counts['Pending']); ?></span>
                    </button>
                    <button class="btn btn-outline-success inquiry-filter-btn" data-status="Done">
                        Done <span class="badge bg-success ms-1" id="countDone"><?php echo e($counts['Done']); ?></span>
                    </button>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="inquiryTable" style="width:100%">
                        <thead class="bg-primary text-white text-center">
                            <tr>
                                <th>Date</th>
                                <th>Passenger Name</th>
                                <th>Description</th>
                                <th>Visa Price</th>
                                <th>Payment Received</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Added By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Inquiry Modal -->
<div class="modal fade" id="addInquiryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="addInquiryForm">
                <?php echo csrf_field(); ?>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Add Inquiry</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Passenger Name *</label>
                        <input type="text" name="passenger_name" id="passenger_name" class="form-control" placeholder="Passenger name" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Phone Number</label>
                        <input type="text" name="phone_number" id="phone_number" class="form-control" placeholder="Phone number">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Visa Price</label>
                                <input type="number" step="0.01" min="0" name="visa_price" id="visa_price" class="form-control" placeholder="Visa price (€)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Payment Received</label>
                                <input type="number" step="0.01" min="0" name="payment_received" id="payment_received" class="form-control" placeholder="Payment received (€)">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Priority</label>
                                <select name="priority" id="priority" class="form-control">
                                    <option value="Normal">Normal</option>
                                    <option value="High">High</option>
                                    <option value="Low">Low</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="Pending">Pending</option>
                                    <option value="Done">Done</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="3" placeholder="Describe the inquiry..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Inquiry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Inquiry Modal -->
<div class="modal fade" id="editInquiryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editInquiryForm">
                <?php echo csrf_field(); ?>
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Edit Inquiry</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Passenger Name *</label>
                        <input type="text" name="passenger_name" id="edit_passenger_name" class="form-control" placeholder="Passenger name" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Phone Number</label>
                        <input type="text" name="phone_number" id="edit_phone_number" class="form-control" placeholder="Phone number">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Visa Price</label>
                                <input type="number" step="0.01" min="0" name="visa_price" id="edit_visa_price" class="form-control" placeholder="Visa price (€)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Payment Received</label>
                                <input type="number" step="0.01" min="0" name="payment_received" id="edit_payment_received" class="form-control" placeholder="Payment received (€)">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Priority</label>
                                <select name="priority" id="edit_priority" class="form-control">
                                    <option value="Normal">Normal</option>
                                    <option value="High">High</option>
                                    <option value="Low">Low</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Status</label>
                                <select name="status" id="edit_status" class="form-control">
                                    <option value="Pending">Pending</option>
                                    <option value="Done">Done</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold">Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3" placeholder="Describe the inquiry..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info">Update Inquiry</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    var currentStatus = '';

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    var table = $('#inquiryTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?php echo e(route('inquiries.index')); ?>",
            data: function(d) {
                d.status = currentStatus;
            }
        },
        columns: [
            { data: 'date', name: 'created_at' },
            { data: 'passenger', name: 'passenger_name', orderable: false },
            { data: 'description', name: 'description' },
            { data: 'visa_price', name: 'visa_price' },
            { data: 'payment_received', name: 'payment_received' },
            { data: 'priority', name: 'priority', orderable: false },
            { data: 'status', name: 'status', orderable: false },
            { data: 'added_by', name: 'user_id', orderable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']]
    });

    function refreshCounts() {
        $.get("<?php echo e(route('inquiries.counts')); ?>", function(res) {
            $('#countAll').text(res.all);
            $('#countPending').text(res.Pending);
            $('#countDone').text(res.Done);
        });
    }

    // Filter buttons
    $('.inquiry-filter-btn').click(function() {
        $('.inquiry-filter-btn').removeClass('active');
        $(this).addClass('active');
        currentStatus = $(this).data('status');
        table.ajax.reload();
    });

    // Add Inquiry modal
    $('#addInquiryBtn').click(function() {
        $('#addInquiryForm')[0].reset();
        $('#addInquiryModal').modal('show');
    });

    $('#addInquiryForm').submit(function(e) {
        e.preventDefault();
        $.post("<?php echo e(route('inquiries.store')); ?>", $(this).serialize(), function(res) {
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Success', text: res.message, timer: 1500, showConfirmButton: false });
                $('#addInquiryModal').modal('hide');
                table.ajax.reload();
                refreshCounts();
            }
        }).fail(function(xhr) {
            var msg = xhr.responseJSON && xhr.responseJSON.errors
                ? Object.values(xhr.responseJSON.errors).flat().join('<br>')
                : 'Something went wrong!';
            Swal.fire({ icon: 'error', title: 'Error', html: msg });
        });
    });

    // Mark status
    $(document).on('click', '.mark-status-btn', function() {
        var id = $(this).data('id');
        var status = $(this).data('status');
        $.post("<?php echo e(route('inquiries.update_status')); ?>", { id: id, status: status }, function(res) {
            if (res.success) {
                toastr.success(res.message);
                table.ajax.reload(null, false);
                refreshCounts();
            }
        });
    });

    // Edit Inquiry modal
    var editingId = null;
    $(document).on('click', '.edit-inquiry-btn', function() {
        var id = $(this).data('id');
        editingId = id;
        $.get("<?php echo e(route('inquiries.edit', ['id' => '__ID__'])); ?>".replace('__ID__', id), function(data) {
            $('#edit_passenger_name').val(data.passenger_name);
            $('#edit_phone_number').val(data.phone_number || '');
            $('#edit_visa_price').val(data.visa_price !== null ? data.visa_price : '');
            $('#edit_payment_received').val(data.payment_received !== null ? data.payment_received : '');
            $('#edit_priority').val(data.priority || 'Normal');
            $('#edit_status').val(data.status || 'Pending');
            $('#edit_description').val(data.description || '');
            $('#editInquiryModal').modal('show');
        });
    });

    $('#editInquiryForm').submit(function(e) {
        e.preventDefault();
        if (!editingId) return;
        $.post("<?php echo e(route('inquiries.update', ['id' => '__ID__'])); ?>".replace('__ID__', editingId), $(this).serialize(), function(res) {
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Success', text: res.message, timer: 1500, showConfirmButton: false });
                $('#editInquiryModal').modal('hide');
                table.ajax.reload();
                refreshCounts();
            }
        }).fail(function(xhr) {
            var msg = xhr.responseJSON && xhr.responseJSON.errors
                ? Object.values(xhr.responseJSON.errors).flat().join('<br>')
                : 'Something went wrong!';
            Swal.fire({ icon: 'error', title: 'Error', html: msg });
        });
    });

    // Delete
    $(document).on('click', '.delete-inquiry-btn', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: 'This inquiry will be deleted permanently!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?php echo e(route('inquiries.destroy', ['id' => '__ID__'])); ?>".replace('__ID__', id),
                    type: 'DELETE',
                    success: function(res) {
                        Swal.fire({ icon: 'success', title: 'Deleted!', text: res.message, timer: 1500, showConfirmButton: false });
                        table.ajax.reload();
                        refreshCounts();
                    }
                });
            }
        });
    });

    // Export
    $('#exportBtn').click(function() {
        var url = "<?php echo e(route('inquiries.export')); ?>";
        if (currentStatus) {
            url += '?status=' + currentStatus;
        }
        window.location.href = url;
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\testing_project\resources\views/admin/inquiries/index.blade.php ENDPATH**/ ?>