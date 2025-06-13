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
                        <?php $__currentLoopData = $allClasses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($class->class_id); ?>"><?php echo e($class->class_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="companyFilter">Company</label>
                    <select id="companyFilter" class="styled-select">
                        <option value="">All Companies</option>
                        <?php
                            $allCompanies = collect($allClassCompanies)->flatten()->unique()->sort();
                        ?>
                        <?php $__currentLoopData = $allCompanies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($company); ?>"><?php echo e($company); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

            </div>
        </div>
    </div>

    <!-- Class Pagination -->
    <?php if($classPagination->has_pages): ?>
        <div class="class-pagination-container">
            <div class="class-pagination-info">
                <small class="text-muted">
                    Showing class <?php echo e($classPagination->from); ?> to <?php echo e($classPagination->to); ?> of <?php echo e($classPagination->total); ?> classes
                </small>
            </div>
            <div class="class-pagination-links">
                <?php if($classPagination->on_first_page): ?>
                    <span class="pagination-btn disabled">
                        <i class="fas fa-chevron-left"></i> Previous Class
                    </span>
                <?php else: ?>
                    <?php
                        $prevPage = $classPagination->current_page - 1;
                        $currentUrl = request()->fullUrlWithQuery(['class_page' => $prevPage]);
                    ?>
                    <a href="<?php echo e($currentUrl); ?>" class="pagination-btn">
                        <i class="fas fa-chevron-left"></i> Previous Class
                    </a>
                <?php endif; ?>

                <span class="page-info">
                    Class <?php echo e($classPagination->current_page); ?> of <?php echo e($classPagination->last_page); ?>

                </span>

                <?php if($classPagination->has_more_pages): ?>
                    <?php
                        $nextPage = $classPagination->current_page + 1;
                        $currentUrl = request()->fullUrlWithQuery(['class_page' => $nextPage]);
                    ?>
                    <a href="<?php echo e($currentUrl); ?>" class="pagination-btn">
                        Next Class <i class="fas fa-chevron-right"></i>
                    </a>
                <?php else: ?>
                    <span class="pagination-btn disabled">
                        Next Class <i class="fas fa-chevron-right"></i>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php $__currentLoopData = $paginatedClasses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card shadow-sm chart-section" id="chart-section-<?php echo e($class->class_id); ?>">
        <div class="card-header">
            <h5 class="mb-0"><?php echo e($class->class_name); ?></h5>
        </div>
        <div class="card-body">
            <!-- Charts for current submission only -->
            <div class="submission-charts" id="submission-charts-<?php echo e($class->class_id); ?>">
                <?php $__currentLoopData = $currentSubmissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $submissionNumber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="submission-chart" id="submission-<?php echo e($submissionNumber); ?>-<?php echo e($class->class_id); ?>">
                    <h6>
                        <?php echo e($submissionNumber); ?> Submission
                        <span class="submission-company" id="company-label-<?php echo e($submissionNumber); ?>-<?php echo e($class->class_id); ?>">(All Companies)</span>
                    </h6>
                    <div class="chart-container">
                        <canvas id="chart-<?php echo e($submissionNumber); ?>-<?php echo e($class->class_id); ?>"></canvas>
                    </div>
                    <div class="no-data-message" id="no-data-<?php echo e($submissionNumber); ?>-<?php echo e($class->class_id); ?>" style="display: none;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h6>No Data Available</h6>
                        <p>There is no internship grades data available for this <?php echo e($submissionNumber); ?> submission with the current class and company filters.</p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <!-- Submission Pagination -->
    <?php if($submissionPagination->has_pages): ?>
        <div class="submission-pagination-container">
            <div class="submission-pagination-info">
                <small class="text-muted">
                    Showing submission <?php echo e($submissionPagination->from); ?> to <?php echo e($submissionPagination->to); ?> of <?php echo e($submissionPagination->total); ?> submissions
                </small>
            </div>
            <div class="submission-pagination-links">
                <?php if($submissionPagination->on_first_page): ?>
                    <span class="pagination-btn disabled">
                        <i class="fas fa-chevron-left"></i> Previous Submission
                    </span>
                <?php else: ?>
                    <?php
                        $prevPage = $submissionPagination->current_page - 1;
                        $currentUrl = request()->fullUrlWithQuery(['submission_page' => $prevPage]);
                    ?>
                    <a href="<?php echo e($currentUrl); ?>" class="pagination-btn">
                        <i class="fas fa-chevron-left"></i> Previous Submission
                    </a>
                <?php endif; ?>

                <span class="page-info">
                    Submission <?php echo e($submissionPagination->current_page); ?> of <?php echo e($submissionPagination->last_page); ?>

                </span>

                <?php if($submissionPagination->has_more_pages): ?>
                    <?php
                        $nextPage = $submissionPagination->current_page + 1;
                        $currentUrl = request()->fullUrlWithQuery(['submission_page' => $nextPage]);
                    ?>
                    <a href="<?php echo e($currentUrl); ?>" class="pagination-btn">
                        Next Submission <i class="fas fa-chevron-right"></i>
                    </a>
                <?php else: ?>
                    <span class="pagination-btn disabled">
                        Next Submission <i class="fas fa-chevron-right"></i>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>


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

.submission-company {
    color: #6c757d;
    font-size: 0.85em;
    font-weight: 400;
    margin-left: 8px;
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

/* Pagination Styles */
.class-pagination-container,
.submission-pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background-color: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    margin: 20px 0;
}

.class-pagination-info,
.submission-pagination-info {
    color: #6c757d;
    font-size: 0.875rem;
}

.class-pagination-links,
.submission-pagination-links {
    display: flex;
    align-items: center;
    gap: 15px;
}

.pagination-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 8px 16px;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    font-size: 0.875rem;
    font-weight: 500;
    transition: background-color 0.3s ease;
    border: none;
    cursor: pointer;
}

.pagination-btn:hover {
    background-color: #0056b3;
    color: white;
    text-decoration: none;
}

.pagination-btn.disabled {
    background-color: #6c757d;
    color: #adb5bd;
    cursor: not-allowed;
}

.pagination-btn.disabled:hover {
    background-color: #6c757d;
    color: #adb5bd;
}

.page-info {
    font-weight: 600;
    color: #495057;
    font-size: 0.875rem;
    padding: 0 10px;
}

@media (max-width: 768px) {
    .class-pagination-container,
    .submission-pagination-container {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }

    .class-pagination-links,
    .submission-pagination-links {
        justify-content: center;
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
    <?php $__currentLoopData = $paginatedClasses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $__currentLoopData = $currentSubmissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $submissionNumber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        const ctx<?php echo e($class->class_id); ?><?php echo e($submissionNumber); ?> = document.getElementById(`chart-<?php echo e($submissionNumber); ?>-<?php echo e($class->class_id); ?>`);
        if (ctx<?php echo e($class->class_id); ?><?php echo e($submissionNumber); ?>) {
            const ctx = ctx<?php echo e($class->class_id); ?><?php echo e($submissionNumber); ?>.getContext('2d');
            charts[`<?php echo e($submissionNumber); ?>-<?php echo e($class->class_id); ?>`] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['ICT Learning', '21st Century Skills', 'Expected Outputs'],
                datasets: [
                    {
                        label: '1 - Fully Achieved',
                        data: [0, 0, 0],
                        backgroundColor: '#10B981',
                        borderColor: '#10B981',
                        borderWidth: 1
                    },
                    {
                        label: '2 - Partially Achieved',
                        data: [0, 0, 0],
                        backgroundColor: '#F59E0B',
                        borderColor: '#F59E0B',
                        borderWidth: 1
                    },
                    {
                        label: '3 - Barely Achieved',
                        data: [0, 0, 0],
                        backgroundColor: '#F97316',
                        borderColor: '#F97316',
                        borderWidth: 1
                    },
                    {
                        label: '4 - No Achievement',
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
                        max: 70,
                        title: {
                            display: true,
                            text: 'Number of Students'
                        },
                        ticks: {
                            precision: 0,
                            stepSize: 5
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
                        text: `Internship Grades Distribution by Competency - <?php echo e($submissionNumber); ?> Submission`
                    }
                }
            }
            });
        }
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    // Function to update company dropdown based on selected class
    function updateCompanyDropdown(selectedClassId) {
        const companyFilter = document.getElementById('companyFilter');
        const classCompanies = <?php echo json_encode($allClassCompanies, 15, 512) ?>;

        console.log('Updating company dropdown for class:', selectedClassId);
        console.log('Available class companies:', classCompanies);

        // Clear current options except "All Companies"
        companyFilter.innerHTML = '<option value="">All Companies</option>';

        if (selectedClassId && classCompanies[selectedClassId]) {
            const classCompaniesArray = classCompanies[selectedClassId];

            console.log('Companies for selected class:', classCompaniesArray);

            if (classCompaniesArray.length > 0) {
                // Enable dropdown and add companies for the selected class
                companyFilter.disabled = false;
                classCompaniesArray.forEach(company => {
                    const option = document.createElement('option');
                    option.value = company;
                    option.textContent = company;
                    companyFilter.appendChild(option);
                });
            } else {
                // Disable dropdown if no companies for this class
                companyFilter.disabled = true;
                companyFilter.innerHTML = '<option value="">No companies available</option>';
            }

        } else {
            // If no class selected, show all companies from all classes
            const allCompanies = new Set();
            let hasAnyCompanies = false;

            Object.values(classCompanies).forEach(companies => {
                if (companies.length > 0) {
                    hasAnyCompanies = true;
                    companies.forEach(company => allCompanies.add(company));
                }
            });

            if (hasAnyCompanies) {
                companyFilter.disabled = false;
                Array.from(allCompanies).sort().forEach(company => {
                    const option = document.createElement('option');
                    option.value = company;
                    option.textContent = company;
                    companyFilter.appendChild(option);
                });
            } else {
                companyFilter.disabled = true;
                companyFilter.innerHTML = '<option value="">No companies available</option>';
            }
        }
    }



    // Function to update company labels
    function updateCompanyLabels(company) {
        const companyText = company || 'All Companies';
        document.querySelectorAll('[id^="company-label-"]').forEach(label => {
            label.textContent = `(${companyText})`;
        });
    }

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
        fetch(`<?php echo e(route('educator.intern-grades-analytics.data')); ?>?${params.toString()}`, {
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
                    noDataMessage.style.display = 'flex';
                    submissionChart.style.display = 'block';
                }
            }
        })
        .catch(error => {
            console.error('Error updating chart:', error);
            // Show no data message on error
            chartContainer.style.display = 'none';
            noDataMessage.style.display = 'flex';
            submissionChart.style.display = 'block';
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

        // Update company dropdown based on selected class
        updateCompanyDropdown(selectedClassId);

        // If a specific class is selected, navigate to that class's page
        if (selectedClassId) {
            // Find which page contains this class
            const allClasses = <?php echo json_encode($allClasses->pluck('class_id'), 15, 512) ?>;
            const classIndex = allClasses.indexOf(selectedClassId);

            if (classIndex !== -1) {
                const targetPage = classIndex + 1; // Pages are 1-indexed
                const currentUrl = new URL(window.location);
                currentUrl.searchParams.set('class_page', targetPage);

                // Preserve current filter values
                const companyValue = document.getElementById('companyFilter').value;

                if (companyValue) {
                    currentUrl.searchParams.set('company', companyValue);
                }

                console.log('Navigating to page', targetPage, 'for class', selectedClassId);
                window.location.href = currentUrl.toString();
                return;
            }
        }

        // If "All Classes" is selected, show all sections on current page
        const sections = document.querySelectorAll('.chart-section');

        // Show/hide sections based on selection
        sections.forEach(section => {
            const sectionId = section.getAttribute('id');
            const sectionClassId = sectionId.replace('chart-section-', '');

            if (!selectedClassId || sectionClassId === selectedClassId) {
                section.style.display = 'block';
                // Update current submission charts for this class
                const currentSubmissions = <?php echo json_encode($currentSubmissions, 15, 512) ?>;
                currentSubmissions.forEach(submissionNumber => {
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

            console.log('Company filter changed to:', company);

            // Update company labels
            updateCompanyLabels(company);

            // Update charts for all visible classes
            document.querySelectorAll('.chart-section').forEach(section => {
                const sectionId = section.getAttribute('id');
                const sectionClassId = sectionId.replace('chart-section-', '');

                if (!selectedClassId || sectionClassId === selectedClassId) {
                    const currentSubmissions = <?php echo json_encode($currentSubmissions, 15, 512) ?>;
                    currentSubmissions.forEach(submissionNumber => {
                        console.log('Updating chart for company filter - Class:', sectionClassId, 'Company:', company, 'Submission:', submissionNumber);
                        updateChart(sectionClassId, company, submissionNumber);
                    });
                    updateSubmissionChartsLayout(sectionClassId);
                }
            });
        });



    // Set the class filter to the current class being displayed (for pagination)
    const currentClassId = <?php echo json_encode($paginatedClasses->first()->class_id ?? null, 15, 512) ?>;
    if (currentClassId) {
        const classFilter = document.getElementById('classFilter');
        classFilter.value = currentClassId;

        // Update company dropdown for the current class
        updateCompanyDropdown(currentClassId);
    }

    // Restore filter values from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const companyParam = urlParams.get('company');

    if (companyParam) {
        const companyFilter = document.getElementById('companyFilter');
        companyFilter.value = companyParam;
        updateCompanyLabels(companyParam);
    }

    // Initial load - update all charts and layouts with current filter values
    document.querySelectorAll('.chart-section').forEach(section => {
        const sectionId = section.getAttribute('id');
        const sectionClassId = sectionId.replace('chart-section-', '');
        const company = document.getElementById('companyFilter').value;
        const currentSubmissions = <?php echo json_encode($currentSubmissions, 15, 512) ?>;

        currentSubmissions.forEach(submissionNumber => {
            updateChart(sectionClassId, company, submissionNumber);
        });

        // Ensure the submission charts container is visible
        const submissionCharts = document.getElementById(`submission-charts-${sectionClassId}`);
        if (submissionCharts) {
            submissionCharts.style.display = 'block';
        }
        updateSubmissionChartsLayout(sectionClassId);
    });

    // Initial state check for dropdowns
    const classCompanies = <?php echo json_encode($allClassCompanies, 15, 512) ?>;
    const companyFilter = document.getElementById('companyFilter');

    // Check if there are any companies at all
    const hasAnyCompanies = Object.values(classCompanies).some(companies => companies.length > 0);
    if (!hasAnyCompanies) {
        companyFilter.disabled = true;
        companyFilter.innerHTML = '<option value="">No companies available</option>';
    }
</script>


<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.educator_layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laravel\PNPH-CAPSTONE\resources\views/educator/analytics/intern-grades-progress.blade.php ENDPATH**/ ?>