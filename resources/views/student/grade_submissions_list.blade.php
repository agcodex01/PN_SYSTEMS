@extends('layouts.student_layout')

@section('content')

<style>
/* Reset and Base Styles */
* {
    box-sizing: border-box;
}

/* Main Container */
.submissions-main-container {
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
    padding: 30px 20px;
    background: linear-gradient(135deg, #f0fbff 0%, #ffffff 50%, #f8feff 100%);
    min-height: calc(100vh - 70px);
    font-family: 'Poppins', sans-serif;
    position: relative;
}

.submissions-main-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #22bbea 0%, #ff9933 50%, #22bbea 100%);
    z-index: 1;
    border-radius: 0 0 2px 2px;
}

/* Page Header */
.submissions-header {
    background: linear-gradient(135deg, #22bbea 0%, #1e9bc4 100%);
    color: white;
    padding: 25px 30px;
    border-radius: 16px;
    margin-bottom: 32px;
    box-shadow: 0 8px 32px rgba(34, 187, 234, 0.3);
    position: relative;
}

.submissions-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
    pointer-events: none;
    border-radius: 16px;
}

.submissions-title {
    font-size: 32px;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 15px;
    position: relative;
    z-index: 1;
}

.submissions-title i {
    font-size: 28px;
    opacity: 0.9;
}

.submissions-subtitle {
    font-size: 16px;
    margin: 8px 0 0 0;
    opacity: 0.9;
    font-weight: 400;
    position: relative;
    z-index: 1;
}

/* Filter Section - Pure CSS */
.filter-section {
    background: linear-gradient(135deg, #ffffff 0%, #f8feff 100%);
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(34, 187, 234, 0.12);
    margin-bottom: 32px;
    overflow: hidden;
    border: 1px solid rgba(34, 187, 234, 0.2);
    transition: all 0.3s ease;
}

.filter-section:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 40px rgba(34, 187, 234, 0.15);
    border-color: rgba(34, 187, 234, 0.3);
}

.filter-header {
    background: linear-gradient(135deg, #22bbea 0%, #1a9bc8 100%);
    color: white;
    padding: 24px 28px;
    position: relative;
}

.filter-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
    pointer-events: none;
}

.filter-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #ff9933 0%, #22bbea 100%);
}

.filter-title {
    font-size: 20px;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
    position: relative;
    z-index: 1;
}

.filter-title i {
    font-size: 18px;
    opacity: 0.9;
}

.filter-content {
    padding: 32px 28px;
    background: linear-gradient(135deg, #ffffff 0%, #f8feff 100%);
}

.filter-form {
    margin: 0;
}

.filter-grid {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 24px;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-label {
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 10px;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.filter-select {
    width: 100%;
    padding: 14px 18px;
    border: 2px solid rgba(34, 187, 234, 0.3);
    border-radius: 12px;
    background: linear-gradient(135deg, #ffffff 0%, #f8feff 100%);
    font-size: 16px;
    color: #1e293b;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-family: inherit;
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2322bbea' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 12px center;
    background-repeat: no-repeat;
    background-size: 16px;
    padding-right: 40px;
    box-shadow: 0 2px 4px rgba(34, 187, 234, 0.1);
}

.filter-select:focus {
    border-color: #22bbea;
    box-shadow: 0 0 0 4px rgba(34, 187, 234, 0.15), 0 4px 8px rgba(34, 187, 234, 0.1);
    outline: none;
    background: linear-gradient(135deg, #ffffff 0%, #f0fbff 100%);
    transform: translateY(-1px);
}

.filter-select:hover {
    border-color: #22bbea;
    box-shadow: 0 4px 8px rgba(34, 187, 234, 0.15);
    transform: translateY(-1px);
}

.filter-buttons {
    display: flex;
    gap: 12px;
    align-items: end;
}

.filter-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 24px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
}

.filter-btn-secondary {
    background: linear-gradient(135deg, #ff9933 0%, #e6851a 100%);
    color: white;
    box-shadow: 0 4px 16px rgba(255, 153, 51, 0.3);
    position: relative;
    overflow: hidden;
}

.filter-btn-secondary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.filter-btn-secondary:hover::before {
    left: 100%;
}

.filter-btn-secondary:hover {
    background: linear-gradient(135deg, #e6851a 0%, #ff9933 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(255, 153, 51, 0.4);
}

/* Submissions Table Section - Pure CSS */
.submissions-section {
    background: linear-gradient(135deg, #ffffff 0%, #f8feff 100%);
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(34, 187, 234, 0.12);
    margin-bottom: 32px;
    overflow: hidden;
    border: 1px solid rgba(34, 187, 234, 0.2);
    transition: all 0.3s ease;
}

.submissions-section:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 40px rgba(34, 187, 234, 0.15);
    border-color: rgba(34, 187, 234, 0.3);
}

.submissions-section-header {
    background: linear-gradient(135deg, #ff9933 0%, #e6851a 100%);
    color: white;
    padding: 24px 28px;
    position: relative;
}

.submissions-section-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
    pointer-events: none;
}

.submissions-section-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #22bbea 0%, #ff9933 100%);
}

.submissions-section-title {
    font-size: 22px;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
    position: relative;
    z-index: 1;
}

.submissions-section-title i {
    font-size: 20px;
    opacity: 0.9;
}

.submissions-section-subtitle {
    font-size: 15px;
    margin: 8px 0 0 0;
    opacity: 0.9;
    font-weight: 400;
    position: relative;
    z-index: 1;
}

.submissions-table-content {
    padding: 0;
    background: linear-gradient(135deg, #ffffff 0%, #f8feff 100%);
    overflow-x: auto;
}

.submissions-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin: 0;
    min-width: 800px;
}

.submissions-table thead th {
    background: linear-gradient(135deg, #f0fbff 0%, #e6f7ff 100%);
    color: #22bbea;
    font-weight: 800;
    text-transform: uppercase;
    font-size: 13px;
    letter-spacing: 1.2px;
    padding: 20px 24px;
    border: none;
    border-bottom: 4px solid #22bbea;
    text-align: left;
    position: sticky;
    top: 0;
    z-index: 10;
}

.submissions-table tbody td {
    padding: 20px 24px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    color: #1e293b;
    font-size: 15px;
    transition: all 0.2s ease;
}

.submissions-table tbody tr {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.submissions-table tbody tr:hover {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.submissions-table tbody tr:last-child td {
    border-bottom: none;
}

.semester-display,
.term-display,
.year-display {
    font-weight: 700;
    color: #1e293b;
    font-size: 15px;
}

.date-display {
    color: #64748b;
    font-weight: 600;
    font-size: 14px;
}

.actions-display {
    white-space: nowrap;
}

.action-button {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    margin: 2px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-primary-action {
    background: linear-gradient(135deg, #22bbea 0%, #1a9bc8 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(34, 187, 234, 0.3);
    position: relative;
    overflow: hidden;
}

.btn-primary-action::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn-primary-action:hover::before {
    left: 100%;
}

.btn-primary-action:hover {
    background: linear-gradient(135deg, #1a9bc8 0%, #22bbea 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(34, 187, 234, 0.4);
}

.btn-info-action {
    background: linear-gradient(135deg, #22bbea 0%, #1a9bc8 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(34, 187, 234, 0.3);
    position: relative;
    overflow: hidden;
}

.btn-info-action::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn-info-action:hover::before {
    left: 100%;
}

.btn-info-action:hover {
    background: linear-gradient(135deg, #1a9bc8 0%, #22bbea 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(34, 187, 234, 0.4);
}

.btn-secondary-action {
    background: linear-gradient(135deg, #ff9933 0%, #e6851a 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(255, 153, 51, 0.3);
    position: relative;
    overflow: hidden;
}

.btn-secondary-action::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn-secondary-action:hover::before {
    left: 100%;
}

.btn-secondary-action:hover {
    background: linear-gradient(135deg, #e6851a 0%, #ff9933 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(255, 153, 51, 0.4);
}

.status-display {
    display: inline-flex;
    align-items: center;
    padding: 10px 18px;
    border-radius: 25px;
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.badge-approved {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
    border-color: #b8dabd;
}

.badge-pending {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    color: #856404;
    border-color: #f0d43a;
}

.badge-rejected {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
    border-color: #f1aeb5;
}

.badge-submitted {
    background: linear-gradient(135deg, #cce5ff 0%, #b3d9ff 100%);
    color: #004085;
    border-color: #99ccff;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 80px 30px;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-radius: 16px;
    color: #64748b;
    margin: 32px 0;
    border: 2px dashed #cbd5e1;
}

.empty-state-icon {
    font-size: 64px;
    margin-bottom: 25px;
    opacity: 0.6;
    color: #22bbea;
    display: block;
}

.empty-state-title {
    font-size: 24px;
    margin-bottom: 15px;
    color: #374151;
    font-weight: 700;
}

.empty-state-text {
    margin: 0;
    font-size: 16px;
    color: #6b7280;
    font-weight: 500;
}
</style>

<div class="submissions-main-container">
    <!-- Page Header -->
    <div class="submissions-header">
        <h1 class="submissions-title">
            <i class="fas fa-file-alt"></i>
            My Grade Submissions
        </h1>
        <p class="submissions-subtitle">View and manage all your grade submission records</p>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="filter-header">
            <h3 class="filter-title">
                <i class="fas fa-filter"></i>
                Filter Options
            </h3>
        </div>
        <div class="filter-content">
            <form method="GET" action="{{ route('student.grade-submissions.list') }}" class="filter-form">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label for="filter_key" class="filter-label">Filter by Period</label>
                        <select name="filter_key" id="filter_key" class="filter-select" onchange="this.form.submit()">
                            <option value="">All Submissions</option>
                            @if(isset($filterOptions))
                                @foreach($filterOptions as $option)
                                    <option value="{{ $option }}" {{ request('filter_key') == $option ? 'selected' : '' }}>{{ $option }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <!-- @if(request('filter_key'))
                        <div class="filter-buttons">
                            <a href="{{ route('student.grade-submissions.list') }}" class="filter-btn filter-btn-secondary">
                                <i class="fas fa-times"></i>
                                Clear Filter
                            </a>
                        </div>
                    @endif -->
                </div>
            </form>
        </div>
    </div>

    <!-- Submissions Section -->
    @if($gradeSubmissions->isEmpty())
        <!-- Empty State -->
        <div class="empty-state">
            <i class="fas fa-file-alt empty-state-icon"></i>
            <h4 class="empty-state-title">No Grade Submissions Found</h4>
            <p class="empty-state-text">You don't have any grade submissions yet. They will appear here when available.</p>
        </div>
    @else
        <div class="submissions-section">
            <div class="submissions-section-header">
                <h3 class="submissions-section-title">
                    <i class="fas fa-table"></i>
                    Submissions List
                </h3>
                <p class="submissions-section-subtitle">Complete list of your grade submission records</p>
            </div>
            <div class="submissions-table-content">
                <table class="submissions-table">
                    <thead>
                        <tr>
                            <th>Semester</th>
                            <th>Term</th>
                            <th>Academic Year</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gradeSubmissions as $submission)
                            @php
                                $studentPivot = $submission->students->where('pivot.user_id', Auth::id())->first();
                                $overallStatus = $studentPivot ? ($studentPivot->pivot->status ?? 'pending') : 'pending';

                                // Determine status badge class
                                $badgeClass = 'badge-' . str_replace(' ', '-', $overallStatus);
                            @endphp
                            <tr>
                                <td class="semester-display">{{ $submission->semester ?? 'N/A' }}</td>
                                <td class="term-display">{{ ucfirst($submission->term ?? 'N/A') }}</td>
                                <td class="year-display">{{ $submission->academic_year ?? 'N/A' }}</td>
                                <td>
                                    <span class="status-display {{ $badgeClass }}">
                                        {{ ucfirst($overallStatus) }}
                                    </span>
                                </td>
                                <td class="date-display">{{ $submission->created_at ? $submission->created_at->format('M d, Y') : 'N/A' }}</td>
                                <td class="actions-display">
                                    @if(in_array($overallStatus, ['submitted', 'approved']))
                                        <a href="{{ route('student.grades', ['submission_id' => $submission->id]) }}" class="action-button btn-info-action">
                                            <i class="fas fa-chart-line"></i>
                                            <span>View Grades</span>
                                        </a>
                                        <a href="{{ route('student.view-submission', $submission->id) }}" class="action-button btn-secondary-action">
                                            <i class="fas fa-file-alt"></i>
                                            <span>View Details</span>
                                        </a>
                                    @else
                                        <a href="{{ route('student.submit-grades.show', $submission->id) }}" class="action-button btn-primary-action">
                                            <i class="fas fa-upload"></i>
                                            <span>Submit Grades</span>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

@endsection