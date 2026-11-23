<?php $__env->startSection('title', 'Visa & Umrah Reports'); ?>

<?php $__env->startSection('style'); ?>
    <style>
        .report-section { margin-bottom: 30px; }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-12 d-flex justify-content-between">
                    <h4 class="mb-0">Visa & Umrah Reports</h4>
                    <div>
                        <button class="btn btn-info btn-sm" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                        <a href="<?php echo e(route('visa_umrah.dashboard')); ?>" class="btn btn-secondary btn-sm"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    </div>
                </div>
            </div>

            
            <div class="card mb-3">
                <div class="card-header bg-white"><h5 class="mb-0">Filters</h5></div>
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('visa_umrah.reports')); ?>">
                        <div class="row">
                            <div class="col-md-2">
                                <label>From Date</label>
                                <input type="date" name="date_from" class="form-control form-control-sm" value="<?php echo e($filter['date_from']); ?>">
                            </div>
                            <div class="col-md-2">
                                <label>To Date</label>
                                <input type="date" name="date_to" class="form-control form-control-sm" value="<?php echo e($filter['date_to']); ?>">
                            </div>
                            <div class="col-md-2">
                                <label>Type</label>
                                <select name="booking_type" class="form-control form-control-sm">
                                    <option value="">All</option>
                                    <option value="visa" <?php echo e($filter['booking_type'] == 'visa' ? 'selected' : ''); ?>>Visa</option>
                                    <option value="umrah" <?php echo e($filter['booking_type'] == 'umrah' ? 'selected' : ''); ?>>Umrah</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Visa Country</label>
                                <select name="visa_country" class="form-control form-control-sm">
                                    <option value="">All</option>
                                    <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($c); ?>" <?php echo e($filter['visa_country'] == $c ? 'selected' : ''); ?>><?php echo e($c); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Client</label>
                                <input type="text" name="client_name" class="form-control form-control-sm" value="<?php echo e($filter['client_name']); ?>">
                            </div>
                            <div class="col-md-2">
                                <label>Agency</label>
                                <select name="agency_id" class="form-control form-control-sm">
                                    <option value="">All</option>
                                    <?php $__currentLoopData = $agencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($a->id); ?>" <?php echo e($filter['agency_id'] == $a->id ? 'selected' : ''); ?>><?php echo e($a->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Status</label>
                                <select name="status" class="form-control form-control-sm">
                                    <option value="">All</option>
                                    <?php $__currentLoopData = \App\Models\VisaUmrahBooking::VISA_STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($s); ?>" <?php echo e($filter['status'] == $s ? 'selected' : ''); ?>><?php echo e(ucwords(str_replace('_', ' ', $s))); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button class="btn btn-primary btn-sm mr-1"><i class="fas fa-filter"></i> Apply</button>
                                <a href="<?php echo e(route('visa_umrah.reports')); ?>" class="btn btn-secondary btn-sm">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            
            <div class="card mb-3 report-section">
                <div class="card-header bg-white"><h5 class="mb-0">Overall Profit / Loss</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-4 col-6"><div class="border rounded p-2 text-center bg-light mb-2">
                            <small class="text-muted">Total Sales</small><h6>€<?php echo e(number_format($overall['total_sales'], 2)); ?></h6>
                        </div></div>
                        <div class="col-lg-3 col-md-4 col-6"><div class="border rounded p-2 text-center bg-light mb-2">
                            <small class="text-muted">Client Received</small><h6 class="text-success">€<?php echo e(number_format($overall['client_received'], 2)); ?></h6>
                        </div></div>
                        <div class="col-lg-3 col-md-4 col-6"><div class="border rounded p-2 text-center bg-light mb-2">
                            <small class="text-muted">Client Pending</small><h6 class="text-danger">€<?php echo e(number_format($overall['client_pending'], 2)); ?></h6>
                        </div></div>
                        <div class="col-lg-3 col-md-4 col-6"><div class="border rounded p-2 text-center bg-light mb-2">
                            <small class="text-muted">Agency Payable</small><h6>€<?php echo e(number_format($overall['total_agency_payable'], 2)); ?></h6>
                        </div></div>
                        <div class="col-lg-3 col-md-4 col-6"><div class="border rounded p-2 text-center bg-light mb-2">
                            <small class="text-muted">Agency Paid</small><h6>€<?php echo e(number_format($overall['agency_paid'], 2)); ?></h6>
                        </div></div>
                        <div class="col-lg-3 col-md-4 col-6"><div class="border rounded p-2 text-center bg-light mb-2">
                            <small class="text-muted">Agency Pending</small><h6 class="text-danger">€<?php echo e(number_format($overall['agency_pending'], 2)); ?></h6>
                        </div></div>
                        <div class="col-lg-3 col-md-4 col-6"><div class="border rounded p-2 text-center bg-light mb-2">
                            <small class="text-muted">Expenses</small><h6>€<?php echo e(number_format($overall['expenses'], 2)); ?></h6>
                        </div></div>
                        <div class="col-lg-3 col-md-4 col-6"><div class="border rounded p-2 text-center bg-success text-white mb-2">
                            <small>Gross Profit</small><h6>€<?php echo e(number_format($overall['gross_profit'], 2)); ?></h6>
                        </div></div>
                        <div class="col-lg-3 col-md-4 col-6"><div class="border rounded p-2 text-center bg-primary text-white mb-2">
                            <small>Net Profit</small><h6>€<?php echo e(number_format($overall['net_profit'], 2)); ?></h6>
                        </div></div>
                    </div>
                </div>
            </div>

            
            <div class="card mb-3 report-section">
                <div class="card-header bg-white"><h5 class="mb-0">Client Ledger</h5></div>
                <div class="card-body">
                    <table class="table table-bordered table-sm" id="clientLedgerTable">
                        <thead>
                        <tr><th>Client</th><th>Phone</th><th>Bookings</th><th>Total Sale</th><th>Received</th><th>Pending</th></tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $clientLedger; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($row['client_name']); ?></td>
                                <td><?php echo e($row['phone'] ?? '-'); ?></td>
                                <td><?php echo e($row['bookings']); ?></td>
                                <td class="text-right"><?php echo e(number_format($row['total'], 2)); ?></td>
                                <td class="text-right text-success"><?php echo e(number_format($row['received'], 2)); ?></td>
                                <td class="text-right text-danger"><?php echo e(number_format($row['pending'], 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>

            
            <div class="card mb-3 report-section">
                <div class="card-header bg-white"><h5 class="mb-0">Agency Ledger</h5></div>
                <div class="card-body">
                    <table class="table table-bordered table-sm" id="agencyLedgerTable">
                        <thead>
                        <tr><th>Agency</th><th>Total Payable</th><th>Total Paid</th><th>Outstanding</th></tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $agencyLedger; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($row['agency']); ?></td>
                                <td class="text-right"><?php echo e(number_format($row['payable'], 2)); ?></td>
                                <td class="text-right text-success"><?php echo e(number_format($row['paid'], 2)); ?></td>
                                <td class="text-right text-danger"><?php echo e(number_format($row['pending'], 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>

            
            <div class="card mb-3 report-section">
                <div class="card-header bg-white"><h5 class="mb-0">Booking Profit Report (Visa / Umrah)</h5></div>
                <div class="card-body">
                    <table class="table table-bordered table-sm" id="bookingProfitTable">
                        <thead>
                        <tr>
                            <th>Booking #</th><th>Type</th><th>Client</th><th>Travel Date</th>
                            <th>Client Sale</th><th>Client Received</th><th>Agency Payable</th><th>Agency Paid</th>
                            <th>Gross Profit</th><th>Net Profit</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($b->booking_no); ?></td>
                                <td><?php echo e(ucfirst($b->booking_type)); ?></td>
                                <td><?php echo e($b->client_name); ?></td>
                                <td><?php echo e($b->travel_date ?? '-'); ?></td>
                                <td class="text-right"><?php echo e(number_format($b->client_total_full, 2)); ?></td>
                                <td class="text-right text-success"><?php echo e(number_format((float)$b->cr_sum, 2)); ?></td>
                                <td class="text-right"><?php echo e(number_format($b->agency_total_full, 2)); ?></td>
                                <td class="text-right"><?php echo e(number_format((float)$b->ap_sum, 2)); ?></td>
                                <td class="text-right"><?php echo e(number_format($b->gross_profit, 2)); ?></td>
                                <td class="text-right <?php echo e($b->net_profit >= 0 ? 'text-success' : 'text-danger'); ?>"><?php echo e(number_format($b->net_profit, 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>

            
            <div class="card mb-3 report-section">
                <div class="card-header bg-white"><h5 class="mb-0">Hotel Profit Report</h5></div>
                <div class="card-body">
                    <table class="table table-bordered table-sm" id="hotelProfitTable">
                        <thead>
                        <tr><th>Hotel</th><th>City</th><th>Booking</th><th>Client Price</th><th>Agency Cost</th><th>Profit</th></tr>
                        </thead>
                        <tbody>
                        <?php $hotelTotals = [0,0,0]; ?>
                        <?php $__currentLoopData = $hotelServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $hotelTotals[0] += $h->client_price; $hotelTotals[1] += $h->agency_cost; $hotelTotals[2] += $h->profit();
                            ?>
                            <tr>
                                <td><?php echo e($h->name); ?></td>
                                <td><?php echo e($h->city); ?></td>
                                <td><?php echo e($h->booking->booking_no ?? '-'); ?></td>
                                <td class="text-right"><?php echo e(number_format($h->client_price, 2)); ?></td>
                                <td class="text-right"><?php echo e(number_format($h->agency_cost, 2)); ?></td>
                                <td class="text-right"><?php echo e(number_format($h->profit(), 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <tr class="font-weight-bold">
                            <td>Total</td><td></td><td></td>
                            <td class="text-right"><?php echo e(number_format($hotelTotals[0], 2)); ?></td>
                            <td class="text-right"><?php echo e(number_format($hotelTotals[1], 2)); ?></td>
                            <td class="text-right"><?php echo e(number_format($hotelTotals[2], 2)); ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            
            <div class="card mb-3 report-section">
                <div class="card-header bg-white"><h5 class="mb-0">Transport Profit Report</h5></div>
                <div class="card-body">
                    <table class="table table-bordered table-sm" id="transportProfitTable">
                        <thead>
                        <tr><th>Route</th><th>Type</th><th>Booking</th><th>Client Price</th><th>Agency Cost</th><th>Profit</th></tr>
                        </thead>
                        <tbody>
                        <?php $transportTotals = [0,0,0]; ?>
                        <?php $__currentLoopData = $transportServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $transportTotals[0] += $t->client_price; $transportTotals[1] += $t->agency_cost; $transportTotals[2] += $t->profit();
                            ?>
                            <tr>
                                <td><?php echo e($t->pickup_location); ?> → <?php echo e($t->dropoff_location); ?></td>
                                <td><?php echo e($t->vehicle_type); ?></td>
                                <td><?php echo e($t->booking->booking_no ?? '-'); ?></td>
                                <td class="text-right"><?php echo e(number_format($t->client_price, 2)); ?></td>
                                <td class="text-right"><?php echo e(number_format($t->agency_cost, 2)); ?></td>
                                <td class="text-right"><?php echo e(number_format($t->profit(), 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <tr class="font-weight-bold">
                            <td>Total</td><td></td><td></td>
                            <td class="text-right"><?php echo e(number_format($transportTotals[0], 2)); ?></td>
                            <td class="text-right"><?php echo e(number_format($transportTotals[1], 2)); ?></td>
                            <td class="text-right"><?php echo e(number_format($transportTotals[2], 2)); ?></td>
                        </tr>
                        </tbody>
                    </table>
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
            ['clientLedgerTable', 'agencyLedgerTable', 'bookingProfitTable', 'hotelProfitTable', 'transportProfitTable'].forEach(function (id) {
                $('#' + id).DataTable();
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\testing_project\resources\views/admin/visa_umrah/reports.blade.php ENDPATH**/ ?>