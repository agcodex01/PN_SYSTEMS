@extends('layouts.nav')

@section('content')


<link rel="stylesheet" href="{{ asset('css/training/school/create.css') }}">
<div class="page-container">
    <div class="header-section">
        <h2>Create New School</h2>
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

    <form action="{{ route('training.schools.store') }}" method="POST" class="form-container">
        @csrf

        <div class="form-group">
            <label for="school_id">School ID</label>
            <input type="text" id="school_id" name="school_id" value="{{ old('school_id') }}" required>
            @error('school_id')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="name">School Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="department">Department</label>
            <input type="text" id="department" name="department" value="{{ old('department') }}" required>
            @error('department')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="course">Course</label>
            <input type="text" id="course" name="course" value="{{ old('course') }}" required>
            @error('course')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="semester_count">Number of Semesters</label>
            <input type="number" id="semester_count" name="semester_count" value="{{ old('semester_count') }}" required>
            @error('semester_count')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Grade Range Configuration</label>
            <div class="grade-range-selector">
        <div class="input-group">
            <label for="passingGradeMin">Passing Grade Min</label>
            <input type="number" step="0.1" id="passingGradeMin" name="passing_grade_min" value="{{ old('passing_grade_min') }}" required>
        </div>
        <div class="input-group">
            <label for="passingGradeMax">Passing Grade Max</label>
            <input type="number" step="0.1" id="passingGradeMax" name="passing_grade_max" value="{{ old('passing_grade_max') }}" required>
        </div>
        <div class="input-group">
            <label for="failingGradeMin">Failing Grade Min</label>
            <input type="number" step="0.1" id="failingGradeMin" name="failing_grade_min" value="{{ old('failing_grade_min') }}" required>
        </div>
        <div class="input-group">
            <label for="failingGradeMax">Failing Grade Max</label>
            <input type="number" step="0.1" id="failingGradeMax" name="failing_grade_max" value="{{ old('failing_grade_max') }}" required>
        </div>
    </div>

        <div class="form-group">
            <label>Terms</label>
            <div class="checkbox-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="terms[]" value="prelim" {{ in_array('prelim', old('terms', [])) ? 'checked' : '' }}>
                    Prelim
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" name="terms[]" value="midterm" {{ in_array('midterm', old('terms', [])) ? 'checked' : '' }}>
                    Midterm
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" name="terms[]" value="semi_final" {{ in_array('semi_final', old('terms', [])) ? 'checked' : '' }}>
                    Semi Final
                </label>
                <label class="checkbox-label">
                    <input type="checkbox" name="terms[]" value="final" {{ in_array('final', old('terms', [])) ? 'checked' : '' }}>
                    Final
                </label>
            </div>
            @error('terms')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="subjects-section">
            <h3>Subjects</h3>
            <div id="subjects-container">
                @foreach(old('subjects', []) as $index => $subject)
                    <div class="subject-row">
                        <input type="text" name="subjects[{{ $index }}][offer_code]" placeholder="Offer Code" value="{{ $subject['offer_code'] ?? '' }}" required>
                        <input type="text" name="subjects[{{ $index }}][name]" placeholder="Subject Name" value="{{ $subject['name'] ?? '' }}" required>
                        <input type="text" name="subjects[{{ $index }}][instructor]" placeholder="Instructor" value="{{ $subject['instructor'] ?? '' }}" required>
                        <input type="text" name="subjects[{{ $index }}][schedule]" placeholder="Schedule" value="{{ $subject['schedule'] ?? '' }}" required>
                        <button type="button" class="btn-remove" onclick="removeSubject(this)">×</button>
                    </div>
                @endforeach
            </div>
            <button type="button" id="add-subject" class="btn-add">Add Subject</button>
        </div>

        <div class="classes-section">
            <h3>Classes</h3>
            <div id="classes-container">
                @foreach(old('classes', []) as $index => $class)
                    <div class="class-row">
                        <div class="class-header">
                            <input type="text" name="classes[{{ $index }}][class_id]" placeholder="Class ID" value="{{ $class['class_id'] ?? '' }}" required>
                            <input type="text" name="classes[{{ $index }}][name]" placeholder="Class Name" value="{{ $class['name'] ?? '' }}" required>
                            <input type="hidden" name="classes[{{ $index }}][batch]" class="batch-input">
                            <button type="button" class="btn-select-students" data-class-index="{{ $index }}">Select Students</button>
                            <button type="button" class="btn-remove" onclick="removeClass(this)">×</button>
                        </div>
                        <div class="students-container" id="students-container-{{ $index }}">
                            <!-- Students will be loaded here via AJAX when batch is selected -->
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="button" id="add-class" class="btn-add">Add Class</button>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Create School</button>
            <a href="{{ route('training.manage-students') }}" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<!-- Student Selection Modal -->
<div id="studentModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Select Students</h3>
            <button type="button" class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="filter-section">
                <select id="batchFilter">
                    <option value="">All Batches</option>
                    @foreach($batches as $batch)
                        <option value="{{ $batch->batch }}">{{ $batch->batch }}</option>
                    @endforeach
                </select>
            </div>
            <div id="modalStudentsContainer" class="students-list">
                <!-- Students will be loaded here -->
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-submit" id="confirmStudentSelection">Confirm Selection</button>
            <button type="button" class="btn-cancel close-modal">Cancel</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentClassIndex = null;
    const modal = document.getElementById('studentModal');
    const closeButtons = document.querySelectorAll('.close-modal');
    const confirmButton = document.getElementById('confirmStudentSelection');
    const batchFilter = document.getElementById('batchFilter');

    // Close modal when clicking close button or outside the modal
    closeButtons.forEach(button => {
        button.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    });

    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    // Handle batch filter change
    batchFilter.addEventListener('change', function() {
        loadStudentsByBatch(this.value);
    });

    // Handle confirm button click
    confirmButton.addEventListener('click', function() {
        const selectedStudents = Array.from(document.querySelectorAll('#modalStudentsContainer input[type="checkbox"]:checked'))
            .map(checkbox => ({
                id: checkbox.value,
                name: checkbox.getAttribute('data-name'),
                student_id: checkbox.getAttribute('data-student-id')
            }));

        // Get the selected batch
        const selectedBatch = batchFilter.value;
        
        // Update the hidden batch input for the current class
        const batchInput = document.querySelector(`input[name="classes[${currentClassIndex}][batch]"]`);
        if (batchInput) {
            batchInput.value = selectedBatch;
        }

        updateSelectedStudentsList(currentClassIndex, selectedStudents);
        modal.style.display = 'none';
    });

    // Function to load students by batch
    function loadStudentsByBatch(batchId) {
        const url = `/training/students/by-batch${batchId ? '?batch_id=' + batchId : ''}`;
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(students => {
                console.log('Received students:', students);
                const container = document.getElementById('modalStudentsContainer');
                container.innerHTML = students.map(student => {
                    const studentId = `${student.batch}${student.group}${student.student_number}${student.training_code}`;
                    const fullName = `${student.user_lname}, ${student.user_fname}`;
                    return `
                        <div class="student-item">
                            <input type="checkbox" 
                                   id="modal_student_${student.user_id}" 
                                   value="${student.user_id}"
                                   data-name="${fullName}"
                                   data-student-id="${studentId}">
                            <label for="modal_student_${student.user_id}">
                                ${studentId} - ${fullName}
                            </label>
                        </div>
                    `;
                }).join('');

                // Check previously selected students
                const selectedStudentsInput = document.querySelector(`input[name="classes[${currentClassIndex}][student_ids][]"]`);
                if (selectedStudentsInput) {
                    const selectedIds = Array.from(document.querySelectorAll(`#selected-students-${currentClassIndex} .selected-student-tag`))
                        .map(tag => tag.getAttribute('data-student-id'));
                    selectedIds.forEach(id => {
                        const checkbox = document.querySelector(`#modal_student_${id}`);
                        if (checkbox) checkbox.checked = true;
                    });
                }
            })
            .catch(error => {
                console.error('Error loading students:', error);
                document.getElementById('modalStudentsContainer').innerHTML = 
                    `<p class="error-message">Error loading students: ${error.message}</p>`;
            });
    }
    let subjectCount = {{ count(old('subjects', [])) }};
    let classCount = {{ count(old('classes', [])) }};

    document.getElementById('add-subject').addEventListener('click', function() {
        const container = document.getElementById('subjects-container');
        const row = document.createElement('div');
        row.className = 'subject-row';
        row.innerHTML = `
            <input type="text" name="subjects[${subjectCount}][offer_code]" placeholder="Offer Code" required>
            <input type="text" name="subjects[${subjectCount}][name]" placeholder="Subject Name" required>
            <input type="text" name="subjects[${subjectCount}][instructor]" placeholder="Instructor" required>
            <input type="text" name="subjects[${subjectCount}][schedule]" placeholder="Schedule" required>
            <button type="button" class="btn-remove" onclick="removeSubject(this)">×</button>
        `;
        container.appendChild(row);
        subjectCount++;
    });

    // Handle select students button click
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-select-students')) {
            currentClassIndex = e.target.getAttribute('data-class-index');
            modal.style.display = 'block';
            const batchSelect = document.querySelector(`select[name="classes[${currentClassIndex}][batch_id]"]`);
            batchFilter.value = batchSelect.value;
            loadStudentsByBatch(batchFilter.value);
        }
    });

    function updateSelectedStudentsList(classIndex, students) {
        const container = document.getElementById(`students-container-${classIndex}`);
        
        // Create selected students display
        const selectedStudentsHtml = `
            <div class="selected-students">
                <h4>Selected Students:</h4>
                <div class="selected-students-list">
                    ${students.map(student => `
                        <div class="selected-student-tag" data-student-id="${student.id}">
                            ${student.student_id} - ${student.name}
                            <span class="remove-student" onclick="removeSelectedStudent(this, ${classIndex}, ${student.id})">&times;</span>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;

        // Add hidden inputs for student IDs
        const hiddenInputsHtml = students.map(student => 
            `<input type="hidden" name="classes[${classIndex}][student_ids][]" value="${student.id}">`
        ).join('');

        container.innerHTML = selectedStudentsHtml + hiddenInputsHtml;
    }

    window.removeSelectedStudent = function(element, classIndex, studentId) {
        const tag = element.closest('.selected-student-tag');
        tag.remove();
    };

    document.getElementById('add-class').addEventListener('click', function() {
        const container = document.getElementById('classes-container');
        const row = document.createElement('div');
        row.className = 'class-row';
        row.innerHTML = `
            <div class="class-header">
                <input type="text" name="classes[${classCount}][class_id]" placeholder="Class ID" required>
                <input type="text" name="classes[${classCount}][name]" placeholder="Class Name" required>
                <input type="hidden" name="classes[${classCount}][batch]" class="batch-input">
                <button type="button" class="btn-select-students" data-class-index="${classCount}">Select Students</button>
                <button type="button" class="btn-remove" onclick="removeClass(this)">×</button>
            </div>
            <div id="students-container-${classCount}" class="students-container"></div>
        `;
        container.appendChild(row);
        attachBatchChangeListener(classCount);
        classCount++;
    });

    function removeSubject(button) {
        const row = button.closest('.subject-row');
        row.remove();
        updateSubjectIndices();
    }

    function updateSubjectIndices() {
        const rows = document.querySelectorAll('.subject-row');
        rows.forEach((row, index) => {
            const inputs = row.querySelectorAll('input');
            inputs.forEach(input => {
                const name = input.name;
                input.name = name.replace(/\[\d+\]/, `[${index}]`);
            });
        });
        subjectCount = rows.length;
    }

    function removeClass(button) {
        const row = button.closest('.class-row');
        row.remove();
        updateClassIndices();
    }

    function updateClassIndices() {
        const rows = document.querySelectorAll('.class-row');
        rows.forEach((row, index) => {
            const inputs = row.querySelectorAll('input, select');
            inputs.forEach(input => {
                const name = input.name;
                input.name = name.replace(/\[\d+\]/, `[${index}]`);
                if (input.classList.contains('batch-select')) {
                    input.dataset.index = index;
                }
            });
            const studentsContainer = row.querySelector('.students-container');
            if (studentsContainer) {
                studentsContainer.id = `students-container-${index}`;
            }
        });
        classCount = rows.length;
    }

    function attachBatchChangeListener(index) {
        const select = document.querySelector(`select[name="classes[${index}][batch_id]"]`);
        select.addEventListener('change', function() {
            const batchId = this.value;
            const studentsContainer = document.getElementById(`students-container-${index}`);
            
            if (!batchId) {
                studentsContainer.innerHTML = '';
                return;
            }

            fetch(`/training/batches/${batchId}/students`)
                .then(response => response.json())
                .then(students => {
                    studentsContainer.innerHTML = students.map(student => `
                        <div class="student-item">
                            <input type="checkbox" 
                                   name="classes[${index}][student_ids][]" 
                                   value="${student.id}" 
                                   id="class_${index}_student_${student.id}">
                            <label for="class_${index}_student_${student.id}">
                                ${student.student_id} - ${student.name}
                            </label>
                        </div>
                    `).join('');
                })
                .catch(error => {
                    console.error('Error fetching students:', error);
                    studentsContainer.innerHTML = '<p class="error-message">Error loading students</p>';
                });
        });
    }

    // Attach batch change listeners to existing class rows
    document.querySelectorAll('.batch-select').forEach(select => {
        const index = select.dataset.index;
        attachBatchChangeListener(index);
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rangeInputs = document.querySelectorAll('input[name="grade_range"]');
    const passingRangeSpan = document.querySelector('#passingRange span');
    const failingRangeSpan = document.querySelector('#failingRange span');
    const passingGradeMin = document.getElementById('passingGradeMin');
    const passingGradeMax = document.getElementById('passingGradeMax');

    function updateGradeRanges(value) {
        if (value === '1') {
            passingRangeSpan.textContent = '1.0 - 3.0';
            failingRangeSpan.textContent = '3.1 - 5.0';
            passingGradeMin.value = '1.0';
            passingGradeMax.value = '3.0';
        } else {
            passingRangeSpan.textContent = '3.0 - 5.0';
            failingRangeSpan.textContent = '1.0 - 3.0';
            passingGradeMin.value = '3.0';
            passingGradeMax.value = '5.0';
        }
    }

    rangeInputs.forEach(input => {
        input.addEventListener('change', (e) => {
            updateGradeRanges(e.target.value);
        });
    });
});
</script>

@endsection 