<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }}</title>
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard2.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
</head>
<body>
    <div class="top-bar">
        <img class="PN-logo" src="{{ asset('images/PN-logo.png') }}" alt="PN Logo">

        {{-- Add Logged in as info and Logout --}}
        @auth
            @php
                $user = Auth::user();
            @endphp

            <div class="user-info" style="color: #333; font-weight: 500; display: flex; align-items: center; gap: 15px;">
                Logged in as: 
                <span style="color: #ff9933;">
                    {{ $user->user_fname }} {{ $user->user_mInitial }} {{ $user->user_lname }} {{ $user->suffix }}
                </span> 
                | Role: 
                <span style="color: #ff9933;">
                    {{ ucfirst($user->user_role) }}
                </span>

                <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display: inline;">
                    @csrf
                    <button type="button" class="logout-btn" style="background: none; border: none; color: inherit; cursor: pointer;" onclick="confirmLogout()">
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
            <ul class="menu">
                <li><a href="{{ route('admin.dashboard') }}"><img src="{{ asset('images/Dashboard.png') }}" alt="Dashboard"> Dashboard</a></li>
                <li><a href="{{ route('admin.pnph_users.index') }}"><img src="{{ asset('images/mu.png') }}" alt="Students Info"> Manage Users</a></li>

            </ul>
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
</body>
</html>
