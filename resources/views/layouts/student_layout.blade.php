<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Student Dashboard' }}</title>
    <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f8f9fa;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Header */
    .top-bar {
        background-color: #22bbea !important;
        padding: 0 !important;
        display: flex !important;
        align-items: center !important;
        height: 70px !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        z-index: 1000 !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important;
    }

    /* Ensure our logo styles take precedence */
    .top-bar .PN-logo {
        height: 50px !important;
        width: auto !important;
        max-width: 220px !important;
        object-fit: contain !important;
        margin: 0 !important;
        display: block !important;
        flex-shrink: 0 !important;
        transition: all 0.3s ease !important;
    }
    
    .logo-link {
        display: flex !important;
        align-items: center !important;
        height: 100% !important;
        padding: 10px 20px !important;
        text-decoration: none !important;
        margin: 0 !important;
    }

    /* Main Layout */
    .main-wrapper {
        display: flex;
        padding-top: 70px; /* Height of top-bar */
        min-height: 100vh;
        width: 100%;
        position: relative;
    }

    /* Sidebar */
    .sidebar {
        background-color: #ffffff;
        width: 260px;
        position: fixed;
        top: 69px; /* Adjusted to connect with header */
        left: 0;
        bottom: 0;
        overflow-y: auto;
        box-shadow: 2px 0 10px rgba(0,0,0,0.05);
        z-index: 900;
        padding: 0;
        margin: 0;
    }

    /* Menu Styling */
    .menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .menu li:first-child {
        margin-top: 10px;
    }
    
    .menu li {
        margin-bottom: 5px;
    }

    .menu li a {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        color: #333;
        text-decoration: none;
        font-size: 15px;
        transition: all 0.2s ease;
        border-radius: 8px;
    }

    .menu-icon {
        width: 24px;
        height: 24px;
        margin-right: 12px;
        object-fit: contain;
    }

    .menu li a:hover {
        background-color: #f1f5f9;
        color: #22bbea;
    }

    .menu li.active a {
        background-color: #e3f2fd;
        color: #22bbea;
        font-weight: 500;
    }
    
    .menu li.active .menu-icon {
        /* Remove the blue filter to keep the original image */
        filter: none;
    }

    .menu li img {
        width: 24px;
        height: 24px;
    }
    
    /* Sidebar Logo */
    .sidebar-logo {
        padding: 20px 0;
        text-align: center;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 20px;
    }
    
    .dashboard-logo {
        max-width: 80%;
        height: auto;
        max-height: 80px;
        object-fit: contain;
    }

    .menu li:hover {
        background-color: #f1f5f9;
    }

    .menu li.active {
        background-color: #f1f5f9;
    }

    /* Main Content */
    .content {
        flex: 1;
        margin-left: 260px; /* Same as sidebar width */
        padding: 30px;
        min-height: calc(100vh - 70px);
        background-color: #f8f9fa;
    }

    /* User Info */
    .user-info {
        margin-left: auto;
        color: #fff;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .user-info span {
        color: #fff;
    }

    .user-role {
        background-color: rgba(255, 255, 255, 0.2);
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 13px;
    }

    /* Logout Button */
    .logout-btn {
        background: none;
        border: none;
        color: #fff;
        cursor: pointer;
        padding: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        transition: all 0.2s ease;
    }

    .logout-btn:hover {
        background-color: rgba(255, 255, 255, 0.2);
        color: #ff9933;
    }
    </style>
</head>
<body>
    <div class="top-bar">
        <a href="{{ route('student.dashboard') }}" class="logo-link">
            <img class="PN-logo" src="{{ asset('images/PN-logo.png') }}" alt="PN Logo">
        </a>

        <!-- Debug Info -->
        @php
            $user = Auth::user();
            $role = strtolower($user->user_role ?? 'none');
        @endphp
        <!-- Debug: User Role: {{ $role }} -->
        <!-- Debug: Is Student: {{ $role === 'student' ? 'Yes' : 'No' }} -->
        <!-- Debug: Route: {{ request()->path() }} -->
        
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

    <div class="main-wrapper">
        <aside class="sidebar">
            @auth
                @php $role = strtolower(Auth::user()->user_role ?? ''); @endphp
                @if($role === 'student')
                    <ul class="menu">
                        <li class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('student.dashboard') }}">
                                <img src="{{ asset('images/Dashboard.png') }}" alt="Dashboard" class="menu-icon">
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('student.grades') ? 'active' : '' }}">
                            <a href="{{ route('student.grades') }}">
                                <img src="{{ asset('images/Dashboard.png') }}" alt="Grade Status" class="menu-icon">
                                <span>Grade Status</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('student.profile') ? 'active' : '' }}">
                            <a href="{{ route('student.profile') }}" id="profile-link">
                                <img src="{{ asset('images/me.png') }}" alt="Profile" class="menu-icon">
                                <span>My Profile</span>
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

    // Debug profile link click
    document.addEventListener('DOMContentLoaded', function() {
        const profileLink = document.getElementById('profile-link');
        if (profileLink) {
            console.log('Profile link found:', profileLink.href);
            profileLink.addEventListener('click', function(e) {
                console.log('Profile link clicked');
                console.log('Href:', this.href);
                // e.preventDefault(); // Uncomment to prevent navigation for testing
            });
        } else {
            console.error('Profile link not found!');
        }
    });
    </script>

    @stack('scripts')
</body>
</html> 