<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/admin/show.css')); ?>">


<div class="user-details-container">
    <h1 class="text-center">User Details</h1>
    <div class="user-details-card">
        <p><strong>User ID:</strong> &nbsp <?php echo e($user->user_id); ?></p>
        <p><strong>First Name:</strong>&nbsp  <?php echo e($user->user_fname); ?></p>
        <p><strong>Last Name:</strong> &nbsp  <?php echo e($user->user_lname); ?></p>
        <p><strong>Email:</strong> &nbsp <?php echo e($user->user_email); ?></p>
        <p><strong>Role:</strong> &nbsp <?php echo e($user->user_role); ?></p>
        <p><strong>Status:</strong> &nbsp 
            <span class="<?php echo e($user->status === 'active' ? 'status-active' : 'status-inactive'); ?>">
                <?php echo e($user->status === 'active' ? 'Activated' : 'Deactivated'); ?>

            </span>
        </p>
        <div class="action-buttons">
            <a href="<?php echo e(route('admin.pnph_users.index')); ?>" class="btn btn-primary">Back to User List</a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin_layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laravel\PNPH-CAPSTONE\resources\views/admin/pnph_users/show.blade.php ENDPATH**/ ?>