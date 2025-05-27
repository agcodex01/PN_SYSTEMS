@extends('layouts.student_layout')

@section('content')
<div class="submission-container">
    <div class="submission-card">
        <div class="card-header-custom">
            <h2 style="color: #333;">Grade Submission</h2>
            <p style="color: #555;">{{ $gradeSubmission->semester }} {{ $gradeSubmission->term }} {{ $gradeSubmission->academic_year }}</p>
        </div>

        <div class="card-body-custom">
            <!-- Display validation errors -->
            @if($errors->any())
                <div class="alert-custom alert-danger-custom">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('error'))
                <div class="alert-custom alert-danger-custom">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert-custom alert-success-custom">
                    {{ session('success') }}
                </div>
            @endif

            @php
                $proof = \App\Models\GradeSubmissionProof::where('grade_submission_id', $gradeSubmission->id)
                    ->where('user_id', Auth::user()->user_id)
                    ->first();
            @endphp

            @if($proof && $proof->status === 'rejected')
                <div class="rejection-notice">
                    <h3>Previous Submission Rejected</h3>
                    <p>Your previous proof was rejected. Please resubmit your grades and proof.</p>
                </div>
            @endif

            @if($proof && $proof->status === 'approved')
                <div class="alert-custom alert-success-custom">
                    <h3>Grades Approved</h3>
                    <p>Your grades have been approved.</p>
                </div>
            @endif

            <form action="{{ route('student.submit-grades.store', $gradeSubmission->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grades-section">
                    <h3>Enter Grades</h3>
                    @if($subjects->isNotEmpty())
                        <table class="grades-table">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subjects as $subject)
                                    <tr>
                                        <td>{{ $subject->name }}</td>
                                        <td>
                                            <input type="text" 
                                               name="grades[{{ $subject->id }}]" 
                                               value="{{ $subject->grade ?? '' }}"
                                               class="grade-input {{ $errors->has('grades.' . $subject->id) ? 'is-invalid' : '' }}"
                                               pattern="^(5(\.0)?|[1-4](\.[0-9]{1,2})?|INC|NC|DR)$"
                                               title="Please match requested format: 1.0-5.0 or INC, NC, DR"
                                               required>
                                        @error('grades.' . $subject->id)
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                            <small class="form-text text-muted">Please match requested format: 1.0-5.0 or INC, NC, DR</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert-custom alert-warning-custom">
                            No subjects found for this submission. You may still upload your proof and submit.
                        </div>
                    @endif
                </div>

                <div class="proof-section mt-4">
                    <h3>Upload Proof</h3>
                    <div class="form-group">
                        <label for="proof">Upload your proof document (PDF, DOC, DOCX, JPG, JPEG, PNG)</label>
                        <input type="file" 
                               name="proof" 
                               id="proof" 
                               class="form-control" 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               required>
                        <small class="form-text text-muted">Maximum file size: 10MB</small>
                    </div>
                </div>

                <div class="form-actions mt-4">
                    <!-- SUBMIT BUTTON ALWAYS RENDERED -->
                    <button type="submit" class="btn-custom btn-primary-custom">
                        {{ $proof && $proof->status === 'rejected' ? 'Resubmit Grades' : 'Submit Grades' }}
                    </button>
                    <a href="{{ route('student.dashboard') }}" class="btn-custom btn-secondary-custom">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .submission-container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 0 15px;
    }

    .submission-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        overflow: hidden;
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

    .card-body-custom {
        padding: 20px;
    }

    .alert-custom {
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .alert-danger-custom {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }

    .alert-success-custom {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }

    .alert-warning-custom {
        background-color: #fff3cd;
        border: 1px solid #ffeeba;
        color: #856404;
    }

    .rejection-notice {
        background-color: #fff3cd;
        border: 1px solid #ffeeba;
        color: #856404;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .rejection-notice h3 {
        margin-top: 0;
        margin-bottom: 10px;
        color: #856404;
    }

    .grades-section {
        margin-bottom: 30px;
    }

    .grades-section h3 {
        margin-top: 0;
        margin-bottom: 15px;
        color: var(--dark-text);
    }

    .grades-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .grades-table th,
    .grades-table td {
        padding: 10px;
        border: 1px solid #ddd;
    }

    .grades-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-align: left;
    }

    .grade-input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
    }

    .form-text {
        display: block;
        margin-top: 5px;
        font-size: 0.875rem;
    }

    .text-muted {
        color: #6c757d;
    }

    .proof-section {
        margin-bottom: 30px;
    }

    .proof-section h3 {
        margin-top: 0;
        margin-bottom: 15px;
        color: var(--dark-text);
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        color: var(--dark-text);
    }

    .form-control {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
    }

    .form-actions {
        display: flex;
        gap: 10px;
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

    .btn-custom.btn-primary-custom {
        background-color: #007bff !important;
        color: #fff !important;
        border: 2px solid #0056b3 !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .btn-custom.btn-primary-custom:hover {
        background-color: #0056b3 !important;
    }
    .btn-custom.btn-secondary-custom {
        background-color: #6c757d !important;
        color: #fff !important;
        border: 2px solid #545b62 !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .btn-custom.btn-secondary-custom:hover {
        background-color: #545b62 !important;
    }
</style>
@endsection 