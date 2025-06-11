<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/training/view-student.css')); ?>">

<div class="view-student-container">
    <h1>Student Details</h1>

    <div class="student-details">
        <div class="detail-row">
            <span class="detail-label">User ID:</span>
            <span class="detail-value"><?php echo e($student->user_id); ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Student ID:</span>
            <span class="detail-value"><?php echo e($student->studentDetail->student_id ?? 'N/A'); ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Last Name:</span>
            <span class="detail-value"><?php echo e($student->user_lname); ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">First Name:</span>
            <span class="detail-value"><?php echo e($student->user_fname); ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Middle Initial:</span>
            <span class="detail-value"><?php echo e($student->user_mInitial); ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Suffix:</span>
            <span class="detail-value"><?php echo e($student->user_suffix); ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Batch:</span>
            <span class="detail-value"><?php echo e($student->studentDetail->batch ?? 'N/A'); ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Gender:</span>
            <span class="detail-value"><?php echo e($student->studentDetail->gender ?? 'N/A'); ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Group:</span>
            <span class="detail-value"><?php echo e($student->studentDetail->group ?? 'N/A'); ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Student Number:</span>
            <span class="detail-value"><?php echo e($student->studentDetail->student_number ?? 'N/A'); ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Training Code:</span>
            <span class="detail-value"><?php echo e($student->studentDetail->training_code ?? 'N/A'); ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Email:</span>
            <span class="detail-value"><?php echo e($student->user_email); ?></span>
        </div>
    </div>

    <div class="action-buttons">
        <a href="<?php echo e(route('educator.students.index')); ?>" class="btn btn-secondary">Back to List</a>
    </div>
</div>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.educator_layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laravel\PNPH-CAPSTONE\resources\views/educator/view-student.blade.php ENDPATH**/ ?>