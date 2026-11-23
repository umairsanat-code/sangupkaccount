
<?php $__env->startSection('content'); ?>
<div class="login-box" style="width: 100%; max-width: 430px;">
    <div class="card border-0 shadow-lg" style="border-radius: 18px; background: rgba(255,255,255,0.96);">
        <div class="card-body login-card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <img src="<?php echo e(asset('/img/logo/logo.svg')); ?>" class="img-responsive logo" alt="Logo">
                <h4 class="font-weight-bold mb-1" style="color:#0f172a;">Welcome Back</h4>
                <p class="text-muted mb-0">Sign in to access your dashboard.</p>
            </div>

            <?php if(session()->has('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> <?php echo e(session()->get('error')); ?>

                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('login')); ?>" method="post">
                <?php echo csrf_field(); ?>
                <div class="form-group mb-3">
                    <label class="font-weight-semibold">Email Address</label>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" class="form-control"
                           placeholder="you@example.com" style="height: 46px; border-radius: 10px;">
                    <?php if($errors->has('email')): ?>
                        <span class="invalid-feedback" style="display: block;" role="alert">
                            <strong><?php echo e($errors->first('email')); ?></strong>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="form-group mb-3">
                    <label class="font-weight-semibold">Password</label>
                    <div class="input-group">
                        <input type="password" id="password" required name="password" class="form-control"
                               placeholder="Enter your password" style="height: 46px; border-radius: 10px 0 0 10px;">
                        <div class="input-group-append">
                            <div class="input-group-text bg-white" style="border-radius: 0 10px 10px 0;">
                                <span class="input-group-addon">
                                    <i id="PassShowHide" toggle="#password" class="fa fa-eye toggle-password"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="btn btn-primary btn-block active mt-2" style="height: 46px; border-radius: 10px;">
                    Sign In
                </button>
            </form>

            <p class="mb-0 mt-3 text-right">
                <a href="<?php echo e(route('password.request')); ?>">Forgot password?</a>
            </p>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\testing_project\resources\views/auth/login.blade.php ENDPATH**/ ?>