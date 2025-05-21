@extends('layouts.admin_layout')
@section('content')

<h2>Notifications</h2>
<ul>
    @foreach ($notifications as $notification)
        <li>
            {{ $notification->gradeSubmission->semester }} - {{ $notification->gradeSubmission->term }} ({{ $notification->gradeSubmission->academic_year }})
            <a href="{{ route('student.grade-submissions.show', $notification->grade_submission_id) }}">
                View Submission
            </a>
        </li>
    @endforeach
</ul>

@endsection