@extends('layouts.nav')

@section('content')
<div class="page-container">
    <div class="header-section">
        <h2>Classes</h2>
        <a href="{{ route('training.classes.create', $school) }}" class="add-button">
            Add Class
        </a>
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

    <div class="table-wrapper">
        <div class="table-header">
            <div class="header-cell">CLASS ID</div>
            <div class="header-cell">CLASS NAME</div>
            <div class="header-cell">NO. OF STUDENTS</div>
            <div class="header-cell">ACTIONS</div>
        </div>
        
        @forelse($classes as $class)
            <div class="table-row">
                <div class="cell">{{ $class->class_id }}</div>
                <div class="cell">{{ $class->class_name }}</div>
                <div class="cell">{{ $class->students->count() }} student(s)</div>
                <div class="cell">
                    <div class="action-buttons">
                        <a href="{{ route('training.students.index', ['class' => $class->class_id]) }}" class="action-btn view">
                            <i class="fas fa-eye"></i>
                            view
                        </a>
                        <a href="{{ route('training.classes.edit', $class) }}" class="action-btn edit">
                            <i class="fas fa-edit"></i>
                            edit
                        </a>
                        <form action="{{ route('training.classes.destroy', $class) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn delete" onclick="return confirm('Are you sure you want to delete this class?')">
                                <i class="fas fa-trash"></i>
                                delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="table-row">
                <div class="cell empty-message">No Class found</div>
            </div>
        @endforelse
    </div>
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

.add-button {
    background: #ff9933;
    color: white;
    padding: 8px 16px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 14px;
    border: none;
}

.alert {
    padding: 12px;
    margin-bottom: 20px;
    border-radius: 4px;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.table-wrapper {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.table-header {
    display: grid;
    grid-template-columns: 1fr 2fr 1fr 200px;
    background: #4CAF50;
    color: white;
    font-weight: 500;
}

.header-cell {
    padding: 16px;
    text-align: left;
}

.table-row {
    display: grid;
    grid-template-columns: 1fr 2fr 1fr 200px;
    border-bottom: 1px solid #eee;
    align-items: center;
}

.table-row:nth-child(even) {
    background-color: #f9f9f9;
}

.table-row:hover {
    background-color: #f5f5f5;
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

.action-buttons {
    display: flex;
    gap: 8px;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 14px;
    text-decoration: none;
    color: white;
    border: none;
    cursor: pointer;
    transition: opacity 0.2s;
}

.action-btn:hover {
    opacity: 0.9;
}

.action-btn i {
    font-size: 12px;
}

.view {
    background-color: #17a2b8;
}

.edit {
    background-color: #ffc107;
}

.delete {
    background-color: #dc3545;
}

@media (max-width: 768px) {
    .table-header,
    .table-row {
        grid-template-columns: 1fr 2fr 1fr auto;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .action-btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .table-header,
    .table-row {
        grid-template-columns: 1fr;
    }
    
    .header-cell:not(:first-child),
    .cell:not(:first-child) {
        border-top: 1px solid #eee;
    }
    
    .action-buttons {
        flex-direction: row;
        justify-content: center;
        padding: 8px 0;
    }
}
</style>
@endsection 