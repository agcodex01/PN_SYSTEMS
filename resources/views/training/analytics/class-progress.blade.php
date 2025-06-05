@extends('layouts.nav')

@section('content')

<div class="page-container">
    <div class="header-section">
        <h1 style= "font-weight: 300">📊 Class Progress</h1>
        <hr>
        <p class="text-muted">View the progress distribution of students in a class for a specific submission.</p>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 ">
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

    <!-- Header information section moved above the graph -->
    <div id="headerInfo" class="text-center mb-4" style="display: none;">
        <h4 class="mb-2" id="schoolClassInfo"></h4>
        <p class="text-muted mb-0" id="submissionInfo"></p>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div id="progressChartContainer" class="w-100" style="min-height: 600px; display: flex; justify-content: center; align-items: center; flex-direction: column;">
                <div class="text-center text-muted w-100">
                    <div style="width: 100%; max-width: 800px; margin: 0 auto; padding: 2rem;">
                        <i class="bi bi-pie-chart" style="font-size: 3rem; color: #6c757d; opacity: 0.7; display: block; margin: 0 auto 1rem;"></i>
                        <p class="instruction-text" style="margin: 0; padding: 0 1rem;">Select a school, class, and submission to view class progress chart</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const schoolSelect = document.getElementById('schoolSelect');
    const classSelect = document.getElementById('classSelect');
    const submissionSelect = document.getElementById('submissionSelect');
    const chartContainer = document.getElementById('progressChartContainer');

    let myChart = null; // To hold the Chart.js instance
    let selectedSchoolName = ''; // Store the selected school name

    // Function to initialize the chart area with placeholder/loading state
    function resetChartArea(message = 'Select a school, class, and submission to view class progress chart', showSpinner = false) {
        if (myChart) {
            myChart.destroy(); // Destroy existing chart instance
            myChart = null;
        }
        chartContainer.innerHTML = `
            <div class="text-center text-muted w-100 p-5">
                ${showSpinner ? '<div class="spinner-border text-primary mb-3" role="status"><span class="visually-hidden">Loading...</span></div>' : '<i class="bi bi-pie-chart" style="font-size: 3rem; color: #6c757d; opacity: 0.7; display: block; margin: 0 auto 1rem;"></i>'}
                <p class="instruction-text" style="margin: 0; padding: 0 1rem;">${message}</p>
            </div>
        `;
        chartContainer.style.minHeight = '400px';
        chartContainer.style.display = 'flex';
        chartContainer.style.flexDirection = 'column';
        chartContainer.style.justifyContent = 'center';
        chartContainer.style.alignItems = 'center';
    }

    // Initial reset
    resetChartArea();

    // Fetch schools
    fetch('/training/analytics/schools')
        .then(res => res.json())
        .then(schools => {
            schools.forEach(school => {
                const opt = document.createElement('option');
                opt.value = school.id;
                opt.textContent = school.name;
                schoolSelect.appendChild(opt);
            });
        });

    // Handle school selection change
    schoolSelect.addEventListener('change', function() {
        const schoolId = this.value;
        selectedSchoolName = this.options[this.selectedIndex].text; // Store the selected school name
        classSelect.innerHTML = '<option value="">Select Class</option>';
        submissionSelect.innerHTML = '<option value="">Select Submission</option>';
        classSelect.disabled = true;
        submissionSelect.disabled = true;
        resetChartArea('Loading classes...', true);
        document.getElementById('headerInfo').style.display = 'none';

        if (schoolId) {
            fetch(`/training/analytics/classes/${schoolId}`)
                .then(res => res.json())
                .then(classes => {
                    if (classes.length === 0) {
                        classSelect.innerHTML = '<option value="">No classes found</option>';
                        resetChartArea('No classes found for this school.');
                    } else {
                        classes.forEach(cls => {
                            const opt = document.createElement('option');
                            opt.value = cls.id;
                            opt.textContent = cls.name;
                            classSelect.appendChild(opt);
                        });
                        resetChartArea('Select a class and submission to view class progress chart');
                    }
                    classSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error loading classes:', error);
                    resetChartArea('Failed to load classes. Please try again.');
                });
        } else {
            resetChartArea();
        }
    });

    // Handle class selection change
    classSelect.addEventListener('change', function() {
        const schoolId = schoolSelect.value;
        const classId = this.value;
        submissionSelect.innerHTML = '<option value="">Select Submission</option>';
        submissionSelect.disabled = true;
        resetChartArea('Loading submissions...', true);

        if (schoolId && classId) {
            fetch(`/training/analytics/class-submissions/${schoolId}/${classId}`)
                .then(res => res.json())
                .then(submissions => {
                    if (!submissions || submissions.length === 0) {
                        submissionSelect.innerHTML = '<option value="">No submissions found</option>';
                         resetChartArea('No submissions found for this class.');
                    } else {
                         // Sort submissions: approved first, then by created_at desc
                            const sortedData = [...submissions].sort((a, b) => {
                                if (a.status === 'approved' && b.status !== 'approved') return -1;
                                if (a.status !== 'approved' && b.status === 'approved') return 1;
                                return 0;
                            });

                        sortedData.forEach(sub => {
                             if (sub && sub.id && sub.label) {
                                    const opt = document.createElement('option');
                                    opt.value = sub.id;
                                    // Extract just the Semester, Term, and Year from the label
                                    const labelParts = sub.label.split(' | ');
                                    opt.textContent = labelParts.join(' | '); // Use the formatted label from backend

                                    opt.disabled = false; // Always enable for selection
                                    submissionSelect.appendChild(opt);
                                }
                        });
                         resetChartArea('Select a submission to view class progress chart');
                    }
                    submissionSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error loading submissions:', error);
                    resetChartArea('Failed to load submissions. Please try again.');
                });
        } else {
             resetChartArea('Select a class and submission to view class progress chart');
        }
    });

    // Handle submission selection change and fetch data
    submissionSelect.addEventListener('change', function() {
        const schoolId = schoolSelect.value;
        const classId = classSelect.value;
        const submissionId = this.value;

        if (schoolId && classId && submissionId) {
            fetchProgressData(schoolId, classId, submissionId);
        } else {
            resetChartArea('Select a submission to view class progress chart');
        }
    });

    // Function to update header information
    function updateHeaderInfo(data) {
        const headerInfo = document.getElementById('headerInfo');
        const schoolClassInfo = document.getElementById('schoolClassInfo');
        const submissionInfo = document.getElementById('submissionInfo');

        if (data && data.class_name && data.submission_details) {
            schoolClassInfo.textContent = `${selectedSchoolName} - ${data.class_name}`;
            submissionInfo.textContent = `Semester: ${data.submission_details.semester} | Term: ${data.submission_details.term} | Academic Year: ${data.submission_details.academic_year}`;
            headerInfo.style.display = 'block';
        } else {
            headerInfo.style.display = 'none';
        }
    }

    // Modify the fetchProgressData function to update header info
    function fetchProgressData(schoolId, classId, submissionId) {
        resetChartArea('Loading class progress data...', true);

        fetch(`/training/analytics/class-progress-data?school_id=${schoolId}&class_id=${classId}&submission_id=${submissionId}`)
            .then(async res => {
                const contentType = res.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await res.text();
                    console.error('Non-JSON response:', text);
                    throw new Error('Server returned non-JSON response. Please try again.');
                }

                if (!res.ok) {
                    const errorData = await res.json();
                    throw new Error(errorData.message || `HTTP error! status: ${res.status}`);
                }

                return res.json();
            })
            .then(data => {
                if (data.error) {
                    resetChartArea(`Error: ${data.error}`);
                    updateHeaderInfo(null);
                } else if (data.submission_status === 'not_found') {
                    resetChartArea('Submission not found.');
                    updateHeaderInfo(null);
                } else if (data.total_students === 0) {
                    resetChartArea('No students found for this class and submission.');
                    updateHeaderInfo(null);
                } else {
                    updateHeaderInfo(data);
                    renderProgressChart(data);
                }
            })
            .catch(error => {
                console.error('Error fetching progress data:', error);
                resetChartArea(`Error fetching data: ${error.message}`);
                updateHeaderInfo(null);
            });
    }

    // Function to render the pie chart
    function renderProgressChart(data) {
        chartContainer.innerHTML = ''; // Clear previous content
        chartContainer.style.minHeight = '600px'; // Increased height
        chartContainer.style.display = 'block';

        const canvas = document.createElement('canvas');
        canvas.style.width = '100%';
        canvas.style.height = '500px'; // Set explicit height for canvas
        chartContainer.appendChild(canvas);

        const ctx = canvas.getContext('2d');

        // Define colors for the chart slices
        const backgroundColors = ['#198754', '#dc3545', '#ffc107', '#6c757d']; // Green, Red, Orange, Muted
        const borderColors = ['#ffffff', '#ffffff', '#ffffff', '#ffffff'];

        myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: data.labels.map((label, index) => {
                    const count = data.counts[label];
                    const percentage = data.data[index];
                    return `${label}: ${count} (${percentage}%)`;
                }),
                datasets: [{
                    data: data.data,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 14 // Increased font size for legend
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Class Progress',
                        font: {
                            size: 16 // Increased font size for title
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label;
                            }
                        }
                    }
                },
                layout: {
                    padding: 30 // Increased padding around the chart
                }
            }
        });
    }

    // Basic styles for instruction text and spinner (can be moved to CSS file)
    const style = document.createElement('style');
    style.innerHTML = `
        .instruction-text {
            font-size: 1.1rem;
            color: #495057;
            font-weight: 500;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }
        .bi-pie-chart {
             color: #6c757d;
             opacity: 0.7;
             transition: all 0.3s ease;
             margin: 0 auto;
             display: block;
        }
        .text-center:hover .bi-pie-chart {
            transform: scale(1.1);
            opacity: 0.9;
        }
         .spinner-border {
            display: inline-block;
            width: 2rem;
            height: 2rem;
            vertical-align: -0.125em;
            border: 0.25em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            -webkit-animation: .75s linear infinite spinner-border;
            animation: .75s linear infinite spinner-border;
        }
        @keyframes spinner-border {
            to { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);

});
</script>

@endsection 