<?php $__env->startSection('content'); ?>

<link rel="stylesheet" href="<?php echo e(asset('css/training/school.css')); ?>">

<h1 style="font-weight: 300;">Schools</h1>
<hr>
<div class="page-container">
    <div class="header-section">
        <a href="<?php echo e(route('training.schools.create')); ?>" class="btn btn-primary">
            Add New School
        </a>
    </div>

    <div class="table-wrapper">
        <div class="table-header">
            <div class="header-cell">ID</div>
            <div class="header-cell">School</div>
            <div class="header-cell">Department</div>
            <div class="header-cell">Course</div>
            <div class="header-cell">Actions</div>
        </div>
        
        <?php $__empty_1 = true; $__currentLoopData = $schools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $school): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="table-row">
                <?php if(is_object($school)): ?>
                    <div class="cell"><?php echo e($school->school_id); ?></div>
                    <div class="cell"><?php echo e($school->name); ?></div>
                    <div class="cell"><?php echo e($school->department); ?></div>
                    <div class="cell"><?php echo e($school->course); ?></div>
                    <div class="cell">
                        <div class="action-buttons">
                            <a href="<?php echo e(route('training.schools.show', $school)); ?>" class="btn-icon" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo e(route('training.schools.edit', $school)); ?>" class="btn-icon" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?php echo e(route('training.schools.destroy', $school)); ?>" method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this school?');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn-icon" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="cell" colspan="5">Invalid school data</div>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="table-row">
                <div class="cell empty-message">No schools found</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laravel\PNPH-CAPSTONE\resources\views/training/manage-students.blade.php ENDPATH**/ ?>