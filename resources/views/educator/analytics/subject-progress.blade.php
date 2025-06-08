@extends('layouts.educator_layout')

@section('content')
<div class="page-container">
    <div class="header-section">
        <h1 style="font-weight: 300">📊 Subject Progress Analytics</h1>
        <hr>
        <p class="text-muted">View and analyze student progress across subjects. Select a school, class, and submission to view the report.</p>
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
        <div id="progressChartContainer">
            <div class="text-center p-5 text-muted">
                <i class="bi bi-bar-chart-line" style="font-size: 2.5rem; opacity: 0.5;"></i>
                <p class="mt-3 mb-0">Select a school, class, and submission to view subject progress</p>
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
    let progressChart = null;
    
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
        
        const container = document.getElementById('progressChartContainer');
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
                    <p class="mt-3 mb-0">Select a school, class, and submission to view subject progress</p>
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
                        <p class="mt-3 mb-0">Select a class and submission to view subject progress</p>
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
        
        const container = document.getElementById('progressChartContainer');
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
                    <p class="mt-3 mb-0">Select a class and submission to view subject progress</p>
                </div>`;
            return;
        }
        
        // Load submissions for the selected school and class
        fetch(`/educator/analytics/class-submissions/${schoolId}/${classId}`)
            .then(res => res.json())
            .then(submissions => {
                submissionSelect.innerHTML = '<option value="">Select Submission</option>';
                
                if (!submissions || submissions.length === 0) {
                    submissionSelect.innerHTML = '<option value="">No submissions found</option>';
                    container.innerHTML = `
                        <div class="text-center p-5 text-muted">
                            <i class="bi bi-bar-chart-line" style="font-size: 2.5rem; opacity: 0.5;"></i>
                            <p class="mt-3 mb-0">No submissions found for this class</p>
                        </div>`;
                    return;
                }

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
                        <p class="mt-3 mb-0">Select a submission to view subject progress</p>
                    </div>`;
            })
            .catch(error => {
                console.error('Error loading submissions:', error);
                submissionSelect.innerHTML = '<option value="">Error loading submissions</option>';
                container.innerHTML = `
                    <div class="text-center p-5 text-danger">
                        <i class="bi bi-exclamation-triangle" style="font-size: 2.5rem;"></i>
                        <p class="mt-3 mb-0">Error loading submissions. Please try again.</p>
                    </div>`;
            });
    });
    
    // Submission change handler
    document.getElementById('submissionSelect').addEventListener('change', function() {
        const schoolId = document.getElementById('schoolSelect').value;
        const classId = document.getElementById('classSelect').value;
        const submissionId = this.value;
        
        const container = document.getElementById('progressChartContainer');
        
        if (!submissionId) {
            container.innerHTML = `
                <div class="text-center p-5 text-muted">
                    <i class="bi bi-bar-chart-line" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    <p class="mt-3 mb-0">Select a submission to view subject progress</p>
                </div>`;
            return;
        }
        
        container.innerHTML = `
            <div class="d-flex justify-content-center p-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span class="ms-2">Loading data...</span>
            </div>`;
        
        // Fetch subject progress data
        fetch(`/educator/analytics/subject-progress-data?school_id=${schoolId}&class_id=${classId}&submission_id=${submissionId}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    container.innerHTML = `
                        <div class="text-center p-5 text-danger">
                            <i class="bi bi-exclamation-triangle" style="font-size: 2.5rem;"></i>
                            <p class="mt-3 mb-0">${data.error}</p>
                        </div>`;
                    return;
                }
                
                // Process and display the data
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
                
                // Create chart container
                container.innerHTML = `
                    <div class="card-body p-0">
                        <div class="chart-container" style="position: relative; height: 500px; width: 100%;">
                            <canvas id="subjectProgressChart"></canvas>
                        </div>
                    </div>`;
                
                // Prepare data for chart
                const subjects = data.subjects.map(s => s.subject || 'Unknown Subject');
                const passedData = data.subjects.map(s => parseInt(s.passed) || 0);
                const failedData = data.subjects.map(s => parseInt(s.failed) || 0);
                const incData = data.subjects.map(s => parseInt(s.inc) || 0);
                const drData = data.subjects.map(s => parseInt(s.dr) || 0);
                const ncData = data.subjects.map(s => parseInt(s.nc) || 0);
                
                // Destroy previous chart if it exists
                if (progressChart) {
                    progressChart.destroy();
                }
                
                // Create new chart
                const ctx = document.getElementById('subjectProgressChart').getContext('2d');
                progressChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: subjects,
                        datasets: [
                            {
                                label: 'Passed',
                                data: passedData,
                                backgroundColor: 'rgba(40, 167, 69, 0.7)',
                                borderColor: 'rgba(40, 167, 69, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Failed',
                                data: failedData,
                                backgroundColor: 'rgba(220, 53, 69, 0.7)',
                                borderColor: 'rgba(220, 53, 69, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'INC',
                                data: incData,
                                backgroundColor: 'rgba(255, 193, 7, 0.7)',
                                borderColor: 'rgba(255, 193, 7, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'DR',
                                data: drData,
                                backgroundColor: 'rgba(23, 162, 184, 0.7)',
                                borderColor: 'rgba(23, 162, 184, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'NC',
                                data: ncData,
                                backgroundColor: 'rgba(108, 117, 125, 0.7)',
                                borderColor: 'rgba(108, 117, 125, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                stacked: true,
                                title: {
                                    display: true,
                                    text: 'Subjects'
                                }
                            },
                            y: {
                                stacked: true,
                                title: {
                                    display: true,
                                    text: 'Number of Students'
                                },
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Subject Progress Distribution',
                                font: {
                                    size: 16
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.dataset.label || '';
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error loading data:', error);
                container.innerHTML = `
                    <div class="text-center p-5 text-danger">
                        <i class="bi bi-exclamation-triangle" style="font-size: 2.5rem;"></i>
                        <p class="mt-3 mb-0">Error loading data. Please try again.</p>
                    </div>`;
            });
    });
});
</script>
@endsection 