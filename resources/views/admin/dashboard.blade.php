@extends('layouts.admin_layout')
@section('content')

<link rel="stylesheet" href="{{ asset('css/admin/dashboard2.css') }}">



    <div class="admin-dash">
    <h1 class="text-center">Dashboard</h1>
    <hr>

    
        

  
        <div class="row">
            @foreach($rolesCount as $role => $count)
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title text-center">{{ $count }}</h2>
                            <p class="card-text text-center">{{ $role }} Users</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>





    <h1>Roles Count Data</h1>
    <hr>
    <br>

    <!-- Display the Chart -->


    <div class="chart-container" style="display: flex; justify-content: center;">
    <div class="chart" >
        <canvas id="myChart"></canvas>
    </div>
    </div>
    </div>

















    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Get keys and values from the PHP array
        const ctx = document.getElementById('myChart');

        var dataKeys = @json(array_keys($rolesCount));  // Role names (labels)
        var dataValues = @json(array_values($rolesCount));  // Role counts (data)

        console.log(dataKeys);
        console.log(dataValues);

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: dataKeys,  // Using the role names as labels
                datasets: [{
                    label: 'Number of Users',
                    data: dataValues,  // Using the role counts as data
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

@endsection
