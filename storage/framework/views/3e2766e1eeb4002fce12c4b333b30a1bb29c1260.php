<div class="table-responsive">
    <table class="table table-striped custom-table">
        <thead>
        <tr>
            <th>#</th>
            <th>Ticket Number</th>
            <th>Flight Date</th>
            <th>Status</th>
            <th>Refund Status</th>
            <th>Issue Price</th>
            <th>Refund Amount</th>
            <th>Refund Charges</th>
            <td>Action</td>
        </tr>
        </thead>
        <tbody>
        <?php $__currentLoopData = $refundedTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($loop->iteration); ?></td>
                <td><?php echo e($ticket->ticket_number); ?></td>
                <td><?php echo e($ticket->flight_date); ?></td>
                <td><?php echo e($ticket->status); ?></td>
                <td><?php echo e($ticket->refund_status); ?></td>
                <td>€<?php echo e($ticket->sales->sum('sale_price')); ?></td>
                <td>€<?php echo e($ticket->sales->sum('refund_amount')); ?></td>
                <td>€<?php echo e($ticket->sales->sum('service_charges') + $ticket->sales->sum('airline_charges')); ?></td>
                <td>
                    <button
                        class="btn btn-success btn-sm"
                        onclick="activateRefundedTicket(<?php echo e($ticket->id); ?>)">
                        Activate Again
                    </button>
                </td>

            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function activateRefundedTicket(ticketId) {

        Swal.fire({
            title: 'Activate Ticket?',
            text: 'This refunded ticket will be activated again.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Activate',
            cancelButtonText: 'Cancel'
        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({
                    url: '/tickets/' + ticketId + '/activate-refund',
                    type: 'POST',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>'
                    },
                    success: function (res) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Activated!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // Reload refunded list
                        loadRefundedTickets();
                    },
                    error: function () {

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Something went wrong. Please try again.'
                        });
                    }
                });
            }
        });
    }
</script>

<?php /**PATH D:\laragon\www\testing_project\resources\views/admin/tickets/partials/refunded_ajax.blade.php ENDPATH**/ ?>