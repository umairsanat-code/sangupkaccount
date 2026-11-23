<?php $__env->startSection('content'); ?>

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <h1>Create Ticket</h1>
            </div>
        </section>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h3 class="card-title">Add New Ticket</h3>
                        </div>

                        <div class="card-body">
                            <form action="<?php echo e(route('daily_report.store')); ?>" method="POST">
                                <?php echo csrf_field(); ?>

                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Date</label>
                                        <input type="date" class="form-control" name="date" id="date" readonly>
                                    </div>

                                    <div class="col-md-6">
                                        <label>Cancellation Time</label>
                                        <input type="datetime-local" class="form-control" name="time_limit">
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <label>Ticket Number</label>
                                        <input type="text" name="ticket_number" class="form-control">
                                    </div>

                                    <div class="col-md-6">
                                        <label>PNR</label>
                                        <input type="text" name="pnr" class="form-control" required>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <label>Issue Amount</label>
                                        <input type="text" name="issued_price" class="form-control">
                                    </div>

                                    <div class="col-md-6">
                                        <label>Pax Name</label>
                                        <input type="text" name="pax_name" class="form-control" required>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <label>System</label>
                                        <input type="text" name="system" class="form-control" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label>Remarks</label>
                                        <textarea name="remarks" class="form-control"></textarea>
                                    </div>
                                </div>

                                <!-- Payment Stage -->
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <label>Payment Stage</label>
                                        <select name="payment_stage" class="form-control" required>
                                            <option value="">Select</option>
                                            <option value="first">First Payment</option>
                                            <option value="second">Second Payment</option>
                                            <option value="remaining">Remaining</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label>Sale Price</label>
                                        <input type="text" name="sale_price" class="form-control" required>
                                    </div>
                                </div>

                                <!-- Payment + Type -->
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <label>Payment</label>
                                        <input type="text" name="payment" class="form-control" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label>Type</label>
                                        <select name="type" id="type" class="form-control" required>
                                            <option value="">Select Type</option>
                                            <option value="cash">Cash</option>
                                            <option value="card">Card</option>
                                            <option value="bank">Bank</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Card Name (Hidden by default) -->
                                <div class="row mt-2" id="card_name_div" style="display:none;">
                                    <div class="col-md-6">
                                        <label>Card Name</label>
                                        <select name="card_name" class="form-control">
                                            <option value="">Select Card</option>

                                            <optgroup label="SANGU Cards">
                                                <option value="SANTANDER SANGU">SANTANDER SANGU</option>
                                                <option value="BBVA SANGU">BBVA SANGU</option>
                                                <option value="LACIAXA SANGU">LACIAXA SANGU</option>
                                            </optgroup>

                                            <optgroup label="IMRO Cards">
                                                <option value="BBVA IMRO">BBVA IMRO</option>
                                                <option value="LACIAXA IMRO">LACIAXA IMRO</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>

                                <button class="btn btn-primary mt-3">Create Ticket</button>
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

            let today = new Date().toISOString().split('T')[0];
            document.getElementById("date").value = today;

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

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\testing_project\resources\views/admin/daily_report/create.blade.php ENDPATH**/ ?>