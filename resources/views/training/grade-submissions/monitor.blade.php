@extends('layouts.nav')

@section('content')
<div class="monitor-container">
    <div class="monitor-card">
        <div class="card-header-custom">
            <h2>Grade Submission Monitor</h2>
            {{--
            @if($gradeSubmission)
                <p class="submission-id-small">Submission ID: {{ $gradeSubmission->id }}</p>
            @endif
            --}}
        </div>

        <div class="card-body-custom">
            @if(isset($message))
                <div class="alert-custom alert-warning-custom">
                    {{ $message }}
                </div>
            @endif

            {{-- Filtering Form --}}
            <div class="filter-section">
                 <h3>Filter Submissions</h3>
                 <form action="{{ route('training.grade-submissions.index') }}" method="GET" class="filter-form-custom">
                     <div class="form-group-custom filter-group">
                         <label for="filter_key" class="visually-hidden">Semester Term Academic Year</label>
                         <select name="filter_key" id="filter_key" class="form-control-custom">
                             <option value="">All Submissions</option>
                             @foreach ($filterOptions as $option)
                                 <option value="{{ $option }}" {{ request('filter_key') == $option ? 'selected' : '' }}>{{ $option }}</option>
                             @endforeach
                         </select>
                     </div>
                     <button type="submit" class="btn-custom btn-primary-custom btn-sm-custom">Filter</button>
                     <a href="{{ route('training.grade-submissions.index') }}" class="btn-custom btn-secondary-custom btn-sm-custom">Reset</a>
                 </form>
            </div>
        </div>
    </div>

    @foreach($schools as $school)
        @php $schoolSubmissions = $submissionsBySchool[$school->school_id] ?? collect(); @endphp
        @if($schoolSubmissions->isNotEmpty())
            <div class="school-container">
                <div class="school-header">
                    <h3>{{ $school->name }}</h3>
                </div>
                <div class="school-content">
                    @foreach($schoolSubmissions as $gradeSubmission)
                        @php
                            // Fetch students for this submission
                            $students = \DB::table('grade_submission_subject')
                                ->join('pnph_users', 'grade_submission_subject.user_id', '=', 'pnph_users.user_id')
                                ->where('grade_submission_subject.grade_submission_id', $gradeSubmission->id)
                                ->where('pnph_users.user_role', 'student')
                                ->select('pnph_users.user_id', 'pnph_users.user_fname', 'pnph_users.user_lname')
                                ->distinct()
                                ->get()
                                ->map(function ($student) {
                                    return (object)[
                                        'user_id' => $student->user_id,
                                        'name' => $student->user_fname . ' ' . $student->user_lname
                                    ];
                                });
                            // Fetch subjects for this submission
                            $subjects = \DB::table('grade_submission_subject')
                                ->join('subjects', 'grade_submission_subject.subject_id', '=', 'subjects.id')
                                ->where('grade_submission_subject.grade_submission_id', $gradeSubmission->id)
                                ->select('subjects.*')
                                ->distinct()
                                ->get();
                            // Fetch grades for this submission
                            $rawGrades = \DB::table('grade_submission_subject')
                                ->join('subjects', 'grade_submission_subject.subject_id', '=', 'subjects.id')
                                ->where('grade_submission_subject.grade_submission_id', $gradeSubmission->id)
                                ->select(
                                    'grade_submission_subject.user_id',
                                    'grade_submission_subject.subject_id',
                                    'grade_submission_subject.grade',
                                    'grade_submission_subject.status',
                                    'subjects.name as subject_name'
                                )
                                ->get();
                            $grades = [];
                            foreach ($rawGrades as $grade) {
                                if (!isset($grades[$grade->user_id])) {
                                    $grades[$grade->user_id] = [];
                                }
                                $grades[$grade->user_id][$grade->subject_id] = (object)[
                                    'grade' => $grade->grade,
                                    'status' => $grade->status,
                                    'subject_name' => $grade->subject_name
                                ];
                            }
                        @endphp
                        <div class="submission-section">
                            <div class="submission-header">
                                <h4>{{ $gradeSubmission->semester }} {{ $gradeSubmission->term }} {{ $gradeSubmission->academic_year }}</h4>
                            </div>
                            <div class="table-responsive-custom">
                                <table class="grade-monitor-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center-custom" style="width: 80px">Student ID</th>
                                            <th style="width: 180px">Name</th>
                                            @foreach($subjects as $subject)
                                                <th class="text-center-custom">{{ $subject->name }}</th>
                                            @endforeach
                                            <th class="text-center-custom" style="width: 120px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($students as $student)
                                            <tr>
                                                <td class="text-center-custom small-text">{{ $student->user_id }}</td>
                                                <td class="small-text">{{ $student->name }}</td>
                                                @foreach($subjects as $subject)
                                                    <td class="text-center-custom">
                                                        @php
                                                            $grade = $grades[$student->user_id][$subject->id] ?? null;
                                                            $gradeValue = $grade ? $grade->grade : null;
                                                        @endphp
                                                        @if($gradeValue !== null)
                                                            <div class="grade-value small-text">
                                                                @if(in_array(strtoupper($gradeValue), ['INC', 'NC', 'DR']))
                                                                    {{ strtoupper($gradeValue) }}
                                                                @else
                                                                    {{ number_format((float)$gradeValue, 1) }}
                                                                @endif
                                                            </div>
                                                        @else
                                                            <span class="text-muted-custom small-text">Not submitted</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                                <td class="text-center-custom">
                                                    @php
                                                        $proof = \App\Models\GradeSubmissionProof::where('grade_submission_id', $gradeSubmission->id)
                                                            ->where('user_id', $student->user_id)
                                                            ->first();
                                                    @endphp
                                                    @if($proof)
                                                        <a href="{{ route('training.grade-submissions.view-proof', ['gradeSubmission' => $gradeSubmission->id, 'student' => $student->user_id]) }}"
                                                           class="btn-custom btn-sm-custom btn-primary-custom">
                                                            View Proof
                                                        </a>
                                                    @else
                                                        <span class="text-muted-custom small-text">No proof</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
</div>

<style>
    :root {
        --primary-color: #22bbea;
        --secondary-color: #ff9933;
        --success-color: #28a745;
        --danger-color: #dc3545;
        --warning-color: #ffc107;
        --info-color: #17a2b8;
        --light-bg: #f8f9fa;
        --dark-text: #343a40;
        --border-color: #dee2e6;
        --card-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        --error-color: #dc3545;
    }

    body {
        font-family: 'Arial', sans-serif; /* Using a common sans-serif font */
        line-height: 1.6;
        margin: 0;
        padding: 0;
        background-color: var(--light-bg);
        color: var(--dark-text);
    }

    .monitor-container {
        max-width: 1200px; /* Wider container for the monitor table */
        margin: 20px auto;
        padding: 0 15px;
    }

    .monitor-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
        margin-bottom: 20px;
    }

    .card-header-custom {
        background-color: var(--primary-color);
        color: #fff;
        padding: 15px 20px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }

    .card-header-custom h2 {
        margin: 0 0 5px 0;
        font-size: 1.5rem;
        font-weight: 600;
    }
     .submission-id-small {
        margin: 0;
        font-size: 0.9rem;
        opacity: 0.9;
     }

    .card-body-custom {
        padding: 20px;
    }

    .alert-custom {
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }

     .alert-warning-custom {
        background-color: #fff3cd;
        border: 1px solid #ffc107;
        color: #856404;
    }

    .filter-section {
        margin-bottom: 20px;
        padding: 15px;
        background-color: var(--light-bg);
        border-radius: 5px;
    }
     .filter-section h3 {
        margin-top: 0;
        margin-bottom: 10px;
        font-size: 1.25rem;
        color: var(--dark-text);
     }

    .filter-form-custom {
        display: flex;
        align-items: center;
        gap: 15px; /* Space between form elements */
        flex-wrap: wrap; /* Allow items to wrap on smaller screens */
    }

    .form-group-custom.filter-group {
        margin-bottom: 0; /* Remove margin from form group in flex container */
        flex-grow: 1; /* Allow the select to grow */
        max-width: 300px; /* Limit width for better layout */
    }

     .form-control-custom {
        width: 100%; /* Make select fill its container */
        padding: 8px 10px;
        border: 1px solid var(--border-color);
        border-radius: 5px;
        font-size: 1rem;
        box-sizing: border-box;
     }
     .form-control-custom:focus {
         border-color: var(--primary-color);
         outline: none;
         box-shadow: 0 0 5px rgba(0, 123, 255, 0.25);
     }

    .visually-hidden {
        position: absolute;
        width: 1px;
        height: 1px;
        margin: -1px;
        padding: 0;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        border: 0;
    }

    .btn-custom {
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1rem;
        transition: background-color 0.3s ease;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }

     .btn-sm-custom {
        padding: 5px 10px;
        font-size: 0.875rem;
     }

    .btn-primary-custom {
        background-color: var(--primary-color);
        color: #fff;
    }

    .btn-primary-custom:hover {
        background-color: #0056b3;
    }

    .btn-secondary-custom {
        background-color: var(--secondary-color);
        color: #fff;
    }

    .btn-secondary-custom:hover {
        background-color: #ff751a;
    }

    .table-responsive-custom {
        width: 100%;
        overflow-x: auto; /* Add horizontal scroll on small screens */
        margin-top: 20px; /* Space above the table */
    }

    .grade-monitor-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid var(--border-color);
    }

    .grade-monitor-table th,
    .grade-monitor-table td {
        padding: 10px;
        border: 1px solid var(--border-color);
        text-align: left;
        vertical-align: middle;
        font-size: 0.9rem;
    }

    .grade-monitor-table th {
        background-color: var(--light-bg);
        font-weight: 600;
        text-align: center; /* Center table headers */
    }

    .grade-monitor-table td {
         text-align: center; /* Center table cells by default */
    }

     .grade-monitor-table tbody tr:nth-child(even) {
        background-color: #f9f9f9; /* Zebra striping */
     }

    .grade-monitor-table tbody tr:hover {
        background-color: #e9e9e9;
    }

    .text-center-custom {
        text-align: center;
    }

    .small-text {
        font-size: 0.85rem;
    }

    .grade-value {
        font-weight: 500;
    }

     .text-muted-custom {
        color: #6c757d;
     }

    .debug-section {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
    }
     .debug-section h3 {
        font-size: 1.25rem;
        margin-top: 0;
        margin-bottom: 10px;
        color: var(--dark-text);
     }

    .debug-pre {
        background-color: #e9ecef;
        padding: 15px;
        border-radius: 5px;
        overflow-x: auto; /* Add scroll for long debug output */
        font-size: 0.85rem;
        color: #333;
    }

    .school-container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: var(--card-shadow);
        margin-bottom: 30px;
        overflow: hidden;
    }

    .school-header {
        background-color: var(--primary-color);
        color: #fff;
        padding: 15px 20px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }

    .school-header h3 {
        margin: 0;
        font-size: 1.3rem;
        font-weight: 600;
    }

    .school-content {
        padding: 20px;
    }

    .submission-section {
        margin-bottom: 30px;
    }

    .submission-section:last-child {
        margin-bottom: 0;
    }

    .submission-header {
        margin-bottom: 15px;
    }

    .submission-header h4 {
        margin: 0;
        color: var(--dark-text);
        font-size: 1.1rem;
        font-weight: 500;
    }
</style>
@endsection 