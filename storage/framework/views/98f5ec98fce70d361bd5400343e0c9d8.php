<?php $__env->startSection('content'); ?>
<div class="dashboard-container">
    <h1>My Grade Submissions</h1>
    
    <?php if(session('error')): ?>
        <div class="alert alert-danger">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('success')): ?>
        <div class="alert alert-success">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if($gradeSubmissions->isEmpty()): ?>
        <div class="no-submissions">
            <p>No grade submissions found.</p>
        </div>
    <?php else: ?>
        <div class="submissions-grid">
            <?php $__currentLoopData = $gradeSubmissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $submission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="submission-card">
                    <div class="card-header">
                        <h3><?php echo e($submission->term ?? 'N/A'); ?></h3>
                    </div>
                    <div class="card-content">
                        <div class="info-row">
                            <span class="label">Semester:</span>
                            <span class="value"><?php echo e($submission->semester ?? 'N/A'); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="label">Academic Year:</span>
                            <span class="value"><?php echo e($submission->academic_year ?? 'N/A'); ?></span>
                        </div>

                        <?php
                            // Get the student's pivot data
                            $studentPivot = $submission->students->where('pivot.user_id', Auth::id())->first();
                            $pivotStatus = $studentPivot ? ($studentPivot->pivot->status ?? 'pending') : 'pending';
                            
                            // Get the latest proof status
                            $proof = $submission->proofs->where('user_id', Auth::id())->sortByDesc('created_at')->first();
                            $proofStatus = $proof ? $proof->status : null;
                            
                            // Determine the overall status to display
                            $overallStatus = $pivotStatus; // Default to pivot status
                            
                            // If there's a proof with a more specific status, use that
                            if ($proofStatus && in_array($proofStatus, ['approved', 'rejected'])) {
                                $overallStatus = $proofStatus;
                            } elseif ($proofStatus === 'pending' && $pivotStatus === 'submitted') {
                                $overallStatus = 'pending_review';
                            }
                        ?>
                        
                        <div class="info-row">
                            <span class="label">Status:</span>
                            <span class="status <?php echo e($overallStatus); ?>">
                                <?php if($overallStatus === 'pending_review'): ?>
                                    Pending Review
                                <?php else: ?>
                                    <?php echo e(ucfirst($overallStatus)); ?>

                                <?php endif; ?>
                            </span>
                        </div>
                        
                        <div class="info-row">
                            <span class="label">Submitted:</span>
                            <span class="date"><?php echo e($submission->created_at ? $submission->created_at->format('M d, Y') : 'N/A'); ?></span>
                        </div>

                        
                        <div class="card-actions">
                            <?php
                                // Check if the student has any grades submitted for this submission
                                $hasGrades = $submission->students->contains('pivot.user_id', Auth::id()) && 
                                          $submission->students->where('pivot.user_id', Auth::id())->first()->pivot->grade !== null;
                                
                                // Check if there's a proof submitted
                                $hasProof = $proof !== null;
                            ?>
                            
                            <?php if(!$hasGrades && !$hasProof): ?>
                                
                                <a href="<?php echo e(route('student.submit-grades.show', $submission->id)); ?>" class="btn-submit-grades">Submit Grades</a>
                            <?php elseif($overallStatus === 'rejected' || $pivotStatus === 'rejected' || $proofStatus === 'rejected'): ?>
                                <a href="<?php echo e(route('student.submit-grades.show', $submission->id)); ?>" class="btn-submit-grades">Resubmit Grades</a>
                            <?php elseif(in_array($overallStatus, ['submitted', 'pending_review', 'pending'])): ?>
                                <a href="<?php echo e(route('student.view-submission', $submission->id)); ?>" class="btn-view-submission">View Submission</a>
                            <?php elseif($overallStatus === 'approved'): ?>
                                <a href="<?php echo e(route('student.view-submission', $submission->id)); ?>" class="btn-view-submission">View Approved Submission</a>
                            <?php else: ?>
                                <a href="<?php echo e(route('student.submit-grades.show', $submission->id)); ?>" class="btn-submit-grades">Submit Grades</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>

<style>
.dashboard-container {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

h1 {
    color: #333;
    margin-bottom: 20px;
    font-size: 24px;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: .25rem;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.submissions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.submission-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.card-header {
    background: #f8f9fa;
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.card-header h3 {
    margin: 0;
    color: #333;
    font-size: 18px;
}

.card-content {
    padding: 15px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.info-row:last-child {
    margin-bottom: 0;
}

.label {
    color: #666;
    font-weight: 500;
}

.value,
.date {
    color: #333;
}

.status {
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 14px;
    font-weight: 500;
}

.status.pending {
    background: #fff3cd;
    color: #856404;
}

.status.approved {
    background: #d4edda;
    color: #155724;
}

.status.rejected {
    background: #f8d7da;
    color: #721c24;
}

.status.submitted {
     background-color: #cce5ff;
     color: #004085;
}

.no-submissions {
    text-align: center;
    padding: 40px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.no-submissions p {
    color: #6c757d;
    font-size: 1.1em;
    margin: 0;
}

@media (max-width: 768px) {
    .submissions-grid {
        grid-template-columns: 1fr;
    }
}

/* Styles for the subjects list (removed as subjects are not listed directly on the card anymore) */
/*
.card-content h4 {
    margin-top: 15px;
    margin-bottom: 5px;
    color: #555;
    font-size: 16px;
}

.card-content ul {
    list-style: disc inside;
    padding-left: 0;
    margin-bottom: 10px;
}

.card-content ul li {
    margin-bottom: 3px;
    color: #666;
}
*/

.card-actions {
    margin-top: auto; /* Push actions to the bottom */
    padding-top: 15px; /* Add some space above the button */
    border-top: 1px solid #eee; /* Optional: Add a separator */
    text-align: right; /* Align button to the right */
}

.btn-submit-grades,
.btn-view-submission {
    display: inline-block;
    color: white;
    padding: 8px 16px;
    border-radius: 5px;
    text-decoration: none; /* Remove underline */
    font-size: 14px;
    transition: background-color 0.3s ease;
}

.btn-submit-grades {
    background-color: #007bff; /* Primary blue color */
}

.btn-view-submission {
    background-color: #6c757d; /* Secondary gray color */
}

.btn-submit-grades:hover {
    background-color: #0056b3;
}

.btn-view-submission:hover {
    background-color: #5a6268;
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.student_layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laravel\PNPH-CAPSTONE\resources\views/student/dashboard.blade.php ENDPATH**/ ?>