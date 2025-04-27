@extends('layouts.nav')

@section('content')
<div class="page-container">
    <div class="header-section">
        <a href="javascript:history.back()" class="back-button">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <h2>Class Details</h2>
        <a href="{{ route('training.schools.show', ['school' => $class->school->school_id]) }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Go to School page
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

    <div class="content-section">
        <div class="class-details card">
            <h3>Class Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <label>Class ID:</label>
                    <span>{{ $class->class_id }}</span>
                </div>
                <div class="info-item">
                    <label>Class Name:</label>
                    <span>{{ $class->class_name }}</span>
                </div>
                <div class="info-item">
                    <label>School:</label>
                    <span>{{ $class->school->name }}</span>
                </div>
                <div class="info-item">
                    <label>Department:</label>
                    <span>{{ $class->school->department }}</span>
                </div>
                <div class="info-item">
                    <label>Course:</label>
                    <span>{{ $class->school->course }}</span>
                </div>
            </div>
        </div>

        <div class="students-list card">
            <h3>Students List</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Student Number</th>
                            <th>Training Code</th>
                            <th>Group</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($class->students as $student)
                            <tr>
                                <td>{{ $student->user_id }}</td>
                                <td>{{ $student->user_fname }} {{ $student->user_mInitial }}. {{ $student->user_lname }}</td>
                                <td>{{ $student->studentDetail->student_number }}</td>
                                <td>{{ $student->studentDetail->training_code }}</td>
                                <td>{{ $student->studentDetail->group }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No students assigned to this class.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
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
    justify-content: space-between;
    margin-bottom: 20px;
}

.header-section h2 {
    font-size: 24px;
    color: #333;
    margin: 0;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background-color: #ff9933;
    border: 1px solid #ddd;
    border-radius: 4px;
    color: #333;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-back:hover {
    background-color:rgb(253, 126, 0);
    text-decoration: none;
    color: white;
}

.back-button {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 8px 15px;
    background-color: #ff9933;
    color: white;
    border-radius: 4px;
    text-decoration: none;
    font-size: 14px;
    transition: background-color 0.2s;
}

.back-button:hover {
    background-color: #5a6268;
    color: white;
}

.back-button i {
    font-size: 12px;
}

.content-section {
    display: grid;
    gap: 20px;
}

.card {
    background: white;
    padding: 24px;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.card h3 {
    margin-top: 0;
    margin-bottom: 20px;
    color: #333;
    font-size: 18px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 16px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.info-item label {
    font-weight: 500;
    color: #666;
    font-size: 14px;
}

.info-item span {
    color: #333;
    font-size: 16px;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th,
.table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.table th {
    background-color: #4CAF50;
    font-weight: 500;
    color: white;
}

.table tr:hover {
    background-color: #f8f9fa;
}

.text-center {
    text-align: center;
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection
