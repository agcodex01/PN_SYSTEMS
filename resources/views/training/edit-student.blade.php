@extends('layouts.nav')

@section('content')
<link rel="stylesheet" href="{{ asset('css/training/edit-student.css') }}">

<div class="edit-student-container">
    <h1>Edit Student Information</h1>

    <form action="{{ route('training.students.update', $student->user_id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="user_id">Student ID</label>
            <input type="text" name="user_id" id="user_id" class="form-control" value="{{ $student->user_id }}" readonly>
        </div>

        <div class="form-group">
            <label for="user_lname">Last Name</label>
            <input type="text" name="user_lname" id="user_lname" class="form-control" value="{{ $student->user_lname }}" required>
        </div>

        <div class="form-group">
            <label for="user_fname">First Name</label>
            <input type="text" name="user_fname" id="user_fname" class="form-control" value="{{ $student->user_fname }}" required>
        </div>

        <div class="form-group">
            <label for="user_mInitial">Middle Initial</label>
            <input type="text" name="user_mInitial" id="user_mInitial" class="form-control" value="{{ $student->user_mInitial }}">
        </div>

        <div class="form-group">
            <label for="user_suffix">Suffix</label>
            <input type="text" name="user_suffix" id="user_suffix" class="form-control" value="{{ $student->user_suffix }}">
        </div>

        <div class="form-group">
            <label for="user_email">Email</label>
            <input type="email" name="user_email" id="user_email" class="form-control" value="{{ $student->user_email }}" required>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('training.students.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
</div>
@endsection 