

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Daily Sale Tickets</h4>
                <button id="exportExcelBtn" class="btn btn-success btn-sm">
                    <i class="fa fa-file-excel"></i> Export Excel
                </button>
            </div>

            <div class="card-body">
                <!-- Date Filter -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="start_date" class="fw-bold">Start Date</label>
                        <input type="date" id="start_date" class="form-control shadow-sm border-primary">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="fw-bold">End Date</label>
                        <input type="date" id="end_date" class="form-control shadow-sm border-primary">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button id="filterBtn" class="btn btn-primary w-100 shadow-sm">
                            <i class="fa fa-search"></i> Filter
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="dailyTableTicket" style="width:100%">
                        <thead class="bg-primary text-white text-center">
                            <tr>
                                <th>Dated</th>
                                <th>Time</th>
                                <th>Pax Name</th>
                                <th>PNR</th>
                                <th>System</th>
                                <th>Issued Price</th>
                                <th>Sale Price</th>
                                <th>Pending Price</th>
                                <th>Added By</th>
                                <th>Ticket Number</th>
                                <th>Type</th>
                                <th>Detail Note</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Note Modal -->
<div class="modal fade" id="noteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Note Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="fullNoteText" style="color:black"></div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {

    // --- DataTable ---
    let table = $('#dailyTableTicket').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?php echo e(route('daily_report.with_ticket')); ?>",
            data: function(d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            }
        },
        columns: [
            { data: 'date', name: 'date' },
            { data: 'time_limit', name: 'time_limit' },
            { data: 'pax_name', name: 'pax_name' },
            { data: 'pnr', name: 'pnr' },
            { data: 'system', name: 'system' },
            { data: 'issued_price', name: 'issued_price' },
            { data: 'sale_price', name: 'sale_price' },
            { data: 'pendings', name: 'pendings' },
            { data: 'added_by', name: 'added_by' },
            { data: 'ticket_number', name: 'ticket_number' },
            { data: 'type', name: 'type' },
            { data: 'remarks', name: 'remarks' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // --- Filter Button ---
    $('#filterBtn').click(function() {
        table.ajax.reload();
    });

    // --- Export Excel Button ---
    $('#exportExcelBtn').click(function() {
        const start = $('#start_date').val();
        const end = $('#end_date').val();

        if (!start || !end) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Dates',
                text: 'Please select both start and end dates before exporting!',
                confirmButtonColor: '#3085d6'
            });
            return;
        }

        const url = `<?php echo e(route('daily_report.export_ticket_excel')); ?>?start_date=${start}&end_date=${end}`;
        window.location.href = url;
    });

});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\testing_project\resources\views/admin/daily_report/index_with_ticket.blade.php ENDPATH**/ ?>