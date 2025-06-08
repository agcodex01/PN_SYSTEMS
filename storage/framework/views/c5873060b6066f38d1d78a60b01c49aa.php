<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title ?? 'Dashboard'); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/nav.css')); ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
    :root {
        --sidebar-width: 250px;
        --topbar-height: 80px;
        --content-padding: 20px;
    }
    
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    
    body {
        margin: 0;
        padding: 0;
        font-family: 'Poppins', sans-serif;
        background-color: #f1f5f9;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .logout-btn {
        background: none;
        border: none;
        color: inherit;
        cursor: pointer;
    }

    .logout-btn:hover {
        color: #ff9933;
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

    .layout-container {
        display: flex;
        flex: 1;
        min-height: calc(100vh - var(--topbar-height));
        margin-top: var(--topbar-height);
        position: relative;
        width: 100%;
    }

    .sidebar {
        background-color: #ffffff;
        width: var(--sidebar-width);
        padding: 20px 0;
        position: fixed;
        top: var(--topbar-height);
        left: 0;
        bottom: 0;
        overflow-y: auto;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        z-index: 100;
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
        display: flex;
        align-items: center;
        gap: 10px;
        color: #333333;
        text-decoration: none;
        width: 100%;
        font-size: 15px;
    }

    .dropdown > a {
        display: flex;
        align-items: center;
        width: 100%;
    }

    .dropdown-content {
        display: none;
        padding: 5px 0;
        margin-top: 8px;
    }

    .dropdown.active .dropdown-content {
        display: block;
    }

    .dropdown-content a {
        padding: 8px 0 8px 34px;
        color: #333333;
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        font-size: 14px;
        transition: background-color 0.2s;
    }

    .dropdown-content a img {
        width: 20px;
        height: 20px;
    }

    .dropdown-content a:hover {
        background-color: #f1f5f9;
    }

    .dropdown-content a.active {
        background-color: #f1f5f9;
        font-weight: 600;
    }

    .dropdown > a::after {
        content: '▼';
        font-size: 10px;
        margin-left: auto;
    }

    .dropdown.active > a::after {
        content: '▲';
    }

    .menu li img {
        width: 24px;
        height: 24px;
    }

    .content {
        flex-grow: 1;
        padding: var(--content-padding);
        overflow-y: auto;
        background-color: #f8f9fa;
        margin-left: var(--sidebar-width);
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

            <div class="user-info" style="color: #333; font-weight: 500; display: flex; align-items: center; gap: 15px;">
                Logged in as: 
                <span style="color: white;">
                    <?php echo e($user->user_fname); ?> <?php echo e($user->user_mInitial); ?> <?php echo e($user->user_lname); ?> <?php echo e($user->suffix); ?>

                </span> 
                | Role: 
                <span style="color: white;">
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


    <div class="layout-container">
        <aside class="sidebar">
            <ul class="menu">

                <li class="<?php echo e(request()->routeIs('educator.dashboard') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('educator.dashboard')); ?>">
                        <img src="<?php echo e(asset('images/Dashboard.png')); ?>" alt="Dashboard"> Dashboard
                    </a>
                </li>
                <li class="<?php echo e(request()->routeIs('educator.students-info') ? 'active' : ''); ?>">
                    <a href="<?php echo e(route('educator.students.index')); ?>">
                        <img src="<?php echo e(asset('images/mu.png')); ?>" alt="Students Info"> Students Info
                    </a>
                </li>

               
                <li class="dropdown <?php echo e(request()->routeIs('educator.analytics.*') ? 'active' : ''); ?>" id="educatorAnalyticsDropdown">
                    <a href="#" onclick="toggleDropdown(event)">
                        <img src="<?php echo e(asset('images/analytics.png')); ?>" alt="Analytics"> Analytics
                    </a>
                    <div class="dropdown-content">
                        <a href="<?php echo e(route('educator.analytics.class-grades')); ?>" class="<?php echo e(request()->routeIs('educator.analytics.class-grades') ? 'active' : ''); ?>">
                            <img src="<?php echo e(asset('images/class grades.png')); ?>" alt="Class Grades"> Class Grades
                        </a>
                        <a href="<?php echo e(route('educator.analytics.subject-progress')); ?>" class="<?php echo e(request()->routeIs('educator.analytics.subject-progress') ? 'active' : ''); ?>">
                            <img src="<?php echo e(asset('images/subject progress.png')); ?>" alt="Subject Progress"> Subject Progress
                        </a>
                        <a href="<?php echo e(route('educator.analytics.subject-intervention')); ?>" class="<?php echo e(request()->routeIs('educator.analytics.subject-intervention') ? 'active' : ''); ?>">
                            <img src="<?php echo e(asset('images/subject intervention.png')); ?>" alt="Subject Intervention"> Subject Intervention
                        </a>
                        <a href="<?php echo e(route('educator.analytics.class-progress')); ?>" class="<?php echo e(request()->routeIs('educator.analytics.class-progress') ? 'active' : ''); ?>">
                            <img src="<?php echo e(asset('images/analytics.png')); ?>" alt="Class Progress"> Class Progress
                        </a>
                        <a href="<?php echo e(route('educator.analytics.intern-grades-progress')); ?>" class="<?php echo e(request()->routeIs('educator.analytics.intern-grades-progress') ? 'active' : ''); ?>">
                            <img src="<?php echo e(asset('images/internship grades.png')); ?>" alt="Internship Grades Progress"> Internship Grades Progress
                        </a>
                    </div>
                </li>
                <li>
                    <a href="<?php echo e(route('educator.intervention')); ?>">
                        <img src="<?php echo e(asset('images/is.png')); ?>" alt="Intervention Status"> Intervention Status
                    </a>
                </li>
                <li>
                    <a href="#">
                        <img src="<?php echo e(asset('images/me.png')); ?>" alt="Profile"> Profile
                    </a>
                </li>

            

            </ul>
        </aside>

        <main class="content">
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>

    <script>

    function toggleDropdown(event) {
        event.preventDefault();
        const dropdown = event.target.closest('.dropdown');
        if (dropdown) {
            dropdown.classList.toggle('active');
        }
    }

    
     function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                document.getElementById('logout-form').submit();
            }
        }
    
</script>

<?php echo $__env->yieldPushContent('scripts'); ?>

   
</body>
</html>
<?php /**PATH C:\laravel\PNPH-CAPSTONE\resources\views/layouts/educator_layout.blade.php ENDPATH**/ ?>