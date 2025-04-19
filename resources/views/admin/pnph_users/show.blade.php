@extends('layouts.admin_layout')

@section('content')
<link rel="stylesheet" href="{{ asset('css/admin/show.css') }}">


<div class="user-details-container">
    <h1 class="text-center">User Details</h1>
    <div class="user-details-card">
        <p><strong>User ID:</strong> {{ $user->user_id }}</p>
        <p><strong>First Name:</strong> {{ $user->user_fname }}</p>
        <p><strong>Last Name:</strong> {{ $user->user_lname }}</p>
        <p><strong>Email:</strong> {{ $user->user_email }}</p>
        <p><strong>Role:</strong> {{ $user->user_role }}</p>
        <p><strong>Status:</strong> 
            <span class="{{ $user->status === 'active' ? 'status-active' : 'status-inactive' }}">
                {{ $user->status === 'active' ? 'Activated' : 'Deactivated' }}
            </span>
        </p>
        <div class="action-buttons">
            <a href="{{ route('admin.pnph_users.index') }}" class="btn btn-primary">Back to User List</a>
        </div>
    </div>
</div>
@endsection