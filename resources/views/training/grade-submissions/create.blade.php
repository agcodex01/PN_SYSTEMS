@extends('layouts.nav')

@section('content')


<h1>Create Grade Submission</h1>
<hr>
<div class="container">
    <!-- Error Message -->
    @if (session('error'))
        <div class="alert error">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('training.grade-submissions.store') }}" method="POST" class="form-container">
        @csrf

         <div class="form-group">
                <label for="school_id">Select School</label>
                <select name="school_id" id="school_id" required>
                    <option value="">-- Select School --</option>
                    @foreach ($schools as $school)
                        <option value="{{ $school->school_id }}" 
                            {{ request('school_id') == $school->school_id ? 'selected' : '' }}>
                            {{ $school->name }}
                        </option>
                    @endforeach
                </select>
        </div>

        <div class="form-group">
            <label for="class_id">Select Class</label>
            <select name="class_id" id="class_id" required>
                <option value="">-- Select Class --</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->class_id }}">{{ $class->class_name }} ({{ $class->batch }})</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="semester">Semester</label>
            <input type="text" name="semester" id="semester" required>
        </div>

        <div class="form-group">
            <label for="term">Term</label>
            <input type="text" name="term" id="term" required>
        </div>

        <div class="form-group">
            <label for="academic_year">Academic Year</label>
            <input type="text" name="academic_year" id="academic_year" required>
        </div>

        <div class="form-group">
            <label for="subject_ids">Select Subjects</label>
            <select name="subject_ids[]" id="subject_ids" multiple required>
                <!-- Subjects will populate dynamically based on school selection -->
                @if (!empty($subjects))
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->offer_code }})</option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="form-actions">
    <button type="submit" class="btn-submit">Create Submission</button>
    <a href="{{ route('training.grade-submissions.index') }}" class="btn-cancel">Cancel</a>
</div>
    </form>
</div>

<!-- JavaScript to reload subjects when school is selected -->
<script>
document.getElementById('school_id').addEventListener('change', function () {
    const schoolId = this.value;
    const subjectsDropdown = document.getElementById('subject_ids');
    subjectsDropdown.innerHTML = ''; // Clear the dropdown

    // If a school is selected, reload the page with school_id
    if (schoolId) {
        window.location.href = `?school_id=${schoolId}`;
    }
});
</script>


<!-- Simple CSS Styling -->
<style>


h1 {
    margin-bottom: 20px;
    font-weight: 300;
}

hr {
    margin-bottom: 20px;
}

.alert.error {
    background-color: #f8d7da;
    padding: 10px 15px;
    border: 1px solid #f5c6cb;
    color: #721c24;
    border-radius: 5px;
    margin-bottom: 20px;
}

.form-container {
    background-color: beige;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ddd;
    width: 60%;
    align-items: center;    
    margin-left: 20%;

}

.form-group {
    margin:20px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
}

input[type="text"] {
    width: 600px;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

select{
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
    width: 620px;
}

select[multiple] {
    height: 150px;
    width: 630px;
    
}

.btn-submit:hover {
    background-color: #45a049;
}


.form-actions {
    display: flex;
    justify-content: space-between;
    gap: 10px; /* Add spacing between buttons */
}

.btn-cancel {
    display: block;
    width: 48%; /* Same width as the submit button */
    background-color: #f44336; /* Red color for cancel */
    color: white;
    font-size: 1rem;
    padding: 12px;
    border: none;
    border-radius: 5px;
    text-align: center;
    text-decoration: none; /* Remove underline */
    cursor: pointer;
}

.btn-cancel:hover {
    background-color: #d32f2f; /* Darker red on hover */
}

.btn-submit {
    width: 48%; /* Adjust width to match cancel button */
    background-color: #4CAF50;
    color: white;
    font-size: 1rem;
    padding: 12px;
    border: none;
    border-radius: 5px;
    text-align: center;
    text-decoration: none; /* Remove underline */
    cursor: pointer;
}
</style>
@endsection
