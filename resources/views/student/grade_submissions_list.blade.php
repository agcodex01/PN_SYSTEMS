@extends('layouts.student_layout')

@section('content')
<div class="grade-submissions-list-container">
    <h1>My Grade Submissions</h1>

    <form method="GET" action="{{ route('student.grade-submissions.list') }}" style="margin-bottom: 20px;">
        <select name="filter_key" onchange="this.form.submit()" style="padding: 6px 12px; border-radius: 4px; border: 1px solid #ccc;">
            <option value="">All Submissions</option>
            @if(isset($filterOptions))
                @foreach($filterOptions as $option)
                    <option value="{{ $option }}" {{ request('filter_key') == $option ? 'selected' : '' }}>{{ $option }}</option>
                @endforeach
            @endif
        </select>
    </form>

    @if($gradeSubmissions->isEmpty())
        <div class="no-submissions">
            <p>No grade submissions found.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Semester</th>
                        <th>Term</th>
                        <th>Academic Year</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($gradeSubmissions as $submission)
                        @php
                            $studentPivot = $submission->students->where('pivot.user_id', Auth::id())->first();
                            $overallStatus = $studentPivot ? ($studentPivot->pivot->status ?? 'pending') : 'pending';
                        @endphp
                        <tr>
                            <td>{{ $submission->semester ?? 'N/A' }}</td>
                            <td>{{ $submission->term ?? 'N/A' }}</td>
                            <td>{{ $submission->academic_year ?? 'N/A' }}</td>
                            <td>
                                <span class="status {{ $overallStatus }}">
                                    {{ ucfirst($overallStatus) }}
                                </span>
                            </td>
                            <td>{{ $submission->created_at ? $submission->created_at->format('M d, Y') : 'N/A' }}</td>
                            <td>
                                @if(in_array($overallStatus, ['submitted', 'approved']))
                                    <a href="{{ route('student.view-submission', $submission->id) }}" class="btn btn-sm btn-secondary">View Submission</a>
                                @else
                                    <a href="{{ route('student.submit-grades.show', $submission->id) }}" class="btn btn-sm btn-primary">Submit Grades</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<style>
.grade-submissions-list-container {
    padding: 20px;
    max-width: 1100px;
    margin: 0 auto;
}

h1 {
    color: #333;
    margin-bottom: 20px;
    font-size: 24px;
}

.table-responsive {
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
}

.table th, .table td {
    padding: 10px 12px;
    border: 1px solid #ddd;
    text-align: left;
}

.table th {
    background: #f8f9fa;
    color: #333;
    font-weight: 600;
}

.status {
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 14px;
    font-weight: 500;
}
.status.pending {
    background: #fff3cd;
    color: #856404;
}
.status.approved {
    background: #d4edda;
    color: #155724;
}
.status.rejected {
    background: #f8d7da;
    color: #721c24;
}
.status.submitted {
    background-color: #cce5ff;
    color: #004085;
}
.btn {
    display: inline-block;
    color: white;
    padding: 6px 14px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 13px;
    transition: background-color 0.3s ease;
}
.btn-primary {
    background-color: #007bff;
}
.btn-secondary {
    background-color: #6c757d;
}
.btn-primary:hover {
    background-color: #0056b3;
}
.btn-secondary:hover {
    background-color: #5a6268;
}
.no-submissions {
    text-align: center;
    padding: 40px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.no-submissions p {
    color: #6c757d;
    font-size: 1.1em;
    margin: 0;
}
</style>
@endsection 