<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo e(asset('css/login.css')); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>Login</title>
    
</head>
<body>
    <div class="login-container">
        <img src="<?php echo e(asset('images/pnlogo.png')); ?>" alt="Logo">
        <form action="<?php echo e(route('login')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <label for="user_id">Faculty ID / Student ID</label>
            <input type="text" name="user_id" id="user_id"  >
            <label for="password">Password</label>
            <input type="password" name="password" >
            <button type="submit">Login</button>
        
            <a href="<?php echo e(route('forgot-password')); ?>" >Forgot Password?</a>
        </form>


 
        <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <p class="error-message" style="color: red"><?php echo e($error); ?></p>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger">
            <p class="error-message"  style="color: red"><?php echo e(session('error')); ?></p>
        </div>
    <?php endif; ?>


    </div>

 
    


    
</body>
</html><?php /**PATH C:\laravel\PNPH-CAPSTONE\resources\views/login.blade.php ENDPATH**/ ?>