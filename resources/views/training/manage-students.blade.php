@extends('layouts.nav')

@section('content')

<link rel="stylesheet" href="{{ asset('css/training/school.css') }}">

<h1 style="font-weight: 300;">Schools</h1>
<hr>

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

<div class="page-container">
    <div class="header-section">
        <a href="{{ route('training.schools.create') }}" class="btn btn-primary">
            Add New School
        </a>
    </div>

    <div class="table-wrapper">
        <div class="table-header">
            <div class="header-cell">ID</div>
            <div class="header-cell">School</div>
            <div class="header-cell">Department</div>
            <div class="header-cell">Course</div>
            <div class="header-cell">Actions</div>
        </div>
        
        @forelse($schools as $school)
            <div class="table-row">
                @if(is_object($school))
                    <div class="cell">{{ $school->school_id }}</div>
                    <div class="cell">{{ $school->name }}</div>
                    <div class="cell">{{ $school->department }}</div>
                    <div class="cell">{{ $school->course }}</div>
                    <div class="cell">
                        <div class="action-buttons">
                            <a href="{{ route('training.schools.show', $school) }}" class="btn-icon" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('training.schools.edit', $school) }}" class="btn-icon" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('training.schools.destroy', $school) }}" method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this school?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="cell" colspan="5">Invalid school data</div>
                @endif
            </div>
        @empty
            <div class="table-row">
                <div class="cell empty-message">No schools found</div>
            </div>
        @endforelse
    </div>
</div>

@endsection
