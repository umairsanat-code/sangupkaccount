<?php $__env->startSection('title', 'Agency: ' . $agency->name); ?>

<?php $__env->startSection('content'); ?>
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-12">
                    <a href="<?php echo e(route('visa_umrah.agencies.index')); ?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
                    <a href="<?php echo e(route('visa_umrah.agencies.edit', $agency->id)); ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                    <button class="btn btn-info btn-sm" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h3 class="card-title mb-0"><?php echo e($agency->name); ?></h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr><th style="width:200px">Contact Person</th><td><?php echo e($agency->contact_person ?? '-'); ?></td></tr>
                                <tr><th>Phone</th><td><?php echo e($agency->phone ?? '-'); ?></td></tr>
                                <tr><th>WhatsApp</th><td><?php echo e($agency->whatsapp ?? '-'); ?></td></tr>
                                <tr><th>Email</th><td><?php echo e($agency->email ?? '-'); ?></td></tr>
                                <tr><th>Location</th><td><?php echo e($agency->city ?? '-'); ?>, <?php echo e($agency->country ?? '-'); ?></td></tr>
                                <tr><th>Address</th><td><?php echo e($agency->address ?? '-'); ?></td></tr>
                                <tr>
                                    <th>Services</th>
                                    <td>
                                        <?php $__currentLoopData = ($agency->services ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="badge badge-info"><?php echo e(ucfirst($s)); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><div class="border rounded p-2 text-center bg-white">
                            <small class="text-muted">Total Payable</small>
                            <h6>€<?php echo e(number_format($totalPayable, 2)); ?></h6>
                        </div></div>
                        <div class="col-md-4"><div class="border rounded p-2 text-center bg-white">
                            <small class="text-muted">Total Paid</small>
                            <h6 class="text-success">€<?php echo e(number_format($totalPaid, 2)); ?></h6>
                        </div></div>
                        <div class="col-md-4"><div class="border rounded p-2 text-center bg-white">
                            <small class="text-muted">Outstanding Balance</small>
                            <h6 class="text-danger">€<?php echo e(number_format($totalPayable - $totalPaid, 2)); ?></h6>
                        </div></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">Main Service Bookings (Visa / Umrah)</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-sm" id="agencyBookingsTable">
                                <thead>
                                <tr>
                                    <th>Booking #</th>
                                    <th>Type</th>
                                    <th>Client</th>
                                    <th>Travel Date</th>
                                    <th>Ref #</th>
                                    <th>Payable</th>
                                    <th>Paid</th>
                                    <th>Pending</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($b->booking_no); ?></td>
                                        <td><?php echo e(ucfirst($b->booking_type)); ?></td>
                                        <td><?php echo e($b->client_name); ?></td>
                                        <td><?php echo e($b->travel_date ?? '-'); ?></td>
                                        <td><?php echo e($b->main_agency_reference ?? '-'); ?></td>
                                        <td class="text-right"><?php echo e(number_format((float)$b->main_agency_cost + (float)$b->services_agency_sum, 2)); ?></td>
                                        <td class="text-right"><?php echo e(number_format((float)$b->agency_paid_sum, 2)); ?></td>
                                        <td class="text-right text-danger">
                                            <?php echo e(number_format(((float)$b->main_agency_cost + (float)$b->services_agency_sum) - (float)$b->agency_paid_sum, 2)); ?>

                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('visa_umrah.bookings.show', ['type' => $b->booking_type, 'booking' => $b->id])); ?>" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">Services (Hotel / Transport) via this Agency</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-sm" id="agencyServicesTable">
                                <thead>
                                <tr>
                                    <th>Booking</th>
                                    <th>Type</th>
                                    <th>Name</th>
                                    <th>Client Price</th>
                                    <th>Agency Cost</th>
                                    <th>Paid</th>
                                    <th>Pending</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($s->booking->booking_no ?? '-'); ?></td>
                                        <td><?php echo e(ucfirst($s->service_type)); ?></td>
                                        <td><?php echo e($s->name); ?></td>
                                        <td class="text-right"><?php echo e(number_format($s->client_price, 2)); ?></td>
                                        <td class="text-right"><?php echo e(number_format($s->agency_cost, 2)); ?></td>
                                        <td class="text-right"><?php echo e(number_format((float)$s->agency_paid_sum, 2)); ?></td>
                                        <td class="text-right text-danger"><?php echo e(number_format($s->agency_cost - (float)$s->agency_paid_sum, 2)); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
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
            $('#agencyBookingsTable').DataTable({order: [[0, 'desc']]});
            $('#agencyServicesTable').DataTable({order: [[0, 'desc']]});
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\testing_project\resources\views/admin/visa_umrah/agencies/show.blade.php ENDPATH**/ ?>