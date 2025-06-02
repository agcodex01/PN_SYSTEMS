@extends('layouts.nav')

@section('content')
<style>
/* Pure CSS for Intern Grades Index Page */

/* Overall Page Container */
.page-container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Header Section */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.page-header h1 {
    font-size: 24px;
    color: #333;
    margin: 0;
}

.actions button {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 10px; /* Reduced padding */
    border-radius: 5px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s ease-in-out;
    font-size: 14px; /* Reduced font size */
    background-color: #22bbea;
    color: white;
    border: none;
}

.actions button:hover {
    background-color: #1a9bc7;
}

.actions button svg {
    width: 16px;
    height: 16px;
    margin-right: 4px; /* Add some space between icon and text */
}

/* School Filter */
.filter-section {
    margin-bottom: 20px;
    padding: 15px;
    background-color: #f9f9f9;
    border-radius: 5px;
    border: 1px solid #eee;
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-section label {
    font-weight: 500;
    color: #555;
}

.filter-section select {
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
}

/* Alert Messages */
.alert {
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
    border: 1px solid transparent;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border-color: #c3e6cb;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border-color: #f5c6cb;
}

/* Grades Tables Section */
.class-grades-section {
    margin-bottom: 30px;
}

.class-grades-section h2 {
    font-size: 20px;
    color: #444;
    margin-top: 20px;
    margin-bottom: 15px;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.table-responsive {
    overflow-x: auto;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th,
.table td {
    padding: 12px 15px;
    border: 1px solid #ddd;
    text-align: left;
}

.table th {
    background-color: #22bbea; /* Header background color */
    color: white; /* Header text color */
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
}

.table tbody tr:nth-child(even) {
    background-color: #f2f2f2;
}

.table tbody tr:hover {
    background-color: #e9e9e9;
}

/* Center align specific columns */
.table td.text-center,
.table th.text-center {
    text-align: center;
}

/* Badge Styles */
.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-align: center;
}

.badge.bg-success {
    background-color: #d4edda;
    color: #155724;
}

.badge.bg-warning {
    background-color: #fff3cd;
    color: #856404;
}

.badge.bg-orange {
     background-color: #ffeeba;
     color: #856404;
}

.badge.bg-danger {
    background-color: #f8d7da;
    color: #721c24;
}

.badge.bg-secondary {
    background-color: #e2e3e5;
    color: #495057;
}

/* Status Badge Styles */
.status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-align: center;
}

.status-badge.status-green {
    background-color: #d4edda;
    color: #155724;
}

.status-badge.status-yellow {
    background-color: #fff3cd;
    color: #856404;
}

.status-badge.status-orange {
     background-color: #ffeeba;
     color: #856404;
}

.status-badge.status-red {
    background-color: #f8d7da;
    color: #721c24;
}

.status-badge.status-gray {
    background-color: #e2e3e5;
    color: #495057;
}

/* Action Buttons in Table Cells */
.action-buttons {
    display: flex;
    gap: 5px;
    justify-content: center;
    align-items: center;
}

.action-buttons a,
.action-buttons button { /* Apply styles to both links and buttons */
    display: inline-flex;
    align-items: center;
    padding: 5px 8px;
    font-size: 12px;
    border-radius: 4px;
    text-decoration: none; /* Remove underline from links */
    cursor: pointer;
    transition: background-color 0.2s ease-in-out;
    border: none; /* Remove default button border */
}

.action-buttons .btn-primary { /* Using btn-primary class for consistency */
    background-color: #007bff;
    color: white;
}

.action-buttons .btn-danger { /* Using btn-danger class for consistency */
    background-color: #dc3545;
    color: white;
}

.action-buttons .btn-primary:hover {
    background-color: #0056b3;
}

.action-buttons .btn-danger:hover {
    background-color: #c82333;
}

.action-buttons svg {
    width: 16px;
    height: 16px;
}

/* No Grades Message */
.no-grades-message {
    text-align: center;
    padding: 50px 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    border: 1px dashed #ccc;
    margin-top: 30px;
}

.no-grades-message svg {
    width: 50px;
    height: 50px;
    color: #aaa;
    margin-bottom: 15px;
}

.no-grades-message .message-title {
    font-size: 20px;
    color: #555;
    margin-bottom: 10px;
}

.no-grades-message .text-muted {
    color: #777;
}

</style>

<div class="page-container">
    <!-- Header Section -->
    <div class="page-header">
        <h1>Intern Grades</h1>
        <div class="actions">
            <button type="button"
                    onclick="window.location.href = '{{ route('training.intern-grades.create') }}';">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Intern Grade
            </button>
        </div>
    </div>

    <!-- School Filter -->
    <div class="filter-section">
        <form action="{{ route('training.intern-grades.index') }}" method="get" class="form-inline">
            <label for="school_filter">Filter by School:</label>
            <select name="school_filter" id="school_filter" onchange="this.form.submit();">
                <option value="">-- Select School --</option>
                @foreach ($schools as $school)
                    <option value="{{ $school->school_id }}" {{ (request('school_filter') == $school->school_id) ? 'selected' : '' }}>
                        {{ $school->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Grades Tables -->
    @if (count($groupedGrades) > 0)
        @foreach ($groupedGrades as $classId => $grades)
            <div class="class-grades-section">
                <h2>Class: {{ $grades->first()->classModel->class_name ?? 'N/A' }}</h2>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Company</th>
                                <th class="text-center">ICT Learning</th>
                                <th class="text-center">21st Century Skills</th>
                                <th class="text-center">Expected Outputs</th>
                                <th class="text-center">Average</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($grades as $grade)
                                <tr>
                                    <td>{{ $grade->intern->studentDetail->student_id ?? 'N/A' }}</td>
                                    <td>{{ $grade->intern->user_fname }} {{ $grade->intern->user_lname }}</td>
                                    <td>{{ $grade->company_name }}</td>
                                    <td class="text-center">{{ $grade->grades['ict_learning_competency'] ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $grade->grades['twenty_first_century_skills'] ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $grade->grades['expected_outputs_deliverables'] ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ match(round($grade->final_grade)) { 1 => 'bg-success', 2 => 'bg-warning', 3 => 'bg-orange', 4 => 'bg-danger', default => 'bg-secondary' } }}">
                                            {{ number_format($grade->final_grade, 1) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="status-badge 
                                            @if($grade->status === 'Fully Achieved') status-green
                                            @elseif($grade->status === 'Partially Achieved') status-yellow
                                            @elseif($grade->status === 'Barely Achieved') status-orange
                                            @elseif($grade->status === 'No Achievement') status-red
                                            @else status-gray
                                            @endif">
                                            {{ $grade->status }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-buttons">
                                            <a href="{{ route('training.intern-grades.edit', $grade->id) }}" class="btn-primary"
                                               title="Edit Grade">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </a>
                                            <form action="{{ route('training.intern-grades.destroy', $grade->id) }}" 
                                                  method="post" 
                                                  style="display: inline-block;"
                                                  onsubmit="return confirm('Are you sure you want to delete this grade?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-danger"
                                                        title="Delete Grade">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @else
        <div class="no-grades-message">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="message-title">No grades found</h3>
            <p class="text-muted">Get started by creating a new grade entry.</p>
        </div>
    @endif
</div>
@endsection 
