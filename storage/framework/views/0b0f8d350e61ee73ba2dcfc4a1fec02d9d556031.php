<?php $__env->startSection('title', 'New Agency'); ?>

<?php $__env->startSection('content'); ?>
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h3 class="card-title mb-0">New Agency / Supplier</h3>
                            <a href="<?php echo e(route('visa_umrah.agencies.index')); ?>" class="btn btn-secondary btn-sm float-right">Back</a>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo e(route('visa_umrah.agencies.store')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <?php echo $__env->make('admin.visa_umrah.agencies._form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <button class="btn btn-primary">Create Agency</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\testing_project\resources\views/admin/visa_umrah/agencies/create.blade.php ENDPATH**/ ?>