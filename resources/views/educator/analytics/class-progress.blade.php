@extends('layouts.educator_layout')

@section('content')
<div class="page-container">
    <div class="header-section">
        <h1 style="font-weight: 300">📊 Class Progress Analytics</h1>
        <hr>
        <p class="text-muted">Monitor overall class performance and progress across different subjects and terms.</p>
    </div>

    <!-- Filters Section -->
    <div class="filter-card">
        <div class="filter-card-header">
            <h5>
                <i class="bi bi-funnel me-2"></i>
                Filter Class Progress
            </h5>
        </div>
        <div class="filter-card-body">
            <div class="filter-inline-container">
                <div class="filter-group">
                    <label for="schoolSelect">School</label>
                    <select id="schoolSelect">
                        <option value="">Select School</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="classSelect">Class</label>
                    <select id="classSelect" disabled>
                        <option value="">Select School First</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="submissionSelect">Submission</label>
                    <select id="submissionSelect" disabled>
                        <option value="">Select Class First</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Display -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-bar-chart me-2"></i>
                Class Progress Report
            </h5>
        </div>
        <div class="card-body">
            <div id="progressContainer">
                <div class="text-center p-5 text-muted">
                    <i class="bi bi-bar-chart-line" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    <p class="mt-3 mb-0">Select a school, class, and submission to view class progress data</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load schools
    fetch('/educator/analytics/schools')
        .then(res => res.json())
        .then(data => {
            const schoolSelect = document.getElementById('schoolSelect');
            data.forEach(school => {
                const opt = document.createElement('option');
                opt.value = school.id;
                opt.textContent = school.name;
                schoolSelect.appendChild(opt);
            });
        });

    // School change handler
    document.getElementById('schoolSelect').addEventListener('change', function() {
        const schoolId = this.value;
        const classSelect = document.getElementById('classSelect');
        const submissionSelect = document.getElementById('submissionSelect');
        
        classSelect.innerHTML = '<option value="">Select Class</option>';
        submissionSelect.innerHTML = '<option value="">Select Submission</option>';
        classSelect.disabled = true;
        submissionSelect.disabled = true;
        
        const container = document.getElementById('progressContainer');
        container.innerHTML = `
            <div class="d-flex justify-content-center p-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span class="ms-2">Loading classes...</span>
            </div>`;
        
        if (!schoolId) {
            container.innerHTML = `
                <div class="text-center p-5 text-muted">
                    <i class="bi bi-bar-chart-line" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    <p class="mt-3 mb-0">Select a school, class, and submission to view class progress data</p>
                </div>`;
            return;
        }
        
        // Load classes for the selected school
        fetch(`/educator/analytics/classes/${schoolId}`)
            .then(res => res.json())
            .then(classes => {
                classSelect.innerHTML = '<option value="">Select Class</option>';
                classes.forEach(cls => {
                    const opt = document.createElement('option');
                    opt.value = cls.id;
                    opt.textContent = cls.name;
                    classSelect.appendChild(opt);
                });
                classSelect.disabled = false;
                
                container.innerHTML = `
                    <div class="text-center p-5 text-muted">
                        <i class="bi bi-bar-chart-line" style="font-size: 2.5rem; opacity: 0.5;"></i>
                        <p class="mt-3 mb-0">Select a class and submission to view class progress data</p>
                    </div>`;
            });
    });
    
    // Class change handler
    document.getElementById('classSelect').addEventListener('change', function() {
        const schoolId = document.getElementById('schoolSelect').value;
        const classId = this.value;
        const submissionSelect = document.getElementById('submissionSelect');
        
        submissionSelect.innerHTML = '<option value="">Select Submission</option>';
        submissionSelect.disabled = true;
        
        const container = document.getElementById('progressContainer');
        container.innerHTML = `
            <div class="d-flex justify-content-center p-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span class="ms-2">Loading submissions...</span>
            </div>`;
        
        if (!classId) {
            container.innerHTML = `
                <div class="text-center p-5 text-muted">
                    <i class="bi bi-bar-chart-line" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    <p class="mt-3 mb-0">Select a class and submission to view class progress data</p>
                </div>`;
            return;
        }
        
        // Load submissions for the selected school and class
        fetch(`/educator/analytics/class-submissions/${schoolId}/${classId}`)
            .then(res => res.json())
            .then(submissions => {
                submissionSelect.innerHTML = '<option value="">Select Submission</option>';
                submissions.forEach(submission => {
                    const opt = document.createElement('option');
                    opt.value = submission.id;
                    opt.textContent = submission.label;
                    submissionSelect.appendChild(opt);
                });
                submissionSelect.disabled = false;
                
                container.innerHTML = `
                    <div class="text-center p-5 text-muted">
                        <i class="bi bi-bar-chart-line" style="font-size: 2.5rem; opacity: 0.5;"></i>
                        <p class="mt-3 mb-0">Select a submission to view class progress data</p>
                    </div>`;
            })
            .catch(error => {
                console.error('Error loading submissions:', error);
                container.innerHTML = `
                    <div class="alert alert-danger m-3">
                        <i class="bi-exclamation-triangle-fill me-2"></i>
                        Failed to load submissions. Please try again.
                    </div>`;
            });
    });
    
    // Submission change handler
    document.getElementById('submissionSelect').addEventListener('change', function() {
        const schoolId = document.getElementById('schoolSelect').value;
        const classId = document.getElementById('classSelect').value;
        const submissionId = this.value;
        
        const container = document.getElementById('progressContainer');
        container.innerHTML = `
            <div class="d-flex justify-content-center p-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span class="ms-2">Loading data...</span>
            </div>`;
        
        if (!submissionId) {
            container.innerHTML = `
                <div class="text-center p-5 text-muted">
                    <i class="bi bi-bar-chart-line" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    <p class="mt-3 mb-0">Select a submission to view class progress data</p>
                </div>`;
            return;
        }
        
        // Fetch class progress data
        fetch(`/educator/analytics/class-progress-data?school_id=${schoolId}&class_id=${classId}&submission_id=${submissionId}`)
            .then(res => res.json())
            .then(data => {
                // Check if we have data
                const hasData = data.students && data.students.length > 0;
                
                if (!hasData) {
                    container.innerHTML = `
                        <div class="text-center p-5 text-muted">
                            <i class="bi bi-exclamation-circle" style="font-size: 2.5rem; opacity: 0.5;"></i>
                            <p class="mt-3 mb-0">No data available for the selected criteria</p>
                        </div>`;
                    return;
                }

                // Create header with school, class, and submission info
                let headerHtml = `
                    <div class="card-header bg-light p-3">
                        <div class="d-flex flex-column align-items-center justify-content-center w-100">
                            <div style="font-size: 1.5rem; font-weight: 600; line-height: 1.2; text-align: center;">
                                ${data.school.name} - ${data.class_name}
                            </div>
                            <div class="mt-2" style="font-size: 1rem; color: #6c757d; white-space: nowrap; text-align: center;">
                                ${data.submission.semester ? `Semester: ${data.submission.semester}` : ''}
                                ${data.submission.term ? ` | Term: ${data.submission.term}` : ''}
                                ${data.submission.academic_year ? ` | Academic Year: ${data.submission.academic_year}` : ''}
                            </div>
                        </div>
                    </div>`;
                
                // Create table
                let tableHtml = `
                    <div class="table-responsive">
                        <div class="table-container">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="background-color: #22BBEA !important; color: white; border-color: #22BBEA !important;">Student Name</th>
                                        <th class="text-center" style="background-color: #22BBEA !important; color: white; border-color: #22BBEA !important;">Total Subjects</th>
                                        <th class="text-center" style="background-color: #22BBEA !important; color: white; border-color: #22BBEA !important;">Passed</th>
                                        <th class="text-center" style="background-color: #22BBEA !important; color: white; border-color: #22BBEA !important;">Failed</th>
                                        <th class="text-center" style="background-color: #22BBEA !important; color: white; border-color: #22BBEA !important;">Average Grade</th>
                                        <th class="text-center" style="background-color: #22BBEA !important; color: white; border-color: #22BBEA !important;">Overall Status</th>
                                    </tr>
                                </thead>
                                <tbody>`;
                
                // Add rows for each student
                data.students.forEach(student => {
                    // Determine row class based on status
                    let rowClass = '';
                    if (student.overall_status === 'Failed') {
                        rowClass = 'table-danger';
                    } else if (student.overall_status === 'Pending') {
                        rowClass = 'table-warning';
                    } else {
                        rowClass = 'table-success';
                    }
                    
                    tableHtml += `
                        <tr class="${rowClass}">
                            <td>${student.student_name}</td>
                            <td class="text-center">${student.total_subjects}</td>
                            <td class="text-center">${student.passed_subjects}</td>
                            <td class="text-center">${student.failed_subjects}</td>
                            <td class="text-center">${student.average_grade.toFixed(2)}</td>
                            <td class="text-center">
                                <span class="badge ${getStatusBadgeClass(student.overall_status)}">
                                    ${student.overall_status}
                                </span>
                            </td>
                        </tr>`;
                });
                
                // Close table
                tableHtml += `
                                </tbody>
                            </table>
                        </div>
                    </div>`;
                    
                container.innerHTML = headerHtml + tableHtml;
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                container.innerHTML = `
                    <div class="text-center p-5 text-danger">
                        <i class="bi bi-exclamation-triangle" style="font-size: 2.5rem;"></i>
                        <p class="mt-3 mb-0">Error loading data. Please try again.</p>
                    </div>`;
            });
    });
});

// Helper function to get badge class based on status
function getStatusBadgeClass(status) {
    switch (status) {
        case 'Passed':
            return 'bg-success';
        case 'Pending':
            return 'bg-warning';
        case 'Failed':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}
</script>

<style>
/* Page Container */
.page-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.header-section {
    margin-bottom: 30px;
}

.header-section h1 {
    font-weight: 300;
    color: #333;
    margin-bottom: 10px;
}

.header-section hr {
    border: none;
    height: 1px;
    background-color: #ddd;
    margin-bottom: 15px;
}

/* Filter Card Styling */
.filter-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: none;
    margin-bottom: 1.5rem;
}

.filter-card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 15px 20px;
    border-radius: 8px 8px 0 0;
}

.filter-card-header h5 {
    margin: 0;
    font-weight: 500;
    color: #495057;
}

.filter-card-body {
    padding: 20px;
}

/* Filter Section Styling */
.filter-inline-container {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: end;
    margin-bottom: 20px;
}

.filter-group {
    display: flex;
    flex-direction: column;
    min-width: 200px;
    flex: 1;
}

.filter-group label {
    margin-bottom: 5px;
    font-weight: 500;
    color: #495057;
    font-size: 14px;
}

.filter-group select {
    padding: 8px 12px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    background-color: #fff;
    font-size: 14px;
}

.filter-group select:focus {
    border-color: #22bbea;
    box-shadow: 0 0 0 0.2rem rgba(34, 187, 234, 0.25);
    outline: none;
}

.filter-group select:disabled {
    background-color: #f8f9fa;
    color: #6c757d;
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .filter-inline-container {
        flex-direction: column;
        gap: 15px;
    }

    .filter-group {
        min-width: 100%;
    }
}

.card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: none;
}

.card-header {
    background: #22bbea;
    color: white;
    padding: 16px 24px;
    border-radius: 8px 8px 0 0;
    font-weight: 600;
    font-size: 16px;
    letter-spacing: 0.5px;
}

.card-body {
    padding: 20px;
}

.table-container {
    max-width: 95%;
    margin: 0 auto;
    padding: 0 15px;
}

.table {
    border: 1px solid #dee2e6;
    width: 100%;
    font-size: 1rem;
    margin: 20px auto;
}

.table th, .table td {
    border: 1px solid #dee2e6;
    vertical-align: middle;
    padding: 12px 15px;
    text-align: center;
}

.table thead th {
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    background-color: #f8f9fa;
    text-align: center;
}
</style>
@endsection
