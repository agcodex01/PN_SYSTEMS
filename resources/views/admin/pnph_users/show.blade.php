@extends('layouts.admin_layout')

@section('content')
<div class="container">
    <h1>User Details</h1>
    <p><strong>User ID:</strong> {{ $user->user_id }}</p>
    <p><strong>First Name:</strong> {{ $user->user_fname }}</p>
    <p><strong>Last Name:</strong> {{ $user->user_lname }}</p>
    <p><strong>Email:</strong> {{ $user->user_email }}</p>
    <p><strong>Role:</strong> {{ $user->user_role }}</p>
    <p><strong>Status:</strong> {{ $user->status === 'active' ? 'Activated' : 'Deactivated' }}</p>
    <a href="{{ route('admin.pnph_users.index') }}" class="btn btn-primary">Back to User List</a>
</div>
@endsection