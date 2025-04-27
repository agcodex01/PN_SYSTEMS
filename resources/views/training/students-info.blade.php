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
            <div class="header-cell">USER ID</div>
            <div class="header-cell">STUDENT ID</div>
            <div class="header-cell">LAST NAME</div>
            <div class="header-cell">FIRST NAME</div>
            <div class="header-cell">MI</div>
            <div class="header-cell">SUFFIX</div>
            <div class="header-cell">SEX</div>
            <div class="header-cell">EMAIL</div>
            <div class="header-cell act1">ACTIONS</div>
        </div>
        
        @forelse($students as $student)
            <div class="table-row">
                <div class="cell">{{ $student->user_id }}</div>
                <div class="cell">{{ $student->studentDetail->student_id ?? 'N/A' }}</div>
                <div class="cell">{{ $student->user_lname }}</div>
                <div class="cell">{{ $student->user_fname }}</div>
                <div class="cell">{{ $student->user_mInitial }}</div>
                <div class="cell">{{ $student->user_suffix ?? '' }}</div>
                <div class="cell">{{ $student->studentDetail->gender ?? 'N/A' }}</div>
                <div class="cell">{{ $student->user_email }}</div>
                <div class="cell">
                    <div class="action-buttons">
                        <a href="{{ route('training.students.view', $student->user_id) }}" class="btn btn-view">View</a>
                        <a href="{{ route('training.students.edit', $student->user_id) }}" class="btn btn-edit">Edit</a>
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

    .act1{
        text-align:center;
    }

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
    border-radius: 4px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.table-header {
    display: grid;
    grid-template-columns: 80px 120px 120px 120px 50px 80px 80px 1fr 150px;
    background: #4CAF50;
    color: white;
    font-weight: 500;
}

.table-row {
    text-align: center;
    /* text-align: justify; */
    display: grid;
    grid-template-columns: 80px 120px 120px 120px 50px 80px 80px 1fr 150px;
    border-bottom: 1px solid #eee;
    align-items: center;
    transition: background-color 0.2s;
}

.table-row:hover {
    background-color: #f8f9fa;
}

.cell {
    padding: 12px 8px;
    font-size: 14px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.header-cell {
    text-align:center;
    padding: 12px 8px;
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
}

.action-buttons {
    display: flex;
    gap: 4px;
    justify-content: center;
    align-items: center;
    text-align: center;
}

.btn {
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 13px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    text-align: center;
    display: inline-block;
}

.btn-view {
    background: #17a2b8;
    color: white !important;
    min-width: 40px;
}

.btn-edit {
    background: #ffc107;
    color: #000 !important;
    min-width: 40px;
}

.pagination-container {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}

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
