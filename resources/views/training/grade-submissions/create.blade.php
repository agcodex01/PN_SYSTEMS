@extends('layouts.nav')

@section('content')

<div class="create-submission-container">
    <h1>Create Grade Submission</h1>
    
    @if (session('error'))
        <div class="alert-custom alert-error-custom">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('training.grade-submissions.store') }}" method="POST" class="submission-form">
        @csrf

        <div class="form-grid">
            <div class="form-group-custom">
                <label for="school_id">Select School:</label>
                <select name="school_id" id="school_id" required>
                    <option value="">-- Select School --</option>
                    @foreach ($schools as $school)
                        <option value="{{ $school->school_id }}" 
                            data-terms='@json($school->terms)'
                            {{ old('school_id') == $school->school_id ? 'selected' : '' }}>
                            {{ $school->name }}
                        </option>
                    @endforeach
                </select>
                @error('school_id')
                    <span class="error-message-custom">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group-custom">
                <label for="class_id">Select Class:</label>
                <select name="class_id" id="class_id" required disabled>
                    <option value="">-- Select Class --</option>
                </select>
                 @error('class_id')
                    <span class="error-message-custom">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group-custom">
                <label for="semester">Semester:</label>
                <select name="semester" id="semester" required>
                    <option value="">-- Select Semester --</option>
                    <option value="1st" {{ old('semester') == '1st' ? 'selected' : '' }}>1st Semester</option>
                    <option value="2nd" {{ old('semester') == '2nd' ? 'selected' : '' }}>2nd Semester</option>
                </select>
                 @error('semester')
                    <span class="error-message-custom">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group-custom">
                <label for="term">Term:</label>
                <select name="term" id="term" required disabled>
                    <option value="">-- Select Term --</option>
                </select>
                 @error('term')
                    <span class="error-message-custom">{{ $message }}</span>
                @enderror
            </div>

             <div class="form-group-custom">
                <label for="academic_year">Academic Year:</label>
                <input type="text" name="academic_year" id="academic_year" placeholder="e.g., 2023-2024" required value="{{ old('academic_year') }}">
                 @error('academic_year')
                    <span class="error-message-custom">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group-custom">
            <label>Select Subjects:</label>
            <div id="subjects-container" class="subjects-grid">
                <div class="text-muted-custom">
                    Select a school to view available subjects
                </div>
            </div>
             @error('subject_ids')
                <span class="error-message-custom">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-actions-custom">
            <button type="submit" class="btn-custom btn-primary-custom">Create Submission</button>
            <a href="{{ route('training.grade-submissions.index') }}" class="btn-custom btn-secondary-custom">Cancel</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const schoolSelect = document.getElementById('school_id');
    const classSelect = document.getElementById('class_id');
    const termSelect = document.getElementById('term');
    const subjectsContainer = document.getElementById('subjects-container');

    // Function to populate terms dropdown
    function populateTerms(terms) {
        termSelect.innerHTML = '<option value="">-- Select Term --</option>';
        if (terms && terms.length > 0) {
            terms.forEach(term => {
                const option = document.createElement('option');
                option.value = term;
                option.textContent = term.charAt(0).toUpperCase() + term.slice(1).replace('_', ' ');
                termSelect.appendChild(option);
            });
            termSelect.disabled = false;
        } else {
            termSelect.disabled = true;
        }
         // Select the old value if available
        const oldTerm = '{{ old('term') }}';
        if (oldTerm) {
            termSelect.value = oldTerm;
        }
    }

    // Function to populate classes dropdown
    function populateClasses(classes) {
        classSelect.innerHTML = '<option value="">-- Select Class --</option>';
        if (classes && classes.length > 0) {
            classes.forEach(class_ => {
                const option = document.createElement('option');
                option.value = class_.class_id;
                option.textContent = `${class_.class_name} (${class_.batch})`;
                classSelect.appendChild(option);
            });
            classSelect.disabled = false;
        } else {
             classSelect.disabled = true;
        }
        // Select the old value if available
        const oldClass = '{{ old('class_id') }}';
        if (oldClass) {
            classSelect.value = oldClass;
        }
    }

    // Function to populate subjects checklist
    function populateSubjects(subjects) {
         subjectsContainer.innerHTML = '';
         const oldSubjects = @json(old('subject_ids', []));

         if (subjects.length === 0) {
             subjectsContainer.innerHTML = `
                 <div class="text-muted-custom">
                     No subjects available for this school
                 </div>
             `;
             return;
         }

         subjects.forEach(subject => {
             const isChecked = oldSubjects.includes(subject.id);
             subjectsContainer.innerHTML += `
                 <div class="subject-checkbox-item">
                     <input type="checkbox" name="subject_ids[]" 
                            value="${subject.id}" 
                            id="subject_${subject.id}"
                            ${isChecked ? 'checked' : ''}>
                     <label for="subject_${subject.id}">
                         ${subject.name} (${subject.offer_code})
                     </label>
                 </div>
             `;
         });
    }

    // Initial load based on old input
    const oldSchoolId = '{{ old('school_id') }}';
    if (oldSchoolId) {
        const selectedOption = schoolSelect.querySelector(`option[value="${oldSchoolId}"]`);
        if (selectedOption) {
             const terms = JSON.parse(selectedOption.dataset.terms || '[]');
             populateTerms(terms);

             // Fetch and populate classes for the old school
            fetch(`/training/api/schools/${oldSchoolId}/classes`)
                .then(response => response.json())
                .then(classes => {
                    populateClasses(classes);
                })
                .catch(error => {
                    console.error('Error fetching old classes:', error);
                });

            // Fetch and populate subjects for the old school
            fetch(`/training/subjects/by-school-and-class?school_id=${oldSchoolId}`)
                .then(response => response.json())
                .then(subjects => {
                    populateSubjects(subjects);
                })
                .catch(error => {
                    console.error('Error fetching old subjects:', error);
                });
        }
    }

    // Handle school selection
    schoolSelect.addEventListener('change', function() {
        const schoolId = this.value;
        const selectedOption = this.options[this.selectedIndex];
        const terms = JSON.parse(selectedOption.dataset.terms || '[]');

        // Reset and disable dependent fields
        classSelect.innerHTML = '<option value="">-- Select Class --</option>';
        classSelect.disabled = true;
        
        termSelect.innerHTML = '<option value="">-- Select Term --</option>';
        termSelect.disabled = true;
        
        subjectsContainer.innerHTML = `
            <div class="text-muted-custom">
                Select a school to view available subjects
            </div>
        `;

        if (schoolId) {
            // Populate terms
            populateTerms(terms);

            // Fetch classes for selected school
            fetch(`/training/api/schools/${schoolId}/classes`)
                .then(response => response.json())
                .then(classes => {
                    populateClasses(classes);
                })
                .catch(error => {
                    console.error('Error fetching classes:', error);
                    classSelect.innerHTML = '<option value="">Error loading classes</option>';
                    classSelect.disabled = true;
                });

            // Fetch subjects for selected school
            fetch(`/training/subjects/by-school-and-class?school_id=${schoolId}`)
                .then(response => response.json())
                .then(subjects => {
                    populateSubjects(subjects);
                })
                .catch(error => {
                    console.error('Error fetching subjects:', error);
                    subjectsContainer.innerHTML = `
                        <div class="error-message-custom">
                            Error loading subjects. Please try again.
                        </div>
                    `;
                });
        }
    });

    // Form validation
    document.querySelector('.submission-form').addEventListener('submit', function(e) {
        const selectedSubjects = document.querySelectorAll('input[name="subject_ids[]"]:checked');
        if (selectedSubjects.length === 0) {
            e.preventDefault();
            alert('Please select at least one subject');
        }
    });
});
</script>

<style>
    :root {
        --primary-color: #22bbea; /* Assuming a standard blue as primary color */
        --secondary-color: #ff9933;
        --success-color: #28a745;
        --danger-color: #dc3545;
        --warning-color: #ffc107;
        --info-color: #17a2b8;
        --light-bg: #f8f9fa;
        --dark-text: #343a40;
        --border-color: #ced4da;
        --card-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        --error-color: #dc3545;
    }

    body {
        font-family: 'Arial', sans-serif; /* Changed font for a more modern feel */
        line-height: 1.6;
        margin: 0;
        padding: 0;
        background-color: var(--light-bg);
        color: var(--dark-text);
    }

    .create-submission-container {
        max-width: 800px; /* Adjusted max-width for better form layout */
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: var(--card-shadow);
    }

    h1 {
        text-align: center; /* Center the heading */
        color: var(--primary-color);
        margin-bottom: 20px;
        font-size: 2rem; /* Slightly larger heading */
        font-weight: 600; /* Bolder heading */
    }

    .alert-custom {
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }

    .alert-error-custom {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }

    .submission-form {
        display: grid; /* Use CSS Grid for layout */
        gap: 20px; /* Add space between grid items */
    }

    .form-grid {
        display: grid; /* Grid for the top row of inputs */
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); /* Responsive columns */
        gap: 20px; /* Space between grid items in this section */
    }

    .form-group-custom {
        margin-bottom: 0; /* Remove default margin-bottom */
        display: flex;
        flex-direction: column; /* Stack label and input */
    }

    .form-group-custom label {
        display: block; /* Label on its own line */
        margin-bottom: 8px; /* Space between label and input */
        font-weight: 600; /* Bolder label */
        color: var(--dark-text);
    }

    .form-group-custom select,
    .form-group-custom input[type="text"] {
        width: 100%; /* Full width */
        padding: 10px;
        border: 1px solid var(--border-color);
        border-radius: 5px;
        font-size: 1rem;
        box-sizing: border-box; /* Include padding and border in element's total width and height */
    }

     .form-group-custom select:focus,
    .form-group-custom input[type="text"]:focus {
        border-color: var(--primary-color); /* Highlight on focus */
        outline: none; /* Remove default outline */
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.25); /* Subtle shadow on focus */
    }

    .subjects-checkbox-list {
        border: 1px solid var(--border-color);
        border-radius: 5px;
        padding: 15px;
        background-color: var(--light-bg);
        max-height: 200px; /* Limit height and add scroll */
        overflow-y: auto;
    }

    .subjects-grid {
        border: 1px solid var(--border-color);
        border-radius: 5px;
        padding: 15px;
        background-color: var(--light-bg);
        max-height: 250px; /* Slightly increased height */
        overflow-y: auto;

        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); /* Responsive grid for checkboxes */
        gap: 10px; /* Space between checkbox items */
    }

    .subjects-checkbox-list .text-muted-custom {
        color: #6c757d; /* Muted text color */
        font-size: 0.9rem;
    }

    .subjects-grid .text-muted-custom {
         color: #6c757d; /* Muted text color */
        font-size: 0.9rem;
    }

    .subject-item-custom {
        margin-bottom: 10px;
    }

    .subject-checkbox-item {
         /* No margin-bottom needed due to grid gap */
         display: flex; /* Use flexbox to align items horizontally */
         align-items: center; /* Vertically center items */
    }

    .subject-item-custom input[type="checkbox"] {
        margin-right: 8px;
        vertical-align: middle; /* Align checkbox with text */
    }

    .subject-checkbox-item input[type="checkbox"] {
        margin-right: 8px; /* Space between checkbox and label */
        /* vertical-align: middle; Remove as flexbox handles vertical alignment */
    }

    .subject-item-custom label {
        font-weight: normal; /* Normal weight for subject labels */
        color: var(--dark-text);
        cursor: pointer;
         vertical-align: middle; /* Align label with checkbox */
    }
     .subject-checkbox-item label {
        font-weight: normal; /* Normal weight for subject labels */
        color: var(--dark-text);
        cursor: pointer;
         /* vertical-align: middle; Remove as flexbox handles vertical alignment */
    }

    .form-actions-custom {
        margin-top: 20px;
        display: flex;
        gap: 10px; 
        justify-content: center;
    }

    .btn-custom {
        padding: 7px 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1rem;
        transition: background-color 0.3s ease;
        text-decoration: none; 
        display: inline-block;
        text-align: center;
    }

    .btn-primary-custom {
        background-color: var(--primary-color);
        color: #fff;
    }

    .btn-primary-custom:hover {
        background-color:rgb(0, 137, 179);
    }

    .btn-secondary-custom {
        background-color: var(--secondary-color);
        color: #fff;
    }

    .btn-secondary-custom:hover {
        background-color:rgb(255, 128, 0);
    }

    .error-message-custom {
        color: var(--error-color);
        font-size: 0.875rem;
        margin-top: 5px;
    }
</style>
@endsection
