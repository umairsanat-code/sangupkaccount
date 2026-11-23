<div class="row">
    <div class="col-md-6">
        <label>Agency Name *</label>
        <input type="text" name="name" class="form-control" required value="<?php echo e(old('name', $agency->name ?? '')); ?>">
    </div>
    <div class="col-md-6">
        <label>Contact Person</label>
        <input type="text" name="contact_person" class="form-control" value="<?php echo e(old('contact_person', $agency->contact_person ?? '')); ?>">
    </div>
    <div class="col-md-6">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control" value="<?php echo e(old('phone', $agency->phone ?? '')); ?>">
    </div>
    <div class="col-md-6">
        <label>WhatsApp</label>
        <input type="text" name="whatsapp" class="form-control" value="<?php echo e(old('whatsapp', $agency->whatsapp ?? '')); ?>">
    </div>
    <div class="col-md-6">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?php echo e(old('email', $agency->email ?? '')); ?>">
    </div>
    <div class="col-md-6">
        <label>Country</label>
        <input type="text" name="country" class="form-control" value="<?php echo e(old('country', $agency->country ?? '')); ?>">
    </div>
    <div class="col-md-6">
        <label>City</label>
        <input type="text" name="city" class="form-control" value="<?php echo e(old('city', $agency->city ?? '')); ?>">
    </div>
    <div class="col-md-6">
        <label>Services</label>
        <select name="services[]" class="form-control" multiple size="4">
            <?php $__currentLoopData = ['visa', 'umrah', 'hotel', 'transport', 'tickets']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($s); ?>"
                    <?php echo e(in_array($s, old('services', $agency->services ?? [])) ? 'selected' : ''); ?>>
                    <?php echo e(ucfirst($s)); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="col-md-12">
        <label>Address</label>
        <textarea name="address" class="form-control" rows="2"><?php echo e(old('address', $agency->address ?? '')); ?></textarea>
    </div>
</div>
<br><?php /**PATH C:\laragon\www\testing_project\resources\views/admin/visa_umrah/agencies/_form.blade.php ENDPATH**/ ?>