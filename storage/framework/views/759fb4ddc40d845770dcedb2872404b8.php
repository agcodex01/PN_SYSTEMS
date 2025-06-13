<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo e(asset('css/login.css')); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <title>Login</title>

    <style>
        /* Force Poppins font for all elements */
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
    </style>
</head>
<body>
    <!-- Theme Loader -->
    <div class="loader-overlay" id="pageLoader">
        <div class="loader"></div>
    </div>
    <!-- Back Button -->
    <div class="back-button-container">
        <a href="<?php echo e(route('main-menu')); ?>" class="back-button">
            <i class="fas fa-arrow-left"></i>
            <span class="back-text">Back to Main Menu</span>
        </a>
    </div>

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

    <script>
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

        // Show loader when form is submitted or links are clicked
        document.addEventListener('DOMContentLoaded', function() {
            // Show loader on form submission
            document.querySelector('form').addEventListener('submit', function(e) {
                if (this.checkValidity()) {
                    showLoader();
                }
            });

            // Show loader on navigation links
            document.querySelectorAll('a:not([href^="#"]):not([href^="javascript:"]):not([target="_blank"]):not([href^="mailto:"]):not([href^="tel:"])').forEach(link => {
                link.addEventListener('click', function() {
                    showLoader();
                });
            });

            // Hide loader on browser back/forward navigation
            window.addEventListener('pageshow', function(event) {
                if (event.persisted) {
                    hideLoader();
                }
            });

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
    </script>
</body>
</html><?php /**PATH C:\laravel\PNPH-CAPSTONE\resources\views/login.blade.php ENDPATH**/ ?>