@extends('layouts.student_layout')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<style>
    .grade-status-chart {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        position: sticky;
        top: 20px;
        z-index: 100;
    }
    .chart-container {
        position: relative;
        height: 300px;
        margin: 20px 0;
        width: 100%;
    }
</style>
<style>
    /* Layout */
    .dashboard-container {
        padding: 1.5rem;
        min-height: 100vh;
        overflow-y: hidden;
    }
    
    /* Chart Styles */
    .chart-container {
        position: relative;
        height: 300px;
        margin-bottom: 2rem;
        max-height: 60vh;
        overflow: hidden;
    }
    
    .chart-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }
    
    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    
    .chart-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
        color: #2d3748;
    }
    
    /* Subject List */
    .subject-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1rem;
    }
    
    .subject-card {
        background: #fff;
        border-radius: 8px;
        padding: 1.25rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        border-left: 4px solid #e9ecef;
    }
    
    .subject-code {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 5px;
        font-size: 0.95em;
    }
    
    .subject-name {
        color: #4a5568;
        margin-bottom: 10px;
        font-size: 0.9em;
    }
    
    .subject-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.9em;
        color: #718096;
    }
    
    /* Status Badges */
    .status-badge {
        display: inline-block;
        padding: 0.35em 0.65em;
        border-radius: 50rem;
        font-size: 0.75em;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    /* Status Colors */
    .status-pass,
    .status-Pass {
        border-left-color: #28a745;
    }
    
    .status-fail,
    .status-Fail {
        border-left-color: #dc3545;
    }
    
    .status-inc,
    .status-Incomplete {
        border-left-color: #ffc107;
    }
    
    .status-nc,
    .status-NC {
        border-left-color: #6c757d;
    }
    
    .status-dr,
    .status-DR {
        border-left-color: #343a40;
    }
    
    /* Status Badge Colors */
    .status-badge {
        background-color: #e9ecef;
        color: #495057;
    }
    
    .status-pass .status-badge,
    .status-Pass .status-badge {
        background-color: #d4edda;
        color: #155724;
    }
    
    .status-fail .status-badge,
    .status-Fail .status-badge {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .status-inc .status-badge,
    .status-Incomplete .status-badge {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .status-nc .status-badge,
    .status-NC .status-badge {
        background-color: #e2e3e5;
        color: #383d41;
    }
    
    .status-dr .status-badge,
    .status-DR .status-badge {
        background-color: #d6d8db;
        color: #1b1e21;
    }
    
    /* Empty State */
    .no-subjects {
        text-align: center;
        padding: 2rem;
        background: #f8f9fa;
        border-radius: 8px;
        color: #6c757d;
    }
    
    .no-subjects i {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    
    .no-subjects h4 {
        margin-bottom: 0.5rem;
        color: #2d3748;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .subject-list {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 480px) {
        .subject-list {
            grid-template-columns: 1fr;
        }
    }
    
    /* Print styles */
    @media print {
        .no-print {
            display: none !important;
        }
        
        .subject-card {
            break-inside: avoid;
            page-break-inside: avoid;
            -webkit-column-break-inside: avoid;
        }
    }
</style>

/* Mobile Responsive Styles */
@media (max-width: 768px) {
    /* Filter Form */
    .d-flex.justify-content-between.align-items-center.mb-4 {
        flex-direction: column;
    }

    .d-flex.justify-content-between.align-items-center.mb-4 form {
        width: 100%;
    }

    .d-flex.justify-content-between.align-items-center.mb-4 .d-flex {
        flex-direction: column;
        width: 100%;
    }

    .d-flex.justify-content-between.align-items-center.mb-4 select,
    .d-flex.justify-content-between.align-items-center.mb-4 button {
        width: 100%;
        margin: 5px 0;
    }

    /* Chart Container */
    .grade-status-chart {
        padding: 15px;
        margin: 15px 0;
    }

    .chart-container {
        height: 250px;
    }

    /* Chart Card */
    .chart-card {
        padding: 15px;
    }
}

@media (max-width: 480px) {
    /* Filter Form */
    .d-flex.justify-content-between.align-items-center.mb-4 h1 {
        font-size: 1.5rem;
    }

    /* Chart Container */
    .grade-status-chart {
        padding: 10px;
        margin: 10px 0;
    }

    .chart-container {
        height: 200px;
    }

    /* Chart Card */
    .chart-card {
        padding: 10px;
    }
}

/* Make chart responsive */
.chart-container canvas {
    max-width: 100%;
    height: auto !important;
}
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Page Header and Filter -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">My Grade Status</h1>
        <form action="{{ route('student.grades') }}" method="GET" class="d-flex align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <select name="term" id="term" class="form-select form-select-sm" style="width: auto;">
                    <option value="">All Terms</option>
                    @foreach($terms as $term)
                        <option value="{{ $term }}" {{ request('term') == $term ? 'selected' : '' }}>{{ ucfirst($term) }}</option>
                    @endforeach
                </select>
                <select name="academic_year" id="academic_year" class="form-select form-select-sm" style="width: auto;">
                    <option value="">All Years</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                @if(request()->has('term') || request()->has('academic_year'))
                    <a href="{{ route('student.grades') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Debug Info -->
    @php
        // Debug: Log the subjects data
        if (isset($subjectsWithGrades)) {
            \Log::info('subjectsWithGrades:', ['count' => $subjectsWithGrades->count(), 'data' => $subjectsWithGrades->toArray()]);
        } else {
            \Log::warning('subjectsWithGrades is not set');
        }
    @endphp
    
    <!-- Sort subjectsWithGrades by academic_year (desc) and term (desc) -->
    @php
        if (isset($subjectsWithGrades)) {
            $subjectsWithGrades = $subjectsWithGrades->sortByDesc(function($subject) {
                $year = $subject->academic_year ?? '';
                $term = $subject->term ?? '';
                return $year . '-' . $term;
            })->values();
        }
    @endphp
    
    <!-- Subjects by Status Chart -->
    @if(isset($subjectsWithGrades) && $subjectsWithGrades->count() > 0)
        @php
            $subjectLabels = [];
            $subjectGrades = [];
            $subjectColors = [];
            $subjectStatuses = [];

            foreach ($subjectsWithGrades as $subject) {
                // Only include approved grades
                $status = strtolower($subject->status ?? $subject->pivot->status ?? '');
                if ($status !== 'approved') continue;

                $subjectName = $subject->subject_name ?? $subject->name ?? 'N/A';
                $subjectCode = $subject->subject_code ?? $subject->code ?? '';
                $grade = is_numeric($subject->grade ?? $subject->pivot->grade ?? null) ? floatval($subject->grade ?? $subject->pivot->grade ?? 0) : 0;

                // Label: Subject Name (Code) or just Subject Name
                $subjectLabels[] = $subjectName . ' (' . $subjectCode . ')';

                // Grade
                $subjectGrades[] = $grade;

                // Status based on grade
                if ($grade >= 3.0 && $grade <= 5.0) {
                    $subjectColors[] = 'rgba(40, 167, 69, 0.8)';
                    $friendlyStatus = 'Passed';
                } elseif ($grade > 0 && $grade < 3.0) {
                    $subjectColors[] = 'rgba(220, 53, 69, 0.8)';
                    $friendlyStatus = 'Failed';
                } elseif ($grade == 0) {
                    $subjectColors[] = 'rgba(255, 193, 7, 0.8)';
                    $friendlyStatus = 'Incomplete';
                } else {
                    $subjectColors[] = 'rgba(108, 117, 125, 0.8)';
                    $friendlyStatus = 'No Credit';
                }
                $subjectStatuses[] = $friendlyStatus;
            }
        @endphp
        
        @php
            // Reverse arrays so newest is last (rightmost bar)
            $subjectLabels = array_reverse($subjectLabels);
            $subjectGrades = array_reverse($subjectGrades);
            $subjectColors = array_reverse($subjectColors);
            $subjectStatuses = array_reverse($subjectStatuses);
        @endphp
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="chart-container" style="height: 300px;">
                    <canvas id="gradeStatusChart"></canvas>
                </div>
            </div>
        </div>
        
        @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('gradeStatusChart');
            if (!ctx) return;
            
            const labels = @json($subjectLabels);
            const data = @json($subjectGrades);
            const backgroundColors = @json($subjectColors);
            const statuses = @json($subjectStatuses);
            
            // Debug output
            console.log('Chart labels:', labels);
            console.log('Chart data:', data);
            
            if (!Array.isArray(labels) || labels.length === 0 || !Array.isArray(data) || data.length === 0 || !Array.isArray(backgroundColors) || backgroundColors.length === 0) {
                console.error('No subjects data available');
                return;
            }
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Grade',
                        data: data,
                        backgroundColor: backgroundColors,
                        borderColor: backgroundColors.map(c => c.replace('0.8', '1')),
                        borderWidth: 1,
                        borderRadius: 4,
                        barThickness: 40,
                        maxBarThickness: 50
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 5.0,
                            ticks: {
                                stepSize: 1,
                                callback: function(value) {
                                    return value.toFixed(1);
                                }
                            },
                            title: {
                                display: true,
                                text: 'Grade',
                                font: {
                                    weight: 'bold'
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Subject',
                                font: {
                                    weight: 'bold'
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                title: function() { return []; },
                                label: function(context) {
                                    const status = statuses[context.dataIndex] || 'N/A';
                                    return [
                                        `Grade: ${context.parsed.y}`,
                                        `Status: ${status}`
                                    ];
                                }
                            }
                        },
                        datalabels: {
                            display: false
                        }
                    }
                }
            });
        });
        </script>
        @endpush
    @endif

    <!-- Grade Status Distribution Chart -->
    <div class="grade-status-chart">
        <div class="chart-container">
            <canvas id="gradeStatusChart"></canvas>
        </div>
    </div>

    <!-- Grades Table -->
    {{-- @include('student.partials.grades_table', [
        'grades' => $subjectsWithGrades,
        'showActions' => true
    ]) --}}

    

    
    <!-- Grade Status Distribution Chart -->
    <div class="chart-card">
 
        <div class="chart-container">
            <canvas id="gradeStatusChart"></canvas>
        </div>
    </div>
    
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
// Initialize Grade Status Chart
        const ctx = document.getElementById('gradeStatusChart').getContext('2d');
        
        // Get the status counts from the server
        const statusCounts = @json($statusCounts ?? []);
        
        // Prepare data for the chart
        const labels = [];
        const data = [];
        const backgroundColors = [];
        const borderColors = [];
        
        // Map status to colors
        const statusColors = {
            'approved': { bg: 'rgba(40, 167, 69, 0.8)', border: 'rgba(40, 167, 69, 1)' },
            'pass': { bg: 'rgba(40, 167, 69, 0.8)', border: 'rgba(40, 167, 69, 1)' },
            'pending': { bg: 'rgba(255, 193, 7, 0.8)', border: 'rgba(255, 193, 7, 1)' },
            'rejected': { bg: 'rgba(220, 53, 69, 0.8)', border: 'rgba(220, 53, 69, 1)' },
            'fail': { bg: 'rgba(220, 53, 69, 0.8)', border: 'rgba(220, 53, 69, 1)' },
            'incomplete': { bg: 'rgba(23, 162, 184, 0.8)', border: 'rgba(23, 162, 184, 1)' },
            'inc': { bg: 'rgba(255, 193, 7, 0.8)', border: 'rgba(255, 193, 7, 1)' },
            'no credit': { bg: 'rgba(108, 117, 125, 0.8)', border: 'rgba(108, 117, 125, 1)' },
            'nc': { bg: 'rgba(108, 117, 125, 0.8)', border: 'rgba(108, 117, 125, 1)' },
            'dropped': { bg: 'rgba(33, 37, 41, 0.8)', border: 'rgba(33, 37, 41, 1)' },
            'dr': { bg: 'rgba(33, 37, 41, 0.8)', border: 'rgba(33, 37, 41, 1)' }
        };
        
        // Process status counts
        Object.entries(statusCounts).forEach(([status, count]) => {
            if (count > 0) {
                const formattedStatus = status === 'inc' ? 'Incomplete' : 
                                     status === 'nc' ? 'No Credit' :
                                     status === 'dr' ? 'Dropped' :
                                     status.charAt(0).toUpperCase() + status.slice(1);
                
                labels.push(formattedStatus);
                data.push(count);
                
                // Get colors based on status
                const color = statusColors[status.toLowerCase()] || { bg: 'rgba(108, 117, 125, 0.8)', border: 'rgba(108, 117, 125, 1)' };
                backgroundColors.push(color.bg);
                borderColors.push(color.border);
            }
        });
        
        // Create chart
        if (data.length > 0) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Number of Subjects',
                        data: data,
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 1,
                        borderRadius: 4,
                        maxBarThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Subjects',
                                font: {
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                stepSize: 1,
                                precision: 0,
                                maxTicksLimit: 6,  // Allow up to 6 ticks (0-5)
                                callback: function(value) {
                                    // Only show integer values
                                    if (value % 1 === 0) {
                                        return value;
                                    }
                                }
                            },
                            max: 5,  // Set maximum value to 5
                            grid: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.parsed.y} subject${context.parsed.y !== 1 ? 's' : ''} with status: ${context.label}`;
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        } else {
            document.querySelector('.chart-container').innerHTML = `
                <div class="text-center p-4">
                    <p class="text-muted">No grade data available yet.</p>
                </div>`;
        }
    }
});
</script>
@endpush

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize Grade Status Chart
    const ctx = document.getElementById('gradeStatusChart');
    if (ctx) {
        const statusCounts = @json($statusCounts ?? []);
        
        if (Object.keys(statusCounts).length > 0) {
            const labels = Object.keys(statusCounts);
            const data = Object.values(statusCounts);
            
            // Map status to colors
            const statusColors = {
                'incomplete': 'rgba(23, 162, 184, 0.8)',
                'no credit': 'rgba(108, 117, 125, 0.8)',
                'dropped': 'rgba(52, 58, 64, 0.8)',
                'passed': 'rgba(40, 167, 69, 0.8)',
                'failed': 'rgba(220, 53, 69, 0.8)'
            };
            
            const backgroundColors = labels.map(status => statusColors[status.toLowerCase()] || 'rgba(108, 117, 125, 0.8)');
            const borderColors = backgroundColors.map(color => color.replace('0.8', '1'));
            
            const statuses = @json($subjectStatuses);
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels.map(label => label.charAt(0).toUpperCase() + label.slice(1)),
                    datasets: [{
                        label: 'Number of Subjects',
                        data: data,
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 1,
                        borderRadius: 4,
                        barThickness: 'flex',
                        maxBarThickness: 40,
                        minBarLength: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            padding: 12,
                            cornerRadius: 6,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    const status = statuses[context.dataIndex] || 'N/A';
                                    return `${context.parsed.y} subject${context.parsed.y !== 1 ? 's' : ''} with status: ${status}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: {
                                    size: 12
                                }
                            },
                            grid: {
                                display: true,
                                drawBorder: false,
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 12
                                }
                            }
                        }
                    }
                }
            });
        } else {
            document.querySelector('.chart-container').innerHTML = `
                <div class="text-center p-4">
                    <p class="text-muted">No grade data available yet.</p>
                </div>`;
        }
    }
});
</script>
@endpush

<style>
    .chart-legend {
        font-size: 0.97em;
        margin-top: 10px;
    }
    .legend-item {
        margin-right: 18px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .legend-color {
        display: inline-block;
        width: 18px;
        height: 18px;
        border-radius: 4px;
        margin-right: 6px;
        border: 1px solid #ccc;
    }
</style>
