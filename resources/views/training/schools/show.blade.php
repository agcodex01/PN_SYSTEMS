@extends('layouts.nav')

@section('content')
<div class="page-container">
    <div class="header-section">
        <a href="javascript:history.back()" class="back-button">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <h2>School Details</h2>
        <a href="{{ url('/training/classes/create?school=' . $school->school_id) }}" class="btn-add">
            <i class="fas fa-plus"></i>
            Add New Class
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

    <!-- School Details Card -->
    <div class="school-details-card">
        <h3>School Information</h3>
        <div class="detail-row">
            <span class="label">School ID:</span>
            <span class="value">{{ $school->school_id }}</span>
        </div>
        <div class="detail-row">
            <span class="label">School Name:</span>
            <span class="value">{{ $school->name }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Department:</span>
            <span class="value">{{ $school->department }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Course:</span>
            <span class="value">{{ $school->course }}</span>
        </div>
        <div class="detail-row">
            <span class="label">No. of Semester:</span>
            <span class="value">{{ $school->semester_count }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Terms:</span>
            <span class="value">
                <ul class="terms-list">
                    @foreach($school->terms as $term)
                        <li>{{ $term }}</li>
                    @endforeach
                </ul>
            </span>
        </div>

        <div class="detail-row">
            <span class="label">Grade Ranges:</span>
            <span class="value">
                <div class="grade-ranges">
                    <div class="grade-range passing">
                        <span class="grade-label">Passing:</span>
                        <span class="grade-value">{{ number_format($school->passing_grade_min, 1) }} - {{ number_format($school->passing_grade_max, 1) }}</span>
                    </div>
                    <div class="grade-range failing">
                        <span class="grade-label">Failing:</span>
                        <span class="grade-value">
                            @if($school->passing_grade_min == 1.0)
                                {{ number_format($school->passing_grade_max + 0.1, 1) }} - 5.0
                            @else
                                1.0 - {{ number_format($school->passing_grade_min - 0.1, 1) }}
                            @endif
                        </span>
                    </div>
                </div>
            </span>
        </div>

        <!-- Subjects Section -->
        <h3 class="mt-4">Subjects</h3>
        <div class="subjects-table-container">
            <div class="subjects-table-header">
                <div class="header-cell">Subject Name</div>
                <div class="header-cell">Offer Code</div>
                <div class="header-cell">Instructor</div>
                <div class="header-cell">Schedule</div>
            </div>
            @forelse($school->subjects as $subject)
                <div class="subjects-table-row">
                    <div class="cell">{{ $subject->name }}</div>
                    <div class="cell">{{ $subject->offer_code }}</div>
                    <div class="cell">{{ $subject->instructor }}</div>
                    <div class="cell">{{ $subject->schedule }}</div>
                </div>
            @empty
                <div class="subjects-table-row">
                    <div class="cell" colspan="4" style="text-align: center;">No subjects found.</div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Classes Table -->
    <div class="section-header">
        <h3>Classes</h3>
    </div>

    <div class="table-container">
        <div class="table-header">
            <div class="header-cell">Class ID</div>
            <div class="header-cell">Class Name</div>
            <div class="header-cell">No. of Students</div>
            <div class="header-cell">Actions</div>
        </div>
        
        @forelse($classes as $class)
            <div class="table-row">
                <div class="cell">{{ $class->class_id }}</div>
                <div class="cell">{{ $class->class_name }}</div>
                <div class="cell">{{ $class->students->count() }}</div>
                <div class="cell">
                    <div class="action-buttons">
                        <a href="{{ route('training.classes.show', $class) }}" class="action-btn view">
                            <!-- <i class="fas fa-eye"></i> -->
                            view
                        </a>
                        <a href="{{ route('training.classes.edit', $class) }}" class="action-btn edit">
                            <i class="fas fa-edit"></i>
                            edit
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="table-row">
                <div class="cell" colspan="4" style="text-align: center;">No classes found.</div>
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
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.header-section h2 {
    font-size: 24px;
    color: #333;
    margin: 0;
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

.btn-add {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background-color: #4CAF50;
    color: white;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 500;
}

.btn-add:hover {
    background-color: #45a049;
}

.school-details-card {
    background: white;
    padding: 24px;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 24px;
}

.detail-row {
    display: flex;
    margin-bottom: 16px;
}

.detail-row:last-child {
    margin-bottom: 0;
}

.label {
    font-weight: 500;
    width: 120px;
    color: #666;
}

.value {
    color: #333;
}

.section-header {
    margin: 24px 0 16px;
}

.section-header h3 {
    font-size: 20px;
    color: #333;
    margin: 0;
}

.table-container {
    background: white;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    overflow: hidden;
}

.table-header {
    display: grid;
    grid-template-columns: 1fr 2fr 1fr 1fr;
    background: #4CAF50;
    padding: 12px;
    font-weight: 500;
    color: white;
}

.table-row {
    display: grid;
    grid-template-columns: 1fr 2fr 1fr 1fr;
    padding: 12px;
    border-bottom: 1px solid #eee;
}

.table-row:last-child {
    border-bottom: none;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 8px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 14px;
}

.action-btn.edit {
    background-color: #22bbea;
    color: white;
}

.action-btn.view {
    background-color: #ff9933;
    color: white;
    border: none;
    cursor: pointer;
}

.action-btn.edit:hover {
    background-color:rgb(20, 123, 225);
}

.action-btn.view:hover {
    background-color:rgb(249, 128, 8);
}

.subjects-table-container {
    background: white;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-top: 16px;
}

.subjects-table-header {
    display: grid;
    grid-template-columns: 2fr 1fr 2fr 2fr;
    background: #4CAF50;
    padding: 12px;
    font-weight: 500;
    color: white;
}

.subjects-table-row {
    display: grid;
    grid-template-columns: 2fr 1fr 2fr 2fr;
    padding: 12px;
    border-bottom: 1px solid #eee;
}

.subjects-table-row:last-child {
    border-bottom: none;
}

.subjects-table-row:hover {
    background-color: #f8f9fa;
}

.subjects-table-row .cell {
    padding: 0 8px;
}

.terms-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.terms-list li {
    display: inline-block;
    background: #22bbea;
    padding: 4px 8px;
    border-radius: 4px;
    margin: 2px;
    color: white;
}

.terms-list li:hover {
    background:rgb(61, 200, 255);
    color:rgb(73, 77, 79);
}

.mt-4 {
    margin-top: 24px;
}

.grade-ranges {
    display: flex;
    gap: 20px;
}

.grade-range {
    padding: 8px 12px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.grade-range.passing {
    background-color: #e8f5e9;
    color: #2e7d32;
}

.grade-range.failing {
    background-color: #ffebee;
    color: #c62828;
}

.grade-label {
    font-weight: 500;
}

.grade-value {
    font-family: monospace;
}

.school-details-card h3 {
    margin-bottom: 16px;
    color: #333;
    font-size: 1.2em;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 8px;
}

.alert {
    padding: 12px;
    border-radius: 4px;
    margin-bottom: 16px;
}

.alert-success {
    background-color: #E8F5E9;
    color: #2E7D32;
    border: 1px solid #A5D6A7;
}

.alert-error {
    background-color: #FFEBEE;
    color: #C62828;
    border: 1px solid #FFCDD2;
}
</style>
@endsection
