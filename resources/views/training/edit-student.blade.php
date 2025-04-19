@extends('layouts.nav')

@section('content')
<link rel="stylesheet" href="{{ asset('css/training/edit-student.css') }}">

<style>
.student-id-section {
    border: 1px solid #ddd;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
    background-color: #f8f9fa;
}

.student-id-section .form-group {
    margin-bottom: 15px;
}

.student-id-preview {
    font-family: monospace;
    font-size: 1.2em;
    padding: 10px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-top: 10px;
}

.form-group {
    margin-bottom: 1rem;
}

.form-control {
    width: 100%;
    padding: 0.375rem 0.75rem;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
}

select.form-control {
    cursor: pointer;
}

.student-id-components {
    display: flex;
    gap: 10px;
    align-items: center;
    margin-bottom: 10px;
}

.student-id-components .form-group {
    margin-bottom: 0;
}

.student-number-input {
    width: 100px;
}

.alert {
    padding: 10px;
    margin: 10px 0;
    border-radius: 4px;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
}
</style>

<div class="edit-student-container">
    <h1>Edit Student Information</h1>

    <form action="{{ route('training.students.update', $student->user_id) }}" method="POST" id="studentForm" onsubmit="return validateForm()">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="user_id">User ID</label>
            <input type="text" name="user_id" id="user_id" class="form-control" value="{{ $student->user_id }}" readonly>
        </div>

        <div class="form-group">
            <label for="batch">Batch Year</label>
            <input type="text" name="batch" id="batch" class="form-control" value="{{ $student->studentDetail->batch ?? '' }}" required 
                   placeholder="Enter batch year (e.g. 2024)" pattern="[0-9]{4}" maxlength="4"
                   onchange="updateStudentId()">
        </div>

        <div class="student-id-section">
            <h4>Student ID Components</h4>
            <div class="student-id-components">
                <div class="form-group">
                    <label for="group">Group</label>
                    <select name="group" id="group" class="form-control" required onchange="updateStudentId()">
                        <option value="">Select Group</option>
                        <option value="01" {{ ($student->studentDetail->group ?? '') == '01' ? 'selected' : '' }}>Group 01</option>
                        <option value="02" {{ ($student->studentDetail->group ?? '') == '02' ? 'selected' : '' }}>Group 02</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="student_number">Student Number</label>
                    <input type="text" name="student_number" id="student_number" 
                           class="form-control student-number-input" required
                           pattern="[0-9]{4}" maxlength="4" placeholder="0001"
                           value="{{ $student->studentDetail->student_number ?? '' }}"
                           onchange="updateStudentId()">
                </div>

                <div class="form-group">
                    <label for="training_code">Training Code</label>
                    <select name="training_code" id="training_code" class="form-control" required onchange="updateStudentId()">
                        <option value="">Select Code</option>
                        @foreach(['C1', 'C2', 'C3', 'C4', 'T1', 'T2', 'T3', 'T4'] as $code)
                            <option value="{{ $code }}" {{ ($student->studentDetail->training_code ?? '') == $code ? 'selected' : '' }}>
                                {{ $code }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- <div class="student-id-preview">
                Generated ID: <span id="generatedStudentId">-</span>
            </div> -->
        </div>

        <input type="hidden" name="student_id" id="student_id" required>

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

<script>
function updateStudentId() {
    const batch = document.getElementById('batch').value;
    const group = document.getElementById('group').value;
    const studentNumber = document.getElementById('student_number').value.padStart(4, '0');
    const trainingCode = document.getElementById('training_code').value;

    if (batch && group && studentNumber && trainingCode) {
        const studentId = `${batch}${group}${studentNumber}${trainingCode}`;
        console.log('Generated Student ID:', studentId);
    }
}

function validateForm() {
    const batch = document.getElementById('batch').value;
    const group = document.getElementById('group').value;
    const studentNumber = document.getElementById('student_number').value;
    const trainingCode = document.getElementById('training_code').value;

    if (!batch || !group || !studentNumber || !trainingCode) {
        alert('Please fill in all Student ID components (Batch Year, Group, Student Number, and Training Code)');
        return false;
    }
    return true;
}

// Add input validation for student number
document.getElementById('student_number').addEventListener('input', function(e) {
    let value = e.target.value;
    // Remove any non-numeric characters
    value = value.replace(/[^0-9]/g, '');
    // Ensure it's not longer than 4 digits
    if (value.length > 4) {
        value = value.slice(0, 4);
    }
    e.target.value = value;
    updateStudentId();
});

// Add input validation for batch year
document.getElementById('batch').addEventListener('input', function(e) {
    let value = e.target.value;
    // Remove any non-numeric characters
    value = value.replace(/[^0-9]/g, '');
    // Ensure it's not longer than 4 digits
    if (value.length > 4) {
        value = value.slice(0, 4);
    }
    e.target.value = value;
    updateStudentId();
});
</script>
@endsection 