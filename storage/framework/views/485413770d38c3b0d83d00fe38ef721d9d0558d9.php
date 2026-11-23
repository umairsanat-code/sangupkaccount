<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="icon" href="<?php echo e(asset('/img/logo/logo.png')); ?>" type="image/png">

    <title><?php echo e(config('app.name', 'TechSwivel')); ?></title>
    <!-- Styles -->
    <link href="<?php echo e(asset('/css/app.css')); ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
         .active{
            background-color: #19A2FF !important;
            color:white !important;
        }
        .logo{width:20%;margin-bottom:60px;margin-top:40px}
        .eloumo-bg{background-color: #19A2FF;}
    </style>
    <?php echo $__env->yieldContent('style'); ?>
</head>
<body class="hold-transition login-page eloumo-bg">


    <?php echo $__env->yieldContent('content'); ?>


<script src="<?php echo e(asset('/js/app.js')); ?>"></script>
    <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\laragon\www\testing_project\resources\views/layouts/auth.blade.php ENDPATH**/ ?>