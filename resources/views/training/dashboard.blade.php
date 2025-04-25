@extends('layouts.nav')

@section('content')
<div class="dashboard-container" style="padding: 20px;">
    <!-- Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <div style="background: white; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <p class="stat-number">{{ $schoolsCount }}</p>
            <p>Total No. of Schools</p>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <p class="stat-number">{{ $classesCount }}</p>
            <p>Total No. of Classes</p>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <p class="stat-number">{{ $studentsCount }}</p>
            <p>Total No. of Students</p>
        </div>
    </div>

    <!-- Charts -->
    <div style="display: flex; flex-direction: column; gap: 30px;">
        <!-- Batch Chart -->
        <div style="background: white; width: 100%; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1400px; margin: 0 auto;">
            <h3 style="text-align: center; margin-bottom: 20px; color: #333;">Students by Batch</h3>
            <div style="height: 300px;">
                <canvas id="batchChart"></canvas>
            </div>
        </div>

        <!-- Gender Chart -->
        <div style="background: white; width: 100%; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1400px; margin: 0 auto;">
            <h3 style="text-align: center; margin-bottom: 20px; color: #333;">Student Gender Distribution</h3>
            <div style="height: 400px;">
                <canvas id="genderChart"></canvas>
            </div>
        </div>
    </div>
</div>

<style>
.dashboard-container {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: #22bbea;
    padding: 30px;
    border-radius: 10px;
    text-align: center;
    color: white;
}

.stat-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
}

.stat-number {
    margin: 0;
    font-size: 48px;
    font-weight: bold;
    line-height: 1;
}

.stat-label {
    margin: 0;
    font-size: 18px;
    text-decoration: none;
    color: black;
}



.chart-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-top: 30px;
    text-align: center;
}

.chart-card h3 {
    margin: 0 0 20px;
    color: #333;
    text-align: center;
}
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    // Gender Distribution Chart
    const genderCtx = document.getElementById('genderChart');
    if (genderCtx) {
        new Chart(genderCtx, {
            type: 'pie',
            data: {
                labels: ['Male', 'Female'],
                datasets: [{
                    data: [{{ $maleCount }}, {{ $femaleCount }}],
                    backgroundColor: ['#22bbea', '#ff9933']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
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
