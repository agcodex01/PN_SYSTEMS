<?php $__env->startSection('content'); ?>

<div class="content-wrapper">
    <div class="analytics-container">
    <div class="header-section">
        <h1 style="font-weight: 300">📊 Internship Grades Analytics</h1>
        <hr>
        <p class="text-muted">View the internship grades distribution by competency for different submissions.</p>
    </div>

    <div class="filter-card">
        <div class="filter-card-header">
            <h5>
                <i class="bi bi-funnel me-2"></i>
                Filter Internship Grades
            </h5>
        </div>
        <div class="filter-card-body">
            <div class="filter-inline-container">
                <div class="filter-group">
                    <label for="classFilter">Class</label>
                    <select id="classFilter" class="styled-select">
                        <option value="">All Classes</option>
                        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($class->class_id); ?>"><?php echo e($class->class_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="companyFilter">Company</label>
                    <select id="companyFilter" class="styled-select">
                        <option value="">All Companies</option>
                        <?php $__currentLoopData = $classCompanies[array_key_first($classCompanies)]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($company); ?>"><?php echo e($company); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="submissionNumberFilter">Submission</label>
                    <select id="submissionNumberFilter" class="styled-select">
                        <option value="">All Submissions</option>
                        <option value="1st">1st Submission</option>
                        <option value="2nd">2nd Submission</option>
                        <option value="3rd">3rd Submission</option>
                        <option value="4th">4th Submission</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card shadow-sm chart-section" id="chart-section-<?php echo e($class->class_id); ?>">
        <div class="card-header">
            <h5 class="mb-0"><?php echo e($class->class_name); ?></h5>
        </div>
        <div class="card-body">
            <!-- Charts for each submission number -->
            <div class="submission-charts" id="submission-charts-<?php echo e($class->class_id); ?>">
                <div class="submission-chart" id="submission-1st-<?php echo e($class->class_id); ?>" style="display: none;">
                    <h6>1st Submission</h6>
                    <div class="chart-container">
                        <canvas id="chart-1st-<?php echo e($class->class_id); ?>"></canvas>
                    </div>
                    <div class="no-data-message" id="no-data-1st-<?php echo e($class->class_id); ?>" style="display: none;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h6>No Data Available</h6>
                        <p>There is no data available for the selected filters.</p>
                    </div>
                </div>

                <div class="submission-chart" id="submission-2nd-<?php echo e($class->class_id); ?>" style="display: none;">
                    <h6>2nd Submission</h6>
                    <div class="chart-container">
                        <canvas id="chart-2nd-<?php echo e($class->class_id); ?>"></canvas>
                    </div>
                    <div class="no-data-message" id="no-data-2nd-<?php echo e($class->class_id); ?>" style="display: none;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h6>No Data Available</h6>
                        <p>There is no data available for the selected filters.</p>
                    </div>
                </div>

                <div class="submission-chart" id="submission-3rd-<?php echo e($class->class_id); ?>" style="display: none;">
                    <h6>3rd Submission</h6>
                    <div class="chart-container">
                        <canvas id="chart-3rd-<?php echo e($class->class_id); ?>"></canvas>
                    </div>
                    <div class="no-data-message" id="no-data-3rd-<?php echo e($class->class_id); ?>" style="display: none;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h6>No Data Available</h6>
                        <p>There is no data available for the selected filters.</p>
                    </div>
                </div>

                <div class="submission-chart" id="submission-4th-<?php echo e($class->class_id); ?>" style="display: none;">
                    <h6>4th Submission</h6>
                    <div class="chart-container">
                        <canvas id="chart-4th-<?php echo e($class->class_id); ?>"></canvas>
                    </div>
                    <div class="no-data-message" id="no-data-4th-<?php echo e($class->class_id); ?>" style="display: none;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h6>No Data Available</h6>
                        <p>There is no data available for the selected filters.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>

<style>
/* Content Wrapper - matches training pattern */
.content-wrapper {
    margin-top: 70px;
    margin-left: 270px; /* Account for sidebar width + extra space */
    padding: 20px;
    min-height: 100vh;
}

.analytics-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.header-section {
    margin-bottom: 25px;
    padding-top: 10px;
}

.header-section h1 {
    font-weight: 300;
    color: #333;
    margin-bottom: 15px;
    font-size: 2rem;
    line-height: 1.2;
    padding-top: 5px;
}

.header-section hr {
    border: none;
    height: 1px;
    background-color: #ddd;
    margin-bottom: 15px;
}

.header-section .text-muted {
    color: #6c757d;
    font-size: 1rem;
    margin-bottom: 0;
}

/* Filter Card Styling - matches working educator analytics */
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

/* Filter Section Styling - matches working educator analytics */
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

.styled-select {
    padding: 8px 12px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    background-color: #fff;
    font-size: 14px;
}

.styled-select:focus {
    border-color: #22bbea;
    box-shadow: 0 0 0 0.2rem rgba(34, 187, 234, 0.25);
    outline: none;
}

.styled-select:disabled {
    background-color: #f8f9fa;
    color: #6c757d;
    cursor: not-allowed;
}

/* Card styling - matches working educator analytics */
.card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: none;
    margin-bottom: 20px;
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

/* Chart specific styling */
.submission-charts {
    display: grid;
    gap: 15px;
    margin-top: 15px;
    width: 100%;
    box-sizing: border-box;
}

.submission-chart {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
    border: 1px solid #e9ecef;
    width: 100%;
    box-sizing: border-box;
}

.submission-chart h6 {
    color: #495057;
    font-size: 1rem;
    margin-bottom: 15px;
    text-align: center;
    font-weight: 500;
}

.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
    box-sizing: border-box;
    background-color: white;
    border-radius: 4px;
    padding: 10px;
}

.no-data-message {
    display: none;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px;
    text-align: center;
    background-color: #f8f9fa;
    border-radius: 8px;
    margin-top: 20px;
}

.no-data-message svg {
    width: 48px;
    height: 48px;
    color: #6c757d;
    margin-bottom: 16px;
}

.no-data-message h6 {
    color: #495057;
    font-size: 1.1rem;
    margin-bottom: 8px;
}

.no-data-message p {
    color: #6c757d;
    margin: 0;
}

/* Responsive design */
@media (max-width: 768px) {
    .content-wrapper {
        margin-left: 0;
        margin-top: 60px;
        padding: 15px;
    }

    .analytics-container {
        padding: 15px;
    }

    .filter-inline-container {
        flex-direction: column;
        gap: 15px;
    }

    .filter-group {
        min-width: 100%;
    }

    .submission-charts {
        grid-template-columns: 1fr !important;
        gap: 10px;
    }

    .chart-container {
        height: 250px;
    }

    .header-section h1 {
        font-size: 1.7rem;
    }
}

@media (max-width: 480px) {
    .content-wrapper {
        padding: 10px;
    }

    .analytics-container {
        padding: 12px;
    }

    .chart-container {
        height: 220px;
    }

    .header-section h1 {
        font-size: 1.5rem;
    }
}
</style>

    <?php $__env->startSection('scripts'); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>

<?php $__env->startPush('scripts'); ?>
<!-- Include Chart.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>

<script>
    // Add CSRF token to all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Store all charts
    const charts = {};

    // Initialize charts for each class and submission number
    <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    ['1st', '2nd', '3rd', '4th'].forEach(submissionNumber => {
        const ctx = document.getElementById(`chart-${submissionNumber}-<?php echo e($class->class_id); ?>`).getContext('2d');
        charts[`${submissionNumber}-<?php echo e($class->class_id); ?>`] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['ICT Learning', '21st Century Skills', 'Expected Outputs'],
                datasets: [
                    {
                        label: 'Grade 1',
                        data: [0, 0, 0],
                        backgroundColor: '#10B981',
                        borderColor: '#10B981',
                        borderWidth: 1
                    },
                    {
                        label: 'Grade 2',
                        data: [0, 0, 0],
                        backgroundColor: '#F59E0B',
                        borderColor: '#F59E0B',
                        borderWidth: 1
                    },
                    {
                        label: 'Grade 3',
                        data: [0, 0, 0],
                        backgroundColor: '#F97316',
                        borderColor: '#F97316',
                        borderWidth: 1
                    },
                    {
                        label: 'Grade 4',
                        data: [0, 0, 0],
                        backgroundColor: '#EF4444',
                        borderColor: '#EF4444',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 10,
                        title: {
                            display: true,
                            text: 'Number of Students'
                        },
                        ticks: {
                            precision: 0,
                            stepSize: 2
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Competencies'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: `Internship Grades Distribution by Competency - ${submissionNumber} Submission`
                    }
                }
            }
        });
    });
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    // Function to update a specific chart
    function updateChart(classId, company, submissionNumber) {
        const chartContainer = document.querySelector(`#submission-${submissionNumber}-${classId} .chart-container`);
        const noDataMessage = document.getElementById(`no-data-${submissionNumber}-${classId}`);
        const submissionChart = document.getElementById(`submission-${submissionNumber}-${classId}`);

        // Build query parameters
        const params = new URLSearchParams();
        if (company) params.append('company', company);
        params.append('class_id', classId);
        params.append('submission_number', submissionNumber);

        // Make an AJAX call to get updated data
        fetch(`<?php echo e(route('educator.intern-grades-progress-data')); ?>?${params.toString()}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Received data for class', classId, 'with company', company, 'and submission', submissionNumber, ':', data);

            if (charts[`${submissionNumber}-${classId}`] && data.classChartData[classId]) {
                const chartData = data.classChartData[classId].chart_data;

                // Update the chart data
                charts[`${submissionNumber}-${classId}`].data.labels = chartData.labels;
                charts[`${submissionNumber}-${classId}`].data.datasets = chartData.datasets;
                charts[`${submissionNumber}-${classId}`].update();

                // Show/hide based on hasData flag
                if (chartData.hasData) {
                    chartContainer.style.display = 'block';
                    noDataMessage.style.display = 'none';
                    submissionChart.style.display = 'block';
                } else {
                    chartContainer.style.display = 'none';
                    noDataMessage.style.display = 'none';
                    submissionChart.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('Error updating chart:', error);
            alert('Failed to update chart data. Please try again.');
        });
    }

        // Function to update the layout of submission charts
    // Function to update submission charts layout
    function updateSubmissionChartsLayout(classId) {
        const submissionCharts = document.getElementById(`submission-charts-${classId}`);
        const visibleCharts = Array.from(submissionCharts.querySelectorAll('.submission-chart'))
            .filter(chart => chart.style.display !== 'none');

        // Always use block display for charts
        submissionCharts.style.display = 'block';

        // Update grid columns based on number of visible charts
        if (visibleCharts.length === 1) {
            submissionCharts.style.gridTemplateColumns = '1fr';
        } else if (visibleCharts.length === 2) {
            submissionCharts.style.gridTemplateColumns = 'repeat(2, 1fr)';
        } else if (visibleCharts.length === 3) {
            submissionCharts.style.gridTemplateColumns = 'repeat(3, 1fr)';
        } else if (visibleCharts.length === 4) {
            submissionCharts.style.gridTemplateColumns = 'repeat(2, 1fr)';
        }
    }

    // Add event listener to class filter
    document.getElementById('classFilter').addEventListener('change', function() {
        const selectedClassId = this.value;
        const company = document.getElementById('companyFilter').value;

        // Get all chart sections
        const sections = document.querySelectorAll('.chart-section');

        // Show/hide sections based on selection
        sections.forEach(section => {
            const sectionId = section.getAttribute('id');
            const sectionClassId = sectionId.replace('chart-section-', '');

            if (!selectedClassId || sectionClassId === selectedClassId) {
                section.style.display = 'block';
                // Update all submission charts for this class
                ['1st', '2nd', '3rd', '4th'].forEach(submissionNumber => {
                    updateChart(sectionClassId, company, submissionNumber);
                });
                // Ensure the submission charts container is visible
                const submissionCharts = document.getElementById(`submission-charts-${sectionClassId}`);
                if (submissionCharts) {
                    submissionCharts.style.display = 'block';
                }
                updateSubmissionChartsLayout(sectionClassId);
            } else {
                section.style.display = 'none';
            }
        });
    });

        // Add event listener to company filter
        document.getElementById('companyFilter').addEventListener('change', function() {
            const company = this.value;
            const selectedClassId = document.getElementById('classFilter').value;

            // Update charts for all visible classes
            document.querySelectorAll('.chart-section').forEach(section => {
                const sectionId = section.getAttribute('id');
                const sectionClassId = sectionId.replace('chart-section-', '');

                if (!selectedClassId || sectionClassId === selectedClassId) {
                    ['1st', '2nd', '3rd', '4th'].forEach(submissionNumber => {
                        updateChart(sectionClassId, company, submissionNumber);
                    });
                    updateSubmissionChartsLayout(sectionClassId);
                }
            });
        });

        // Add event listener to submission number filter
        document.getElementById('submissionNumberFilter').addEventListener('change', function() {
            const selectedSubmission = this.value;
            const selectedClassId = document.getElementById('classFilter').value;
            const company = document.getElementById('companyFilter').value;

            // Show/hide submission charts based on selection
            document.querySelectorAll('.submission-chart').forEach(chart => {
                const chartId = chart.getAttribute('id');
                const [_, submissionNumber, classId] = chartId.split('-');

                if (!selectedSubmission || submissionNumber === selectedSubmission) {
                    if (selectedClassId && classId === selectedClassId) {
                        updateChart(classId, company, submissionNumber);
                    }
                } else {
                    chart.style.display = 'none';
                }
            });

            // Update layout for each class
            document.querySelectorAll('.chart-section').forEach(section => {
                const sectionId = section.getAttribute('id');
                const sectionClassId = sectionId.replace('chart-section-', '');
                if (!selectedClassId || sectionClassId === selectedClassId) {
                    // Ensure the submission charts container is visible
                    const submissionCharts = document.getElementById(`submission-charts-${sectionClassId}`);
                    if (submissionCharts) {
                        submissionCharts.style.display = 'block';
                    }
                    updateSubmissionChartsLayout(sectionClassId);
                }
            });
        });

    // Initial load - update all charts and layouts
    document.querySelectorAll('.chart-section').forEach(section => {
        const sectionId = section.getAttribute('id');
        const sectionClassId = sectionId.replace('chart-section-', '');
        const company = document.getElementById('companyFilter').value;
        ['1st', '2nd', '3rd', '4th'].forEach(submissionNumber => {
            updateChart(sectionClassId, company, submissionNumber);
        });
        // Ensure the submission charts container is visible
        const submissionCharts = document.getElementById(`submission-charts-${sectionClassId}`);
        if (submissionCharts) {
            submissionCharts.style.display = 'block';
        }
        updateSubmissionChartsLayout(sectionClassId);
    });
</script>


<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.educator_layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laravel\PNPH-CAPSTONE\resources\views/educator/analytics/intern-grades-progress.blade.php ENDPATH**/ ?>