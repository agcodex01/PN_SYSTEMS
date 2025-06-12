<?php $__env->startSection('content'); ?>
<div class="dashboard-container">
    <h1>My Profile</h1>
    
    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <h2><?php echo e($user->user_fname); ?> <?php echo e($user->user_mInitial ? $user->user_mInitial . '.' : ''); ?> <?php echo e($user->user_lname); ?> <?php echo e($user->user_suffix ?? ''); ?></h2>
                <span class="profile-role"><?php echo e(ucfirst($user->user_role)); ?></span>
            </div>
            
            <div class="profile-details">
                <div class="detail-row">
                    <span class="detail-label">Student ID:</span>
                    <span class="detail-value"><?php echo e($user->user_id); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value"><?php echo e($user->user_email); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="status-badge <?php echo e(strtolower($user->status)); ?>">
                        <?php echo e(ucfirst($user->status)); ?>

                    </span>
                </div>

                <?php if(method_exists($user, 'classes') && $user->classes && $user->classes->isNotEmpty()): ?>
                    <div class="detail-row">
                        <span class="detail-label">Class:</span>
                        <span class="detail-value">
                            <?php $__currentLoopData = $user->classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo e($class->class_name); ?>

                                <?php if(!$loop->last): ?>, <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </span>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<style>
.profile-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.profile-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.profile-header {
    background: #4a6cf7;
    color: white;
    padding: 30px 20px;
    text-align: center;
    position: relative;
}

.profile-avatar {
    width: 100px;
    height: 100px;
    margin: 0 auto 15px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 60px;
    color: #4a6cf7;
    border: 4px solid white;
}

.profile-header h2 {
    margin: 10px 0 5px;
    font-size: 24px;
}

.profile-role {
    display: inline-block;
    background: rgba(255, 255, 255, 0.2);
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
}

.profile-details {
    padding: 30px;
}

.detail-row {
    display: flex;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.detail-row:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.detail-label {
    font-weight: 600;
    color: #555;
    width: 150px;
    flex-shrink: 0;
}

.detail-value {
    color: #333;
    flex-grow: 1;
}

.status-badge {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: capitalize;
}

.status-badge.active {
    background: #d4edda;
    color: #155724;
}

.status-badge.inactive {
    background: #f8d7da;
    color: #721c24;
}

.profile-actions {
    display: flex;
    justify-content: center;
    padding: 20px;
    background: #f9f9f9;
    border-top: 1px solid #eee;
    gap: 15px;
}

.btn {
    display: inline-flex;
    align-items: center;
    padding: 8px 20px;
    border-radius: 5px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn i {
    margin-right: 8px;
}

.btn-primary {
    background: #4a6cf7;
    color: white;
    border: 1px solid #4a6cf7;
}

.btn-primary:hover {
    background: #3a5ce4;
    border-color: #3a5ce4;
}

.btn-secondary {
    background: #6c757d;
    color: white;
    border: 1px solid #6c757d;
}

.btn-secondary:hover {
    background: #5a6268;
    border-color: #5a6268;
}

/* Mobile Responsive Styles */
@media (max-width: 768px) {
    .dashboard-container {
        padding: 15px;
    }

    .profile-container {
        padding: 10px;
        max-width: 100%;
    }

    .profile-card {
        border-radius: 8px;
    }

    .profile-header {
        padding: 20px 15px;
    }

    .profile-avatar {
        width: 80px;
        height: 80px;
        font-size: 48px;
        margin-bottom: 12px;
    }

    .profile-header h2 {
        font-size: 20px;
        margin: 8px 0 4px;
    }

    .profile-role {
        font-size: 13px;
        padding: 4px 12px;
    }

    .profile-details {
        padding: 20px 15px;
    }

    .detail-row {
        flex-direction: column;
        gap: 5px;
        margin-bottom: 12px;
        padding-bottom: 12px;
    }

    .detail-label {
        width: 100%;
        font-size: 14px;
    }

    .detail-value {
        font-size: 14px;
    }

    .status-badge {
        font-size: 11px;
        padding: 3px 8px;
    }
}

@media (max-width: 480px) {
    .dashboard-container {
        padding: 10px;
    }

    .profile-container {
        padding: 5px;
    }

    .profile-header {
        padding: 15px 10px;
    }

    .profile-avatar {
        width: 70px;
        height: 70px;
        font-size: 40px;
        margin-bottom: 10px;
    }

    .profile-header h2 {
        font-size: 18px;
        margin: 6px 0 3px;
    }

    .profile-role {
        font-size: 12px;
        padding: 3px 10px;
    }

    .profile-details {
        padding: 15px 10px;
    }

    .detail-row {
        margin-bottom: 10px;
        padding-bottom: 10px;
    }

    .detail-label {
        font-size: 13px;
    }

    .detail-value {
        font-size: 13px;
    }

    .status-badge {
        font-size: 10px;
        padding: 2px 6px;
    }
}

/* Ensure proper spacing and alignment */
.dashboard-container h1 {
    margin-bottom: 20px;
    font-size: 24px;
}

@media (max-width: 768px) {
    .dashboard-container h1 {
        margin-bottom: 15px;
        font-size: 20px;
    }
}

@media (max-width: 480px) {
    .dashboard-container h1 {
        margin-bottom: 12px;
        font-size: 18px;
    }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.student_layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laravel\PNPH-CAPSTONE\resources\views/student/profile.blade.php ENDPATH**/ ?>