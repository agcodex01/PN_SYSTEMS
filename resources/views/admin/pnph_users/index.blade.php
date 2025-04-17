@extends('layouts.admin_layout')
@section('content')

    <div class="users-container">
        <h1>Manage Users</h1>

        <button class="add-user-btn">
            <a href="{{ route('admin.pnph_users.create') }}">Create New User</a>
        </button>


        <form action="{{ route('admin.pnph_users.index') }}" method="GET" style="margin-bottom: 20px;">
    <label for="role">Filter by Role:</label>
    <select name="role" id="role" onchange="this.form.submit()">
        <option value="">All Roles</option>
        @foreach ($roles as $role)
            <option value="{{ $role }}" {{ $roleFilter == $role ? 'selected' : '' }}>
                {{ ucfirst($role) }}
            </option>
        @endforeach
    </select>
</form>

        <table class="table">
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
                                <span style="color: green;">Activated</span>
                            @else
                                <span style="color: red;">Deactivated</span>
                            @endif
                        </td>
                        <td>
                            <!-- View Button -->
                            <a href="{{ route('admin.pnph_users.show', $user->user_id) }}" class="btn btn-info">View</a>

                            <!-- Edit Button -->
                            <a href="{{ route('admin.pnph_users.edit', $user->user_id) }}" class="btn btn-warning">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection