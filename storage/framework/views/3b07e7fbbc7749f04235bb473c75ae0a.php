<?php $__env->startSection('content'); ?>

<div class="page-container">
    <div class="header-section">
        <h1 style="font-weight: 300">📊 Class Progress Analytics</h1>
        <hr>
        <p class="text-muted">View and analyze class progress over time. Select a school, class, and submissions to compare progress.</p>
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

    <div id="headerInfo" class="card shadow-sm mb-4" style="display: none;">
        <div class="card-body">
            <h5 class="card-title mb-1" id="schoolClassInfo"></h5>
            <p class="card-text text-muted mb-0" id="submissionInfo"></p>
        </div>
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
    fetch('/educator/analytics/schools')
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
            fetch(`/educator/analytics/classes/${schoolId}`)
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
        document.getElementById('headerInfo').style.display = 'none';

        if (classId) {
            fetch(`/educator/analytics/class-submissions/${schoolId}/${classId}`)
                .then(res => res.json())
                .then(submissions => {
                    if (submissions.length === 0) {
                        submissionSelect.innerHTML = '<option value="">No submissions found</option>';
                        resetChartArea('No submissions found for this class.');
                    } else {
                        submissions.forEach(submission => {
                            const opt = document.createElement('option');
                            opt.value = submission.id;
                            opt.textContent = submission.label;
                            submissionSelect.appendChild(opt);
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

    // Function to fetch and display progress data
    function fetchProgressData(schoolId, classId, submissionId) {
        resetChartArea('Loading class progress data...', true);

        fetch(`/educator/analytics/class-progress-data?school_id=${schoolId}&class_id=${classId}&submission_id=${submissionId}`)
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
                    resetChartArea(data.error);
                    updateHeaderInfo(null);
                    return;
                }

                if (data.submission_status === 'not_found') {
                    resetChartArea('Submission not found.');
                    updateHeaderInfo(null);
                    return;
                }

                if (data.total_students === 0) {
                    resetChartArea('No students found for this class and submission.');
                    updateHeaderInfo(null);
                    return;
                }

                // Update header info
                updateHeaderInfo(data);

                // Create chart container with fixed dimensions
                chartContainer.innerHTML = `
                    <div style="width: 100%; height: 500px; position: relative;">
                        <canvas id="classProgressChart"></canvas>
                    </div>`;

                // Destroy previous chart if it exists
                if (myChart) {
                    myChart.destroy();
                }

                // Create new chart
                const ctx = document.getElementById('classProgressChart').getContext('2d');
                console.log('Chart data:', data);
                console.log('Labels:', data.labels);
                console.log('Values:', data.data);
                console.log('Counts:', data.counts);

                myChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.data,
                            backgroundColor: [
                                'rgba(40, 167, 69, 0.7)',  // Passed - Green
                                'rgba(220, 53, 69, 0.7)',  // Failed - Red
                                'rgba(255, 193, 7, 0.7)',  // Pending - Yellow
                                'rgba(108, 117, 125, 0.7)' // No Grades - Gray
                            ],
                            borderColor: [
                                'rgba(40, 167, 69, 1)',
                                'rgba(220, 53, 69, 1)',
                                'rgba(255, 193, 7, 1)',
                                'rgba(108, 117, 125, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: 20
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    font: {
                                        size: 14
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const count = data.counts[label] || 0;
                                        return `${label}: ${value}% (${count} students)`;
                                    }
                                }
                            }
                        }
                    }
                });

                // Log chart instance
                console.log('Chart instance:', myChart);
            })
            .catch(error => {
                console.error('Error:', error);
                resetChartArea(error.message || 'Failed to load class progress data. Please try again.');
                updateHeaderInfo(null);
            });
    }
});
</script>

<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.educator_layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laravel\PNPH-CAPSTONE\resources\views/educator/analytics/class-progress.blade.php ENDPATH**/ ?>