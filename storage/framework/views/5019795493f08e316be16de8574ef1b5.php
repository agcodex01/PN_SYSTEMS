<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <div class="analytics-container">
    <div class="dashboard-header">
        <h1>Internship Grades Analytics</h1>
        <div class="filters">
            <div class="filter-group">
                <label for="classFilter">Filter by Class:</label>
                <select id="classFilter" class="form-control styled-select">
                    <option value="">All Classes</option>
                    <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($class->class_id); ?>"><?php echo e($class->class_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="filter-group">
                <label for="companyFilter">Filter by Company:</label>
                <select id="companyFilter" class="form-control styled-select">
                    <option value="">All Companies</option>
                    <?php $__currentLoopData = $classCompanies[array_key_first($classCompanies)]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($company); ?>"><?php echo e($company); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="filter-group">
                <label for="submissionNumberFilter">Filter by Submission:</label>
                <select id="submissionNumberFilter" class="form-control styled-select">
                    <option value="">All Submissions</option>
                    <option value="1st">1st Submission</option>
                    <option value="2nd">2nd Submission</option>
                    <option value="3rd">3rd Submission</option>
                    <option value="4th">4th Submission</option>
                </select>
            </div>
        </div>
    </div>
    <hr>

    <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="chart-section" id="chart-section-<?php echo e($class->class_id); ?>">
        <div class="chart-header">
            <h3><?php echo e($class->class_name); ?></h3>
        </div>

        <!-- Charts for each submission number -->
        <div class="submission-charts" id="submission-charts-<?php echo e($class->class_id); ?>">
            <div class="submission-chart" id="submission-1st-<?php echo e($class->class_id); ?>" style="display: none;">
                <h4>1st Submission</h4>
                <div class="chart-container">
                    <canvas id="chart-1st-<?php echo e($class->class_id); ?>"></canvas>
                </div>
                <div class="no-data-message" id="no-data-1st-<?php echo e($class->class_id); ?>" style="display: none;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3>No Data Available</h3>
                    <p>There is no data available for the selected filters.</p>
                </div>
            </div>

            <div class="submission-chart" id="submission-2nd-<?php echo e($class->class_id); ?>" style="display: none;">
                <h4>2nd Submission</h4>
                <div class="chart-container">
                    <canvas id="chart-2nd-<?php echo e($class->class_id); ?>"></canvas>
                </div>
                <div class="no-data-message" id="no-data-2nd-<?php echo e($class->class_id); ?>" style="display: none;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3>No Data Available</h3>
                    <p>There is no data available for the selected filters.</p>
                </div>
            </div>

            <div class="submission-chart" id="submission-3rd-<?php echo e($class->class_id); ?>" style="display: none;">
                <h4>3rd Submission</h4>
                <div class="chart-container">
                    <canvas id="chart-3rd-<?php echo e($class->class_id); ?>"></canvas>
                </div>
                <div class="no-data-message" id="no-data-3rd-<?php echo e($class->class_id); ?>" style="display: none;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3>No Data Available</h3>
                    <p>There is no data available for the selected filters.</p>
                </div>
            </div>

            <div class="submission-chart" id="submission-4th-<?php echo e($class->class_id); ?>" style="display: none;">
                <h4>4th Submission</h4>
                <div class="chart-container">
                    <canvas id="chart-4th-<?php echo e($class->class_id); ?>"></canvas>
                </div>
                <div class="no-data-message" id="no-data-4th-<?php echo e($class->class_id); ?>" style="display: none;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3>No Data Available</h3>
                    <p>There is no data available for the selected filters.</p>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>

<style>
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

.dashboard-header {
    margin-bottom: 20px;
    margin-top: 20px;
}

.dashboard-header h1 {
    color: #2c3e50;
    font-size: 1.8rem;
    margin-bottom: 20px;
}

.filters {
    display: flex;
    gap: 20px;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-group label {
    font-weight: 500;
    color: #555;
    white-space: nowrap;
}

.chart-section {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    width: 100%;
    margin-bottom: 30px;
    box-sizing: border-box;
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.chart-header h3 {
    color: #2c3e50;
    font-size: 1.4rem;
    font-weight: 600;
    margin: 0;
}

.submission-charts {
    display: grid;
    gap: 20px;
    margin-top: 20px;
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

.submission-chart h4 {
    color: #495057;
    font-size: 1.1rem;
    margin-bottom: 15px;
    text-align: center;
}

.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
    box-sizing: border-box;
}

.styled-select {
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background-color: white;
    min-width: 200px;
    max-width: 100%;
}

.styled-select:disabled {
    background-color: #f3f4f6;
    cursor: not-allowed;
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

.no-data-message h3 {
    color: #495057;
    font-size: 1.2rem;
    margin-bottom: 8px;
}

.no-data-message p {
    color: #6c757d;
    margin: 0;
}

/* Add responsive styles */
@media (max-width: 768px) {
    .chart-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .styled-select {
        width: 100%;
    }

    .submission-charts {
        grid-template-columns: 1fr !important;
    }
}
</style>

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
        fetch(`<?php echo e(route('training.intern-grades-analytics.data')); ?>?${params.toString()}`, {
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
<?php echo $__env->make('layouts.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laravel\PNPH-CAPSTONE\resources\views/training/analytics/intern-grades-progress.blade.php ENDPATH**/ ?>