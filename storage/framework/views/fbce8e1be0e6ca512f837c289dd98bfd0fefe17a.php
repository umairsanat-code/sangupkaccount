

<?php $__env->startSection('title', 'Edit Card Payment'); ?>

<?php $__env->startSection('content'); ?>
    <div class="content-wrapper py-4">
        <div class="container-fluid">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Edit Card Payment</h3>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('card-payments.update', $cardPayment)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <?php echo $__env->make('admin.card_payments._form', ['submitLabel' => 'Update Payment'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\testing_project\resources\views/admin/card_payments/edit.blade.php ENDPATH**/ ?>