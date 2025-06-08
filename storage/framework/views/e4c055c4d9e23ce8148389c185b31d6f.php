<?php $__env->startSection('content'); ?>
<div class="monitor-container">
    <div class="monitor-card">
        <div class="card-header-custom">
            <h2>Grade Submission Monitor</h2>
            
        </div>

        <div class="card-body-custom">
            <?php if(isset($message)): ?>
                <div class="alert-custom alert-warning-custom">
                    <?php echo e($message); ?>

                </div>
            <?php endif; ?>

            
            <div class="filter-section">
                 <h3>Filter Submissions</h3>
                 <form action="<?php echo e(route('training.grade-submissions.index')); ?>" method="GET" class="filter-form-custom">
                    <div class="form-group-custom filter-group">
                        <label for="school_id" class="visually-hidden">School</label>
                        <select name="school_id" id="school_id" class="form-control-custom" onchange="this.form.submit()">
                            <option value="">All Schools</option>
                            <?php $__currentLoopData = $schools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $school): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($school->school_id); ?>" <?php echo e(request('school_id') == $school->school_id ? 'selected' : ''); ?>>
                                    <?php echo e($school->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    
                    <div class="form-group-custom filter-group">
                        <label for="class_id" class="visually-hidden">Class</label>
                        <select name="class_id" id="class_id" class="form-control-custom" onchange="this.form.submit()">
                            <option value="">All Classes</option>
                            <?php if(request('school_id')): ?>
                                <?php $__currentLoopData = $classesBySchool[request('school_id')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($class->class_id); ?>" <?php echo e(request('class_id') == $class->class_id ? 'selected' : ''); ?>>
                                        <?php echo e($class->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                     <div class="form-group-custom filter-group">
                         <label for="filter_key" class="visually-hidden">Semester Term Academic Year</label>
                         <select name="filter_key" id="filter_key" class="form-control-custom">
                             <option value="">All Submissions</option>
                             <?php $__currentLoopData = $filterOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                 <option value="<?php echo e($option); ?>" <?php echo e(request('filter_key') == $option ? 'selected' : ''); ?>><?php echo e($option); ?></option>
                             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                         </select>
                     </div>
                     <div class="filter-buttons">
                         <button type="submit" class="btn-custom btn-primary-custom">
                             <i class="fas fa-filter"></i> Filter
                         </button>
                         <button type="button" onclick="location.href='<?php echo e(route('training.grade-submissions.index')); ?>'" class="btn-custom btn-secondary-custom">
                             <i class="fas fa-undo"></i> Reset
                         </button>
                     </div>
                 </form>
            </div>
        </div>
    </div>

    <?php $__currentLoopData = $schools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $school): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $schoolSubmissions = $submissionsBySchool[$school->school_id] ?? collect(); ?>
        <?php if($schoolSubmissions->isNotEmpty()): ?>
            <div class="school-container">
                <div class="school-header">
                    <h3><?php echo e($school->name); ?></h3>
                </div>
                <div class="school-content">
                    <?php $__currentLoopData = $schoolSubmissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gradeSubmission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            // Fetch students for this submission
                            $students = \DB::table('grade_submission_subject')
                                ->join('pnph_users', 'grade_submission_subject.user_id', '=', 'pnph_users.user_id')
                                ->join('student_details', 'pnph_users.user_id', '=', 'student_details.user_id')
                                ->where('grade_submission_subject.grade_submission_id', $gradeSubmission->id)
                                ->where('pnph_users.user_role', 'student')
                                ->select('pnph_users.user_id', 'pnph_users.user_fname', 'pnph_users.user_lname', 'student_details.student_id')
                                ->distinct()
                                ->get()
                                ->map(function ($student) {
                                    return (object)[
                                        'student_id' => $student->student_id,
                                        'user_id' => $student->user_id,
                                        'name' => $student->user_fname . ' ' . $student->user_lname
                                    ];
                                });
                            // Fetch subjects for this submission
                            $subjects = \DB::table('grade_submission_subject')
                                ->join('subjects', 'grade_submission_subject.subject_id', '=', 'subjects.id')
                                ->where('grade_submission_subject.grade_submission_id', $gradeSubmission->id)
                                ->select('subjects.*')
                                ->distinct()
                                ->get();
                            // Fetch grades for this submission
                            $rawGrades = \DB::table('grade_submission_subject')
                                ->join('subjects', 'grade_submission_subject.subject_id', '=', 'subjects.id')
                                ->where('grade_submission_subject.grade_submission_id', $gradeSubmission->id)
                                ->select(
                                    'grade_submission_subject.user_id',
                                    'grade_submission_subject.subject_id',
                                    'grade_submission_subject.grade',
                                    'grade_submission_subject.status',
                                    'subjects.name as subject_name'
                                )
                                ->get();
                            $grades = [];
                            foreach ($rawGrades as $grade) {
                                if (!isset($grades[$grade->user_id])) {
                                    $grades[$grade->user_id] = [];
                                }
                                $grades[$grade->user_id][$grade->subject_id] = (object)[
                                    'grade' => $grade->grade,
                                    'status' => $grade->status,
                                    'subject_name' => $grade->subject_name
                                ];
                            }
                        ?>
                        <div class="submission-section">
                            <div class="submission-header">
                                <h4><?php echo e($gradeSubmission->semester); ?> <?php echo e($gradeSubmission->term); ?> <?php echo e($gradeSubmission->academic_year); ?></h4>
                            </div>
                            <div class="table-responsive-custom">
                                <table class="grade-monitor-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center-custom" style="width: 80px">Student ID</th>
                                            <th style="width: 180px">Name</th>
                                            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <th class="text-center-custom"><?php echo e($subject->name); ?></th>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <th class="text-center-custom" style="width: 120px">Proof</th>
                                            <th class="text-center-custom" style="width: 120px">Status</th>
                                            <th class="text-center-custom" style="width: 120px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr data-submission-id="<?php echo e($gradeSubmission->id); ?>" data-student-id="<?php echo e($student->user_id); ?>">
                                                <td class="text-center-custom small-text"><?php echo e($student->student_id); ?></td>
                                                <td class="small-text"><?php echo e($student->name); ?></td>
                                                <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <td class="text-center-custom">
                                                        <?php
                                                            $grade = $grades[$student->user_id][$subject->id] ?? null;
                                                            $gradeValue = $grade ? $grade->grade : null;
                                                        ?>
                                                        
                                                        <?php if($gradeValue !== null): ?>
                                                            <div class="grade-value small-text">
                                                                <?php if(in_array(strtoupper($gradeValue), ['INC', 'NC', 'DR'])): ?>
                                                                    <?php echo e(strtoupper($gradeValue)); ?>

                                                                <?php else: ?>
                                                                    <?php echo e(number_format((float)$gradeValue, 1)); ?>

                                                                <?php endif; ?>
                                                            </div>
                                                        <?php else: ?>
                                                            <span class="text-muted-custom small-text">Not submitted</span>
                                                        <?php endif; ?>
                                                    </td>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <td class="text-center-custom">
                                                    <?php
                                                        $proof = \App\Models\GradeSubmissionProof::where('grade_submission_id', $gradeSubmission->id)
                                                            ->where('user_id', $student->user_id)
                                                            ->first();
                                                    ?>
                                                    <?php if($proof): ?>
                                                        <a href="<?php echo e(route('training.grade-submissions.view-proof', ['gradeSubmission' => $gradeSubmission->id, 'student' => $student->user_id])); ?>"
                                                           class="btn-custom btn-primary-custom">
                                                            <i class="fas fa-eye"></i> View Proof
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted-custom small-text">No proof</span>
                                                    <?php endif; ?>
                                                 </td>
                                                 <td class="text-center-custom">
                                                     <?php
                                                         // Check if student has uploaded all grades
                                                         $hasAllGrades = true;
                                                         foreach($subjects as $subject) {
                                                             $grade = $grades[$student->user_id][$subject->id] ?? null;
                                                             $gradeValue = $grade ? $grade->grade : null;
                                                             if ($gradeValue === null) {
                                                                 $hasAllGrades = false;
                                                                 break;
                                                             }
                                                         }
                                                         
                                                         // Only check for approval status if all grades are uploaded
                                                         if ($hasAllGrades) {
                                                             $status = DB::table('grade_submission_subject')
                                                                 ->where('grade_submission_subject.grade_submission_id', $gradeSubmission->id)
                                                                 ->where('grade_submission_subject.user_id', $student->user_id)
                                                                 ->value('status') ?? 'pending';
                                                         } else {
                                                             $status = 'pending';
                                                         }
                                                         
                                                         // Check if proof exists
                                                         $proof = \App\Models\GradeSubmissionProof::where('grade_submission_id', $gradeSubmission->id)
                                                             ->where('user_id', $student->user_id)
                                                             ->first();
                                                     ?>
                                                     <span class="status-badge <?php echo e($status === 'approved' ? 'approved' : ($status === 'rejected' ? 'rejected' : 'pending')); ?>">
                                                         <?php echo e($hasAllGrades ? ucfirst($status) : 'Pending Grades'); ?>

                                                     </span>
                                                 </td>
                                                 <td class="text-center-custom">
                                                     <div class="action-buttons">
                                                         <?php
                                                             $hasIncGrade = false;
                                                             foreach($subjects as $subject) {
                                                                 $grade = $grades[$student->user_id][$subject->id] ?? null;
                                                                 $gradeValue = $grade ? $grade->grade : null;
                                                                 if(strtoupper($gradeValue) === 'INC') {
                                                                     $hasIncGrade = true;
                                                                     break;
                                                                 }
                                                             }
                                                         ?>
                                                         
                                                         <?php if($proof && $proof->status === 'pending'): ?>
                                                             <div class="action-group">
                                                                 <form method="POST" action="<?php echo e(route('training.grade-submissions.update-proof-status', ['gradeSubmission' => $gradeSubmission->id, 'student' => $student->user_id])); ?>" class="d-inline">
                                                                     <?php echo csrf_field(); ?>
                                                                     <input type="hidden" name="status" value="approved">
                                                                     <button type="submit" class="action-button btn-success-custom">
                                                                         <i class="fas fa-check-circle"></i> Approve
                                                                     </button>
                                                                 </form>
                                                                 <form method="POST" action="<?php echo e(route('training.grade-submissions.update-proof-status', ['gradeSubmission' => $gradeSubmission->id, 'student' => $student->user_id])); ?>" class="d-inline">
                                                                     <?php echo csrf_field(); ?>
                                                                     <input type="hidden" name="status" value="rejected">
                                                                     <button type="submit" class="action-button btn-danger-custom">
                                                                         <i class="fas fa-times-circle"></i> Reject
                                                                     </button>
                                                                 </form>
                                                             </div>
                                                         <?php else: ?>
                                                             <?php if($hasIncGrade): ?>
                                                                 <form method="POST" action="<?php echo e(route('training.grade-submissions.update-proof-status', ['gradeSubmission' => $gradeSubmission->id, 'student' => $student->user_id])); ?>" class="d-inline">
                                                                     <?php echo csrf_field(); ?>
                                                                     <input type="hidden" name="status" value="pending">
                                                                      <button type="submit" class="action-button btn-warning-custom">
                                                                          <i class="fas fa-edit"></i> Edit Status
                                                                      </button>
                                                                 </form>
                                                             <?php else: ?>
                                                                 <span class="text-muted-custom small-text">
                                                                     Status is final and cannot be changed
                                                                 </span>
                                                             <?php endif; ?>
                                                         <?php endif; ?>
                                                     </div>
                                                 </td>
                                             </tr>
                                         <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                     </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php if(isset($submissions) && $submissions->hasPages()): ?>
    <div class="pagination-container">
        <div class="pagination-info">
            Showing <?php echo e($submissions->firstItem()); ?> to <?php echo e($submissions->lastItem()); ?> of <?php echo e($submissions->total()); ?> entries
        </div>
        <div class="pagination-buttons">
            <?php if($submissions->onFirstPage()): ?>
                <span class="pagination-button disabled">
                    <i class="fas fa-chevron-left"></i> Previous
                </span>
            <?php else: ?>
                <a href="<?php echo e($submissions->previousPageUrl()); ?>" class="pagination-button">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>
            <?php endif; ?>

            <div class="page-info">
                Page <?php echo e($submissions->currentPage()); ?> of <?php echo e($submissions->lastPage()); ?>

            </div>

            <?php if($submissions->hasMorePages()): ?>
                <a href="<?php echo e($submissions->nextPageUrl()); ?>" class="pagination-button">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
            <?php else: ?>
                <span class="pagination-button disabled">
                    Next <i class="fas fa-chevron-right"></i>
                </span>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
    :root {
        --primary-color: #22bbea;
        --secondary-color: #ff9933;
        --success-color: #28a745;
        --danger-color: #dc3545;
        --warning-color: #ffc107;
        --info-color: #17a2b8;
        --light-bg: #f8f9fa;
        --dark-text: #343a40;
        --border-color: #dee2e6;
        --card-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        --error-color: #dc3545;
    }

    body {
        font-family: 'Arial', sans-serif; /* Using a common sans-serif font */
        line-height: 1.6;
        margin: 0;
        padding: 0;
        background-color: var(--light-bg);
        color: var(--dark-text);
    }

    .monitor-container {
        max-width: 1200px; /* Wider container for the monitor table */
        margin: 20px auto;
        padding: 0 15px;
    }

    .monitor-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
        margin-bottom: 20px;
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
     .submission-id-small {
        margin: 0;
        font-size: 0.9rem;
        opacity: 0.9;
     }

    .card-body-custom {
        padding: 20px;
    }

    .alert-custom {
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }

     .alert-warning-custom {
        background-color: #fff3cd;
        border: 1px solid #ffc107;
        color: #856404;
    }

    .filter-section {
        margin-bottom: 20px;
        padding: 15px;
        background-color: var(--light-bg);
        border-radius: 5px;
    }
     .filter-section h3 {
        margin-top: 0;
        margin-bottom: 10px;
        font-size: 1.25rem;
        color: var(--dark-text);
     }

    .filter-form-custom {
        display: flex;
        align-items: center;
        gap: 15px; /* Space between form elements */
        flex-wrap: wrap; /* Allow items to wrap on smaller screens */
    }

    .form-group-custom.filter-group {
        margin-bottom: 0; /* Remove margin from form group in flex container */
        flex-grow: 1; /* Allow the select to grow */
        max-width: 300px; /* Limit width for better layout */
    }

     .form-control-custom {
        width: 100%; /* Make select fill its container */
        padding: 8px 10px;
        border: 1px solid var(--border-color);
        border-radius: 5px;
        font-size: 1rem;
        box-sizing: border-box;
     }
     .form-control-custom:focus {
         border-color: var(--primary-color);
         outline: none;
         box-shadow: 0 0 5px rgba(0, 123, 255, 0.25);
     }

    .visually-hidden {
        position: absolute;
        width: 1px;
        height: 1px;
        margin: -1px;
        padding: 0;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        border: 0;
    }

    .action-buttons-container {
        display: flex;
        gap: 10px;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        padding: 8px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
    }

    .btn-custom {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-width: 100px;
        height: 32px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .btn-custom i {
        font-size: 14px;
    }

    .btn-primary-custom {
        background-color: var(--primary-color);
        color: white;
    }

    .btn-secondary-custom {
        background-color: var(--secondary-color);
        color: white;
    }

    .btn-success-custom {
        background-color: var(--success-color);
        color: white;
    }

    .btn-danger-custom {
        background-color: #dc3545;
        color: white;
    }

    .btn-warning-custom {
        background-color: #ffc107;
        color: #000;
    }

    .filter-buttons {
        display: flex;
        gap: 4px;
        margin-top: 8px;
        justify-content: flex-end;
        align-items: center;
    }

    .action-buttons {
        display: flex;
        gap: 4px;
        justify-content: center;
        align-items: center;
    }

    .action-group {
        display: flex;
        gap: 4px;
        align-items: center;
    }

    /* Hover effects for table rows */
    tr[data-submission-id] {
        transition: background-color 0.2s ease;
    }

    tr[data-submission-id]:hover {
        background-color: rgba(34, 187, 234, 0.05);
    }

    tr[data-submission-id]:hover .btn-custom {
        opacity: 1;
        transform: translateY(0);
    }

    /* Pagination Styles */
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        margin-top: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .pagination-info {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .pagination-buttons {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .pagination-button {
        padding: 8px 16px;
        border-radius: 6px;
        background: white;
        border: 1px solid #ddd;
        color: #333;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: all 0.2s;
    }

    .pagination-button:hover:not(.disabled) {
        background: #f5f5f5;
        border-color: #ccc;
    }

    .pagination-button.disabled {
        color: #aaa;
        cursor: not-allowed;
    }

    .page-info {
        margin: 0 10px;
        font-size: 0.9rem;
        color: #666;
    }

    @media (max-width: 768px) {
        .pagination-container {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start;
        }
    }

    .btn-custom:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .btn-custom:focus {
        outline: none;
        box-shadow: 0 0 0 2px rgba(34, 187, 234, 0.3);
    }

    .btn-custom:active {
        transform: translateY(0);
        box-shadow: 0 0 1px rgba(0,0,0,0.1);
    }

    .btn-custom.disabled {
        opacity: 0.6;
        cursor: not-allowed;
        box-shadow: none;
    }

    .inc-badge {
        background: #ff4444;
        color: white;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 11px;
        margin-left: 4px;
    }

    /* Reduce padding for action buttons in table cells */
    .action-button {
        padding: 4px 8px;
        min-width: 80px;
        height: 28px;
        border-radius: 4px;
        font-size: 12px;
    }

    .table-responsive-custom {
        width: 100%;
        overflow-x: auto; /* Add horizontal scroll on small screens */
        margin-top: 20px; /* Space above the table */
    }

    .grade-monitor-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid var(--border-color);
    }

    .grade-monitor-table th,
    .grade-monitor-table td {
        padding: 10px;
        border: 1px solid var(--border-color);
        text-align: left;
        vertical-align: middle;
        font-size: 0.9rem;
    }

    .grade-monitor-table th {
        background-color: var(--light-bg);
        font-weight: 600;
        text-align: center; /* Center table headers */
    }

    .grade-monitor-table td {
         text-align: center; /* Center table cells by default */
    }

     .grade-monitor-table tbody tr:nth-child(even) {
        background-color: #f9f9f9; /* Zebra striping */
     }

    .grade-monitor-table tbody tr:hover {
        background-color: #e9e9e9;
    }

    .text-center-custom {
        text-align: center;
    }

    .small-text {
        font-size: 0.85rem;
    }

    .grade-value {
        font-weight: 500;
    }

     .text-muted-custom {
        color: #6c757d;
     }

    .debug-section {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
    }
     .debug-section h3 {
        font-size: 1.25rem;
        margin-top: 0;
        margin-bottom: 10px;
        color: var(--dark-text);
     }

    .debug-pre {
        background-color: #e9ecef;
        padding: 15px;
        border-radius: 5px;
        overflow-x: auto; /* Add scroll for long debug output */
        font-size: 0.85rem;
        color: #333;
    }

    .school-container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: var(--card-shadow);
        margin-bottom: 30px;
        overflow: hidden;
    }

    .school-header {
        background-color: var(--primary-color);
        color: #fff;
        padding: 15px 20px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }

    .school-header h3 {
        margin: 0;
        font-size: 1.3rem;
        font-weight: 600;
    }

    .school-content {
        padding: 20px;
    }

    .submission-section {
        margin-bottom: 30px;
    }

    .submission-section:last-child {
        margin-bottom: 0;
    }

    .submission-header {
        margin-bottom: 15px;
    }

    .submission-header h4 {
        margin: 0;
        color: var(--dark-text);
        font-size: 1.1rem;
        font-weight: 500;
    }
</style>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laravel\PNPH-CAPSTONE\resources\views/training/grade-submissions/monitor.blade.php ENDPATH**/ ?>