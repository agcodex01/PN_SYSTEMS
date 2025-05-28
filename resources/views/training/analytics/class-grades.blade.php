@extends('layouts.nav')

@section('content')
<div class="page-container">
    <div class="header-section">
        <h2>Class Grades</h2>
        <p class="text-muted">View and analyze class grades. Select a school, class, and submission to view the grade report.</p>
    </div>
    
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="schoolSelect" class="form-label fw-bold">School</label>
                    <select id="schoolSelect" class="form-select">
                        <option value="">Select School</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="classSelect" class="form-label fw-bold">Class</label>
                    <select id="classSelect" class="form-select" disabled>
                        <option value="">Select Class</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="submissionSelect" class="form-label fw-bold">Submission</label>
                    <select id="submissionSelect" class="form-select" disabled>
                        <option value="">Select Submission</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div id="gradesTableContainer">
                <div class="text-center p-5 text-muted">
                    <i class="bi bi-graph-up" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    <p class="mt-3 mb-0">Select a school, class, and submission to view grade report</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Prevent form submission on Enter key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        return false;
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Prevent form submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            return false;
        });
    }
    console.log('Loading schools...');
    fetch('/training/analytics/schools')
        .then(res => {
            console.log('Schools response status:', res.status);
            return res.json();
        })
        .then(data => {
            console.log('Schools data:', data);
            const schoolSelect = document.getElementById('schoolSelect');
            data.forEach(school => {
                const opt = document.createElement('option');
                opt.value = school.id;
                opt.textContent = school.name;
                schoolSelect.appendChild(opt);
            });
        });

    document.getElementById('schoolSelect').addEventListener('change', function() {
        const schoolId = this.value;
        const classSelect = document.getElementById('classSelect');
        const submissionSelect = document.getElementById('submissionSelect');
        
        classSelect.innerHTML = '<option value="">Select Class</option>';
        submissionSelect.innerHTML = '<option value="">Select Submission</option>';
        classSelect.disabled = true;
        submissionSelect.disabled = true;
        
        const container = document.getElementById('gradesTableContainer');
        container.innerHTML = `
            <div class="d-flex justify-content-center p-5">
                <div class="loading-spinner"></div>
                <span class="ms-2">Loading classes...</span>
            </div>`;
        
        if (schoolId) {
                console.log(`Loading classes for school ${schoolId}...`);
                fetch(`/training/analytics/classes/${schoolId}`)
                    .then(res => {
                        console.log('Classes response status:', res.status);
                        return res.json();
                    })
                    .then(data => {
                        console.log('Classes data:', data);
                    if (data.length === 0) {
                        classSelect.innerHTML = '<option value="">No classes found</option>';
                        container.innerHTML = `
                            <div class="text-center p-5">
                                <i class="bi bi-collection" style="font-size: 3rem; color: #6c757d; opacity: 0.5;"></i>
                                <p class="mt-3 mb-0">No classes found for this school.</p>
                            </div>`;
                    } else {
                        data.forEach(cls => {
                            const opt = document.createElement('option');
                            opt.value = cls.id;
                            opt.textContent = cls.name;
                            classSelect.appendChild(opt);
                        });
                        container.innerHTML = `
                            <div class="text-center p-5 text-muted">
                                <i class="bi bi-graph-up" style="font-size: 2.5rem; opacity: 0.5;"></i>
                                <p class="mt-3 mb-0">Select a class and submission to view grade report</p>
                            </div>`;
                    }
                    classSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error loading classes:', error);
                    container.innerHTML = `
                        <div class="alert alert-danger m-3">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            Failed to load classes. Please try again.
                        </div>`;
                });
        } else {
            container.innerHTML = `
                <div class="text-center p-5 text-muted">
                    <i class="bi bi-graph-up" style="font-size: 2.5rem; opacity: 0.5;"></i>
                    <p class="mt-3 mb-0">Select a school, class, and submission to view grade report</p>
                </div>`;
        }
    });

    document.getElementById('classSelect').addEventListener('change', function() {
        const schoolId = document.getElementById('schoolSelect').value;
        const classId = this.value;
        const submissionSelect = document.getElementById('submissionSelect');
        submissionSelect.innerHTML = '<option value="">Select Submission</option>';
        submissionSelect.disabled = true;
        if (schoolId && classId) {
                    console.log(`Loading submissions for school ${schoolId}, class ${classId}...`);
                    submissionSelect.innerHTML = '<option value="">Loading submissions...</option>';
                    fetch(`/training/analytics/class-submissions/${schoolId}/${classId}`)
                        .then(res => {
                            if (!res.ok) {
                                throw new Error(`HTTP error! status: ${res.status}`);
                            }
                            return res.json();
                        })
                        .then(data => {
                            console.log('Raw submissions data from server:', JSON.stringify(data, null, 2));
                            submissionSelect.innerHTML = '<option value="">Select Submission</option>';
                            
                            if (!data || data.length === 0) {
                                const opt = document.createElement('option');
                                opt.value = '';
                                opt.textContent = 'No submissions found';
                                submissionSelect.appendChild(opt);
                                return;
                            }
                            
                            // Log each submission's status
                            data.forEach((sub, index) => {
                                console.log(`Submission ${index + 1}:`, {
                                    id: sub.id,
                                    label: sub.label,
                                    status: sub.status,
                                    has_incomplete_grades: sub.has_incomplete_grades,
                                    rawObject: sub
                                });
                            });
                            
                            // Sort submissions: approved first, then by created_at desc
                            const sortedData = [...data].sort((a, b) => {
                                if (a.status === 'approved' && b.status !== 'approved') return -1;
                                if (a.status !== 'approved' && b.status === 'approved') return 1;
                                return 0;
                            });
                            
                            sortedData.forEach(sub => {
                                if (sub && sub.id && sub.label) {
                                    const opt = document.createElement('option');
                                    opt.value = sub.id;
                                    
                                    // Extract just the Semester, Term, and Year from the label
                                    // Format is typically: "[Status] - Semester Term Academic Year"
                                    const labelParts = sub.label.split(' - ');
                                    // If we have a status prefix (e.g., "[Approved]"), remove it
                                    const cleanLabel = labelParts.length > 1 ? labelParts.slice(1).join(' - ') : sub.label;
                                    opt.textContent = cleanLabel;
                                    
                                    console.log('Processing submission:', {
                                        id: sub.id,
                                        status: sub.status,
                                        label: sub.label,
                                        statusType: typeof sub.status,
                                        statusLower: sub.status ? sub.status.toLowerCase() : 'undefined'
                                    });
                                    
                                    // Always enable the dropdown option
                                    opt.disabled = false;
                                    
                                    submissionSelect.appendChild(opt);
                                }
                            });
                            submissionSelect.disabled = false;
                });
        }
        document.getElementById('gradesTableContainer').innerHTML = '';
    });

    document.getElementById('submissionSelect').addEventListener('change', fetchGrades);

    function fetchGrades() {
        const schoolId = document.getElementById('schoolSelect').value;
        const classId = document.getElementById('classSelect').value;
        const submissionId = document.getElementById('submissionSelect').value;
        if (schoolId && classId && submissionId) {
            console.log(`Loading grades for school ${schoolId}, class ${classId}, submission ${submissionId}...`);
            
            // Show loading state
            const container = document.getElementById('gradesTableContainer');
            container.innerHTML = `
                <div class="d-flex justify-content-center p-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span class="ms-2">Loading grade data...</span>
                </div>`;
            
            fetch(`/training/analytics/class-grades-data?school_id=${schoolId}&class_id=${classId}&submission_id=${submissionId}`)
                .then(async res => {
                    console.log('Grades response status:', res.status);
                    console.log('Grades response headers:', [...res.headers.entries()]);
                    
                    const data = await res.json();
                    
                    if (!res.ok) {
                        throw new Error(data.error || `HTTP error! status: ${res.status}`);
                    }
                    
                    console.log('Grades data:', data);
                    
                    // Get passing grade range from the school settings or use defaults
                    const defaultPassingMin = {{ $defaultSchool->passing_grade_min ?? 75 }};
                    const defaultPassingMax = {{ $defaultSchool->passing_grade_max ?? 100 }};
                    
                    // Use the school data from the response if available, otherwise use defaults
                    const passingGradeMin = data.school?.passing_grade_min || defaultPassingMin;
                    const passingGradeMax = data.school?.passing_grade_max || defaultPassingMax;
                    
                    // Process student data and determine status independently for each student
                    if (data.students && data.students.length > 0) {
                        data.students = data.students.map(student => {
                            // Calculate average and determine status
                            let total = 0;
                            let count = 0;
                            let hasApprovedGrade = false;
                            let hasFailing = false;
                            let hasPending = false;
                            
                            // Process each grade
                            student.grades = student.grades || [];
                            
                            // Check for any INC grades first
                            const hasIncGrade = student.grades.some(grade => {
                                const gradeValue = grade?.grade?.toString().toLowerCase();
                                return gradeValue === 'inc';
                            });
                            
                            if (hasIncGrade) {
                                student.average = '-';
                                student.status = 'Pending';
                            } else {
                                // Process all grades for calculation
                                student.grades.forEach(grade => {
                                    const status = (grade?.status || 'pending').toLowerCase().trim();
                                    const gradeValue = grade?.grade;
                                    
                                    if (status === 'approved' || status === 'approve') {
                                        hasApprovedGrade = true;
                                        
                                        // Only include numeric grades in calculations
                                        if (gradeValue && !isNaN(gradeValue)) {
                                            total += parseFloat(gradeValue);
                                            count++;
                                            
                                            // Check if grade is within passing range
                                            const numericGrade = parseFloat(gradeValue);
                                            if (numericGrade < passingGradeMin || numericGrade > passingGradeMax) {
                                                hasFailing = true;
                                            }
                                        } else if (['nc', 'dr'].includes(gradeValue?.toString().toLowerCase())) {
                                            hasFailing = true;
                                        }
                                    } else if (status === 'pending' || status === 'pending_approval') {
                                        hasPending = true;
                                    }
                                });
                                
                                // Calculate average if we have valid grades
                                if (count > 0) {
                                    student.average = (total / count).toFixed(2);
                                    
                                    // Determine overall status based on school's passing grade range
                                    const numericAverage = parseFloat(student.average);
                                    if (hasFailing || numericAverage < passingGradeMin || numericAverage > passingGradeMax) {
                                        student.status = 'Failed';
                                    } else {
                                        student.status = 'Passed';
                                    }
                                } else {
                                    student.average = null;
                                    student.status = 'No Valid Grades';
                                }
                            }
                            
                            // Add a note about the student's status
                            const approvedCount = student.grades.filter(g => {
                                const s = (g?.status || '').toLowerCase().trim();
                                return s === 'approved' || s === 'approve';
                            }).length;
                            
                            const pendingCount = student.grades.filter(g => {
                                const s = (g?.status || '').toLowerCase().trim();
                                return s === 'pending' || s === 'pending_approval';
                            }).length;
                            
                            if (pendingCount > 0) {
                                student.status += ` (${pendingCount} Pending)`;
                            } else if (approvedCount === 0) {
                                student.status = 'No Approved Grades';
                            }
                            
                            return student;
                        }).filter(student => student !== null);
                    }
                    
                    renderGradesTable(data);
                })
                .catch(error => {
                    console.error('Error fetching grades:', error);
                    container.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            ${error.message || 'Error loading grade data. Please try again.'}
                        </div>`;
                });
        }
    }

    function renderGradesTable(data) {
        const container = document.getElementById('gradesTableContainer');
        
        if (!data || !data.students || !data.students.length) {
            container.innerHTML = `
                <div class="text-center p-5">
                    <i class="bi bi-exclamation-circle" style="font-size: 3rem; color: #6c757d; opacity: 0.8;"></i>
                    <h5 class="mt-3">No Grade Data Available</h5>
                    <p class="text-muted">No approved grade data found for this submission.</p>
                    <p class="small text-muted mt-2">Please ensure at least one student has approved grades for this submission.</p>
                </div>`;
            return;
        }

        // Show submission info
        const submissionInfo = data.submission ? `
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">${data.submission.term} ${data.submission.academic_year}</h5>
                            <p class="text-muted mb-0">
                                ${data.submission.semester} Semester
                                <span class="mx-2">•</span>
                                Status: 
                                <span class="badge bg-${data.submission.status === 'approved' ? 'success' : 'warning'}">
                                    ${data.submission.status}
                                </span>
                            </p>
                        </div>
                        ${data.submission.status !== 'approved' ? `
                            <div class="alert alert-warning mb-0 py-2">
                                <i class="bi bi-info-circle me-2"></i>
                                This submission is ${data.submission.status}. Some features may be limited.
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>` : '';

        // Process data
        let students = data.students;
        let subjects = data.subjects || [];
        
        let table = `
            ${submissionInfo}
            <div class="table-responsive">
                <table class="grades-table table-hover">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>`;
        
        // Add subject headers
        subjects.forEach(sub => {
            table += `<th>${sub}</th>`;
        });
        
        table += `
                            <th>Average</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>`;

        // Add student rows
        students.forEach(student => {
            table += `
                <tr>
                    <td>${student.student_id || ''}</td>
                    <td>${student.full_name || ''}</td>`;

            // Add grades for each subject with status indicators
            student.grades.forEach(gradeData => {
                const grade = typeof gradeData === 'object' ? gradeData.grade : gradeData;
                const status = typeof gradeData === 'object' ? (gradeData.status || 'pending') : 'approved';
                
                let displayGrade = grade || '-';
                let gradeClass = '';
                let statusBadge = '';
                
                // Add status badge based on status
                const statusLower = status.toLowerCase().trim();
                if (statusLower === 'pending' || statusLower === 'pending_approval') {
                    statusBadge = ' <span class="badge bg-warning text-dark" title="Pending Approval"><i class="bi bi-hourglass"></i></span>';
                } else if (statusLower === 'rejected') {
                    statusBadge = ' <span class="badge bg-danger" title="Rejected"><i class="bi bi-x-circle"></i></span>';
                    gradeClass = 'text-decoration-line-through text-muted';
                } else if (statusLower === 'approved' || statusLower === 'approve') {
                    statusBadge = ' <span class="badge bg-success" title="Approved"><i class="bi bi-check-circle"></i></span>';
                }
                
                // Style based on grade value (only if not rejected)
                if (status !== 'rejected') {
                    if (displayGrade === 'INC' || displayGrade === 'NC' || displayGrade === 'DR') {
                        gradeClass = 'text-warning fw-bold';
                    } else if (displayGrade !== '-' && !isNaN(displayGrade)) {
                        const numericGrade = parseFloat(displayGrade);
                        if (numericGrade < 75) {
                            gradeClass = 'text-danger fw-bold';
                        } else if (numericGrade >= 90) {
                            gradeClass = 'text-success fw-bold';
                        }
                    }
                }
                
                // Add the grade cell with status
                table += `<td class="${gradeClass}">
                    <div class="d-flex flex-column">
                        <span>${displayGrade}</span>
                        ${statusBadge}
                    </div>
                </td>`;
            });

            // Add average and status
            let statusText = student.status || 'No Approved Grades';
            let statusClass = 'text-secondary';
            
            // Determine status class based on the status text
            if (statusText.includes('Passed')) {
                statusClass = 'text-success';
            } else if (statusText.includes('Failed')) {
                statusClass = 'text-danger';
            } else if (statusText.includes('Pending')) {
                statusClass = 'text-warning';
            }
            
            // Format average with 2 decimal places if it's a number
            let averageDisplay = '-';
            if (student.average !== null && !isNaN(student.average)) {
                averageDisplay = parseFloat(student.average).toFixed(2);
            }
            
            // Add a note if there are pending grades
            let statusNote = '';
            if (student.grades && student.grades.some(g => {
                const status = (g?.status || '').toLowerCase().trim();
                return status === 'pending' || status === 'pending_approval';
            })) {
                statusNote = ' <span class="badge bg-warning text-dark">Pending Grades</span>';
            }
            
            table += `
                    <td class="text-center fw-bold">${averageDisplay}</td>
                    <td class="text-center fw-bold ${statusClass}">
                        ${statusText}
                        ${statusNote}
                    </td>
                </tr>`;
        });

        table += `
                    </tbody>
                </table>
            </div>
            <div class="p-3 bg-light border-top">
                <small class="text-muted">Showing ${students.length} students</small>
            </div>`;
        
        container.innerHTML = table;
    }
});
</script>

<style>
.grades-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}
.grades-table th, .grades-table td {
    border: 1px solid #e0e0e0;
    padding: 0.75rem;
    text-align: center;
    vertical-align: middle;
}
.grades-table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #495057;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
}
.grades-table tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.01);
}
.grades-table td {
    color: #212529;
}
.status-passed {
    color: #198754;
    font-weight: 600;
}
.status-failed {
    color: #dc3545;
    font-weight: 600;
}
.status-pending {
    color: #ffc107;
    font-weight: 600;
}
.loading-spinner {
    display: inline-block;
    width: 2rem;
    height: 2rem;
    border: 0.25rem solid rgba(13, 110, 253, 0.3);
    border-radius: 50%;
    border-top-color: #0d6efd;
    animation: spin 1s ease-in-out infinite;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
@endsection
