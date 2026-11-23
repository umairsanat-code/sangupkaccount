<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'TechSwivel')); ?></title>
    <!-- Styles -->
    <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet">
    <style>
        .logo{width:20%;margin-bottom:60px;margin-top:40px}
          .active{
            background-color: #19A2FF !important;
            color:white !important;
        }
    </style>
</head>
<body class="hold-transition login-page" style="background-color: #19A2FF;">
<div class="login-box" >
    <!-- /.login-logo -->
    <div class="card">

        <div class="card-body login-card-body">
            <div class="container text-center">
            <img src="<?php echo e(asset('img/logo/logo.svg')); ?>" class="img-responsive logo">
         </div>
            
            <?php if(session('status')): ?>
            <div class="alert alert-success">
                <?php echo e(session('status')); ?>

            </div>
        <?php endif; ?>
            <form action="<?php echo e(route('password.email')); ?>" method="post">
                <?php echo csrf_field(); ?>
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

                <div class="row">
                 <div class="col-12">
                        <button class="btn btn-primary btn-block active">Verify</button>
                    </div>
                    <div class="col-12 text-center" style="margin-top:25px;">
                        <a href="<?php echo e(route('login')); ?>" class="text text-center" style="color:#19A2FF">Back</a>
                    </div>
                </div>
            </form>
         </div>
    </div>
</div>

<script src="<?php echo e(asset('js/app.js')); ?>"></script>
</body>
</html>
<?php /**PATH C:\laragon\www\testing_project\resources\views/auth/passwords/email.blade.php ENDPATH**/ ?>