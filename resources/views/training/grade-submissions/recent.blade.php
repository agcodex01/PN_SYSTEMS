@extends('layouts.nav')

@section('content')
<div class="page-container">
    <div class="page-header">
        <h1>Recent Grade Submissions</h1>
        <p class="subtitle">Latest 10 grade submissions</p>
    </div>

    <!-- Filter Section -->
    <div class="filter-card">
        <div class="filter-card-header">
            <h5>
                <i class="fas fa-filter"></i>
                Filter Submissions
            </h5>
        </div>
        <div class="filter-card-body">
            <form method="GET" action="{{ route('training.grade-submissions.recent') }}" id="filterForm">
                <div class="filter-inline-container">
                    <div class="filter-group">
                        <label for="school_id">School</label>
                        <select id="school_id" name="school_id">
                            <option value="">All Schools</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->school_id }}"
                                    {{ request('school_id') == $school->school_id ? 'selected' : '' }}>
                                    {{ $school->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="class_id">Class</label>
                        <select id="class_id" name="class_id" {{ !request('school_id') ? 'disabled' : '' }}>
                            <option value="">{{ request('school_id') ? 'All Classes' : 'Select School First' }}</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->class_id }}"
                                    {{ request('class_id') == $class->class_id ? 'selected' : '' }}>
                                    {{ $class->class_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="submission_filter">Submission</label>
                        <select id="submission_filter" name="submission_filter" {{ !request('class_id') ? 'disabled' : '' }}>
                            <option value="">{{ request('class_id') ? 'All Submissions' : 'Select Class First' }}</option>
                            @foreach($submissions as $submission)
                                <option value="{{ $submission['value'] }}"
                                    {{ request('submission_filter') == $submission['value'] ? 'selected' : '' }}>
                                    {{ $submission['display_name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-content">
            @if($recentSubmissions->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <h2>No Recent Submissions</h2>
                    <p>Start by creating a new grade submission to track student progress</p>
                    <a href="{{ route('training.grade-submissions.create') }}" class="button primary">
                        <i class="fas fa-plus"></i> Create New Submission
                    </a>
                </div>
            @else
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>School</th>
                                <th>Class</th>
                                <th>Semester</th>
                                <th>Term</th>
                                <th>Academic Year</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentSubmissions as $submission)
                                <tr>
                                    <td>{{ $submission['school_name'] }}</td>
                                    <td>{{ $submission['class_name'] }}</td>
                                    <td>{{ $submission['semester'] }}</td>
                                    <td>{{ $submission['term'] }}</td>
                                    <td>{{ $submission['academic_year'] }}</td>
                                    <td>{{ $submission['created_at'] }}</td>
                                    <td>
                                        <form action="{{ route('training.grade-submissions.destroy', $submission['id']) }}" method="POST" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button delete" title="Delete Submission" onclick="return confirm('Are you sure you want to delete this submission?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.page-container {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

/* Filter Card Styles */
.filter-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
    overflow: hidden;
}

.filter-card-header {
    background: #22bbea;
    color: white;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e5e5e5;
}

.filter-card-header h5 {
    margin: 0;
    font-size: 1rem;
    font-weight: 500;
    display: flex;
    align-items: center;
}

.filter-card-header i {
    margin-right: 0.5rem;
}

.filter-card-body {
    padding: 1.5rem;
}

.filter-inline-container {
    display: flex;
    gap: 1rem;
    align-items: end;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    flex-direction: column;
    min-width: 200px;
    flex: 1;
}

.filter-group label {
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: #333;
    font-size: 0.9rem;
}

.filter-group select {
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 0.9rem;
    background: white;
    color: #333;
    transition: border-color 0.2s ease;
}

.filter-group select:focus {
    outline: none;
    border-color: #22bbea;
    box-shadow: 0 0 0 3px rgba(34, 187, 234, 0.1);
}

.filter-group select:disabled {
    background: #f5f5f5;
    color: #999;
    cursor: not-allowed;
}

.page-header {
    margin-bottom: 2rem;
}

.page-header h1 {
    color: #333;
    font-size: 1.8rem;
    margin: 0;
    font-weight: 600;
}

.subtitle {
    color: #666;
    margin-top: 0.5rem;
}

.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.card-content {
    padding: 1.5rem;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-icon {
    font-size: 3rem;
    color: #22bbea;
    margin-bottom: 1rem;
}

.empty-state h2 {
    color: #333;
    margin-bottom: 0.5rem;
    font-size: 1.5rem;
}

.empty-state p {
    color: #666;
    margin-bottom: 1.5rem;
}

.button {
    display: inline-flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
    text-decoration: none;
}

.button i {
    margin-right: 0.5rem;
}

.button.primary {
    background: #22bbea;
    color: white;
}

.button.primary:hover {
    background: #1a9bc7;
}

.button.delete {
    background: #fee2e2;
    color: #dc2626;
    padding: 0.5rem;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
}

.button.delete:hover {
    background: #fecaca;
}

.button.delete i {
    margin: 0;
    font-size: 1rem;
}

.table-wrapper {
    overflow-x: auto;
    margin: 0 -1.5rem;
    padding: 0 1.5rem;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

.data-table th {
    background: #22bbea;
    color: white;
    font-weight: 500;
    text-align: left;
    padding: 1rem;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.data-table td {
    padding: 1rem;
    border-bottom: 1px solid #eee;
    color: #333;
}

.data-table tr:hover {
    background: #f8f9fa;
}

.delete-form {
    display: inline-block;
}

@media (max-width: 768px) {
    .page-container {
        padding: 1rem;
    }

    .data-table th,
    .data-table td {
        padding: 0.75rem;
    }

    .filter-inline-container {
        flex-direction: column;
        align-items: stretch;
    }

    .filter-group {
        min-width: auto;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const schoolSelect = document.getElementById('school_id');
    const classSelect = document.getElementById('class_id');
    const submissionSelect = document.getElementById('submission_filter');
    const filterForm = document.getElementById('filterForm');

    // School change handler
    schoolSelect.addEventListener('change', function() {
        const schoolId = this.value;

        // Reset and disable dependent dropdowns
        classSelect.innerHTML = '<option value="">Loading...</option>';
        classSelect.disabled = true;
        submissionSelect.innerHTML = '<option value="">Select Class First</option>';
        submissionSelect.disabled = true;

        if (schoolId) {
            // Fetch classes for selected school
            fetch(`/training/grade-submissions/classes?school_id=${schoolId}`)
                .then(response => response.json())
                .then(classes => {
                    classSelect.innerHTML = '<option value="">All Classes</option>';
                    classes.forEach(classItem => {
                        const option = document.createElement('option');
                        option.value = classItem.class_id;
                        option.textContent = classItem.class_name;
                        classSelect.appendChild(option);
                    });
                    classSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error loading classes:', error);
                    classSelect.innerHTML = '<option value="">Error loading classes</option>';
                });
        } else {
            classSelect.innerHTML = '<option value="">Select School First</option>';
            classSelect.disabled = true;
        }

        // Submit form to apply school filter
        filterForm.submit();
    });

    // Class change handler
    classSelect.addEventListener('change', function() {
        const schoolId = schoolSelect.value;
        const classId = this.value;

        // Reset submission dropdown
        submissionSelect.innerHTML = '<option value="">Loading...</option>';
        submissionSelect.disabled = true;

        if (schoolId && classId) {
            // Fetch submissions for selected school and class
            fetch(`/training/grade-submissions/submissions?school_id=${schoolId}&class_id=${classId}`)
                .then(response => response.json())
                .then(submissions => {
                    submissionSelect.innerHTML = '<option value="">All Submissions</option>';
                    submissions.forEach(submission => {
                        const option = document.createElement('option');
                        option.value = submission.value;
                        option.textContent = submission.display_name;
                        submissionSelect.appendChild(option);
                    });
                    submissionSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error loading submissions:', error);
                    submissionSelect.innerHTML = '<option value="">Error loading submissions</option>';
                });
        } else {
            submissionSelect.innerHTML = '<option value="">Select Class First</option>';
            submissionSelect.disabled = true;
        }

        // Submit form to apply class filter
        filterForm.submit();
    });

    // Submission change handler
    submissionSelect.addEventListener('change', function() {
        // Submit form to apply submission filter
        filterForm.submit();
    });
});
</script>
@endsection