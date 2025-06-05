<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Removed dashboard2.css to prevent style conflicts -->
    <style>
        :root {
            --sidebar-width: 250px;
            --topbar-height: 80px;
            --content-padding: 20px;
            --primary-color: #22bbea;
            --sidebar-bg: #ffffff;
            --content-bg: #f8f9fa;
            --text-color: #333333;
            --hover-bg: #e3f2fd;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: var(--content-bg);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: var(--text-color);
        }

        .top-bar {
            height: var(--topbar-height);
            background: var(--primary-color);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .PN-logo {
            height: 50px;
            width: auto;
            object-fit: contain;
        }

        .user-info {
            color: white;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 16px;
        }

        .user-info span {
            color: white;
            font-weight: 600;
            font-size: 16px;
        }

        .logout-btn {
            background: none;
            border: none;
            color: inherit;
            cursor: pointer;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logout-btn:hover {
            color: #ff9933;
        }

        .layout-container {
            display: flex;
            flex: 1;
            min-height: calc(100vh - var(--topbar-height));
            margin-top: var(--topbar-height);
            position: relative;
            width: 100%;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            position: fixed;
            top: var(--topbar-height);
            left: 0;
            bottom: 0;
            overflow-y: auto;
            border-right: 2px solid var(--primary-color);
            z-index: 100;
        }

        .menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .menu li {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            font-size: 15px;
            cursor: pointer;
            transition: background-color 0.3s;
            color: var(--text-color);
        }

        .menu li:hover {
            background-color: var(--hover-bg);
        }

        .menu li.active {
            background-color: rgba(34, 187, 234, 0.1);
            border-left: 4px solid var(--primary-color);
            padding-left: 16px;
        }

        .menu li a {
            text-decoration: none;
            color: inherit;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .menu li img {
            width: 20px;
            height: 20px;
            margin-right: 12px;
            opacity: 0.8;
        }

        .content {
            flex: 1;
            padding: var(--content-padding);
            margin-left: var(--sidebar-width);
            background-color: var(--content-bg);
            min-height: calc(100vh - var(--topbar-height));
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <img class="PN-logo" src="{{ asset('images/PN-logo.png') }}" alt="PN Logo">

        @auth
            @php
                $user = Auth::user();
                $currentRoute = request()->route()->getName();
            @endphp

            <div class="user-info">
                Logged in as: 
                <span>
                    {{ $user->user_fname }} {{ $user->user_mInitial }} {{ $user->user_lname }} {{ $user->suffix }}
                </span> 
                | Role: 
                <span>
                    {{ ucfirst($user->user_role) }}
                </span>

                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                    @csrf
                    <button type="button" class="logout-btn" onclick="confirmLogout()">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        @endauth
    </div>

    <div class="layout-container">
        <aside class="sidebar">
            <ul class="menu">
                <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <img src="{{ asset('images/Dashboard.png') }}" alt="Dashboard"> Dashboard
                    </a>
                </li>
                <li class="{{ request()->routeIs('admin.pnph_users.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.pnph_users.index') }}">
                        <img src="{{ asset('images/mu.png') }}" alt="Manage Users"> Manage Users
                    </a>
                </li>
            </ul>
        </aside>

        <main class="content">
            @yield('content')
        </main>
    </div>

    @yield('scripts')
    <script>
        function confirmLogout() {
            if (confirm('Are you sure you want to logout?')) {
                document.getElementById('logout-form').submit();
            }
        }
    </script>
</body>
</html>
