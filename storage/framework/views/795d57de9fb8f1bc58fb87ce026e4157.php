<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/admin/edit.css')); ?>">

<div class="edit-user-container">
    <h2 class="page-title">
        Edit User:
        <span class="highlight"><?php echo e($user->user_fname); ?> <?php echo e($user->user_lname); ?></span>
    </h2>

    <form action="<?php echo e(route('admin.pnph_users.update', $user->user_id)); ?>" method="POST" class="edit-form">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="form-row">
            <label>User ID</label>
            <input type="text" value="<?php echo e($user->user_id); ?>" disabled>
            <small class="note">This field cannot be changed.</small>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="user_fname" value="<?php echo e($user->user_fname); ?>" required>
            </div>

            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="user_lname" value="<?php echo e($user->user_lname); ?>" required>
            </div>

            <div class="form-group">
                <label>Middle Initial</label>
                <input type="text" name="user_mInitial" value="<?php echo e($user->user_mInitial); ?>">
            </div>

            <div class="form-group">
                <label>Suffix</label>
                <input type="text" name="user_suffix" value="<?php echo e($user->user_suffix); ?>">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="user_email" value="<?php echo e($user->user_email); ?>" required>
            </div>

            <div class="form-group">
                <label>Role</label>
                <input type="text" name="user_role" value="<?php echo e($user->user_role); ?>" required>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" required>
                    <option value="active" <?php echo e($user->status === 'active' ? 'selected' : ''); ?>>Activate</option>
                    <option value="inactive" <?php echo e($user->status === 'inactive' ? 'selected' : ''); ?>>Deactivate</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update User</button>
            <a href="<?php echo e(route('admin.pnph_users.index')); ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin_layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laravel\PNPH-CAPSTONE\resources\views/admin/pnph_users/edit.blade.php ENDPATH**/ ?>