@extends('layouts.nav')

@section('content')

<link rel="stylesheet" href="{{ asset('css/training/school.css') }}">

<h1 style="font-weight: 300;">Schools</h1>
<hr>
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
                            <a href="{{ route('training.schools.show', $school) }}" class="btn btn-view">
                                view
                            </a>
                            <a href="{{ route('training.schools.edit', $school) }}" class="btn btn-edit">
                                edit
                            </a>
                            <form action="{{ route('training.schools.destroy', $school) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-delete" onclick="return confirm('Are you sure?')">
                                    delete
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
