<div class="table-responsive">
    <table class="table table-striped custom-table">
        <thead>
        <tr>
            <th>#</th>
            <th>Ticket Number</th>
            <th>Added By</th>
            <th>Pax Name</th>
            <th>Flight Date</th>
            <th>PNR</th>
            <th>Status</th>
            <th>Refund Status</th>
            <th>Issue Price</th>
            <th>Sale Price</th>
            <td>Action</td>
        </tr>
        </thead>
        <tbody>
        <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $pendingAmount = $ticket->sales->sum('pending_amount');
            ?>
            <tr class="<?php echo e($pendingAmount > 0 ? 'table-warning' : ''); ?>">
                <td><?php echo e($loop->iteration); ?></td>
                <td><?php echo e($ticket->ticket_number); ?></td>
                <td><?php echo e($ticket->author_name); ?></td>
                <td><?php echo e($ticket->customer_name??$ticket->customer->name); ?></td>
                <td><?php echo e(\Carbon\Carbon::parse($ticket->flight_date)->format('d-m-Y')); ?></td>
                <td><?php echo e($ticket->pnr); ?></td>
                <td><?php echo e($ticket->status); ?></td>
                <td><?php echo e($ticket->refund_status); ?></td>
                <td>€<?php echo e($ticket->purchase_price); ?></td>
                <td>€<?php echo e($ticket->sales->sum('sale_price')); ?></td>
                    <td>
                    <?php if($pendingAmount > 0): ?>
                        <button class="btn btn-outline-success btn-sm open-payment-modal"
                                data-ticket-id="<?php echo e($ticket->id); ?>"
                                data-ticket-number="<?php echo e($ticket->ticket_number); ?>"
                                data-customer-id="<?php echo e($customer->id); ?>"
                                data-pending-amount="<?php echo e($pendingAmount); ?>"
                                data-toggle="modal"
                                data-target="#paymentModal"
                                title="Make Payment">
                            <i class="fas fa-euro-sign"></i>
                        </button>
                    <?php else: ?>
                        <span class="btn btn-outline-success btn-sm" title="Cleared">
                            <i class="fas fa-check-circle"></i>
                        </span>
                    <?php endif; ?>

                    <!-- Refund Button -->
                    <button class="btn btn-outline-danger btn-sm open-refund-modal"
                            data-ticket-id="<?php echo e($ticket->id); ?>"
                            data-sale-id="<?php echo e($ticket->sales()->first()->id ?? ''); ?>"
                            data-sale-price="<?php echo e($ticket->sales()->first()->sale_price ?? 0); ?>"
                            data-toggle="modal"
                            data-target="#refundModal"
                            title="Refund">
                        <i class="fas fa-undo"></i>
                    </button>
                <?php if(auth()->id()=='1'|| auth()->id()=='2'): ?>
                    <!-- Edit Button -->
                    <button class="btn btn-outline-primary btn-sm editTicketBtn"
                            data-id="<?php echo e($ticket->id); ?>"
                            title="Edit Ticket">
                        <i class="fas fa-edit"></i>
                    </button>
                    <?php endif; ?>
                    <!-- Payment History Button -->
                    <button class="btn btn-outline-info btn-sm paymentHistoryTicketBtn"
                            data-id="<?php echo e($ticket->id); ?>"
                            data-toggle="modal"
                            data-target="#paymentHistoryModal"
                            title="Payment History">
                        <i class="fas fa-history"></i>
                    </button>
                    <?php if(auth()->id()=='1'): ?>
                    <!-- Delete Button -->
                                <button class="btn btn-outline-danger btn-sm deleteTicketBtn"
                                        data-id="<?php echo e($ticket->id); ?>"
                                        title="Delete Ticket">
                                    <i class="fas fa-trash"></i>
                                </button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php /**PATH D:\laragon\www\testing_project\resources\views/admin/tickets/partials/active_ajax.blade.php ENDPATH**/ ?>