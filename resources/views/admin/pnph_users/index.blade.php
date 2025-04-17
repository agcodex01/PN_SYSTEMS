@extends('layouts.admin_layout')
@section('content')
<link rel="stylesheet" href="{{ asset('css/admin/index.css') }}">
    


<div class="users-container">
    <h1 class="text-center">Manage Users</h1>

    <!--  New User Button -->
    <div class="add-user-btn-container">
        <a href="{{ route('admin.pnph_users.create') }}" class="btn btn-primary">Create New User</a>
    </div>

    <!-- Filter Dropdown -->
    <form action="{{ route('admin.pnph_users.index') }}" method="GET" class="filter-form">
        <label for="role" class="filter-label">Filter by Role:</label>
        <select name="role" id="role" class="filter-dropdown" onchange="this.form.submit()">
            <option value="">All Roles</option>
            @foreach ($roles as $role)
                <option value="{{ $role }}" {{ $roleFilter == $role ? 'selected' : '' }}>
                    {{ ucfirst($role) }}
                </option>
            @endforeach
        </select>
    </form>

    <!-- Users Table -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Middle Initial</th>
                <th>Suffix</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->user_id }}</td>
                    <td>{{ $user->user_lname }}</td>
                    <td>{{ $user->user_fname }}</td>
                    <td>{{ $user->user_mInitial }}</td>
                    <td>{{ $user->user_suffix }}</td>
                    <td>{{ $user->user_email }}</td>
                    <td>{{ $user->user_role }}</td>
                    <td>
                        @if ($user->status === 'active')
                            <span class="status-active">Activated</span>
                        @else
                            <span class="status-inactive">Deactivated</span>
                        @endif
                    </td>
                    <td class="action-buttons">
                        <!-- View Button -->
                        <a href="{{ route('admin.pnph_users.show', $user->user_id) }}" class="btn btn-info">View</a>

                        <!-- Edit Button -->
                        <a href="{{ route('admin.pnph_users.edit', $user->user_id) }}" class="btn btn-warning">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pagination-container">
    {{ $users->links() }}
</div>
</div>

@endsection