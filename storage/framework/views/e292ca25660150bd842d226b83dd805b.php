<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title ?? 'Student Dashboard'); ?></title>
    <link rel="stylesheet" href="<?php echo e(asset('css/nav.css')); ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">

    <style>
    body {
        margin: 0;  
        font-family: 'Poppins', sans-serif;
        background-color: #f1f5f9;
        height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .top-bar {
        background-color: #22bbea;
        padding: 0 20px;
        display: flex;
        align-items: center;
        height: 80px;
        flex-shrink: 0;
    }

    .PN-logo {
        height: 40px;
    }

    .container {
        display: flex;
        flex: 1;
        overflow: hidden;
    }

    .sidebar {
        background-color: #ffffff;
        width: 250px;
        padding: 20px 0;
        display: flex;
        flex-direction: column;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        flex-shrink: 0;
    }

    .menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .menu li {
        padding: 12px 20px;
        display: flex;
        flex-direction: column;
        cursor: pointer;
        transition: background-color 0.3s;
        border-radius: 8px;
        margin: 0 10px;
    }

    .menu li a {
        color: #333333;
        text-decoration: none;
        width: 100%;
        font-size: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .menu li img {
        width: 24px;
        height: 24px;
    }

    .menu li:hover {
        background-color: #f1f5f9;
    }

    .menu li.active {
        background-color: #f1f5f9;
    }

    .content {
        flex-grow: 1;
        padding: 20px;
        overflow-y: auto;
        background-color: #f8f9fa;
        margin-left: 250px;
    }

    .user-info {
        margin-left: auto;
        color: #333;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .logout-btn {
        background: none;
        border: none;
        color: inherit;
        cursor: pointer;
        padding: 5px;
    }

    .logout-btn:hover {
        color: #ff9933;
    }
    </style>
</head>
<body>
    <div class="top-bar">
        <img class="PN-logo" src="<?php echo e(asset('images/PN-logo.png')); ?>" alt="PN Logo">

        <?php if(auth()->guard()->check()): ?>
            <?php
                $user = Auth::user();
            ?>

            <div class="user-info">
                Logged in as: 
                <span style="color:white;">
                    <?php echo e($user->user_fname); ?> <?php echo e($user->user_mInitial); ?> <?php echo e($user->user_lname); ?> <?php echo e($user->suffix); ?>

                </span> 
                | Role: 
                <span style="color:white;">
                    <?php echo e(ucfirst($user->user_role)); ?>

                </span>

                <form action="<?php echo e(route('logout')); ?>" method="POST" id="logout-form" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <button type="button" class="logout-btn" onclick="confirmLogout()">
                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H8m12 0-4 4m4-4-4-4M9 4H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h2"/>
                        </svg>
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <div class="container">
        <aside class="sidebar">
            <?php if(auth()->guard()->check()): ?>
                <?php $role = strtolower(Auth::user()->user_role ?? ''); ?>
                <?php if($role === 'student'): ?>
                    <ul class="menu">
                        <li class="<?php echo e(request()->routeIs('student.dashboard') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('student.dashboard')); ?>">
                                <img src="<?php echo e(asset('images/dashboard.png')); ?>" alt="Dashboard">
                                Dashboard
                            </a>
                        </li>
                        
                        <li>
                            <a href="#">
                                <img src="<?php echo e(asset('images/me.png')); ?>" alt="Profile">
                                Profile
                            </a>
                        </li>
                    </ul>
                <?php endif; ?>
            <?php endif; ?>
        </aside>

        <main class="content">
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>

    <script>
    function confirmLogout() {
        if (confirm("Are you sure you want to log out?")) {
            document.getElementById('logout-form').submit();
        }
    }
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html> <?php /**PATH C:\laravel\PNPH-CAPSTONE\resources\views/layouts/student_layout.blade.php ENDPATH**/ ?>