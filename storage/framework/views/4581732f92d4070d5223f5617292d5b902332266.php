<?php $__env->startSection('content'); ?>
    <div class="content-wrapper">
        <div class="container mt-5">
            <div class="card border-0 shadow-lg">
                <!-- Header Section -->
                <div class="card-header bg-primary text-white py-4">
                    <h2 class="text-center mb-0">Customer Invoice</h2>
                </div>

                <!-- Body Section -->
                <div class="card-body">
                    <!-- Customer Information -->
                    <div class="mb-5">
                        <h4 class="border-bottom pb-2 text-secondary">Customer Details</h4>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> <?php echo e($customer->name); ?></p>
                                <p><strong>Email:</strong> <?php echo e($customer->email); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Phone:</strong> <?php echo e($customer->phone); ?></p>
                                <p><strong>Address:</strong> <?php echo e($customer->address ?? 'N/A'); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Short Invoice Section -->
                    <div id="shortInvoice" class="mb-4">
                        <h4 class="border-bottom pb-2 text-secondary">Ticket Summary</h4>

                        <!-- Total Amounts -->
                        <div class="d-flex justify-content-between mb-3">
                            <p><strong>Total Paid:</strong> $<?php echo e(number_format($totalPaid, 2)); ?></p>
                            <p><strong>Total Pending:</strong> $<?php echo e(number_format($totalPending, 2)); ?></p>
                        </div>

                        <!-- Tickets and Payment Details -->
                        <table class="table table-sm table-bordered table-striped">
                            <thead class="bg-light">
                            <tr>
                                <th>Ticket #</th>
                                <th>Pax Name</th>
                                <th>Flight Date</th>
                                <th>Sale Price</th>
                                <th>Total Paid</th>
                                <th>Total Pending</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($invoice['ticket']->ticket_number); ?></td>
                                    <td><?php echo e($invoice['ticket']->customer_name ?? $customer->name); ?></td>
                                    <td><?php echo e(\Carbon\Carbon::parse($invoice['ticket']->flight_date)->format('d M, Y')); ?></td>
                                    <td>$<?php echo e(number_format($invoice['ticket']->sales->sum('sale_price'), 2)); ?></td>
                                    <td>$<?php echo e(number_format($invoice['totalPaid'], 2)); ?></td>
                                    <td>$<?php echo e(number_format($invoice['totalPending'], 2)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Toggle Button to Show/Hide Full Invoice -->
                    <div class="text-center mb-4">
                        <button id="toggleFullInvoice" class="btn btn-primary">
                            Show Full Invoice Details
                        </button>
                    </div>

                    <!-- Full Invoice Section (Hidden by default) -->
                    <div id="fullInvoice" class="d-none">
                        <h4 class="border-bottom pb-2 text-secondary">Full Invoice Details</h4>

                        <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="ticket-section border rounded p-3 mb-3 bg-light shadow-sm">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>Ticket #<?php echo e($invoice['ticket']->ticket_number); ?></h5>
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-toggle="collapse" data-target="#ticketDetails<?php echo e($invoice['ticket']->id); ?>">
                                        View Detailed Payments
                                    </button>
                                </div>
                                <div class="collapse mt-3" id="ticketDetails<?php echo e($invoice['ticket']->id); ?>">
                                    <!-- Detailed Payments Table -->
                                    <table class="table table-sm table-bordered table-striped">
                                        <thead class="bg-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Sale Price</th>
                                            <th>Paid Amount</th>
                                            <th>Pending Amount</th>
                                            <th>Payment Date</th>
                                            <th>Payment Method</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $index = 1; ?>
                                        <?php $__currentLoopData = $invoice['ticket']->sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php $__currentLoopData = $sale->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($index++); ?></td>
                                                    <td>$<?php echo e(number_format($sale->sale_price, 2)); ?></td>
                                                    <td>$<?php echo e(number_format($payment->amount_paid, 2)); ?></td>
                                                    <td>$<?php echo e(number_format($sale->pending_amount, 2)); ?></td>
                                                    <td><?php echo e(\Carbon\Carbon::parse($payment->payment_date)->format('d M, Y')); ?></td>
                                                    <td><?php echo e(ucfirst($payment->payment_method)); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                </div>

                <!-- Footer Section -->
                <div class="card-footer bg-light text-center">
                    <button class="btn btn-primary mx-2"><i class="fas fa-download"></i> Download PDF</button>
                    <button class="btn btn-secondary mx-2"><i class="fas fa-print"></i> Print Invoice</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JS for toggling full invoice -->
    <script>
        document.getElementById('toggleFullInvoice').addEventListener('click', function() {
            var shortInvoice = document.getElementById('shortInvoice');
            var fullInvoice = document.getElementById('fullInvoice');
            var buttonText = this;

            // Toggle the visibility of short and full invoice
            if (fullInvoice.classList.contains('d-none')) {
                fullInvoice.classList.remove('d-none');
                shortInvoice.classList.add('d-none');
                buttonText.innerText = 'Hide Full Invoice';
            } else {
                fullInvoice.classList.add('d-none');
                shortInvoice.classList.remove('d-none');
                buttonText.innerText = 'Show Full Invoice Details';
            }
        });
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\testing_project\resources\views/admin/tickets/invoice.blade.php ENDPATH**/ ?>