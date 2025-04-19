@extends('layouts.nav')

@section('content')

<h1 style="font-weight: 300;">Dashboard</h1>
<hr>




<canvas id="batchChart" height="100"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('batchChart').getContext('2d');

    const batchLabels = {!! json_encode($batchCounts->keys()) !!};
    const batchData = {!! json_encode($batchCounts->values()) !!};

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: batchLabels.map(batch => 'Class ' + batch),
            datasets: [{
                label: 'Number of Students',
                data: batchData,
                backgroundColor: '#4CAF50',
                borderRadius: 1,
                maxBarThickness: 20,
                minBarLength: 3,
                backgroundColor: [
                    '#4CAF50',
                    '#FF9800',
                    '#2196F3',
                    '#E91E63',
                    '#9C27B0',
                    '#00BCD4',
                    '#CDDC39',
                    '#FF5722',
                    '#795548',
                    '#607D8B'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: {
                    display: true,
                    text: 'Number of Students per Batch',
                    font: { size: 18 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 70, // 👈 Fix the Y-axis max value here
                ticks: {
                    stepSize: 10 // Optional: show 0, 10, 20... up to 70
                         }
                }
            }
        }
    });
</script>



@endsection