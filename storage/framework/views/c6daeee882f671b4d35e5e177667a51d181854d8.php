<?php $__env->startSection('content'); ?>

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <h1>Edit Ticket</h1>
            </div>
        </section>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h3 class="card-title">Update Ticket</h3>
                        </div>

                        <div class="card-body">
                            <form action="<?php echo e(route('daily_report.update', $ticket->id)); ?>" method="POST" autocomplete="off">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>

                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Date</label>
                                        <input type="date" class="form-control" name="date"
                                               value="<?php echo e(old('date', \Carbon\Carbon::parse($ticket->date)->format('Y-m-d'))); ?>" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label>Time Limit</label>
                                        <input type="datetime-local" class="form-control" name="time_limit"
                                               value="<?php echo e(optional($ticket->time_limit)->copy()->addHours(2)->format('Y-m-d\TH:i')); ?>">
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <label>Ticket Number</label>
                                        <input type="text" name="ticket_number" class="form-control"
                                               value="<?php echo e(old('ticket_number', $ticket->ticket_number)); ?>"
                                               autocomplete="off">
                                    </div>

                                    <div class="col-md-6">
                                        <label>PNR</label>
                                        <input type="text" name="pnr" class="form-control"
                                               value="<?php echo e(old('pnr', $ticket->pnr)); ?>" required>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <label>Issue Price</label>
                                        <input type="text" name="issued_price" class="form-control"
                                               value="<?php echo e(old('issued_price', $ticket->issued_price)); ?>">
                                    </div>

                                    <div class="col-md-6">
                                        <label>Pax Name</label>
                                        <input type="text" name="pax_name" class="form-control"
                                               value="<?php echo e(old('pax_name', $ticket->pax_name)); ?>" required>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <label>System</label>
                                        <input type="text" name="system" class="form-control"
                                               value="<?php echo e(old('system', $ticket->system)); ?>" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label>Remarks</label>
                                        <textarea name="remarks" class="form-control"><?php echo e(old('remarks', $ticket->remarks)); ?></textarea>
                                    </div>
                                </div>

                                <!-- Payment Stage + Type -->
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <label>Payment Stage</label>
                                        <select name="payment_stage" class="form-control" required>
                                            <option value="">Select</option>
                                            <option value="first" <?php echo e(old('payment_stage', $ticket->payment_method) == 'first' ? 'selected' : ''); ?>>First</option>
                                            <option value="second" <?php echo e(old('payment_stage', $ticket->payment_method) == 'second' ? 'selected' : ''); ?>>Second</option>
                                            <option value="remaining" <?php echo e(old('payment_stage', $ticket->payment_method) == 'remaining' ? 'selected' : ''); ?>>Remaining</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label>Type</label>
                                        <select name="type" id="type" class="form-control" required>
                                            <option value="">Select</option>
                                            <option value="cash" <?php echo e(old('type', $ticket->type) == 'cash' ? 'selected' : ''); ?>>Cash</option>
                                            <option value="card" <?php echo e(old('type', $ticket->type) == 'card' ? 'selected' : ''); ?>>Card</option>
                                            <option value="bank" <?php echo e(old('type', $ticket->type) == 'bank' ? 'selected' : ''); ?>>Bank</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Card Name -->
                                <div class="row mt-2" id="card_name_div" style="display:none;">
                                    <div class="col-md-6">
                                        <label>Card Name</label>
                                        <select name="card_name" class="form-control">
                                            <option value="">Select Card</option>

                                            <optgroup label="SANGU">
                                                <option value="SANTANDER SANGU"
                                                    <?php echo e(old('card_name', $payment->card_name ?? '') == 'SANTANDER SANGU' ? 'selected' : ''); ?>>
                                                    SANTANDER SANGU
                                                </option>

                                                <option value="BBVA SANGU"
                                                    <?php echo e(old('card_name', $payment->card_name ?? '') == 'BBVA SANGU' ? 'selected' : ''); ?>>
                                                    BBVA SANGU
                                                </option>

                                                <option value="LACIAXA SANGU"
                                                    <?php echo e(old('card_name', $payment->card_name ?? '') == 'LACIAXA SANGU' ? 'selected' : ''); ?>>
                                                    LACIAXA SANGU
                                                </option>
                                            </optgroup>

                                            <optgroup label="IMRO">
                                                <option value="BBVA IMRO"
                                                    <?php echo e(old('card_name', $payment->card_name ?? '') == 'BBVA IMRO' ? 'selected' : ''); ?>>
                                                    BBVA IMRO
                                                </option>

                                                <option value="LACIAXA IMRO"
                                                    <?php echo e(old('card_name', $payment->card_name ?? '') == 'LACIAXA IMRO' ? 'selected' : ''); ?>>
                                                    LACIAXA IMRO
                                                </option>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <label>Sale Price</label>
                                        <input type="text" name="sale_price" class="form-control"
                                               value="<?php echo e(old('sale_price', $ticket->sale_price)); ?>" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label>Payment</label>
                                        <input type="text" name="payment" class="form-control"
                                               value="<?php echo e(old('payment', $ticket->payment)); ?>" required>
                                    </div>
                                </div>

                                <button class="btn btn-primary mt-3">Update Ticket</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            let type = document.getElementById('type');
            let cardDiv = document.getElementById('card_name_div');

            function toggleCard() {
                if (type.value === 'card') {
                    cardDiv.style.display = 'flex';
                } else {
                    cardDiv.style.display = 'none';
                }
            }

            toggleCard();
            type.addEventListener('change', toggleCard);
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\testing_project\resources\views/admin/daily_report/edit.blade.php ENDPATH**/ ?>