
<?php $__env->startSection('content'); ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.0/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid mb-3">
                <a href="<?php echo e(url('vendors/payment')); ?>" class="btn btn-primary shadow-sm">
                    <i class="fa fa-plus me-1"></i> Create Payment
                </a>
            </div>
        </section>

        <div class="container-fluid">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Vendor Payments</h4>
                </div>
                <div class="card-body">
                    <!-- Filter Row -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label for="agent_name" class="form-label fw-bold">Agent Name</label>
                            <select id="agent_name" class="form-select shadow-sm">
                                <option value="">Select Agent</option>
                                <?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($agent->id); ?>"><?php echo e($agent->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="start_date" class="form-label fw-bold">Start Date</label>
                            <input type="date" id="start_date" class="form-control shadow-sm">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label fw-bold">End Date</label>
                            <input type="date" id="end_date" class="form-control shadow-sm">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-primary w-100 shadow-sm" id="filterBtn">
                                <i class="fa fa-search me-1"></i> Filter
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="vendorTable">
                            <thead class="table-dark text-center">
                            <tr>
                                <th>Agent Name</th>
                                <th>Name</th>
                                <th>Payment Amount</th>
                                <th>Payment Method</th>
                                <th>Bank Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Remaining Amount</th>
                                <th>Note</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Note Modal -->
    <div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="noteModalLabel">Note Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="fullNoteText" style="color:black"></div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pdfmake@0.1.36/build/pdfmake.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pdfmake@0.1.36/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            const table = $('#vendorTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "<?php echo e(route('vendors')); ?>",
                    data: function(d) {
                        d.agent_id = $('#agent_name').val();
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [
                    { data: 'agent_name', name: 'agent_name' },
                    { data: 'ticket_id', name: 'ticket_id' },
                    { data: 'payment_amount', name: 'payment_amount', render: $.fn.dataTable.render.number(',', '.', 2, '€') },
                    { data: 'payment_method', name: 'payment_method', render: function(data){
                            let color = data=='Cash'?'success':data=='Bank'?'primary':'warning';
                            return `<span class="badge bg-${color}">${data}</span>`;
                        }},
                    { data: 'bank_name', name: 'bank_name' },
                    { data: 'start_date', name: 'start_date' },
                    { data: 'end_date', name: 'end_date' },
                    { data: 'remaining_amount', name: 'remaining_amount', render: function(data){
                            let color = data > 0 ? 'danger' : 'success';
                            return `<span class="badge bg-${color}">${data} €</span>`;
                        }},
                    { data: 'note', render: function(data){
                            if(data.length > 12){
                                return `<span class="note-short text-primary fw-bold" style="cursor:pointer" data-full="${data}">${data.substring(0,12)}...</span>`;
                            }
                            return data;
                        }},
                    { data: 'action', orderable:false, searchable:false }
                ],
                dom: '<"d-flex justify-content-start mb-2"Bf>rtip',
                lengthMenu: [[10, 25, 50, -1],[10, 25, 50, "All"]],
                buttons: [
                    { extend: 'excelHtml5', text:'<i class="fa fa-file-excel me-1"></i> Excel', className:'btn btn-success btn-sm shadow-sm' },
                    { extend: 'csvHtml5', text:'<i class="fa fa-file-csv me-1"></i> CSV', className:'btn btn-info btn-sm shadow-sm' },
                    { extend: 'pdfHtml5', text:'<i class="fa fa-file-pdf me-1"></i> PDF', className:'btn btn-danger btn-sm shadow-sm', orientation:'landscape', pageSize:'A4' }
                ]
            });

            // Filter
            $('#filterBtn').click(function() {
                table.ajax.reload();
            });

            // Show full note modal
            $(document).on('click', '.note-short', function() {
                const note = $(this).data('full');
                $('#fullNoteText').text(note);
                const noteModal = new bootstrap.Modal(document.getElementById('noteModal'));
                noteModal.show();
            });

            // SweetAlert Delete
            $('#vendorTable').on('click', '.delete', function () {
                const paymentId = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                }).then((result) => {
                    if(result.isConfirmed) {
                        $.ajax({
                            url: `/vendors/${paymentId}`,
                            type: 'POST',
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            success: function(res) {
                                Swal.fire('Deleted!', 'Payment deleted.', 'success');
                                table.ajax.reload();
                            },
                            error: function(err) {
                                Swal.fire('Error!', 'Something went wrong.', 'error');
                            }
                        });
                    }
                });
            });

        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\testing_project\resources\views/admin/vendors/index.blade.php ENDPATH**/ ?>