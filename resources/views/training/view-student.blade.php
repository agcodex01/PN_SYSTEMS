@extends('layouts.nav')

@section('content')
<link rel="stylesheet" href="{{ asset('css/training/view-student.css') }}">

<div class="view-student-container">
    <h1>Student Details</h1>

    <div class="student-details">
        <div class="detail-row">
            <span class="detail-label">User ID:</span>
            <span class="detail-value">{{ $student->user_id }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Student ID:</span>
            <span class="detail-value">{{ $student->student_id }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Last Name:</span>
            <span class="detail-value">{{ $student->user_lname }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">First Name:</span>
            <span class="detail-value">{{ $student->user_fname }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Middle Initial:</span>
            <span class="detail-value">{{ $student->user_mInitial }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Suffix:</span>
            <span class="detail-value">{{ $student->user_suffix }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Batch:</span>
            <span class="detail-value">{{ $student->batch }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Email:</span>
            <span class="detail-value">{{ $student->user_email }}</span>
        </div>
    </div>

    <div class="action-buttons">
        <a href="{{ route('training.students.edit', $student->user_id) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route('training.students.index') }}" class="btn btn-secondary">Back to List</a>
    </div>
</div>
@endsection 