@extends('layouts.admin_layout')
@section('content')

    <div class="users-container">
        <h1>Manage Users</h1>


           <button class="add-user-btn">
           <a href="{{ route('admin.pnph_users.create') }}" >Create New User</a>

           </button> 

        

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
                        <td>{{ $user->status }}</td>
                        <td>
                            <button class="edit-btn">
                            <a href="{{ route('admin.pnph_users.edit', $user->user_id) }}" class="btn btn-warning">Edit</a>
                            </button>
                            <form action="{{ route('admin.pnph_users.destroy', $user->user_id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>





    </div>
  
@endsection
