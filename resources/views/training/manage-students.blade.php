@extends('layouts.nav')

@section('content')
<div class="page-container">
    <div class="header-section">
        <h2>Schools</h2>
        <a href="{{ route('training.schools.create') }}" class="add-button">
            Add New School
        </a>
    </div>

    <div class="table-wrapper">
        <div class="table-header">
            <div class="header-cell">ID</div>
            <div class="header-cell">School</div>
            <div class="header-cell">Action</div>
        </div>
        
        @forelse($schools as $school)
            <div class="table-row">
                <div class="cell">{{ $school->school_id }}</div>
                <div class="cell">{{ $school->name }}</div>
                <div class="cell">
                    <div class="action-buttons">
                        <a href="{{ route('training.schools.show', $school) }}" class="action-btn view">
                            <i class="fas fa-eye"></i>
                            view
                        </a>
                        <a href="{{ route('training.schools.edit', $school) }}" class="action-btn edit">
                            <i class="fas fa-edit"></i>
                            edit
                        </a>
                        <form action="{{ route('training.schools.destroy', $school) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn delete" onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash"></i>
                                delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="table-row">
                <div class="cell empty-message">No schools found</div>
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
    gap: 15px;
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

.add-button:hover {
    background-color:rgb(246, 124, 2);
    color: white;
    text-decoration: none;
}

.table-wrapper {
    background: white;
    border-radius: 4px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.table-header {
    display: grid;
    grid-template-columns: 200px 1fr 200px;
    background: #4CAF50;
    color: white;
}

.header-cell {
    padding: 16px 24px;
    font-size: 14px;
    font-weight: normal;
}

.table-row {
    display: grid;
    grid-template-columns: 200px 1fr 200px;
    border-bottom: 1px solid #eee;
    background: #f8f9fa;
}

.cell {
    padding: 16px 24px;
    font-size: 14px;
    display: flex;
    align-items: center;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.action-btn {
    width: 50px;
    height: 35px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    text-decoration: none;
}

.view {
    background: #22bbea;
    color: white;
}

.view:hover {
    background: #17a2b8;
    color: white;
}

.edit {
    background:rgb(7, 210, 54);
    color: white;
}

.edit:hover {
    background:rgb(2, 163, 40);
    color: white;
}

.delete {
    background: #ff9933;
    color: white;
}

.delete:hover {
    background:rgb(255, 134, 13);
    color: white;
}

.empty-message {
    grid-column: 1 / -1;
    text-align: center;
    color: #666;
}

@media (max-width: 768px) {
    .page-container {
        padding: 16px;
    }
    
    .table-wrapper {
        overflow-x: auto;
    }
    
    .table-header,
    .table-row {
        min-width: 700px;
    }
}
</style>
@endsection
