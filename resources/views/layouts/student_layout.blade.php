<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Student Dashboard' }}</title>
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
        background-color: #ffffff;
        width: 250px;
        padding: 20px 0;
        display: flex;
        flex-direction: column;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
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
        color: #333333;
        text-decoration: none;
        width: 100%;
        font-size: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .menu li img {
        width: 24px;
        height: 24px;
    }

    .menu li:hover {
        background-color: #f1f5f9;
    }

    .menu li.active {
        background-color: #f1f5f9;
    }

    .content {
        flex-grow: 1;
        padding: 20px;
        overflow-y: auto;
        background-color: #f8f9fa;
        margin-left: 250px;
    }

    .user-info {
        margin-left: auto;
        color: #333;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .logout-btn {
        background: none;
        border: none;
        color: inherit;
        cursor: pointer;
        padding: 5px;
    }

    .logout-btn:hover {
        color: #ff9933;
    }
    </style>
</head>
<body>
    <div class="top-bar">
        <img class="PN-logo" src="{{ asset('images/PN-logo.png') }}" alt="PN Logo">

        @auth
            @php
                $user = Auth::user();
            @endphp

            <div class="user-info">
                Logged in as: 
                <span style="color:white;">
                    {{ $user->user_fname }} {{ $user->user_mInitial }} {{ $user->user_lname }} {{ $user->suffix }}
                </span> 
                | Role: 
                <span style="color:white;">
                    {{ ucfirst($user->user_role) }}
                </span>

                <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display: inline;">
                    @csrf
                    <button type="button" class="logout-btn" onclick="confirmLogout()">
                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H8m12 0-4 4m4-4-4-4M9 4H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h2"/>
                        </svg>
                    </button>
                </form>
            </div>
        @endauth
    </div>

    <div class="container">
        <aside class="sidebar">
            @auth
                @php $role = strtolower(Auth::user()->user_role ?? ''); @endphp
                @if($role === 'student')
                    <ul class="menu">
                        <li class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('student.dashboard') }}">
                                <img src="{{ asset('images/dashboard.png') }}" alt="Dashboard">
                                Dashboard
                            </a>
                        </li>
                        {{-- <li class="{{ request()->routeIs('student.grade-submissions*') ? 'active' : '' }}">
                            <a href="{{ route('student.grade-submissions') }}">
                                <img src="{{ asset('images/gs.png') }}" alt="Grade Submissions">
                                Grade Submissions
                            </a>
                        </li> --}}
                        <li>
                            <a href="#">
                                <img src="{{ asset('images/me.png') }}" alt="Profile">
                                Profile
                            </a>
                        </li>
                    </ul>
                @endif
            @endauth
        </aside>

        <main class="content">
            @yield('content')
        </main>
    </div>

    <script>
    function confirmLogout() {
        if (confirm("Are you sure you want to log out?")) {
            document.getElementById('logout-form').submit();
        }
    }
    </script>

    @stack('scripts')
</body>
</html> 