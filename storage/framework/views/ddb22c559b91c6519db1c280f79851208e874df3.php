<?php $__env->startSection('title', ucfirst($type) . ' Bookings'); ?>

<?php $__env->startSection('content'); ?>
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0"><?php echo e(ucfirst($type)); ?> Bookings</h3>
                            <div>
                                <a href="<?php echo e(route('visa_umrah.bookings.create', ['type' => $type])); ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> New <?php echo e(ucfirst($type)); ?> Booking
                                </a>
                                <a href="<?php echo e(route('visa_umrah.dashboard')); ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <select id="filter_status" class="form-control form-control-sm">
                                        <option value="">All Status</option>
                                        <?php $__currentLoopData = \App\Models\VisaUmrahBooking::VISA_STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($s); ?>"><?php echo e(ucwords(str_replace('_', ' ', $s))); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select id="filter_agency" class="form-control form-control-sm">
                                        <option value="">All Agencies</option>
                                        <?php $__currentLoopData = $agencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($a->id); ?>"><?php echo e($a->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" id="filter_client" class="form-control form-control-sm" placeholder="Client name...">
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-sm btn-secondary" id="resetFilters">Reset</button>
                                </div>
                            </div>

                            <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="bookingTable" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Booking #</th>
                                    <th>Client</th>
                                    <th>Phone</th>
                                    <?php if($type === 'visa'): ?>
                                        <th>Country</th>
                                        <th>Type</th>
                                    <?php else: ?>
                                        <th>Package</th>
                                        <th>Adults</th>
                                    <?php endif; ?>
                                    <th>Travel Date</th>
                                    <th>Status</th>
                                    <th>Services</th>
                                    <th>Agency</th>
                                    <th>Sale</th>
                                    <th>Received</th>
                                    <th>Pending</th>
                                    <th>Agency Payable</th>
                                    <th>Agency Paid</th>
                                    <th>Net Profit</th>
                                    <th>Actions</th>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            var table = $('#bookingTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "<?php echo e(route('visa_umrah.bookings.index', ['type' => $type])); ?>",
                    data: function (d) {
                        d.status = $('#filter_status').val();
                        d.agency_id = $('#filter_agency').val();
                        d.client_name = $('#filter_client').val();
                    }
                },
                columns: [
                    {data: 'booking_no'},
                    {data: 'client_name'},
                    {data: 'client_phone'},
                    <?php if($type === 'visa'): ?>
                    {data: 'visa_country'},
                    {data: 'visa_type'},
                    <?php else: ?>
                    {data: 'umrah_package'},
                    {data: 'num_adults'},
                    <?php endif; ?>
                    {data: 'travel_date'},
                    {data: 'status_badge'},
                    {data: 'services'},
                    {data: 'agency_name'},
                    {data: 'client_total_full', className: 'text-right'},
                    {data: 'client_received_sum', className: 'text-right'},
                    {data: 'client_pending', className: 'text-right'},
                    {data: 'agency_total_full', className: 'text-right'},
                    {data: 'agency_paid_sum', className: 'text-right'},
                    {data: 'net_profit', className: 'text-right'},
                    {data: 'action', orderable: false, searchable: false}
                ],
                order: [[0, 'desc']],
                columnDefs: [
                    {render: function (d) { return '€' + parseFloat(d || 0).toFixed(2); }, targets: [<?php if($type === 'visa'): ?>9,10,11,12,13,14 <?php else: ?> 8,9,10,11,12,13 <?php endif; ?>]}
                ]
            });

            $('#filter_status, #filter_agency').on('change', function () { table.draw(); });
            $('#filter_client').on('keyup', function () {
                clearTimeout(window._cfT);
                window._cfT = setTimeout(function () { table.draw(); }, 400);
            });
            $('#resetFilters').on('click', function () {
                $('#filter_status, #filter_agency').val('');
                $('#filter_client').val('');
                table.draw();
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\testing_project\resources\views/admin/visa_umrah/bookings/index.blade.php ENDPATH**/ ?>