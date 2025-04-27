@extends('layouts.educator_layout')

@section('content')
<link rel="stylesheet" href="{{ asset('css/training/student-info.css') }}">

<div class="page-container">
    <div class="header-section">
       <h1 style="font-weight: 300;">Students Information</h1>
       <hr>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form method="GET" action="{{ route('educator.students.index') }}" class="filter-form">
        <div class="form-group">
            <label for="batch">Filter by Batch</label>
            <select name="batch" id="batch" class="form-control" onchange="this.form.submit()">
                <option value="">Select Batch</option>
                @foreach ($batches as $batch)
                    <option value="{{ $batch }}" {{ request('batch') == $batch ? 'selected' : '' }}>
                        {{ $batch }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    <br>

    <div class="table-wrapper">
        <div class="table-header">
            <div class="header-cell">USER ID</div>
            <div class="header-cell">STUDENT ID</div>
            <div class="header-cell">LAST NAME</div>
            <div class="header-cell">FIRST NAME</div>
            <div class="header-cell">MI</div>
            <div class="header-cell">SUFFIX</div>
            <div class="header-cell">SEX</div>
            <div class="header-cell">EMAIL</div>
            <div class="header-cell act1">ACTIONS</div>
        </div>

        @forelse($students as $student)
            <div class="table-row">
                <div class="cell">{{ $student->user_id }}</div>
                <div class="cell">{{ $student->studentDetail->student_id ?? 'N/A' }}</div>
                <div class="cell">{{ $student->user_lname }}</div>
                <div class="cell">{{ $student->user_fname }}</div>
                <div class="cell">{{ $student->user_mInitial }}</div>
                <div class="cell">{{ $student->user_suffix ?? '' }}</div>
                <div class="cell">{{ $student->studentDetail->gender ?? 'N/A' }}</div>
                <div class="cell">{{ $student->user_email }}</div>
                <div class="cell">
                    <div class="action-buttons">
                        <a href="{{ route('educator.students.view', $student->user_id) }}" class="btn btn-view">View</a>

                       
                    </div>
                </div>
            </div>
        @empty
            <div class="table-row">
                <div class="cell empty-message">No students found</div>
            </div>
        @endforelse
    </div>

    <div class="pagination-container">
        {{ $students->links() }}
    </div>
</div>

@endsection
