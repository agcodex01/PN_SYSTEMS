@extends('layouts.nav')

@section('content')
<link rel="stylesheet" href="{{ asset('css/training/students-info.css') }}">

<style>
/* Ensure main content doesn't affect navigation */
/* .main-content {
    margin-left: 220px; /* Match the navigation width */
    /* padding: 15px;
    min-height: 100vh;
    background-color: #f8f9fa;
} */ 

.content-wrapper {
    background-color: white;
    border-radius: 5px;
    padding: 20px;
    margin-bottom: 20px;
}

.filter-section {
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-label {
    font-weight: 500;
    color: #333;
}

.filter-select {
    padding: 6px 12px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    background-color: white;
    min-width: 150px;
    cursor: pointer;
}

.filter-select:focus {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.action-buttons {
    display: flex;
    gap: 5px;
    justify-content: center;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.table {
    width: 100%;
    table-layout: fixed;
    margin-bottom: 1rem;
    background-color: white;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.table th, .table td {
    padding: 12px;
    vertical-align: middle;
}

/* Column widths */
.table th:nth-child(1), .table td:nth-child(1) { width: 10%; } /* User ID */
.table th:nth-child(2), .table td:nth-child(2) { width: 10%; } /* Student ID */
.table th:nth-child(3), .table td:nth-child(3) { width: 15%; } /* Last Name */
.table th:nth-child(4), .table td:nth-child(4) { width: 15%; } /* First Name */
.table th:nth-child(5), .table td:nth-child(5) { width: 5%; } /* MI */
.table th:nth-child(6), .table td:nth-child(6) { width: 8%; } /* Suffix */
.table th:nth-child(7), .table td:nth-child(7) { width: 8%; } /* Batch */
.table th:nth-child(8), .table td:nth-child(8) { width: 12%; text-align: center; } /* Action */

.table td {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.table thead th {
    background-color: #4CAF50;
    color: white;
    font-weight: 500;
}

/* Center align the Action header */
.table thead th:last-child {
    text-align: center;
}

.btn-info {
    background-color: #17a2b8;
    color: white;
}

.btn-warning {
    background-color: #ffc107;
    color: #000;
}

.btn-info:hover {
    background-color: #138496;
    color: white;
}

.btn-warning:hover {
    background-color: #e0a800;
    color: #000;
}

h1 {
    margin: 0 0 20px 0;
    text-align: center;
    color: #333;
    font-size: 24px;
}

/* Style the pagination */
.pagination-container {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}

</style>

<div class="main-content">
    <div class="content-wrapper">
        <h1>Students Information</h1>

        <div class="filter-section">
            <label class="filter-label">Class:</label>
            <select class="filter-select" id="batchFilter" onchange="filterStudents()">
                <option value="">All Class</option>
                @foreach($batches as $batch)
                    <option value="{{ $batch }}" {{ request('batch') == $batch ? 'selected' : '' }}>
                        Class {{ $batch }}
                    </option>
                @endforeach
            </select>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Student ID</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>MI</th>
                    <th>Suffix</th>
                    <th>Batch</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($students as $student)
                    <tr>
                        <td>{{ $student->user_id }}</td>
                        <td>{{ $student->student_id }}</td>
                        <td>{{ $student->user_lname }}</td>
                        <td>{{ $student->user_fname }}</td>
                        <td>{{ $student->user_mInitial }}</td>
                        <td>{{ $student->user_suffix }}</td>
                        <td>{{ $student->batch }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('training.students.view', $student->user_id) }}" class="btn btn-info btn-sm">View</a>
                                <a href="{{ route('training.students.edit', $student->user_id) }}" class="btn btn-warning btn-sm">Edit</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination-container">
            {{ $students->links() }}
        </div>
    </div>
</div>

<script>
function filterStudents() {
    const batch = document.getElementById('batchFilter').value;
    window.location.href = '{{ route("training.students.index") }}' + (batch ? '?batch=' + batch : '');
}
</script>
@endsection
