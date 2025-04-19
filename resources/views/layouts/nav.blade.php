<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PNPh-SAMS</title>
    <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
</head>
<body>
    <div class="top-bar">
        <img class="PN-logo" src="{{ asset('images/PN-logo.png') }}" alt="PN Logo">
    </div>

    <div class="container">
        <aside class="sidebar">
            <ul class="menu">
                <li><img src="{{ asset('images/Dashboard.png') }}" alt="Dashboard"> Dashboard</li>
                <li><img src="{{ asset('images/mu.png') }}" alt="Students Info"> Students Info</li>
                <li><img src="{{ asset('images/ms.png') }}" alt="Manage Students"> Manage Students</li>
                <li><img src="{{ asset('images/gs.png') }}" alt="Grade Submission"> Grade Submission</li>
                <li><img src="{{ asset('images/analytics.png') }}" alt="Analytics"> Analytics</li>
                <li><img src="{{ asset('images/is.png') }}" alt="Intervention Status"> Intervention Status</li>
                <li><img src="{{ asset('images/me.png') }}" alt="Profile"> Profile</li>
            </ul>
        </aside>

        <main class="content">
            @yield('content')
        </main>
    </div>
</body>
</html>
