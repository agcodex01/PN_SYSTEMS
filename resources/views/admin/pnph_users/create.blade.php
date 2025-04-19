@extends('layouts.admin_layout')
@section('content')


<link rel="stylesheet" href="{{ asset('css/admin/create.css') }}">

<a href="{{ route('admin.pnph_users.index') }}">
    <div class="icon-back">
        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="m15 19-7-7 7-7"/>
        </svg>
    </div>
</a>

<h1>Create New User</h1>
<hr>

<div class="create-user-container">
    <form action="{{ route('admin.pnph_users.store') }}" method="POST" class="create-user-form" onsubmit="return confirmCreateUser()">
        @csrf
        <div class="form-group">
            <label for="user_id">User ID</label>
            <input type="text" name="user_id" class="form-control" required>
            @error('user_id')
                <p style="color: red; font-size: 10px;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="user_lname">Last Name</label>
            <input type="text" name="user_lname" class="form-control" required>
            @error('user_lname')
            <p style="color: red; font-size: 10px;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="user_fname">First Name</label>
            <input type="text" name="user_fname" class="form-control" required>
            @error('user_fname')
                 <p style="color: red; font-size: 10px;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="user_mInitial">Middle Initial</label>
            <input type="text" name="user_mInitial" class="form-control">
            @error('user_mInitial')
                <p style="color: red; font-size: 10px;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="user_suffix">Suffix</label>
            <input type="text" name="user_suffix" class="form-control">
            @error('user_suffix')
                 <p style="color: red; font-size: 10px;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="user_email">Email</label>
            <input type="email" name="user_email" class="form-control" required>
            @error('user_email')
                <p style="color: red; font-size: 10px;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="user_role">Role</label>
            <input type="text" name="user_role" class="form-control" required>
            @error('user_role')
                <p style="color: red; font-size: 10px;">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Create User</button>
    </form>



</div>


<script>
    function confirmCreateUser() {
        return confirm("Are you sure you want to create this user?");
    }
</script>

@endsection