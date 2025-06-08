@extends('layouts.educator_layout')

@section('content')
<div class="page-container">
    <div class="header-section">
        <h1 style="font-weight: 300">📊 Subject Intervention Analytics</h1>
        <hr>
        <p class="text-muted">View and analyze subjects that need intervention based on student performance. Select a school, class, and submission to view the report.</p>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="schoolSelect" class="form-label fw-bold">School</label>
                    <select id="schoolSelect" class="form-select">
                        <option value="">Select School</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="classSelect" class="form-label fw-bold">Class</label>
                    <select id="classSelect" class="form-select" disabled>
                        <option value="">Select Class</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="submissionSelect" class="form-label fw-bold">Submission</label>
                    <select id="submissionSelect" class="form-select" disabled>
                        <option value="">Select Submission</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div id="interventionChartContainer">
            <div class="text-center p-5 text-muted">
                <i class="bi bi-bar-chart-line" style="font-size: 2.5rem; opacity: 0.5;"></i>
                <p class="mt-3 mb-0">Select a school, class, and submission to view subject intervention data</p>
            </div>
        </div>
    </div>
</div>

<br>
<br>
<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Prevent form submission on Enter key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        return false;
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Chart instance
    let interventionChart = null;
    
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
        
        const container = document.getElementById('interventionChartContainer');
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
                    <p class="mt-3 mb-0">Select a school, class, and submission to view subject intervention data</p>
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
                        <p class="mt-3 mb-0">Select a class and submission to view subject intervention data</p>
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
        
        const container = document.getElementById('interventionChartContainer');
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
                    <p class="mt-3 mb-0">Select a class and submission to view subject intervention data</p>
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
                        <p class="mt-3 mb-0">Select a submission to view subject intervention data</p>
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
        
        const container = document.getElementById('interventionChartContainer');
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
                    <p class="mt-3 mb-0">Select a submission to view subject intervention data</p>
                </div>`;
            return;
        }
        
        // Fetch subject intervention data
        fetch(`/educator/analytics/subject-intervention-data?school_id=${schoolId}&class_id=${classId}&submission_id=${submissionId}`)
            .then(res => res.json())
            .then(data => {
                // Check if we have data
                const hasData = data.subjects && data.subjects.length > 0;
                
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
                                        <th class="text-center" style="background-color: #22BBEA !important; color: white; border-color: #22BBEA !important;">Subject</th>
                                        <th class="text-center" style="background-color: #22BBEA !important; color: white; border-color: #22BBEA !important;">Passed</th>
                                        <th class="text-center" style="background-color: #22BBEA !important; color: white; border-color: #22BBEA !important;">Failed</th>
                                        <th class="text-center" style="background-color: #22BBEA !important; color: white; border-color: #22BBEA !important;">INC</th>
                                        <th class="text-center" style="background-color: #22BBEA !important; color: white; border-color: #22BBEA !important;">DR</th>
                                        <th class="text-center" style="background-color: #22BBEA !important; color: white; border-color: #22BBEA !important;">NC</th>
                                        <th class="text-center" style="background-color: #22BBEA !important; color: white; border-color: #22BBEA !important;">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>`;
                
                // Add rows for each subject
                data.subjects.forEach(subject => {
                    // Determine row class based on remarks
                    let rowClass = '';
                    if (subject.remarks === 'Need Intervention') {
                        rowClass = 'table-danger';
                    } else if (subject.remarks === 'Pending') {
                        rowClass = 'table-warning';
                    } else {
                        rowClass = 'table-success';
                    }
                    
                    tableHtml += `
                        <tr class="${rowClass}">
                            <td>${subject.subject}</td>
                            <td class="text-center">${subject.passed}</td>
                            <td class="text-center">${subject.failed}</td>
                            <td class="text-center">${subject.inc}</td>
                            <td class="text-center">${subject.dr}</td>
                            <td class="text-center">${subject.nc}</td>
                            <td>
                                <span class="badge ${getRemarksBadgeClass(subject.remarks)}">
                                    ${subject.remarks}
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

// Helper function to get badge class based on remarks
function getRemarksBadgeClass(remarks) {
    switch (remarks) {
        case 'No Need Intervention':
            return 'bg-success';
        case 'Pending':
            return 'bg-warning';
        case 'Need Intervention':
            return 'bg-danger';
        case 'No Submission Recorded':
            return 'bg-primary';
        default:
            return 'bg-secondary';
    }
}
</script>

<style>
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