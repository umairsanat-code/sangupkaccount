<!-- Modal Trigger Button -->


<!-- Modal -->
<div class="modal fade" id="ticketFormModal" tabindex="-1" aria-labelledby="ticketFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ticketFormModalLabel">Create Ticket</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <form id="ticketForm">
                    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

                    <!-- Hidden Customer ID Field -->
                    <input type="hidden" name="customer_id" value="<?php echo e($customer->id); ?>">

                    <!-- Ticket Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ticket_number">Ticket Number</label>
                                <input type="text" name="ticket_number" class="form-control <?php $__errorArgs = ['ticket_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('ticket_number')); ?>" >
                                <span class="text-danger error-text ticket_number_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="flight_date">Flight Date</label>
                                <input type="date" name="flight_date" class="form-control <?php $__errorArgs = ['flight_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('flight_date')); ?>" required>
                                <span class="text-danger error-text flight_date_error"></span>

                            </div>
                        </div>
                    </div>

                    <!-- Airline and Booking Date -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="airline_id">Airline</label>
                                <select name="airline_id" class="form-control <?php $__errorArgs = ['airline_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">Select Airline</option>
                                    <?php $__currentLoopData = $airlines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $airline): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($airline->id); ?>" <?php echo e(old('airline_id') == $airline->id ? 'selected' : ''); ?>><?php echo e($airline->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <span class="text-danger error-text flight_date_error"></span>


                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">










                                <?php
                                    $hasTickets = \App\Models\Ticket::where('customer_id', $customer->id)->exists();
                                ?>

                                <label for="departure_city">Pax Name</label>
                                <input type="text" name="customer_name"
                                       class="form-control <?php $__errorArgs = ['customer_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('customer_name', $hasTickets ? '' : $customer->name)); ?>">
                                <span class="text-danger error-text customer_name_error"></span>

                            </div>
                            <!--<div class="form-group">-->
                            <!--    <label for="booking_date">Booking Date</label>-->
                            <!--    <input type="date" name="booking_date" class="form-control <?php $__errorArgs = ['booking_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('booking_date')); ?>" required>-->
                            <!--    <span class="text-danger error-text booking_date_error"></span>-->


                            <!--</div>-->
                        </div>
                    </div>

                    <!-- Departure and Destination City -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="departure_city">Departure City</label>
                                <input type="text" name="departure_city" class="form-control <?php $__errorArgs = ['departure_city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('departure_city')); ?>" required>
                                <span class="text-danger error-text departure_city_error"></span>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="destination_city">Destination City</label>
                                <input type="text" name="destination_city" class="form-control <?php $__errorArgs = ['destination_city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('destination_city')); ?>" required>
                                <span class="text-danger error-text destination_city_error"></span>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="departure_city">Vendor  Name</label>
                               <select class="form-control"  id="agent_id" name="agent_id">
                                   <option> Select Vendor Name</option>
                                   <?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                       <option value="<?php echo e($agent->id); ?>"><?php echo e($agent->name); ?></option>
                                   <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                               </select>
                            </div>
                        </div>
                         <div class="col-md-6">
                            <div class="form-group">
                                <?php
                                    $hasTickets = \App\Models\Ticket::where('customer_name', $customer->name)->exists();
//                                    dd($hasTickets,$customer->customer_pnr);
                                ?>

                                <label for="destination_city">PNR</label>
                                <input type="text" name="pnr"
                                       class="form-control <?php $__errorArgs = ['pnr'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('pnr', $hasTickets ?   '':$customer->customer_pnr)); ?>"
                                       placeholder="Enter Pnr" required>
                                <span class="text-danger error-text pnr_error"></span>

                            </div>
                        </div>

                    </div>

                    <!-- Class and Status -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="class">Class</label>
                                <select name="class" class="form-control <?php $__errorArgs = ['class'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">Select Class</option>
                                    <option value="Economy" <?php echo e(old('class') == 'Economy' ? 'selected' : ''); ?>>Economy</option>
                                    <option value="Business" <?php echo e(old('class') == 'Business' ? 'selected' : ''); ?>>Business</option>
                                    <option value="First Class" <?php echo e(old('class') == 'First Class' ? 'selected' : ''); ?>>First Class</option>
                                </select>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                 <select name="status" class="form-control <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">Select Status</option>
                                    <option value="Booked" <?php echo e(old('status') == 'Booked' ? 'selected' : ''); ?>>Issue Ticket</option>
                                    <option value="Cancelled" <?php echo e(old('status') == 'Cancelled' ? 'selected' : ''); ?>>Re-Issue Ticket</option>
                                            <option value="VOIDED" <?php echo e(old('status') == 'VOIDED' ? 'selected' : ''); ?>>VOIDED Ticket</option>

                                </select>
                                <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="purchase_price">Issue Price</label>
                                <input type="text"  name="purchase_price" class="form-control <?php $__errorArgs = ['purchase_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('purchase_price')); ?>" required>
                                <span class="text-danger error-text purchase_price_error"></span>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sale_price">Sale Price</label>
                                <input type="text"  name="sale_price" class="form-control <?php $__errorArgs = ['sale_price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('sale_price')); ?>" required>
                                <span class="text-danger error-text sale_price_error"></span>


                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="sale_date">Issue Date</label>
                            <input type="date" name="sale_date" class="form-control <?php $__errorArgs = ['sale_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('sale_date')); ?>" required>
                            <span class="text-danger error-text sale_date_error"></span>

                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payment_status">Payment Status</label>
                                <select name="payment_status" class="form-control <?php $__errorArgs = ['payment_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="Paid" <?php echo e(old('payment_status') == 'Paid' ? 'selected' : ''); ?>>Paid</option>
                                    <option value="Pending" <?php echo e(old('payment_status') == 'Pending' ? 'selected' : ''); ?>>Pending</option>
                                </select>
                                <?php $__errorArgs = ['payment_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>


                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="sale_date">NIE</label>
                            <input type="text" name="id_card" class="form-control <?php $__errorArgs = ['id_card'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('id_card')); ?>" required>
                            <span class="text-danger error-text id_card_error"></span>

                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payment_status">Passport Number</label>
                                <input type="text" name="passport_no" class="form-control <?php $__errorArgs = ['passport_no'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('passport_no')); ?>" required>

                                <?php $__errorArgs = ['payment_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>


                        </div>

                    </div>
                     <div class="form-group">
                            <label for="payment_date">Note</label>
                            <textarea rows="" cols="" class="form-control" name="note" id="note"></textarea>
                            <span class="text-danger error-text note_error"></span>

                        </div>

                    <!-- Payment Details -->
                    <div class="form-group">
                        <input type="checkbox" id="payment_checkbox" name="payment_received" value="1">
                        <label for="payment_checkbox">Payment Received</label>
                    </div>
                    <div id="payment_fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="amount_paid">Amount Paid</label>
                                    <input type="number" step="0.01" name="amount_paid" class="form-control" value="<?php echo e(old('amount_paid')); ?>">
                                    <span class="text-danger error-text amount_paid_error"></span>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_method">Payment Method</label>
                                    <select name="payment_method" class="form-control">
                                        <option value="Cash">Cash</option>
                                        <option value="Card">Card</option>
                                        <option value="Bank">Bank</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bank_name">Bank Name</label>
                                    <input type="text" name="bank_name" class="form-control <?php $__errorArgs = ['bank_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('bank_name')); ?>">
                                    <span class="text-danger error-text bank_name_error"></span>


                                </div>
                            </div>
<div class="col-md-6">
                            <label for="payment_date">Payment Date</label>
                            <input type="date" name="payment_date" class="form-control <?php $__errorArgs = ['payment_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('payment_date')); ?>">
                            <span class="text-danger error-text payment_date_error"></span>

                        </div>
                        </div>


                    </div>

                    <button type="submit" id="ticketFormBtn" class="btn btn-primary">Create Ticket</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('payment_checkbox').addEventListener('change', function () {
        const paymentFields = document.getElementById('payment_fields');
        paymentFields.style.display = this.checked ? 'block' : 'none';
    });


</script>

<?php /**PATH C:\laragon\www\testing_project\resources\views/admin/tickets/partials/add.blade.php ENDPATH**/ ?>