<?php $__env->startSection('title', 'New ' . ucfirst($type) . ' Booking'); ?>

<?php $__env->startSection('content'); ?>
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h3 class="card-title mb-0">New <?php echo e(ucfirst($type)); ?> Booking</h3>
                            <a href="<?php echo e(route('visa_umrah.bookings.index', ['type' => $type])); ?>" class="btn btn-secondary btn-sm float-right">Back</a>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo e(route('visa_umrah.bookings.store', ['type' => $type])); ?>" method="POST">
                                <?php echo csrf_field(); ?>

                                <h5 class="text-primary">Client Information</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Client Name *</label>
                                        <input type="text" name="client_name" class="form-control" required value="<?php echo e(old('client_name')); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Phone / WhatsApp</label>
                                        <input type="text" name="client_phone" class="form-control" value="<?php echo e(old('client_phone')); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Email</label>
                                        <input type="email" name="client_email" class="form-control" value="<?php echo e(old('client_email')); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Passport Number</label>
                                        <input type="text" name="passport_number" class="form-control" value="<?php echo e(old('passport_number')); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Nationality</label>
                                        <input type="text" name="nationality" class="form-control" value="<?php echo e(old('nationality')); ?>">
                                    </div>
                                </div>

                                <hr>
                                <h5 class="text-primary"><?php echo e(ucfirst($type)); ?> Details</h5>
                                <div class="row">
                                    <?php if($type === 'visa'): ?>
                                        <div class="col-md-4">
                                            <label>Visa Country</label>
                                            <input type="text" name="visa_country" class="form-control" value="<?php echo e(old('visa_country')); ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Visa Type</label>
                                            <input type="text" name="visa_type" class="form-control" placeholder="e.g. Tourist, Business" value="<?php echo e(old('visa_type')); ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Number of Travelers</label>
                                            <input type="number" name="num_travelers" class="form-control" min="1" value="<?php echo e(old('num_travelers')); ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Visa Status</label>
                                            <select name="visa_status" class="form-control">
                                                <?php $__currentLoopData = \App\Models\VisaUmrahBooking::VISA_STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($s); ?>" <?php echo e(old('visa_status') == $s ? 'selected' : ''); ?>><?php echo e(ucwords(str_replace('_', ' ', $s))); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    <?php else: ?>
                                        <div class="col-md-4">
                                            <label>Number of Adults</label>
                                            <input type="number" name="num_adults" class="form-control" min="1" value="<?php echo e(old('num_adults')); ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Number of Children</label>
                                            <input type="number" name="num_children" class="form-control" min="0" value="<?php echo e(old('num_children')); ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Umrah Package</label>
                                            <input type="text" name="umrah_package" class="form-control" placeholder="e.g. 5 Nights Makkah + 4 Nights Madinah" value="<?php echo e(old('umrah_package')); ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Booking Status</label>
                                            <select name="booking_status" class="form-control">
                                                <?php $__currentLoopData = \App\Models\VisaUmrahBooking::BOOKING_STATUSES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($s); ?>" <?php echo e(old('booking_status') == $s ? 'selected' : ''); ?>><?php echo e(ucfirst($s)); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    <?php endif; ?>
                                    <div class="col-md-4">
                                        <label>Travel / Expected Arrival Date</label>
                                        <input type="date" name="travel_date" class="form-control" value="<?php echo e(old('travel_date')); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Return / Departure Date</label>
                                        <input type="date" name="return_date" class="form-control" value="<?php echo e(old('return_date')); ?>">
                                    </div>
                                </div>

                                <hr>
                                <h5 class="text-primary">Agency / Supplier (Main Service)</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Agency / Supplier</label>
                                        <select name="main_agency_id" class="form-control">
                                            <option value="">Select Agency</option>
                                            <?php $__currentLoopData = $agencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($a->id); ?>" <?php echo e(old('main_agency_id') == $a->id ? 'selected' : ''); ?>><?php echo e($a->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Agency Reference Number</label>
                                        <input type="text" name="main_agency_reference" class="form-control" value="<?php echo e(old('main_agency_reference')); ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Agency Cost (€)</label>
                                        <input type="number" name="main_agency_cost" step="0.01" min="0" class="form-control" value="<?php echo e(old('main_agency_cost', 0)); ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Client Total (€)</label>
                                        <input type="number" name="main_client_total" step="0.01" min="0" class="form-control" value="<?php echo e(old('main_client_total', 0)); ?>">
                                    </div>
                                </div>

                                <hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Notes</label>
                                        <textarea name="notes" class="form-control" rows="2"><?php echo e(old('notes')); ?></textarea>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button class="btn btn-primary">Create Booking</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\testing_project\resources\views/admin/visa_umrah/bookings/create.blade.php ENDPATH**/ ?>