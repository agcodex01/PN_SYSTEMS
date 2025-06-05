@extends('layouts.nav')

@section('content')


<h1 style="font-weight: 300;">Classes</h1>
<hr>
<div class="page-container">

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

    <div class="table-wrapper">
        <div class="table-header">
            <div class="header-cell">CLASS ID</div>
            <div class="header-cell">CLASS NAME</div>
            <div class="header-cell">SCHOOL</div>
            <div class="header-cell nos">NOS</div>
            <div class="header-cell actions">ACTIONS</div>
        </div>
        
        @forelse($classes as $class)
            <div class="table-row">
                <div class="cell">{{ $class->class_id }}</div>
                <div class="cell">{{ $class->class_name }}</div>
                <div class="cell">
                    @if($class->school)
                    <a href="{{ url('/training/schools/' . $class->school->school_id) }}" class="school-link">
                        {{ $class->school->name }}
                    </a>
                    @else
                    No School Assigned
                    @endif
                </div>
                <div class="cell nos">{{ $class->students->count() }} student(s)</div>
                <div class="cell">
                    <div class="action-buttons">
                        <a href="{{ route('training.classes.show', $class) }}" class="btn-icon" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('training.classes.edit', $class) }}" class="btn-icon" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('training.classes.destroy', $class) }}" method="POST" class="d-inline delete-form" id="delete-form-{{ $class->id }}" onsubmit="return confirm('Are you sure you want to delete this class?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="table-row">
                <div class="cell empty-message">No Classes found</div>
            </div>
        @endforelse
    </div>
</div>

<script>
function confirmDelete(classId) {
    if (confirm('Are you sure you want to delete this class? This action cannot be undone.')) {
        event.preventDefault();
        document.getElementById('delete-form-' + classId).submit();
    }
}
</script>

<style>
.table-wrapper {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    margin: 20px 0;
}

.table-header {
    display: grid;
    grid-template-columns: 100px 150px 450px 120px 180px;
    background: #22bbea;
    color: white;
    border-radius: 8px 8px 0 0;
    margin-bottom: 1px;
}

.header-cell {
    padding: 15px;
    font-size: 13px;
    font-weight: 500;
    text-transform: uppercase;
}

.table-row {
    display: grid;
    grid-template-columns: 100px 150px 450px 120px 180px;
    border-bottom: 1px solid #eee;
    align-items: center;
    background: white;
    padding: 5px 0;
}

.table-row:last-child {
    border-bottom: none;
    border-radius: 0 0 8px 8px;
}

.cell {
    padding: 10px 15px;
    font-size: 13px;
}

.nos {
    text-align: center;
}

.action-buttons {
    display: flex;
    gap: 10px;
    justify-content: center;
    padding-left: 15px;
    cursor: pointer;
}

.action-btn {
    padding: 5px 12px;
    border-radius: 4px;
    font-size: 13px;
    text-decoration: none;
    color: white;
    text-align: center;
}

.view {
    background-color: #22bbea;
    width: 55px;
}

.view:hover {
    background-color: #17a2b8;
    text-decoration: none;
    color: white;
}

.edit {
    background-color: #ff9933;
    width: 55px;
}

.edit:hover {
    background-color: #ffc107;
    text-decoration: none;
    color: white;
}

.actions {
    margin-left: 15px;
    display: flex;
    gap: 10px;
    justify-content: center;
}

.action-buttons {
    display: flex;
    gap: 8px;
    justify-content: center;
}

.btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    color: #fff;
    background-color: #4a90e2;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}

.btn-icon:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.btn-icon i {
    font-size: 14px;
}

/* View button */
.btn-icon[title="View"] {
    background-color: #4a90e2;
}

.btn-icon[title="View"]:hover {
    background-color: #357abd;
}

/* Edit button */
.btn-icon[title="Edit"] {
    background-color: #f39c12;
}

.btn-icon[title="Edit"]:hover {
    background-color: #d68910;
}

/* Delete button */
.btn-icon[title="Delete"] {
    background-color: #dc3545;
}

.btn-icon[title="Delete"]:hover {
    background-color: #c82333;
}

/* Form styles */
.delete-form {
    display: inline;
    margin: 0;
    padding: 0;
}

.nos {
    text-align: center;
}

.school-link {
    text-decoration: none;
    color: #337ab7;
}

.school-link:hover {
    text-decoration: none;
    color: #23527c;
}

@media (max-width: 1200px) {
    .table-header,
    .table-row {
        grid-template-columns: 100px 150px 400px 120px 180px;
    }
}
</style>
@endsection