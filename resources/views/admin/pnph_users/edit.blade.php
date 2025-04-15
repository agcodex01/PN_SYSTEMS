@extends('layouts.admin_layout')

@section('content')
    <h2>Edit User</h2>

    <form action="{{ route('admin.pnph_users.update', $user->user_id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="user_id">User ID</label>
            <input type="text" name="user_id" value="{{ $user->user_id }}" class="form-control" disabled>
        </div>

        <div class="form-group">
            <label for="user_fname">First Name</label>
            <input type="text" name="user_fname" value="{{ $user->user_fname }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="user_lname">Last Name</label>
            <input type="text" name="user_lname" value="{{ $user->user_lname }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="user_mInitial">Middle Initial</label>
            <input type="text" name="user_mInitial" value="{{ $user->user_mInitial }}" class="form-control">
        </div>

        <div class="form-group">
            <label for="user_suffix">Suffix</label>
            <input type="text" name="user_suffix" value="{{ $user->user_suffix }}" class="form-control">
        </div>

        <div class="form-group">
            <label for="user_email">Email</label>
            <input type="email" name="user_email" value="{{ $user->user_email }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="user_role">Role</label>
            <input type="text" name="user_role" value="{{ $user->user_role }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" class="form-control" required>
                <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Active</option>
                <option value="deactivated" {{ $user->status === 'deactivated' ? 'selected' : '' }}>Deactivated</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Update User</button>
        <a href="{{ route('admin.pnph_users.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
@endsection
