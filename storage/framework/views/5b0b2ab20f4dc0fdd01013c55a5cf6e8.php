<?php $__env->startSection('content'); ?>
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

    <style>
        .dashboard-header {
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .filters {
            display: flex;
            gap: 20px;
            margin-top: 15px;
        }

        .filter-group {
            flex: 1;
        }

        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .styled-select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
        }

        .chart-section {
            margin-bottom: 30px;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .chart-header {
            margin-bottom: 20px;
        }

        .submission-charts {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .submission-chart {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 15px;
        }

        .no-data-message {
            text-align: center;
            padding: 40px 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .no-data-message svg {
            width: 48px;
            height: 48px;
            color: #6c757d;
            margin-bottom: 15px;
        }

        .no-data-message h3 {
            color: #343a40;
            margin-bottom: 10px;
        }

        .no-data-message p {
            color: #6c757d;
            margin: 0;
        }
    </style>

    <?php $__env->startSection('scripts'); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                console.log('Received data:', data);
                // Update the chart with new data
                const chart = charts[`${submissionNumber}-${classId}`];
                const chartData = data.classChartData[classId].chart_data;
                console.log('Chart data for class:', classId, chartData);
                
                chart.data.datasets = chartData.datasets;
                chart.update();

                // Show/hide chart and no-data message
                if (chartData.hasData) {
                    chartContainer.style.display = 'block';
                    noDataMessage.style.display = 'none';
                } else {
                    chartContainer.style.display = 'none';
                    noDataMessage.style.display = 'block';
                }
                submissionChart.style.display = 'block';

                // Update the layout of submission charts
                updateSubmissionChartsLayout(classId);
            })
            .catch(error => {
                console.error('Error fetching chart data:', error);
                chartContainer.style.display = 'none';
                noDataMessage.style.display = 'block';
                submissionChart.style.display = 'block';
            });
        }

        // Function to update the layout of submission charts
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
            const selectedClass = this.value;
            const selectedCompany = document.getElementById('companyFilter').value;
            const selectedSubmission = document.getElementById('submissionNumberFilter').value;

            // Update company options based on selected class
            if (selectedClass) {
                fetch(`<?php echo e(route('educator.intern-grades-progress-data')); ?>?class_id=${selectedClass}`)
                    .then(response => response.json())
                    .then(data => {
                        const companyFilter = document.getElementById('companyFilter');
                        companyFilter.innerHTML = '<option value="">All Companies</option>';
                        
                        if (data.classChartData[selectedClass]) {
                            const companies = data.classChartData[selectedClass].companies || [];
                            companies.forEach(company => {
                                const option = document.createElement('option');
                                option.value = company;
                                option.textContent = company;
                                companyFilter.appendChild(option);
                            });
                        }
                    });
            }

            // Update all charts for the selected class
            if (selectedClass) {
                ['1st', '2nd', '3rd', '4th'].forEach(submissionNumber => {
                    if (!selectedSubmission || selectedSubmission === submissionNumber) {
                        updateChart(selectedClass, selectedCompany, submissionNumber);
                    }
                });
            }
        });

        // Add event listener to company filter
        document.getElementById('companyFilter').addEventListener('change', function() {
            const selectedClass = document.getElementById('classFilter').value;
            const selectedCompany = this.value;
            const selectedSubmission = document.getElementById('submissionNumberFilter').value;

            if (selectedClass) {
                ['1st', '2nd', '3rd', '4th'].forEach(submissionNumber => {
                    if (!selectedSubmission || selectedSubmission === submissionNumber) {
                        updateChart(selectedClass, selectedCompany, submissionNumber);
                    }
                });
            }
        });

        // Add event listener to submission number filter
        document.getElementById('submissionNumberFilter').addEventListener('change', function() {
            const selectedClass = document.getElementById('classFilter').value;
            const selectedCompany = document.getElementById('companyFilter').value;
            const selectedSubmission = this.value;

            if (selectedClass) {
                if (selectedSubmission) {
                    // Show only the selected submission chart
                    ['1st', '2nd', '3rd', '4th'].forEach(submissionNumber => {
                        const chart = document.getElementById(`submission-${submissionNumber}-${selectedClass}`);
                        if (submissionNumber === selectedSubmission) {
                            updateChart(selectedClass, selectedCompany, submissionNumber);
                        } else {
                            chart.style.display = 'none';
                        }
                    });
                } else {
                    // Show all submission charts
                    ['1st', '2nd', '3rd', '4th'].forEach(submissionNumber => {
                        updateChart(selectedClass, selectedCompany, submissionNumber);
                    });
                }
            }
        });

        // Initialize charts for the first class if available
        const firstClass = document.querySelector('.chart-section');
        if (firstClass) {
            const classId = firstClass.id.split('-')[2];
            document.getElementById('classFilter').value = classId;
            ['1st', '2nd', '3rd', '4th'].forEach(submissionNumber => {
                updateChart(classId, '', submissionNumber);
            });
        }
    </script>
    <?php $__env->stopSection(); ?>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.educator_layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laravel\PNPH-CAPSTONE\resources\views/educator/analytics/intern-grades-progress.blade.php ENDPATH**/ ?>