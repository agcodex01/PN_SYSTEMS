<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo e($title ?? 'Student Dashboard'); ?></title>
    <link rel="stylesheet" href="<?php echo e(asset('css/nav.css')); ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* Theme Loader */
    .loader {
        width: 50px;
        aspect-ratio: 1;
        border-radius: 50%;
        border: 8px solid #22bbea;
        border-right-color: #ff9933;
        animation: l2 1s infinite linear;
    }
    @keyframes l2 {
        to { transform: rotate(1turn); }
    }

    /* Loader Overlay */
    .loader-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.9);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        opacity: 1;
        transition: opacity 0.3s ease;
    }

    .loader-overlay.hidden {
        opacity: 0;
        pointer-events: none;
    }

    * {
        font-family: 'Poppins', sans-serif !important;
    }

    /* Preserve icon fonts */
    .fas, .far, .fal, .fab, .fa,
    [class*="fa-"],
    .material-icons,
    .glyphicon {
        font-family: "Font Awesome 6 Free", "Font Awesome 6 Pro", "Font Awesome 5 Free", "Font Awesome 5 Pro", "Material Icons", "Glyphicons Halflings" !important;
    }

    /* Preserve SVG icons */
    svg {
        font-family: inherit !important;
    }

    body {
        font-family: 'Poppins', sans-serif !important;
        background-color: #f8f9fa;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        overflow-x: hidden; /* Prevent horizontal scroll */
        -webkit-text-size-adjust: 100%; /* Prevent text scaling on iOS */
        -webkit-tap-highlight-color: transparent; /* Remove tap highlight */
    }

    /* Header */
    .top-bar {
        background-color: #22bbea !important;
        padding: 0 20px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        height: 70px !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        z-index: 1000 !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important;
    }

    /* Mobile Menu Toggle */
    .mobile-menu-toggle {
        display: none;
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
        padding: 8px;
        border-radius: 4px;
        transition: background-color 0.3s ease;
    }

    .mobile-menu-toggle:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    /* Top bar layout */
    .top-bar-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Ensure our logo styles take precedence */
    .top-bar .PN-logo {
        height: 50px !important;
        width: auto !important;
        max-width: 220px !important;
        object-fit: contain !important;
        margin: 0 !important;
        display: block !important;
        flex-shrink: 0 !important;
        transition: all 0.3s ease !important;
    }

    .logo-link {
        display: flex !important;
        align-items: center !important;
        height: 100% !important;
        padding: 10px 20px !important;
        text-decoration: none !important;
        margin: 0 !important;
    }

    /* Main Layout */
    .main-wrapper {
        display: flex;
        padding-top: 70px; /* Height of top-bar */
        min-height: 100vh;
        width: 100%;
        position: relative;
    }

    /* Sidebar */
    .sidebar {
        background-color: #ffffff;
        width: 260px;
        position: fixed;
        top: 70px; /* Adjusted to connect with header */
        left: 0;
        bottom: 0;
        overflow-y: auto;
        box-shadow: 2px 0 10px rgba(0,0,0,0.05);
        z-index: 900;
        padding: 0;
        margin: 0;
        transition: transform 0.3s ease;
    }

    /* Sidebar overlay for mobile */
    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 850;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .sidebar-overlay.active {
        opacity: 1;
    }

    /* Menu Styling */
    .menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .menu li:first-child {
        margin-top: 10px;
    }
    
    .menu li {
        margin-bottom: 5px;
    }

    .menu li a {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        color: #333;
        text-decoration: none;
        font-size: 15px;
        transition: all 0.2s ease;
        border-radius: 8px;
    }

    .menu-icon {
        width: 24px;
        height: 24px;
        margin-right: 12px;
        object-fit: contain;
    }

    .menu li a:hover {
        background-color: #f1f5f9;
        color: #22bbea;
    }

    .menu li.active a {
        background-color: #e3f2fd;
        color: #22bbea;
        font-weight: 500;
    }
    
    .menu li.active .menu-icon {
        /* Remove the blue filter to keep the original image */
        filter: none;
    }

    .menu li img {
        width: 24px;
        height: 24px;
    }
    
    /* Sidebar Logo */
    .sidebar-logo {
        padding: 20px 0;
        text-align: center;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 20px;
    }
    
    .dashboard-logo {
        max-width: 80%;
        height: auto;
        max-height: 80px;
        object-fit: contain;
    }

    .menu li:hover {
        background-color: #f1f5f9;
    }

    .menu li.active {
        background-color: #f1f5f9;
    }

    /* Main Content */
    .content {
        flex: 1;
        margin-left: 260px; /* Same as sidebar width */
        padding: 30px;
        min-height: calc(100vh - 70px);
        background-color: #f8f9fa;
        width: calc(100% - 260px);
        transition: margin-left 0.3s ease, width 0.3s ease;
    }

    /* User Info */
    .user-info {
        margin-left: auto;
        color: #fff;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .user-info span {
        color: #fff;
    }

    .user-name,
    .user-role-text {
        color: black;
    }

    .logged-in-text,
    .role-separator {
        color:rgb(37, 37, 37);
    }

    .user-role {
        background-color: rgba(255, 255, 255, 0.2);
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 13px;
    }

    /* Logout Button */
    .logout-btn {
        background: none;
        border: none;
        color: #fff;
        cursor: pointer;
        padding: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        transition: all 0.2s ease;
    }

    .logout-btn:hover {
        background-color: rgba(255, 255, 255, 0.2);
        color: #ff9933;
    }

    /* Responsive Design */

    /* Large screens (1200px and up) */
    @media (min-width: 1200px) {
        .content {
            padding: 40px;
        }
    }

    /* Medium screens (992px to 1199px) */
    @media (max-width: 1199px) {
        .sidebar {
            width: 240px;
        }

        .content {
            margin-left: 240px;
            width: calc(100% - 240px);
        }
    }

    /* Small screens (768px to 991px) */
    @media (max-width: 991px) {
        .sidebar {
            width: 220px;
        }

        .content {
            margin-left: 220px;
            width: calc(100% - 220px);
            padding: 25px;
        }

        .user-info {
            font-size: 14px;
        }

        .logged-in-text,
        .role-separator {
            display: none; /* Hide "Logged in as:" and "| Role:" text on smaller screens */
        }

        .user-name {
            display: none; /* Hide user name on smaller screens */
        }

        .user-role {
            display: inline-block;
        }
    }

    /* Tablet screens (768px and below) */
    @media (max-width: 768px) {
        .top-bar {
            padding: 0 15px !important;
        }

        .mobile-menu-toggle {
            display: block;
        }

        .sidebar {
            transform: translateX(-100%);
            width: 280px;
            top: 70px;
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .sidebar-overlay {
            display: block;
        }

        .content {
            margin-left: 0;
            width: 100%;
            padding: 20px 15px;
        }

        .user-info {
            gap: 10px;
        }

        .logged-in-text,
        .role-separator,
        .user-name {
            display: none; /* Hide "Logged in as:", "| Role:" text and user name on mobile */
        }

        .user-role {
            font-size: 12px;
            padding: 3px 8px;
        }

        .menu li a {
            padding: 15px 20px;
            font-size: 16px;
        }

        .menu-icon {
            width: 28px;
            height: 28px;
            margin-right: 15px;
        }
    }

    /* Mobile screens (576px and below) */
    @media (max-width: 576px) {
        .top-bar {
            height: 60px !important;
            padding: 0 10px !important;
        }

        .main-wrapper {
            padding-top: 60px;
        }

        .sidebar {
            top: 60px;
            width: 100%;
            max-width: 300px;
        }

        .content {
            padding: 15px 10px;
        }

        .logo-link {
            padding: 5px 10px !important;
        }

        .PN-logo {
            height: 40px !important;
            max-width: 180px !important;
        }

        .user-info {
            font-size: 12px;
        }

        .user-role {
            font-size: 11px;
            padding: 2px 6px;
        }

        .logout-btn {
            width: 32px;
            height: 32px;
        }

        .logout-btn svg {
            width: 20px;
            height: 20px;
        }
    }

    /* Extra small screens (480px and below) */
    @media (max-width: 480px) {
        .top-bar {
            height: 55px !important;
        }

        .main-wrapper {
            padding-top: 55px;
        }

        .sidebar {
            top: 55px;
        }

        .content {
            padding: 10px 8px;
        }

        .PN-logo {
            height: 35px !important;
            max-width: 150px !important;
        }

        .logged-in-text,
        .role-separator,
        .user-name,
        .user-role-text {
            display: none; /* Hide all user info text on very small screens */
        }

        .menu li a {
            padding: 12px 15px;
            font-size: 15px;
        }

        .menu-icon {
            width: 24px;
            height: 24px;
            margin-right: 12px;
        }
    }

    /* Landscape orientation adjustments for mobile */
    @media (max-height: 500px) and (orientation: landscape) {
        .sidebar {
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        .menu li a {
            padding: 10px 20px;
        }
    }

    /* Touch-friendly improvements */
    @media (hover: none) and (pointer: coarse) {
        .menu li a {
            padding: 18px 20px;
            min-height: 50px;
            display: flex;
            align-items: center;
        }

        .logout-btn {
            min-width: 44px;
            min-height: 44px;
        }

        .mobile-menu-toggle {
            min-width: 44px;
            min-height: 44px;
        }

        /* Ensure all buttons and links are touch-friendly */
        button, .btn, a, input[type="submit"], input[type="button"] {
            min-height: 44px;
            min-width: 44px;
            touch-action: manipulation;
        }

        /* Improve tap targets for small elements */
        .status-badge, .grade, .subject-code {
            padding: 8px 12px;
            min-height: 32px;
        }
    }

    /* Global mobile improvements */
    @media (max-width: 768px) {
        /* Ensure all clickable elements are touch-friendly */
        button, .btn, a, input[type="submit"], input[type="button"],
        .card, .status-card, .subject-card, .submission-card {
            touch-action: manipulation;
            -webkit-tap-highlight-color: rgba(0,0,0,0.1);
        }

        /* Prevent text selection on buttons and cards */
        button, .btn, .card, .status-card, .subject-card {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        /* Improve button spacing and sizing */
        .btn, button {
            padding: 12px 16px;
            min-height: 44px;
            font-size: 16px;
            border-radius: 8px;
        }

        /* Improve form elements */
        input, select, textarea {
            font-size: 16px; /* Prevent zoom on iOS */
            padding: 12px;
            min-height: 44px;
        }
    }
    </style>
</head>
<body>
    <!-- Theme Loader -->
    <div class="loader-overlay" id="pageLoader">
        <div class="loader"></div>
    </div>
    <div class="top-bar">
        <div class="top-bar-left">
            <button class="mobile-menu-toggle" id="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
            <a href="<?php echo e(route('student.dashboard')); ?>" class="logo-link">
                <img class="PN-logo" src="<?php echo e(asset('images/PN-logo.png')); ?>" alt="PN Logo">
            </a>
        </div>

        <!-- Debug Info -->
        <?php
            $user = Auth::user();
            $role = strtolower($user->user_role ?? 'none');
        ?>
        <!-- Debug: User Role: <?php echo e($role); ?> -->
        <!-- Debug: Is Student: <?php echo e($role === 'student' ? 'Yes' : 'No'); ?> -->
        <!-- Debug: Route: <?php echo e(request()->path()); ?> -->
        
        <?php if(auth()->guard()->check()): ?>
            <?php
                $user = Auth::user();
            ?>

            <div class="user-info">
                <span class="logged-in-text">Logged in as:</span>
                <span class="user-name">
                    <?php echo e($user->user_fname); ?> <?php echo e($user->user_mInitial); ?> <?php echo e($user->user_lname); ?> <?php echo e($user->suffix); ?>

                </span>
                <span class="role-separator">| Role:</span>
                <span class="user-role-text">
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

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <div class="main-wrapper">
        <aside class="sidebar">
            <?php if(auth()->guard()->check()): ?>
                <?php $role = strtolower(Auth::user()->user_role ?? ''); ?>
                <?php if($role === 'student'): ?>
                    <ul class="menu">
                        <li class="<?php echo e(request()->routeIs('student.dashboard') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('student.dashboard')); ?>">
                                <img src="<?php echo e(asset('images/Dashboard.png')); ?>" alt="Dashboard" class="menu-icon">
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="<?php echo e(request()->routeIs('student.grades') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('student.grades')); ?>">
                                <img src="<?php echo e(asset('images/Dashboard.png')); ?>" alt="Grade Status" class="menu-icon">
                                <span>Grade Status</span>
                            </a>
                        </li>
                        <li class="<?php echo e(request()->routeIs('student.grade-submissions.list') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('student.grade-submissions.list')); ?>">
                                <img src="<?php echo e(asset('images/mu.png')); ?>" alt="Grade Submissions" class="menu-icon">
                                <span>Grade Submissions</span>
                            </a>
                        </li>
                        <li class="<?php echo e(request()->routeIs('student.profile') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('student.profile')); ?>" id="profile-link">
                                <img src="<?php echo e(asset('images/me.png')); ?>" alt="Profile" class="menu-icon">
                                <span>My Profile</span>
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

    // Hide loader when page is loaded
    window.addEventListener('load', function() {
        const loader = document.getElementById('pageLoader');
        if (loader) {
            loader.classList.add('hidden');
            setTimeout(() => {
                loader.style.display = 'none';
            }, 300);
        }
    });

    // Show loader on user interactions
    document.addEventListener('DOMContentLoaded', function() {
        // Show loader on form submissions
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                // Only show loader if form validation passes
                if (form.checkValidity()) {
                    showLoader();
                }
            });
        });

        // Show loader on navigation links (exclude anchors, javascript links, and external links)
        document.querySelectorAll('a:not([href^="#"]):not([href^="javascript:"]):not([target="_blank"]):not([href^="mailto:"]):not([href^="tel:"])').forEach(link => {
            link.addEventListener('click', function(e) {
                // Don't show loader for dropdown toggles or other non-navigation clicks
                if (!this.getAttribute('onclick') || this.getAttribute('href') !== '#') {
                    // Check if this is a link that might show a confirmation dialog
                    const href = this.getAttribute('href');
                    const onclick = this.getAttribute('onclick');

                    // If it has confirm() in onclick, delay showing loader
                    if (onclick && onclick.includes('confirm(')) {
                        // Don't show loader immediately, let the confirm dialog handle it
                        return;
                    }

                    showLoader();
                }
            });
        });

        // Show loader on button clicks that might navigate
        document.querySelectorAll('button[type="submit"], .btn[href], button[onclick*="location"], button[onclick*="window.location"]').forEach(button => {
            button.addEventListener('click', function(e) {
                const onclick = this.getAttribute('onclick');

                // If it has confirm() in onclick, don't show loader immediately
                if (onclick && onclick.includes('confirm(')) {
                    return;
                }

                showLoader();
            });
        });

        // Hide loader on browser back/forward navigation
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                hideLoader();
            }
        });

        // Hide loader when user navigates back
        window.addEventListener('popstate', function() {
            hideLoader();
        });
    });

    function showLoader() {
        const loader = document.getElementById('pageLoader');
        if (loader) {
            loader.style.display = 'flex';
            loader.classList.remove('hidden');
        }
    }

    function hideLoader() {
        const loader = document.getElementById('pageLoader');
        if (loader) {
            loader.classList.add('hidden');
            setTimeout(() => {
                loader.style.display = 'none';
            }, 300);
        }
    }

    // Enhanced confirm function that handles loader properly
    window.confirmWithLoader = function(message, callback) {
        if (confirm(message)) {
            showLoader();
            if (callback) callback();
            return true;
        }
        return false;
    };

    // Mobile menu functionality
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const sidebar = document.querySelector('.sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        // Toggle mobile menu
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                sidebarOverlay.classList.toggle('active');
                document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
            });
        }

        // Close menu when overlay is clicked
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
            });
        }

        // Close menu when menu item is clicked (mobile)
        const menuLinks = document.querySelectorAll('.menu li a');
        menuLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });

        // Debug profile link click
        const profileLink = document.getElementById('profile-link');
        if (profileLink) {
            console.log('Profile link found:', profileLink.href);
            profileLink.addEventListener('click', function(e) {
                console.log('Profile link clicked');
                console.log('Href:', this.href);
                // e.preventDefault(); // Uncomment to prevent navigation for testing
            });
        } else {
            console.error('Profile link not found!');
        }

        // Prevent zoom on double tap for iOS - but only for non-interactive elements
        let lastTouchEnd = 0;
        document.addEventListener('touchend', function (event) {
            const now = (new Date()).getTime();
            const target = event.target;

            // Don't prevent default for interactive elements
            if (target.tagName === 'BUTTON' ||
                target.tagName === 'A' ||
                target.tagName === 'INPUT' ||
                target.tagName === 'SELECT' ||
                target.tagName === 'TEXTAREA' ||
                target.closest('button') ||
                target.closest('a') ||
                target.closest('.btn') ||
                target.closest('[role="button"]') ||
                target.closest('[onclick]')) {
                lastTouchEnd = now;
                return;
            }

            // Only prevent double-tap zoom on non-interactive elements
            if (now - lastTouchEnd <= 300) {
                event.preventDefault();
            }
            lastTouchEnd = now;
        }, false);
    });
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html> <?php /**PATH C:\laravel\PNPH-CAPSTONE\resources\views/layouts/student_layout.blade.php ENDPATH**/ ?>