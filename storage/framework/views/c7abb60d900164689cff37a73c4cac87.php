

<?php $__env->startSection('content'); ?>

<div class="page-container">
    <div class="header-section">
        <h1 class="header-title">📊 Intervention Status</h1>
        <hr class="header-line">
        <p class="header-description">View and manage subjects that need intervention based on student performance.</p>
    </div>

    <div class="card-container">
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group">
                    <label for="schoolSelect" class="form-label">School</label>
                    <select id="schoolSelect" class="custom-select">
                        <option value="">Select School</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="classSelect" class="form-label">Class</label>
                    <select id="classSelect" class="custom-select" disabled>
                        <option value="">Select Class</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="submissionSelect" class="form-label">Submission</label>
                    <select id="submissionSelect" class="custom-select" disabled>
                        <option value="">Select Submission</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card-container">
        <div class="card-body">
            <div id="interventionTableContainer" class="table-placeholder">
                <div class="placeholder-content">
                    <span class="placeholder-icon">&#128196;</span> <!-- Calendar emoji as placeholder icon -->
                    <p class="placeholder-text">Select a school, class, and submission to view intervention data</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load schools
    fetch('/educator/analytics/schools')
        .then(res => res.json())
        .then(data => {
            const schoolSelect = document.getElementById('schoolSelect');
            data.forEach(school => {
                const opt = document.createElement('option');
                opt.value = school.id;
                opt.textContent = school.name;
                schoolSelect.appendChild(opt);
            });
        });

    // School change handler
    document.getElementById('schoolSelect').addEventListener('change', function() {
        const schoolId = this.value;
        const classSelect = document.getElementById('classSelect');
        const submissionSelect = document.getElementById('submissionSelect');
        
        classSelect.innerHTML = '<option value="">Select Class</option>';
        submissionSelect.innerHTML = '<option value="">Select Submission</option>';
        classSelect.disabled = true;
        submissionSelect.disabled = true;
        
        const container = document.getElementById('interventionTableContainer');
        container.innerHTML = `
            <div class="loading-indicator">
                <div class="spinner"></div>
                <span class="loading-text">Loading classes...</span>
            </div>`;
        
        if (!schoolId) {
            container.innerHTML = `
                <div class="placeholder-content">
                    <span class="placeholder-icon">&#128196;</span>
                    <p class="placeholder-text">Select a school, class, and submission to view intervention data</p>
                </div>`;
            return;
        }
        
        // Load classes for the selected school
        fetch(`/educator/analytics/classes/${schoolId}`)
            .then(res => res.json())
            .then(classes => {
                classSelect.innerHTML = '<option value="">Select Class</option>';
                classes.forEach(cls => {
                    const opt = document.createElement('option');
                    opt.value = cls.id;
                    opt.textContent = cls.name;
                    classSelect.appendChild(opt);
                });
                classSelect.disabled = false;
                
                container.innerHTML = `
                    <div class="placeholder-content">
                        <span class="placeholder-icon">&#128196;</span>
                        <p class="placeholder-text">Select a class and submission to view intervention data</p>
                    </div>`;
            });
    });
    
    // Class change handler
    document.getElementById('classSelect').addEventListener('change', function() {
        const schoolId = document.getElementById('schoolSelect').value;
        const classId = this.value;
        const submissionSelect = document.getElementById('submissionSelect');
        
        submissionSelect.innerHTML = '<option value="">Select Submission</option>';
        submissionSelect.disabled = true;
        
        const container = document.getElementById('interventionTableContainer');
        container.innerHTML = `
            <div class="loading-indicator">
                <div class="spinner"></div>
                <span class="loading-text">Loading submissions...</span>
            </div>`;
        
        if (!classId) {
            container.innerHTML = `
                <div class="placeholder-content">
                    <span class="placeholder-icon">&#128196;</span>
                    <p class="placeholder-text">Select a class and submission to view intervention data</p>
                </div>`;
            return;
        }
        
        // Load submissions for the selected school and class
        fetch(`/educator/analytics/class-submissions/${schoolId}/${classId}`)
            .then(res => res.json())
            .then(submissions => {
                submissionSelect.innerHTML = '<option value="">Select Submission</option>';
                submissions.forEach(sub => {
                    const opt = document.createElement('option');
                    opt.value = sub.id;
                    opt.textContent = sub.label;
                    submissionSelect.appendChild(opt);
                });
                submissionSelect.disabled = false;
                
                container.innerHTML = `
                    <div class="placeholder-content">
                        <span class="placeholder-icon">&#128196;</span>
                        <p class="placeholder-text">Select a submission to view intervention data</p>
                    </div>`;
            });
    });
    
    // Submission change handler
    document.getElementById('submissionSelect').addEventListener('change', function() {
        const schoolId = document.getElementById('schoolSelect').value;
        const classId = document.getElementById('classSelect').value;
        const submissionId = this.value;
        
        const container = document.getElementById('interventionTableContainer');
        container.innerHTML = `
            <div class="loading-indicator">
                <div class="spinner"></div>
                <span class="loading-text">Loading data...</span>
            </div>`;
        
        if (!submissionId) {
            container.innerHTML = `
                <div class="placeholder-content">
                    <span class="placeholder-icon">&#128196;</span>
                    <p class="placeholder-text">Select a submission to view intervention data</p>
                </div>`;
            return;
        }
        
        // Fetch intervention data
        fetch(`/educator/intervention-data?school_id=${schoolId}&class_id=${classId}&submission_id=${submissionId}`)
            .then(res => res.json())
            .then(data => {
                if (!data.subjects || data.subjects.length === 0) {
                    container.innerHTML = `
                        <div class="placeholder-content error-message">
                            <span class="placeholder-icon">&#9888;</span>
                            <p class="placeholder-text">No data available for the selected criteria</p>
                        </div>`;
                    return;
                }

                let tableHtml = `
                    <div class="table-wrapper">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th class="table-header">Subject Name</th>
                                    <th class="table-header">No. of Students</th>
                                    <th class="table-header">Status</th>
                                    <th class="table-header">Action</th>
                                    <th class="table-header">Date</th>
                                    <th class="table-header">Educator Assigned</th>
                                </tr>
                            </thead>
                            <tbody>`;

                data.subjects.forEach(subject => {
                    const totalStudents = subject.passed + subject.failed + subject.inc + subject.dr + subject.nc;
                    const status = subject.remarks;
                    const statusClass = getStatusClass(status);
                    
                    tableHtml += `
                        <tr>
                            <td class="table-cell">${subject.subject}</td>
                            <td class="table-cell">${totalStudents}</td>
                            <td class="table-cell">
                                <span class="status-badge ${statusClass}">${status}</span>
                            </td>
                            <td class="table-cell">
                                <button class="action-button" onclick="viewDetails('${subject.subject}')">
                                    &#128065; View Details
                                </button>
                            </td>
                            <td class="table-cell">${data.submission.academic_year}</td>
                            <td class="table-cell">${data.educator_name || 'Not Assigned'}</td>
                        </tr>`;
                });

                tableHtml += `
                            </tbody>
                        </table>
                    </div>`;

                container.innerHTML = tableHtml;
            })
            .catch(error => {
                console.error('Error:', error);
                container.innerHTML = `
                    <div class="placeholder-content error-message">
                        <span class="placeholder-icon">&#9888;</span>
                        <p class="placeholder-text">An error occurred while loading the data</p>
                    </div>`;
            });
    });
});

function getStatusClass(status) {
    switch (status) {
        case 'Need Intervention':
            return 'status-danger';
        case 'No Need Intervention':
            return 'status-success';
        case 'No Submission Recorded':
            return 'status-secondary';
        default:
            return 'status-primary';
    }
}

function viewDetails(subjectName) {
    // Implement view details functionality
    alert(`Viewing details for ${subjectName}`);
}
</script>

<style>
/* General Layout */
.page-container {
    padding: 20px;
}

.header-section {
    margin-bottom: 30px;
}

.header-title {
    font-weight: 300;
    color: #333;
    margin-bottom: 10px;
}

.header-line {
    border: 0;
    height: 1px;
    background: #eee;
    margin-bottom: 20px;
}

.header-description {
    color: #555;
    margin-bottom: 0;
}

/* Card Styles */
.card-container {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.card-body {
    padding: 20px;
}

/* Form Styles */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-weight: bold;
    margin-bottom: 8px;
    color: #495057;
}

.custom-select {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 1rem;
    appearance: none; /* Remove default arrow */
    background-color: #fff;
    background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%20viewBox%3D%220%200%20292.4%20292.4%22%3E%3Cpath%20fill%3D%22%23000000%22%20d%3D%22M287%2C197.3L159.9%2C69.8c-2.3-2.3-5.3-3.5-8.4-3.5s-6.1%2C1.2-8.4%2C3.5L5.4%2C197.3c-4.7%2C4.7-4.7%2C12.3%2C0%2C17l15%2C15c4.7%2C4.7%2C12.3%2C4.7%2C17%2C0l118.8-118.7l118.8%2C118.7c4.7%2C4.7%2C12.3%2C4.7%2C17%2C0l15-15C291.7%2C209.5%2C291.7%2C202%2C287%2C197.3z%22%2F%3E%3C%2Fsvg%3E'); /* Custom arrow */
    background-repeat: no-repeat;
    background-position: right 10px top 50%;
    background-size: 12px auto;
}

.custom-select:focus {
    border-color: #22BBEA;
    outline: none;
    box-shadow: 0 0 0 3px rgba(34, 187, 234, 0.2);
}

/* Placeholder/Loading States */
.table-placeholder {
    min-height: 200px;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #f8f9fa;
    border-radius: 5px;
    color: #888;
    text-align: center;
    padding: 20px;
}

.placeholder-content {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.placeholder-icon {
    font-size: 2.5rem; /* Large emoji */
    opacity: 0.6;
    margin-bottom: 10px;
}

.placeholder-text {
    margin: 0;
    font-size: 1.1rem;
}

.error-message .placeholder-icon {
    color: #dc3545; /* Red for errors */
}

.loading-indicator {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
}

.spinner {
    border: 4px solid rgba(0, 0, 0, 0.1);
    border-left-color: #22BBEA;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin-bottom: 10px;
}

.loading-text {
    font-size: 1rem;
    color: #555;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Table Styles */
.table-wrapper {
    overflow-x: auto;
    margin-top: 20px;
}

.custom-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0 auto;
    font-size: 1rem;
    min-width: 700px; /* Ensure table doesn't get too small */
}

.custom-table th,
.custom-table td {
    border: 1px solid #dee2e6;
    padding: 12px 15px;
    text-align: center;
    vertical-align: middle;
}

.custom-table thead th {
    background-color: #22BBEA;
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.custom-table tbody tr:nth-child(even) {
    background-color: #f8f9fa;
}

.custom-table tbody tr:hover {
    background-color: #e9ecef;
}

/* Status Badges */
.status-badge {
    display: inline-block;
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    color: white;
    text-align: center;
    min-width: 120px;
}

.status-danger {
    background-color: #dc3545;
}

.status-success {
    background-color: #28a745;
}

.status-secondary {
    background-color: #6c757d;
}

.status-primary {
    background-color: #007bff;
}

/* Action Button */
.action-button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: background-color 0.3s ease;
}

.action-button:hover {
    background-color: #0056b3;
}

.action-button:active {
    background-color: #004085;
}
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.educator_layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laravel\PNPH-CAPSTONE\resources\views/educator/intervention.blade.php ENDPATH**/ ?>