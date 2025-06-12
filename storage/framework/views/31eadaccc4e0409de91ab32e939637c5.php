<?php $__env->startSection('content'); ?>
<style>
/* Pure CSS Grade Status - Training Design */

/* Reset and Base */
* {
    box-sizing: border-box;
}

/* Main Container */
.student-grades-container {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    background: #f8f9fa;
    min-height: calc(100vh - 80px);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Header */
.grades-page-header {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #e9ecef;
}

.grades-page-title {
    font-size: 28px;
    color: #333;
    margin: 0;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 15px;
}

.grades-page-title i {
    color: #22bbea;
    font-size: 26px;
}

/* Cards */
.grades-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
    overflow: hidden;
    border: 1px solid #dee2e6;
}

.grades-card-header {
    background: #22bbea;
    color: white;
    padding: 20px 25px;
    font-weight: 600;
    font-size: 18px;
    display: flex;
    align-items: center;
    gap: 12px;
    border-bottom: none;
}

.grades-card-header i {
    font-size: 16px;
}

.grades-card-body {
    padding: 30px 25px;
}

/* Filter Form */
.grades-filter-form {
    margin: 0;
    width: 100%;
}

.grades-filter-row {
    display: flex;
    gap: 25px;
    align-items: flex-end;
    flex-wrap: wrap;
    margin-bottom: 0;
}

.grades-filter-group {
    flex: 1;
    min-width: 220px;
    display: flex;
    flex-direction: column;
}

.grades-filter-label {
    display: block;
    margin-bottom: 10px;
    color: #495057;
    font-weight: 600;
    font-size: 15px;
    line-height: 1.4;
}

.grades-filter-select {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #ced4da;
    border-radius: 6px;
    font-size: 15px;
    background-color: #ffffff;
    color: #495057;
    transition: all 0.3s ease;
    font-family: inherit;
    line-height: 1.5;
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 12px center;
    background-repeat: no-repeat;
    background-size: 16px;
    padding-right: 40px;
}

.grades-filter-select:focus {
    border-color: #22bbea;
    outline: none;
    box-shadow: 0 0 0 4px rgba(34, 187, 234, 0.15);
    background-color: #fafbfc;
}

.grades-filter-select:hover {
    border-color: #adb5bd;
}

.grades-filter-actions {
    display: flex;
    gap: 15px;
    align-items: flex-end;
    flex-wrap: wrap;
}

/* Buttons */
.grades-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 20px;
    border: 2px solid transparent;
    border-radius: 6px;
    text-decoration: none;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
    min-width: 120px;
    line-height: 1.4;
    text-transform: none;
}

.grades-btn:focus {
    outline: none;
    box-shadow: 0 0 0 4px rgba(34, 187, 234, 0.2);
}

.grades-btn-primary {
    background-color: #22bbea;
    border-color: #22bbea;
    color: white;
}

.grades-btn-primary:hover {
    background-color: #1e9bc4;
    border-color: #1e9bc4;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(34, 187, 234, 0.3);
}

.grades-btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
}

.grades-btn-secondary:hover {
    background-color: #5a6268;
    border-color: #5a6268;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
}
/* Chart Section */
.grades-chart-section {
    text-align: center;
    width: 100%;
}

.grades-chart-title {
    margin: 0 0 25px;
    color: #495057;
    font-size: 20px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
}

.grades-chart-title i {
    color: #22bbea;
    font-size: 18px;
}

.grades-chart-container {
    position: relative;
    height: 450px;
    width: 100%;
    margin: 0;
    background: #fafbfc;
    border-radius: 8px;
    padding: 20px;
    border: 1px solid #e9ecef;
}

/* Table Styles */
.grades-table-wrapper {
    overflow-x: auto;
    border-radius: 8px;
    border: 2px solid #dee2e6;
    background: white;
    margin-top: 20px;
}

.grades-data-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    font-size: 15px;
    margin: 0;
    min-width: 700px;
}

.grades-data-table thead {
    background: #22bbea;
}

.grades-data-table th {
    background-color: #22bbea;
    color: white;
    padding: 18px 20px;
    text-align: left;
    font-weight: 700;
    font-size: 14px;
    border: none;
    white-space: nowrap;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-bottom: 3px solid #1e9bc4;
}

.grades-data-table td {
    padding: 18px 20px;
    border-bottom: 1px solid #e9ecef;
    color: #495057;
    vertical-align: middle;
    line-height: 1.5;
}

.grades-data-table tbody tr {
    transition: all 0.3s ease;
}

.grades-data-table tbody tr:hover {
    background-color: #f8f9fa;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.grades-data-table tbody tr:last-child td {
    border-bottom: none;
}

/* Table Cell Styles */
.grades-subject-code {
    font-weight: 800;
    color: #2c3e50;
    font-family: 'Courier New', 'Monaco', monospace;
    font-size: 14px;
    letter-spacing: 0.5px;
    background: #f8f9fa;
    padding: 8px 12px;
    border-radius: 4px;
    display: inline-block;
}

.grades-subject-name {
    color: #495057;
    font-weight: 600;
    max-width: 280px;
    line-height: 1.4;
}

.grades-term-info,
.grades-year-info {
    color: #6c757d;
    font-weight: 500;
    font-size: 14px;
}

.grades-grade-display {
    font-weight: 800;
    font-size: 18px;
    text-align: center;
    padding: 10px 16px;
    border-radius: 6px;
    min-width: 70px;
    display: inline-block;
    border: 2px solid transparent;
    line-height: 1.2;
}

.grades-grade-excellent {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.grades-grade-good {
    color: #0c5460;
    background-color: #d1ecf1;
    border-color: #bee5eb;
}

.grades-grade-fair {
    color: #856404;
    background-color: #fff3cd;
    border-color: #ffeaa7;
}

.grades-grade-poor {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

/* Status Badges */
.grades-status-badge {
    display: inline-flex;
    align-items: center;
    padding: 8px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.grades-badge-approved,
.grades-badge-passed {
    background-color: #d4edda;
    color: #155724;
    border-color: #c3e6cb;
}

.grades-badge-pending {
    background-color: #fff3cd;
    color: #856404;
    border-color: #ffeaa7;
}

.grades-badge-rejected,
.grades-badge-failed {
    background-color: #f8d7da;
    color: #721c24;
    border-color: #f5c6cb;
}

.grades-badge-incomplete {
    background-color: #d1ecf1;
    color: #0c5460;
    border-color: #bee5eb;
}

/* Empty State */
.grades-no-data {
    text-align: center;
    padding: 60px 30px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    color: #6c757d;
    margin: 30px 0;
    border: 2px dashed #ced4da;
}

.grades-no-data i {
    font-size: 64px;
    margin-bottom: 20px;
    opacity: 0.6;
    color: #22bbea;
    display: block;
}

.grades-no-data h4 {
    margin-bottom: 12px;
    color: #495057;
    font-weight: 700;
    font-size: 24px;
}

.grades-no-data p {
    margin: 0;
    font-size: 16px;
    color: #6c757d;
    font-weight: 500;
}

/* Responsive Design */
@media (max-width: 992px) {
    .student-grades-container {
        padding: 15px;
    }

    .grades-filter-row {
        gap: 20px;
    }

    .grades-filter-group {
        min-width: 200px;
    }
}

@media (max-width: 768px) {
    .student-grades-container {
        padding: 12px;
    }

    .grades-page-header {
        margin-bottom: 25px;
        padding-bottom: 15px;
    }

    .grades-page-title {
        font-size: 24px;
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }

    .grades-card-header {
        padding: 15px 20px;
        font-size: 16px;
    }

    .grades-card-body {
        padding: 20px;
    }

    .grades-filter-row {
        flex-direction: column;
        gap: 20px;
        align-items: stretch;
    }

    .grades-filter-group {
        min-width: 100%;
        width: 100%;
    }

    .grades-filter-select {
        padding: 14px 16px;
        font-size: 16px;
        padding-right: 42px;
    }

    .grades-filter-actions {
        flex-direction: column;
        width: 100%;
        gap: 12px;
    }

    .grades-btn {
        width: 100%;
        justify-content: center;
        padding: 14px 20px;
        font-size: 16px;
    }

    .grades-chart-container {
        height: 350px;
        padding: 15px;
    }

    .grades-data-table th,
    .grades-data-table td {
        padding: 12px 10px;
        font-size: 14px;
    }

    .grades-subject-code {
        font-size: 12px;
        padding: 6px 10px;
    }

    .grades-subject-name {
        max-width: 180px;
        font-size: 14px;
    }

    .grades-grade-display {
        font-size: 16px;
        padding: 8px 12px;
        min-width: 60px;
    }

    .grades-term-info,
    .grades-year-info {
        font-size: 13px;
    }
}

@media (max-width: 576px) {
    .student-grades-container {
        padding: 10px;
    }

    .grades-page-title {
        font-size: 20px;
    }

    .grades-card-header {
        padding: 12px 15px;
        font-size: 14px;
    }

    .grades-card-body {
        padding: 15px;
    }

    .grades-filter-select {
        padding: 12px 14px;
        font-size: 16px;
        padding-right: 38px;
    }

    .grades-btn {
        padding: 12px 18px;
        font-size: 15px;
    }

    .grades-chart-container {
        height: 280px;
        padding: 12px;
    }

    .grades-data-table th,
    .grades-data-table td {
        padding: 10px 8px;
        font-size: 13px;
    }

    .grades-subject-code {
        font-size: 11px;
        padding: 4px 8px;
    }

    .grades-subject-name {
        max-width: 140px;
        font-size: 13px;
    }

    .grades-grade-display {
        font-size: 14px;
        padding: 6px 10px;
        min-width: 50px;
    }

    .grades-status-badge {
        font-size: 11px;
        padding: 6px 10px;
    }

    .grades-no-data {
        padding: 40px 20px;
    }

    .grades-no-data i {
        font-size: 48px;
    }

    .grades-no-data h4 {
        font-size: 20px;
    }

    .grades-no-data p {
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .student-grades-container {
        padding: 8px;
    }

    .grades-page-title {
        font-size: 18px;
    }

    .grades-chart-container {
        height: 250px;
        padding: 10px;
    }

    .grades-data-table th,
    .grades-data-table td {
        padding: 8px 6px;
        font-size: 12px;
    }

    .grades-grade-display {
        font-size: 13px;
        padding: 5px 8px;
        min-width: 45px;
    }
}
</style>

<div class="student-grades-container">
    <!-- Page Header -->
    <div class="grades-page-header">
        <h1 class="grades-page-title">
            <i class="fas fa-chart-line"></i>
            My Grade Status
        </h1>

    </div>

    <!-- Filter Card -->
    <div class="grades-card">
        <div class="grades-card-header">
            <i class="fas fa-filter"></i>
            Filter Options
        </div>
        <div class="grades-card-body">
            <form action="<?php echo e(route('student.grades')); ?>" method="GET" class="grades-filter-form">
                <div class="grades-filter-row">
                    <div class="grades-filter-group">
                        <label for="term" class="grades-filter-label">Term</label>
                        <select name="term" id="term" class="grades-filter-select">
                            <option value="">All Terms</option>
                            <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($term); ?>" <?php echo e(request('term') == $term ? 'selected' : ''); ?>><?php echo e(ucfirst($term)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="grades-filter-group">
                        <label for="academic_year" class="grades-filter-label">Academic Year</label>
                        <select name="academic_year" id="academic_year" class="grades-filter-select">
                            <option value="">All Years</option>
                            <?php $__currentLoopData = $years; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($year); ?>" <?php echo e(request('academic_year') == $year ? 'selected' : ''); ?>><?php echo e($year); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="grades-filter-actions">
                        <button type="submit" class="grades-btn grades-btn-primary">
                            <i class="fas fa-search"></i>
                            Apply Filter
                        </button>
                        <?php if(request()->has('term') || request()->has('academic_year')): ?>
                            <a href="<?php echo e(route('student.grades')); ?>" class="grades-btn grades-btn-secondary">
                                <i class="fas fa-times"></i>
                                Clear Filter
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php
        // Sort subjectsWithGrades by academic_year (desc) and term (desc)
        if (isset($subjectsWithGrades)) {
            $subjectsWithGrades = $subjectsWithGrades->sortByDesc(function($subject) {
                $year = $subject->academic_year ?? '';
                $term = $subject->term ?? '';
                return $year . '-' . $term;
            })->values();
        }
    ?>
    
    <!-- Subjects by Status Chart -->
    <?php if(isset($subjectsWithGrades) && $subjectsWithGrades->count() > 0): ?>
        <?php
            $subjectLabels = [];
            $subjectGrades = [];
            $subjectColors = [];
            $subjectStatuses = [];

            foreach ($subjectsWithGrades as $subject) {
                // Only include approved grades
                $status = strtolower($subject->status ?? $subject->pivot->status ?? '');
                if ($status !== 'approved') continue;

                $subjectName = $subject->subject_name ?? $subject->name ?? 'N/A';
                $subjectCode = $subject->subject_code ?? $subject->code ?? '';
                $grade = is_numeric($subject->grade ?? $subject->pivot->grade ?? null) ? floatval($subject->grade ?? $subject->pivot->grade ?? 0) : 0;

                // Label: Subject Name (Code) or just Subject Name
                $subjectLabels[] = $subjectName . ' (' . $subjectCode . ')';

                // Grade
                $subjectGrades[] = $grade;

                // Status based on grade using school's grading system
                $passingMin = $studentSchool->passing_grade_min ?? 1.0;
                $passingMax = $studentSchool->passing_grade_max ?? 3.0;

                if ($grade >= $passingMin && $grade <= $passingMax) {
                    $subjectColors[] = 'rgba(40, 167, 69, 0.8)';
                    $friendlyStatus = 'Passed';
                } elseif ($grade > 0 && is_numeric($grade)) {
                    $subjectColors[] = 'rgba(220, 53, 69, 0.8)';
                    $friendlyStatus = 'Failed';
                } elseif ($grade == 0) {
                    $subjectColors[] = 'rgba(255, 193, 7, 0.8)';
                    $friendlyStatus = 'Incomplete';
                } else {
                    $subjectColors[] = 'rgba(108, 117, 125, 0.8)';
                    $friendlyStatus = 'No Credit';
                }
                $subjectStatuses[] = $friendlyStatus;
            }
        ?>
        
        <?php
            // Reverse arrays so newest is last (rightmost bar)
            $subjectLabels = array_reverse($subjectLabels);
            $subjectGrades = array_reverse($subjectGrades);
            $subjectColors = array_reverse($subjectColors);
            $subjectStatuses = array_reverse($subjectStatuses);
        ?>

        <!-- Chart Card -->
        <div class="grades-card">
            <div class="grades-card-header">
                <i class="fas fa-chart-bar"></i>
                Grade Distribution Chart
            </div>
            <div class="grades-card-body grades-chart-section">
                <div class="grades-chart-container">
                    <canvas id="gradeStatusChart"></canvas>
                </div>
            </div>
        </div>
        

    <?php else: ?>
        <!-- No Data State -->
        <div class="grades-no-data">
            <i class="fas fa-chart-line"></i>
            <h4>No Grade Data Available</h4>
            <p>You don't have any approved grades to display yet.</p>
        </div>
    <?php endif; ?>

    <!-- Detailed Grade Report -->
    <?php if(isset($subjectsWithGrades) && $subjectsWithGrades->count() > 0): ?>
        <div class="grades-card">
            <div class="grades-card-header">
                <i class="fas fa-table"></i>
                Detailed Grade Report
            </div>
            <div class="grades-card-body">
                <div class="grades-table-wrapper">
                    <table class="grades-data-table">
                        <thead>
                            <tr>
                                <th>Subject Code</th>
                                <th>Subject Name</th>
                                <th>Term</th>
                                <th>Academic Year</th>
                                <th>Grade</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $subjectsWithGrades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $grade = $subject->grade ?? ($subject->pivot->grade ?? null);
                                    $status = strtolower($subject->status ?? ($subject->pivot->status ?? 'pending'));
                                    $gradeDisplay = is_numeric($grade) ? number_format($grade, 2) : ($grade ?? 'N/A');
                                    $subjectCode = $subject->subject_code ?? ($subject->code ?? '');
                                    $subjectName = $subject->subject_name ?? ($subject->name ?? 'Unnamed Subject');

                                    // Get school's grading system
                                    $passingMin = $subject->passing_grade_min ?? ($studentSchool->passing_grade_min ?? 1.0);
                                    $passingMax = $subject->passing_grade_max ?? ($studentSchool->passing_grade_max ?? 3.0);



                                    // Determine grade class based on school's grading system
                                    $gradeClass = 'grades-grade-poor';
                                    if (is_numeric($grade)) {
                                        $gradeValue = floatval($grade);
                                        $range = $passingMax - $passingMin;

                                        if ($gradeValue >= $passingMin && $gradeValue <= $passingMax) {
                                            // Within passing range - determine excellence level
                                            $excellentThreshold = $passingMin + ($range * 0.8); // Top 20%
                                            $goodThreshold = $passingMin + ($range * 0.5); // Top 50%

                                            if ($gradeValue >= $excellentThreshold) {
                                                $gradeClass = 'grades-grade-excellent';
                                            } elseif ($gradeValue >= $goodThreshold) {
                                                $gradeClass = 'grades-grade-good';
                                            } else {
                                                $gradeClass = 'grades-grade-fair';
                                            }
                                        } else {
                                            // Below passing grade
                                            $gradeClass = 'grades-grade-poor';
                                        }
                                    }

                                    // Determine status badge class
                                    $badgeClass = 'grades-badge-' . str_replace(' ', '-', $status);
                                ?>
                                <?php if(!is_null($grade) && $grade !== '' && $grade !== 'N/A'): ?>
                                    <tr>
                                        <td><span class="grades-subject-code"><?php echo e($subjectCode); ?></span></td>
                                        <td class="grades-subject-name"><?php echo e($subjectName); ?></td>
                                        <td class="grades-term-info"><?php echo e(ucfirst($subject->term ?? 'N/A')); ?></td>
                                        <td class="grades-year-info"><?php echo e($subject->academic_year ?? 'N/A'); ?></td>
                                        <td>
                                            <span class="grades-grade-display <?php echo e($gradeClass); ?>"><?php echo e($gradeDisplay); ?></span>
                                        </td>
                                        <td>
                                            <span class="grades-status-badge <?php echo e($badgeClass); ?>">
                                                <?php echo e(ucfirst($status)); ?>

                                            </span>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Grade Status Chart
    const ctx = document.getElementById('gradeStatusChart');
    if (!ctx) return;

    const labels = <?php echo json_encode($subjectLabels ?? [], 15, 512) ?>;
    const data = <?php echo json_encode($subjectGrades ?? [], 15, 512) ?>;
    const backgroundColors = <?php echo json_encode($subjectColors ?? [], 15, 512) ?>;
    const statuses = <?php echo json_encode($chartSubjectStatuses ?? [], 15, 512) ?>;

    // Get school grading system
    const schoolGrading = <?php echo json_encode($studentSchool ?? null, 15, 512) ?>;
    const gradeMin = schoolGrading?.passing_grade_min ?? 1.0;
    const gradeMax = schoolGrading?.passing_grade_max ?? 3.0;
    const passingMin = schoolGrading?.passing_grade_min ?? 1.0;

    // Determine chart scale based on school's grading system
    const chartMax = Math.max(gradeMax, 5.0); // Ensure minimum scale of 5.0
    const stepSize = chartMax <= 5.0 ? 0.5 : 1.0;

    if (!Array.isArray(labels) || labels.length === 0) {
        ctx.closest('.grades-chart-container').innerHTML = `
            <div class="grades-no-data">
                <i class="fas fa-chart-bar"></i>
                <h4>No Grade Data Available</h4>
                <p>No approved grades to display in the chart.</p>
            </div>`;
        return;
    }

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Grade',
                data: data,
                backgroundColor: backgroundColors,
                borderColor: backgroundColors.map(c => c.replace('0.8', '1')),
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false,
                barThickness: 'flex',
                maxBarThickness: 60
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: chartMax,
                    ticks: {
                        stepSize: stepSize,
                        callback: function(value) {
                            return value.toFixed(1);
                        },
                        font: {
                            size: 12,
                            family: 'Poppins'
                        },
                        color: '#6c757d'
                    },
                    title: {
                        display: true,
                        text: `Grade (${gradeMin.toFixed(1)} - ${gradeMax.toFixed(1)})`,
                        font: {
                            weight: 'bold',
                            size: 14,
                            family: 'Poppins'
                        },
                        color: '#495057'
                    },
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 0,
                        font: {
                            size: 11,
                            family: 'Poppins'
                        },
                        color: '#6c757d'
                    },
                    title: {
                        display: true,
                        text: 'Subjects',
                        font: {
                            weight: 'bold',
                            size: 14,
                            family: 'Poppins'
                        },
                        color: '#495057'
                    },
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.9)',
                    titleFont: {
                        size: 14,
                        weight: 'bold',
                        family: 'Poppins'
                    },
                    bodyFont: {
                        size: 13,
                        family: 'Poppins'
                    },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: true,
                    borderColor: '#22bbea',
                    borderWidth: 1,
                    callbacks: {
                        title: function(context) {
                            return context[0].label;
                        },
                        label: function(context) {
                            const status = statuses[context.dataIndex] || 'N/A';
                            const grade = context.parsed.y;
                            const isPassing = grade >= passingMin && grade <= gradeMax;
                            const passStatus = isPassing ? 'Passing' : 'Failing';

                            return [
                                `Grade: ${grade.toFixed(2)}`,
                                `Status: ${status}`,
                                `Result: ${passStatus}`
                            ];
                        }
                    }
                }
            },
            animation: {
                duration: 1200,
                easing: 'easeInOutQuart'
            }
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.student_layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laravel\PNPH-CAPSTONE\resources\views/student/grades.blade.php ENDPATH**/ ?>