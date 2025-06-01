@extends('layouts.nav')

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
    fetch('/training/analytics/schools')
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
        fetch(`/training/analytics/classes/${schoolId}`)
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
        
        if (!schoolId || !classId) {
            container.innerHTML = `
                <div class="text-center p-5 text-muted">
                    <i class="bi bi-bar-chart-line" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    <p class="mt-3 mb-0">Select a class and submission to view subject progress</p>
                </div>`;
            return;
        }
        
        container.innerHTML = `
            <div class="d-flex justify-content-center p-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span class="ms-2">Loading submissions...</span>
            </div>`;
        
        // Load submissions for the selected school and class
        fetch(`/training/analytics/class-submissions/${schoolId}/${classId}`)
            .then(res => res.json())
            .then(submissions => {
                submissionSelect.innerHTML = '<option value="">Select Submission</option>';
                submissions.forEach(sub => {
                    const opt = document.createElement('option');
                    opt.value = sub.id;
                    opt.textContent = sub.label;
                    submissionSelect.appendChild(opt);
                });
                submissionSelect.disabled = false;
                
                container.innerHTML = `
                    <div class="text-center p-5 text-muted">
                        <i class="bi bi-bar-chart-line" style="font-size: 2.5rem; opacity: 0.5;"></i>
                        <p class="mt-3 mb-0">Select a submission to view subject progress</p>
                    </div>`;
            });
    });
    
    // Submission change handler
    document.getElementById('submissionSelect').addEventListener('change', function() {
        const schoolId = document.getElementById('schoolSelect').value;
        const classId = document.getElementById('classSelect').value;
        const submissionId = this.value;
        
        if (!schoolId || !classId || !submissionId) {
            return;
        }
        
        fetchSubjectProgressData(schoolId, classId, submissionId);
    });
    
    function fetchSubjectProgressData(schoolId, classId, submissionId) {
        const container = document.getElementById('progressChartContainer');
        container.innerHTML = `
            <div class="d-flex justify-content-center p-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span class="ms-2">Loading subject progress data...</span>
            </div>`;
        
        fetch(`/training/analytics/subject-progress-data?school_id=${schoolId}&class_id=${classId}&submission_id=${submissionId}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    container.innerHTML = `
                        <div class="alert alert-danger m-4">
                            ${data.error}
                        </div>`;
                    return;
                }
                
                renderSubjectProgressChart(data);
            })
            .catch(error => {
                console.error('Error:', error);
                container.innerHTML = `
                    <div class="alert alert-danger m-4">
                        An error occurred while loading the data. Please try again.
                    </div>`;
            });
    }
    
    function renderSubjectProgressChart(data) {
        const container = document.getElementById('progressChartContainer');
        
        // Check if there's any data to display
        const hasData = data.subjects && data.subjects.some(subject => 
            subject.passed > 0 || subject.failed > 0 || subject.inc > 0 || 
            subject.dr > 0 || subject.nc > 0
        );

        // Create header with school, class, and submission info
        let headerHtml = `
            <div class="card-header bg-light p-3">
                <div class="d-flex flex-column align-items-center justify-content-center w-100">
                    <div style="font-size: 1.5rem; font-weight: 600; line-height: 1.2; text-align: center; margin-top: 10px;">
                        <br>
                        ${data.school.name} - ${data.class_name}
                    </div>
                    <div class="mt-2" style="font-size: 1rem; color: #6c757d; white-space: nowrap; text-align: center; margin-bottom: 1rem;">
                        ${data.submission.semester ? `Semester: ${data.submission.semester}` : ''}
                        ${data.submission.term ? ` | Term: ${data.submission.term}` : ''}
                        ${data.submission.academic_year ? ` | Academic Year: ${data.submission.academic_year}` : ''}
                    </div>
                    ${!hasData ? `
                    <div class="text-center py-2">
                        <i class="bi bi-exclamation-circle" style="font-size: 2.5rem; color: #6c757d; margin-bottom: 0.5rem;"></i>
                        <h5 class="mb-1" style="color: #6c757d;">No Data Available</h5>
                        <p class="text-muted mb-0">There are no approved grades for the selected submission.</p>
                    </div>
                    ` : ''}
                </div>
            </div>`;
            
        // Set the container HTML with header
        container.innerHTML = headerHtml;
        
        // Only proceed to create chart if there's data
        if (!hasData) {
            return;
        }
        
        // Add chart container
        const chartContainer = document.createElement('div');
        chartContainer.className = 'card-body p-0';
        chartContainer.innerHTML = `
            <div class="chart-container" style="position: relative; height: 500px; width: 100%;">
                <canvas id="subjectProgressChart"></canvas>
            </div>`;
        container.appendChild(chartContainer);
        
        // Debug: Log the data structure
        console.log('Subjects data:', data.subjects);
        
        // Prepare data for chart
        const subjects = data.subjects.map(s => {
            console.log('Subject:', s);
            return s.subject || 'Unknown Subject';
        });
        
        const passedData = data.subjects.map(s => parseInt(s.passed) || 0);
        const failedData = data.subjects.map(s => parseInt(s.failed) || 0);
        const incData = data.subjects.map(s => parseInt(s.inc) || 0);
        const drData = data.subjects.map(s => parseInt(s.dr) || 0);
        const ncData = data.subjects.map(s => parseInt(s.nc) || 0);
        
        console.log('Processed data:', { subjects, passedData, failedData, incData, drData, ncData });
        
        try {
            // Destroy previous chart if it exists
            if (progressChart) {
                progressChart.destroy();
            }
            
            // Get chart canvas
            const chartCanvas = document.getElementById('subjectProgressChart');
            if (!chartCanvas) {
                throw new Error('Chart canvas not found');
            }
            
            const ctx = chartCanvas.getContext('2d');
            if (!ctx) {
                throw new Error('Could not get 2D context for chart');
            }
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
                        title: {
                            display: true,
                            text: 'Subjects',
                            font: {
                                weight: 'bold'
                            }
                        },
                        stacked: true,
                        ticks: {
                            autoSkip: false,
                            maxRotation: 0,  // Set to 0 degrees (horizontal)
                            minRotation: 0,  // Set to 0 degrees (horizontal)
                            padding: 10,     // Add some padding
                            callback: function(value) {
                                // Split long subject names into multiple lines if needed
                                const maxLength = 15; // Max characters per line
                                if (this.getLabelForValue(value).length > maxLength) {
                                    return this.getLabelForValue(value).match(new RegExp(`.{1,${maxLength}}`, 'g')).join('\n');
                                }
                                return this.getLabelForValue(value);
                            }
                        },
                        grid: {
                            display: false // Hide vertical grid lines for cleaner look
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Number of Students',
                            font: {
                                weight: 'bold'
                            }
                        },
                        beginAtZero: true,
                        stacked: true,
                        max: 10, // Set maximum value to 10
                        min: 0,   // Ensure it starts at 0
                        ticks: {
                            stepSize: 1,  // Show every integer value
                            maxTicksLimit: 11, // 0 to 10 inclusive
                            callback: function(value) {
                                // Only show integer values
                                if (value % 1 === 0) {
                                    return value;
                                }
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y;
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
        } catch (error) {
            console.error('Error initializing chart:', error);
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-danger m-3';
            errorDiv.textContent = 'Error initializing chart: ' + error.message;
            container.appendChild(errorDiv);
        }
    }
});
</script>

<style>
.chart-container {
    min-height: 500px; /* Increased height to accommodate straight labels */
    padding-bottom: 40px; /* Add some bottom padding */
}

/* Ensure chart has enough space for x-axis labels */
.chartjs-size-monitor,
.chartjs-size-monitor-shrink,
.chartjs-size-monitor-expand {
    position: relative;
    overflow: visible !important;
}

/* Style for x-axis labels */
.chartjs-render-monitor text {
    white-space: normal !important;
    text-align: center;
    line-height: 1.2;
}

/* Ensure the chart is responsive */
canvas {
    max-width: 100%;
    height: auto !important;
}

/* Make sure the chart container doesn't overflow */
@media (max-width: 768px) {
    .chart-container {
        height: 500px !important;
    }
    
    /* Make sure the chart is scrollable on small screens */
    .table-responsive {
        overflow-x: auto;
    }
}

/* Style for the card header */
.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

/* Style for the status badges */
.badge {
    font-size: 0.8rem;
    padding: 0.35em 0.65em;
}

/* Custom scrollbar for the table */
.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Make sure the page doesn't have horizontal scroll */
body {
    overflow-x: hidden;
}
</style>
@endsection
