@extends('layouts.nav')

@section('content')


<link rel="stylesheet" href="{{ asset('css/training/manage-students.css') }}">

<h1 style ="font-weight: 300;">Schools</h1>
<hr>
<div class="page-container">
    <div class="header-section">
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


@endsection
