@extends('layouts.admin_layout')

@section('content')
<link rel="stylesheet" href="{{ asset('css/admin/edit.css') }}">


<div class="container">
        <h2>Edit User: &nbsp <span style="color: #ff9933; font-weight: 400;"> {{ $user->user_fname }} {{ $user->user_lname }}</span></h2>

        <form action="{{ route('admin.pnph_users.update', $user->user_id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="user_id">User ID</label>
                <input type="text" name="user_id" value="{{ $user->user_id }}" disabled>
                <span class="text-muted">This field cannot be changed.</span>
            </div>

            <div class="form-group">
                <label for="user_fname">First Name</label>
                <input type="text" name="user_fname" value="{{ $user->user_fname }}" required>
            </div>

            <div class="form-group">
                <label for="user_lname">Last Name</label>
                <input type="text" name="user_lname" value="{{ $user->user_lname }}" required>
            </div>

            <div class="form-group">
                <label for="user_mInitial">Middle Initial</label>
                <input type="text" name="user_mInitial" value="{{ $user->user_mInitial }}">
            </div>

            <div class="form-group">
                <label for="user_suffix">Suffix</label>
                <input type="text" name="user_suffix" value="{{ $user->user_suffix }}">
            </div>

            <div class="form-group">
                <label for="user_email">Email</label>
                <input type="email" name="user_email" value="{{ $user->user_email }}" required>
            </div>

            <div class="form-group">
                <label for="user_role">Role</label>
                <input type="text" name="user_role" value="{{ $user->user_role }}" required>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" required>
                    <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Activate</option>
                    <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Deactivate</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">Update User</button>
                <a href="{{ route('admin.pnph_users.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection