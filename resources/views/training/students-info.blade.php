@extends('layouts.nav')

@section('content')
<div class="page-container">
    <div class="header-section">
        <h2>Students</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="table-wrapper">
        <div class="table-header">
            <div class="header-cell">User ID</div>
            <div class="header-cell">Student ID</div>
            <div class="header-cell">Name</div>
            <div class="header-cell">Email</div>
            <div class="header-cell">Batch</div>
            <div class="header-cell text-center">Actions</div>
        </div>
        
        @forelse($students as $student)
            <div class="table-row">
                <div class="cell">{{ $student->user_id }}</div>
                <div class="cell">{{ $student->studentDetail->student_id ?? 'N/A' }}</div>
                <div class="cell">{{ $student->user_lname }}, {{ $student->user_fname }} {{ $student->user_mInitial }} {{ $student->user_suffix }}</div>
                <div class="cell">{{ $student->user_email }}</div>
                <div class="cell">{{ $student->studentDetail->batch ?? 'N/A' }}</div>
                <div class="cell">
                    <div class="action-buttons">
                        <a href="{{ route('training.students.view', $student->user_id) }}" class="btn btn-info">View</a>
                        <a href="{{ route('training.students.edit', $student->user_id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('training.students.destroy', $student->user_id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to deactivate this student?')">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="table-row">
                <div class="cell empty-message">No students found</div>
            </div>
        @endforelse
    </div>

    <div class="pagination-container">
        {{ $students->links() }}
    </div>
</div>

<style>
.page-container {
    padding: 20px;
    max-width: 100%;
    margin: 0 auto;
}

.header-section {
    margin-bottom: 20px;
}

.header-section h2 {
    font-size: 24px;
    color: #333;
    margin: 0;
}

.alert {
    padding: 12px;
    margin-bottom: 20px;
    border-radius: 4px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.table-wrapper {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.table-header {
    display: grid;
    grid-template-columns: 100px 180px 1fr 250px 100px 280px;
    background: #4CAF50;
    color: white;
    font-weight: 500;
}

.header-cell {
    padding: 16px;
    font-size: 14px;
}

.table-row {
    display: grid;
    grid-template-columns: 100px 180px 1fr 250px 100px 280px;
    border-bottom: 1px solid #eee;
    align-items: center;
}

.table-row:last-child {
    border-bottom: none;
}

.table-row:hover {
    background-color: #f8f9fa;
}

.cell {
    padding: 16px;
    font-size: 14px;
}

.empty-message {
    grid-column: 1 / -1;
    text-align: center;
    color: #666;
    padding: 20px;
}

.text-center {
    text-align: center;
}

.action-buttons {
    display: flex;
    gap: 8px;
    justify-content: center;
}

.btn {
    padding: 3px;
    border-radius: 4px;
    font-size: 14px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: background-color 0.2s;
    width: 50px;
    text-align: center;
    display: inline-block;
}

.btn-info {
    background: #17a2b8;
    color: white;
}

.btn-info:hover {
    background: #138496;
    color: white;
    text-decoration: none;
}

.btn-warning {
    background: #ffc107;
    color: #000;
}

.btn-warning:hover {
    background: #e0a800;
    color: #000;
    text-decoration: none;
}

.btn-danger {
    width: 60px;
    padding: 5px;
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #c82333;
    color: white;
    text-decoration: none;
}

.pagination-container {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}

/* Pagination Styles */
.pagination {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    gap: 4px;
}

.page-item {
    display: inline-block;
}

.page-link {
    padding: 8px 12px;
    border: 1px solid #dee2e6;
    color: #4CAF50;
    text-decoration: none;
    border-radius: 4px;
}

.page-item.active .page-link {
    background-color: #4CAF50;
    color: white;
    border-color: #4CAF50;
}

.page-link:hover {
    background-color: #e9ecef;
}

.page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
}
</style>
@endsection
