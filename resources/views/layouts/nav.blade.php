<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PNPh-SAMS</title>
    <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">

    <style>
    body {
        margin: 0;  
        font-family: 'Poppins', sans-serif;
        background-color: #f1f5f9;
        height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .top-bar {
        background-color: #22bbea;
        padding: 0 20px;
        display: flex;
        align-items: center;
        height: 80px;
        flex-shrink: 0;
    }

    .PN-logo {
        height: 40px;
    }

    .container {
        display: flex;
        flex: 1;
        overflow: hidden;
    }

    .sidebar {
        background-color: #ffffff; /* WHITE background na */
        width: 250px;
        padding: 20px 0;
        display: flex;
        flex-direction: column;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1); /* subtle shadow for divider effect */
        flex-shrink: 0;
    }

    .menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .menu li {
        padding: 12px 20px;
        display: flex;
        flex-direction: column;
        cursor: pointer;
        transition: background-color 0.3s;
        border-radius: 8px;
        margin: 0 10px;
    }

    .menu li a {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #333333;
        text-decoration: none;
        width: 100%;
        font-size: 15px;
    }

    .dropdown > a {
        display: flex;
        align-items: center;
        width: 100%;
    }

    .dropdown-content {
        display: none;
        padding: 5px 0;
        margin-top: 8px;
    }

    .dropdown.active .dropdown-content {
        display: block;
    }

    .dropdown-content a {
        padding: 8px 0 8px 34px;
        color: #333333;
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        font-size: 14px;
        transition: background-color 0.2s;
    }

    .dropdown-content a img {
        width: 20px;
        height: 20px;
    }

    .dropdown-content a:hover {
        background-color: #f1f5f9;
    }

    .dropdown-content a.active {
        background-color: #f1f5f9;
        font-weight: 600;
    }

    .dropdown > a::after {
        content: '▼';
        font-size: 10px;
        margin-left: auto;
    }

    .dropdown.active > a::after {
        content: '▲';
    }

    .menu li img {
        width: 24px;
        height: 24px;
    }

    .content {
        flex-grow: 1;
        padding: 20px;
        overflow-y: auto;
        background-color: #f8f9fa;
        margin-left: 250px;
    }
</style>





</head>
<body>
    <div class="top-bar">
        <img class="PN-logo" src="{{ asset('images/PN-logo.png') }}" alt="PN Logo">
    </div>

    <div class="container">
        <aside class="sidebar">
            <ul class="menu">
                <li class="{{ request()->routeIs('training.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('training.dashboard') }}">
                        <img src="{{ asset('images/Dashboard.png') }}" alt="Dashboard"> Dashboard
                    </a>
                </li>
                <li class="{{ request()->routeIs('training.students-info') ? 'active' : '' }}">
                    <a href="{{ route('training.students-info') }}">
                        <img src="{{ asset('images/mu.png') }}" alt="Students Info"> Students Info
                    </a>
                </li>

                <li class="dropdown {{ request()->routeIs('training.manage-students') || request()->routeIs('training.schools') || request()->routeIs('training.classes.*') ? 'active' : '' }}" id="manageDropdown">
                    <a href="#" onclick="toggleDropdown(event)">
                        <img src="{{ asset('images/ms.png') }}" alt="Manage Students"> Manage Students
                    </a>
                    <div class="dropdown-content">
                        <a href="{{ route('training.manage-students') }}" class="{{ request()->routeIs('training.schools') ? 'active' : '' }}">
                            <img src="{{ asset('images/school.png') }}" alt="Schools"> Schools
                        </a>
                        <a href="{{ route('training.classes.index') }}" class="{{ request()->routeIs('training.classes.*') ? 'active' : '' }}">
                            <img src="{{ asset('images/classes.png') }}" alt="Classes"> Classes
                        </a>
                    </div>
                </li>


                <li>
                    <a href="#">
                        <img src="{{ asset('images/gs.png') }}" alt="Grade Submission"> Grade Submission
                    </a>
                </li>
                <li>
                    <a href="#">
                        <img src="{{ asset('images/analytics.png') }}" alt="Analytics"> Analytics
                    </a>
                </li>
                <li>
                    <a href="#">
                        <img src="{{ asset('images/is.png') }}" alt="Intervention Status"> Intervention Status
                    </a>
                </li>
                <li>
                    <a href="#">
                        <img src="{{ asset('images/me.png') }}" alt="Profile"> Profile
                    </a>
                </li>
            </ul>
        </aside>

        <main class="content">
            @yield('content')
        </main>
    </div>

    <script>
    function toggleDropdown(event) {
        event.preventDefault();
        const dropdown = document.getElementById('manageDropdown');
        dropdown.classList.toggle('active');
    }
</script>

    
    <!-- Scripts -->
    @stack('scripts')
</body>
</html>
