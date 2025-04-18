@extends('layouts.nav')

@section('content')
<link rel="stylesheet" href="{{ asset('css/training/students-info.css') }}">

<div class="students-container">
    <h1 class="text-center">Students Information</h1>

    <!-- Students Table -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>MI</th>
                <th>Suffix</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $student)
                <tr>
                    <td>{{ $student->user_id }}</td>
                    <td>{{ $student->user_lname }}</td>
                    <td>{{ $student->user_fname }}</td>
                    <td>{{ $student->user_mInitial }}</td>
                    <td>{{ $student->user_suffix }}</td>
                    <td>{{ $student->user_email }}</td>
                    <td class="action-buttons">
                        <a href="{{ route('training.students.edit', $student->user_id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('training.students.destroy', $student->user_id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this student?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pagination-container">
        {{ $students->links() }}
    </div>
</div>
@endsection
