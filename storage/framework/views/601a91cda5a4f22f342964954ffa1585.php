<?php $__env->startSection('content'); ?>
<div class="submission-container">
    <div class="submission-card">
        <div class="card-header-custom">
            <h2 style="color: #333;">Grade Submission</h2>
            <p style="color: #555;"><?php echo e($gradeSubmission->semester); ?> <?php echo e($gradeSubmission->term); ?> <?php echo e($gradeSubmission->academic_year); ?></p>
        </div>

        <div class="card-body-custom">
            <!-- Display validation errors -->
            <?php if($errors->any()): ?>
                <div class="alert-custom alert-danger-custom">
                    <ul>
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert-custom alert-danger-custom">
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            <?php if(session('success')): ?>
                <div class="alert-custom alert-success-custom">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php
                $proof = \App\Models\GradeSubmissionProof::where('grade_submission_id', $gradeSubmission->id)
                    ->where('user_id', Auth::user()->user_id)
                    ->first();
            ?>

            <?php if($gradeSubmission->status === 'rejected' || ($proof && $proof->status === 'rejected')): ?>
                <div class="rejection-notice">
                    <h3>Previous Submission Rejected</h3>
                    <p>Your previous submission was rejected. Please review and resubmit your grades and proof.</p>
                </div>
            <?php elseif(($proof && $proof->status === 'approved') || $gradeSubmission->status === 'approved'): ?>
                <div class="alert-custom alert-success-custom">
                    <h3>Grades Approved</h3>
                    <p>Your grades have been approved and cannot be modified.</p>
                </div>
                <?php
                    // If somehow we got here with approved status, we should redirect
                    return redirect()->route('student.dashboard');
                ?>
            <?php endif; ?>

            <form action="<?php echo e(route('student.submit-grades.store', $gradeSubmission->id)); ?>" method="POST" enctype="multipart/form-data" id="grade-submission-form">
                <?php echo csrf_field(); ?>

                <div class="grades-section">
                    <h3>Enter Grades</h3>
                    <?php if($subjects->isNotEmpty()): ?>
                        <table class="grades-table">
                            <thead>
                                <tr>
                                    <th class="subject-column">Subject</th>
                                    <th class="grade-column">Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($subject->name); ?></td>
                                        <td>
                                            <input type="text"
                                               name="grades[<?php echo e($subject->id); ?>]"
                                               value="<?php echo e($subject->grade ?? ''); ?>"
                                               class="grade-input <?php echo e($errors->has('grades.' . $subject->id) ? 'is-invalid' : ''); ?>"
                                               pattern="^(5(\.0)?|[1-4](\.[0-9]{1,2})?|INC|NC|DR)$"
                                               title="Please match requested format: 1.0-5.0 or INC, NC, DR"
                                               required>
                                        <?php $__errorArgs = ['grades.' . $subject->id];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="invalid-feedback">
                                                <?php echo e($message); ?>

                                            </div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert-custom alert-warning-custom">
                            No subjects found for this submission. You may still upload your proof and submit.
                        </div>
                    <?php endif; ?>
                </div>

                <div class="proof-section mt-4">
                    <h3>Upload Proof</h3>
                    <div class="form-group">
                        <label for="proof">Upload your proof document (PDF, DOC, DOCX, JPG, JPEG, PNG)</label>
                        <input type="file"
                               name="proof"
                               id="proof"
                               class="form-control file-input-mobile"
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,image/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                               capture="environment"
                               required>
                        <small class="form-text text-muted">Maximum file size: 10MB</small>
                        <div id="file-selected" class="file-feedback" style="display: none;">
                            <span class="file-name"></span>
                            <span class="file-size"></span>
                        </div>
                    </div>
                </div>

                <div class="form-actions mt-4">
                    <!-- SUBMIT BUTTON ALWAYS RENDERED -->
                    <button type="submit" class="btn-custom btn-primary-custom">
                        <?php echo e($proof && $proof->status === 'rejected' ? 'Resubmit Grades' : 'Submit Grades'); ?>

                    </button>
                    <a href="<?php echo e(route('student.grade-submissions.list')); ?>" class="btn-custom btn-secondary-custom">
                        <i class="fas fa-arrow-left" style="margin-right: 5px;"></i>
                        Back to Submissions
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .submission-container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 0 15px;
    }

    .submission-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        overflow: hidden;
    }

    .card-header-custom {
        background-color: var(--primary-color);
        color: #fff;
        padding: 15px 20px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }

    .card-header-custom h2 {
        margin: 0 0 5px 0;
        font-size: 1.5rem;
        font-weight: 600;
    }

    .card-body-custom {
        padding: 20px;
    }

    .alert-custom {
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .alert-danger-custom {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }

    .alert-success-custom {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }

    .alert-warning-custom {
        background-color: #fff3cd;
        border: 1px solid #ffeeba;
        color: #856404;
    }

    .rejection-notice {
        background-color: #fff3cd;
        border: 1px solid #ffeeba;
        color: #856404;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .rejection-notice h3 {
        margin-top: 0;
        margin-bottom: 10px;
        color: #856404;
    }

    .grades-section {
        margin-bottom: 30px;
    }

    .grades-section h3 {
        margin-top: 0;
        margin-bottom: 15px;
        color: var(--dark-text);
    }

    .grades-table {
        width: 80%;
        max-width: 600px;
        border-collapse: collapse;
        margin: 0 auto 20px auto;
        table-layout: fixed;
    }

    .grades-table th,
    .grades-table td {
        padding: 12px;
        border: 1px solid #ddd;
        vertical-align: middle;
    }

    .grades-table th {
        background-color: #22bbea;
        color: white;
        font-weight: 600;
        text-align: center;
    }

    .grades-table tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .grades-table tbody tr:hover {
        background-color: #f0f8ff;
    }

    /* Table column widths */
    .subject-column {
        width: 70%;
        text-align: left;
    }

    .grade-column {
        width: 30%;
        text-align: center;
        min-width: 100px;
    }

    .grade-input {
        width: 70px;
        min-width: 70px;
        max-width: 70px;
        padding: 8px 6px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
        text-align: center;
        font-weight: 500;
        margin: 0 auto;
        display: block;
    }

    .grade-input:focus {
        border-color: #22bbea;
        outline: none;
        box-shadow: 0 0 0 2px rgba(34, 187, 234, 0.2);
    }

    .form-text {
        display: block;
        margin-top: 5px;
        font-size: 0.875rem;
    }

    .text-muted {
        color: #6c757d;
    }

    .proof-section {
        margin-bottom: 30px;
    }

    .proof-section h3 {
        margin-top: 0;
        margin-bottom: 15px;
        color: var(--dark-text);
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        color: var(--dark-text);
    }

    .form-control {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
    }

    /* Mobile-friendly file input */
    .file-input-mobile {
        padding: 12px;
        font-size: 16px; /* Prevent zoom on iOS */
        border: 2px dashed #ddd;
        border-radius: 8px;
        background-color: #f9f9f9;
        cursor: pointer;
        touch-action: manipulation;
    }

    .file-input-mobile:focus {
        border-color: #22bbea;
        outline: none;
        box-shadow: 0 0 0 2px rgba(34, 187, 234, 0.2);
    }

    .file-feedback {
        margin-top: 10px;
        padding: 10px;
        background-color: #e8f5e8;
        border: 1px solid #4caf50;
        border-radius: 4px;
        color: #2e7d32;
    }

    .file-name {
        font-weight: bold;
        display: block;
    }

    .file-size {
        font-size: 0.9em;
        color: #666;
    }

    .form-actions {
        display: flex;
        gap: 10px;
    }

    .btn-custom {
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1rem;
        transition: background-color 0.3s ease;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }

    .btn-custom.btn-primary-custom {
        background-color: #007bff !important;
        color: #fff !important;
        border: 2px solid #0056b3 !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .btn-custom.btn-primary-custom:hover {
        background-color: #0056b3 !important;
    }
    .btn-custom.btn-secondary-custom {
        background-color: #6c757d !important;
        color: #fff !important;
        border: 2px solid #545b62 !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .btn-custom.btn-secondary-custom:hover {
        background-color: #545b62 !important;
    }

    /* Mobile responsive improvements */
    @media (max-width: 768px) {
        .submission-container {
            padding: 0 10px;
        }

        .grades-table {
            width: 90%;
            font-size: 0.9rem;
        }

        .grades-table th,
        .grades-table td {
            padding: 10px;
        }

        .subject-column {
            width: 65%;
        }

        .grade-column {
            width: 35%;
            min-width: 90px;
        }

        .grade-input {
            width: 70px;
            min-width: 70px;
            max-width: 70px;
            padding: 10px 6px;
            font-size: 16px; /* Prevent zoom on iOS */
        }

        .file-input-mobile {
            padding: 16px;
            font-size: 16px;
            min-height: 60px;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn-custom {
            width: 100%;
            margin-bottom: 10px;
        }
    }

    /* Extra small screens */
    @media (max-width: 480px) {
        .grades-table {
            width: 95%;
            font-size: 0.85rem;
        }

        .grades-table th,
        .grades-table td {
            padding: 8px;
        }

        .subject-column {
            width: 60%;
        }

        .grade-column {
            width: 40%;
            min-width: 80px;
        }

        .grade-input {
            width: 65px;
            min-width: 65px;
            max-width: 65px;
            padding: 8px 4px;
            font-size: 16px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('proof');
    const fileSelected = document.getElementById('file-selected');
    const fileName = fileSelected.querySelector('.file-name');
    const fileSize = fileSelected.querySelector('.file-size');
    const form = document.getElementById('grade-submission-form');

    // File input change handler
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];

        if (file) {
            console.log('File selected:', {
                name: file.name,
                size: file.size,
                type: file.type
            });

            // Show file feedback
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            fileSelected.style.display = 'block';

            // Validate file size (10MB = 10485760 bytes)
            if (file.size > 10485760) {
                alert('File size must be less than 10MB. Please choose a smaller file.');
                fileInput.value = '';
                fileSelected.style.display = 'none';
                return;
            }

            // Validate file type
            const allowedTypes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'image/jpeg',
                'image/jpg',
                'image/png'
            ];

            const allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
            const fileExtension = file.name.split('.').pop().toLowerCase();

            if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(fileExtension)) {
                alert('Please select a valid file type: PDF, DOC, DOCX, JPG, JPEG, or PNG');
                fileInput.value = '';
                fileSelected.style.display = 'none';
                return;
            }

            console.log('File validation passed');
        } else {
            fileSelected.style.display = 'none';
        }
    });

    // Form submit handler
    form.addEventListener('submit', function(e) {
        const file = fileInput.files[0];

        if (!file) {
            e.preventDefault();
            alert('Please select a proof file before submitting.');
            return false;
        }

        console.log('Form submitting with file:', file.name);

        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Uploading...';
        }
    });

    // Format file size for display
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    console.log('File upload JavaScript initialized');
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.student_layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laravel\PNPH-CAPSTONE\resources\views/student/submission_form.blade.php ENDPATH**/ ?>