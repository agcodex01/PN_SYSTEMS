@extends('layouts.nav')

@section('content')

<link rel="stylesheet" href="{{ asset('css/training/dashboard.css') }}">

<div class="dashboard-container" style="padding: 20px;">

    <h1 style="margin-bottom: 20px; color: #333; font-weight: 300;">Dashboard</h1>
    <hr>

    <!-- Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <div style="background: beige; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <p class="stat-number">{{ $schoolsCount }}</p>
            <p>Total No. of Schools</p>
        </div>

        <div style="background: beige; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <p class="stat-number">{{ $classesCount }}</p>
            <p>Total No. of Classes</p>
        </div>

        <div style="background: beige; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <p class="stat-number">{{ $studentsCount }}</p>
            <p>Total No. of Students</p>
        </div>
    </div>

    <h1 style="font-weight:300; margin-bottom: 20px; color: #333;">Student by Batch Analytics</h1>
    <hr>
    <!-- Charts -->
    <div style="display: flex; flex-direction: column; gap: 30px;">
        <!-- Batch Chart -->
        <div style="background: beige; width: 90%; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1400px; margin: 0 auto;">
            <h3 style="text-align: center; margin-bottom: 20px; color: #333;">Students by Batch</h3>
            <div style="height: 300px;">
                <canvas id="batchChart"></canvas>
            </div>
        </div>



    <h1 style="font-weight:300;  color: #333;">Sex by Batch Analytics</h1>
    <hr style="margin-top: -20px;>
    <div class="options">
    <select id="batchFilter" style="width:110px; padding: 8px; border-radius: 4px; border: 1px solid #ddd;">
                    <option value="all">All Batches</option>
                    @foreach($batchCounts->keys() as $batch)
                        <option value="{{ $batch }}">Batch {{ $batch }}</option>
                    @endforeach
                </select>
    </div>
      
        <div style="background: beige; width: 90%; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1400px; margin: 0 auto;">
            <div style="text-align: center; margin-bottom: 20px;">
                <h3 style="color: #333; margin-bottom: 10px;">Sex Distribution</h3>
            </div>
            <div style="height: 400px;">
                <canvas id="genderChart" ></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Items Section -->
    <h1 style="font-weight:300; margin-bottom: 20px; color: #333;">Recent Activity</h1>
    <hr>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <!-- Recent Students -->
        <div style="background: beige; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="color: #333; margin-bottom: 15px;">Recent Students</h3>
            <div class="recent-list">
                @foreach($recentStudents as $student)
                    <div class="recent-item">
                        <i class="fas fa-user"></i>
                        <div>
                            <strong>{{ $student->user_fname }} {{ $student->user_lname }}</strong>
                            <small>Batch {{ $student->studentDetail->batch ?? 'N/A' }}</small>
                        </div>
                        <span class="recent-date">{{ $student->created_at->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Schools -->
        <div style="background: beige; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="color: #333; margin-bottom: 15px;">Recent Schools</h3>
            <div class="recent-list">
                @foreach($recentSchools as $school)
                    <div class="recent-item">
                        <i class="fas fa-school"></i>
                        <div>
                            <strong>{{ $school->name }}</strong>
                            <small>{{ $school->department }} - {{ $school->course }}</small>
                        </div>
                        <span class="recent-date">{{ $school->created_at->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Classes -->
        <div style="background: beige; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="color: #333; margin-bottom: 15px;">Recent Classes</h3>
            <div class="recent-list">
                @foreach($recentClasses as $class)
                    <div class="recent-item">
                        <i class="fas fa-chalkboard"></i>
                        <div>
                            <strong>{{ $class->class_name }}</strong>
                            <small>{{ $class->school->name ?? 'N/A' }}</small>
                        </div>
                        <span class="recent-date">{{ $class->created_at->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>






@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Wait for DOM to be ready

document.addEventListener('DOMContentLoaded', function() {
    // Gender Distribution Chart
    const genderCtx = document.getElementById('genderChart');
    let genderChart;
    
    // Initial gender data for all batches
    const genderData = {
        all: {
            male: {{ $maleCount }},
            female: {{ $femaleCount }}
        },
        @foreach($batchCounts->keys() as $batch)
        '{{ $batch }}': {
            male: {{ $genderByBatch[$batch]['male'] ?? 0 }},
            female: {{ $genderByBatch[$batch]['female'] ?? 0 }}
        },
        @endforeach
    };

    function updateGenderChart(batchValue) {
        const data = genderData[batchValue];
        
        if (genderChart) {
            genderChart.destroy();
        }

        genderChart = new Chart(genderCtx, {
            type: 'pie',
            data: {
                labels: ['Male', 'Female'],
                datasets: [{
                    data: [data.male, data.female],
                    backgroundColor: ['#22bbea', '#ff9933']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: batchValue === 'all' ? 'All Batches' : `Batch ${batchValue}`,
                        font: {
                            size: 16
                        }
                    }
                }
            }
        });
    }

    // Initialize chart with all batches
    if (genderCtx) {
        updateGenderChart('all');

        // Add event listener for batch filter
        document.getElementById('batchFilter').addEventListener('change', function(e) {
            updateGenderChart(e.target.value);
        });
    }

    // Students by Batch Chart
    const batchCtx = document.getElementById('batchChart');
    if (batchCtx) {
        new Chart(batchCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($batchCounts->keys()) !!},
                datasets: [{
                    label: 'Number of Students',
                    data: {!! json_encode($batchCounts->values()) !!},
                    backgroundColor: '#22bbea',
                    barThickness: 100,  // Fixed bar width
                    maxBarThickness: 150 // Maximum bar width
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f0f0f0'
                        }
                    }
                },
                layout: {
                    padding: {
                        left: 20,
                        right: 20
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
});
</script>



@endpush



@endsection

