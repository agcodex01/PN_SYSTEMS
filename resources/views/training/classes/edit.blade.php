@extends('layouts.nav')

@section('content')
<div class="page-container">
    <div class="header-section">
        <h2>Edit Class</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('training.classes.update', $class) }}" method="POST" class="form-container">
        @csrf
        @method('PUT')
        <input type="hidden" name="school_id" value="{{ $class->school_id }}">

        <div class="form-group">
            <label for="class_id">Class ID</label>
            <input type="text" id="class_id" name="class_id" value="{{ old('class_id', $class->class_id) }}" required>
            @error('class_id')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="class_name">Class Name</label>
            <input type="text" id="class_name" name="class_name" value="{{ old('class_name', $class->class_name) }}" required>
            @error('class_name')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="student_ids">Select Students</label>
            <div class="filter-section">
                <select id="batchFilter" class="form-select">
                    <option value="">All Batches</option>
                    @foreach($students->pluck('studentDetail.batch')->unique() as $batch)
                        <option value="{{ $batch }}">{{ $batch }}</option>
                    @endforeach
                </select>
            </div>
            <div class="students-container">
                @foreach($students as $student)
                    <div class="student-checkbox" data-batch="{{ $student->studentDetail->batch }}">
                        <input type="checkbox" 
                            id="student_{{ $student->user_id }}" 
                            name="student_ids[]" 
                            value="{{ $student->user_id }}"
                            {{ (is_array(old('student_ids', $class->students->pluck('user_id')->toArray())) && 
                                in_array($student->user_id, old('student_ids', $class->students->pluck('user_id')->toArray()))) ? 'checked' : '' }}>
                        <label for="student_{{ $student->user_id }}">
                            {{ $student->user_id }} - {{ $student->user_fname }} {{ $student->user_mInitial }}. {{ $student->user_lname }}
                            <span class="batch-tag">{{ $student->studentDetail->batch }}</span>
                        </label>
                    </div>
                @endforeach
            </div>
            @error('student_ids')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const batchFilter = document.getElementById('batchFilter');
            const studentCheckboxes = document.querySelectorAll('.student-checkbox');

            batchFilter.addEventListener('change', function() {
                const selectedBatch = this.value;
                
                studentCheckboxes.forEach(checkbox => {
                    if (!selectedBatch || checkbox.dataset.batch === selectedBatch) {
                        checkbox.style.display = 'flex';
                    } else {
                        checkbox.style.display = 'none';
                    }
                });
            });
        });
        </script>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Update Class</button>
            <a href="{{ route('training.schools.show', ['school' => $class->school_id]) }}" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<style>
.page-container {
    padding: 20px;
    max-width: 100%;
}

.header-section {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
}

.header-section h2 {
    font-size: 24px;
    color: #333;
    margin: 0;
}

.form-container {
    background: white;
    padding: 24px;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #333;
    font-weight: 500;
}

.form-select,
.form-group input[type="text"] {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.students-container {
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 12px;
    background: #f8f9fa;
}

.student-checkbox {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
    padding: 4px;
}

.student-checkbox:hover {
    background: #e9ecef;
    border-radius: 4px;
}

.student-checkbox input[type="checkbox"] {
    margin-right: 8px;
}

.student-checkbox label {
    margin-bottom: 0;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
}

.batch-tag {
    background: #e9ecef;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.85em;
    color: #495057;
}

.filter-section {
    margin-bottom: 12px;
}

.filter-section select {
    width: 200px;
    padding: 6px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: white;
    font-size: 14px;
}

.error-message {
    color: #dc3545;
    font-size: 14px;
    margin-top: 4px;
}

.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 24px;
}

.btn-submit {
    padding: 8px 16px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}

.btn-submit:hover {
    background-color: #45a049;
}

.btn-cancel {
    padding: 8px 16px;
    background-color: #f8f9fa;
    color: #333;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-decoration: none;
    font-size: 14px;
}

.btn-cancel:hover {
    background-color: #e9ecef;
}
</style>
@endsection
