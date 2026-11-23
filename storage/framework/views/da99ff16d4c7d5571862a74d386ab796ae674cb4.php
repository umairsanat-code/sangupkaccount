<div class="row g-3">
    <div class="col-md-6">
        <label for="user_id" class="form-label">User</label>
        <select name="user_id" id="user_id" class="form-control">
            <option value="">Select User</option>
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option
                    value="<?php echo e($user->id); ?>"
                    <?php echo e(old('user_id', $cardPayment->user_id) == $user->id ? 'selected' : ''); ?>

                >
                    <?php echo e(trim(($user->firstName ?? '') . ' ' . ($user->lastName ?? '')) ?: ('User #' . $user->id)); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div class="col-md-6">
        <label for="bank_name" class="form-label">Bank Name</label>
        <select name="bank_name" id="bank_name" class="form-control">
            <option value="">Select Bank</option>
            <?php $__currentLoopData = $bankNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bankName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option
                    value="<?php echo e($bankName); ?>"
                    <?php echo e(old('bank_name', $cardPayment->bank_name) === $bankName ? 'selected' : ''); ?>

                >
                    <?php echo e($bankName); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>














    <div class="col-md-3">
        <label for="received_amount" class="form-label">Received Amount</label>
        <input
            type="number"
            name="received_amount"
            id="received_amount"
            class="form-control"
            step="0.01"
            min="0"
            value="<?php echo e(old('received_amount', $cardPayment->received_amount)); ?>"
        >
    </div>

    <div class="col-md-3">
        <label for="received_date" class="form-label">Received Date</label>
        <input
            type="date"
            name="received_date"
            id="received_date"
            class="form-control"
            value="<?php echo e(old('received_date', optional($cardPayment->received_date)->format('Y-m-d'))); ?>"
        >
    </div>

    <div class="col-md-3">
        <label for="sent_amount" class="form-label">Sent Amount</label>
        <input
            type="number"
            name="sent_amount"
            id="sent_amount"
            class="form-control"
            step="0.01"
            min="0"
            value="<?php echo e(old('sent_amount', $cardPayment->sent_amount)); ?>"
        >
    </div>

    <div class="col-md-3">
        <label for="sent_date" class="form-label">Sent Date</label>
        <input
            type="date"
            name="sent_date"
            id="sent_date"
            class="form-control"
            value="<?php echo e(old('sent_date', optional($cardPayment->sent_date)->format('Y-m-d'))); ?>"
        >
    </div>
</div>

<div class="mt-4 d-flex">
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save mr-1"></i> <?php echo e($submitLabel ?? 'Save'); ?>

    </button>
    <a href="<?php echo e(route('card-payments.index')); ?>" class="btn btn-outline-secondary ml-2">Cancel</a>
</div>
<?php /**PATH C:\laragon\www\testing_project\resources\views/admin/card_payments/_form.blade.php ENDPATH**/ ?>