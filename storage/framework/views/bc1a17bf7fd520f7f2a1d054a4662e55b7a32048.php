
<?php $__env->startSection('content'); ?>
<div class="login-box" >
 <!-- /.login-logo -->
   <div class="card">
    <div class="card-body login-card-body">
         <div class="container text-center">
            <img src="<?php echo e(asset('/img/logo/logo.png')); ?>" class="img-responsive logo">
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
                <div class="row">
                <div class="col-md-12 col-lg-12 col-sm-12 col-sx-12">
                <div class="form-group mb-3">
                    <label>Email Address</label>
                    <input type="email"  name="email" value="<?php echo e(old('email')); ?>" class="form-control"
                           placeholder="Email">
                  <?php if($errors->has('email')): ?>
                        <span class="invalid-feedback" style="display: block;" role="alert">
                    <strong><?php echo e($errors->first('email')); ?></strong>
                 </span>
                    <?php endif; ?>
                </div>
                </div>
                <div class="col-md-12 col-lg-12 col-sm-12 col-sx-12">
                    <label>Password</label>
                <div class="input-group mb-3">
                  <input type="password" id="password" required name="password" class="form-control"
                           placeholder="Password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="input-group-addon"><i id="PassShowHide" toggle="#password" class="fa fa-eye toggle-password"></i></span>
                        </div>
                </div>
            </div>
        </div>
           </div>
                <div class="row">
                   <div class="col-12">
                        <button class="btn btn-primary btn-block active" >Sign In</button>
                    </div>
                </div>
            </form>
             <p class="mb-1 text text-right">
               <a href="<?php echo e(route('password.request')); ?>"> Forgot password</a>
            </p>


        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\testing_project\resources\views/auth/login.blade.php ENDPATH**/ ?>