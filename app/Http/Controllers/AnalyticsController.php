<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\GradeSubmission;
use App\Models\PNUser;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    // Show the Class Grades page
    public function showClassGrades()
    {
        // Get the first school's passing grade range as default
        $school = School::select('passing_grade_min', 'passing_grade_max')->first();
        
        return view('training.analytics.class-grades', [
            'defaultSchool' => $school
        ]);
    }

    // Get all schools
    public function getSchools()
    {
        $schools = School::select('school_id as id', 'name')->get();
        return response()->json($schools);
    }

    // Get classes for a school
    public function getClassesBySchool($schoolId)
    {
        $classes = ClassModel::where('school_id', $schoolId)
            ->select('class_id as id', 'class_name as name')
            ->get();
        return response()->json($classes);
    }

    // Get terms/semesters for a school
    public function getTermsBySchool($schoolId)
    {
        $school = School::where('school_id', $schoolId)->first();
        $terms = $school ? ($school->terms ?? []) : [];
        return response()->json($terms);
    }

    // Get submissions (semester/term/year) for a school/class
    public function getClassSubmissions($schoolId, $classId)
    {
        $submissions = GradeSubmission::where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->orderByDesc('created_at')
            ->get();
            
        \Log::info('Submissions query:', [
            'school_id' => $schoolId,
            'class_id' => $classId,
            'count' => $submissions->count()
        ]);
        
        $result = $submissions->map(function($sub) {
            $label = [];
            if (!empty($sub->semester)) $label[] = 'Semester: ' . $sub->semester;
            if (!empty($sub->term)) $label[] = 'Term: ' . $sub->term;
            if (!empty($sub->academic_year)) $label[] = 'Year: ' . $sub->academic_year;
            
            // Check for incomplete grades only if submission is not approved
            $incompleteGrades = false;
            if ($sub->status !== 'approved') {
                $incompleteGrades = DB::table('grade_submission_subject')
                    ->where('grade_submission_id', $sub->id)
                    ->whereNull('grade')
                    ->exists();
            }
            
            // If no specific fields, use created_at as identifier
            if (empty($label)) {
                $label[] = 'Submission: ' . $sub->created_at->format('Y-m-d H:i:s');
            }
            
            return [
                'id' => $sub->id,
                'label' => implode(' | ', $label),
                'status' => $sub->status,
                'has_incomplete_grades' => $incompleteGrades
            ];
        });
        
        \Log::info('Formatted submissions:', $result->toArray());
        
        return response()->json($result);
    }

    // Fetch class grades for the selected school, class, and submission
    public function fetchClassGrades(\Illuminate\Http\Request $request)
    {
        $schoolId = $request->query('school_id');
        $classId = $request->query('class_id');
        $submissionId = $request->query('submission_id');
        if (!$schoolId || !$classId || !$submissionId) {
            return response()->json([]);
        }

        $school = School::where('school_id', $schoolId)->first();
        if (!$school) return response()->json([]);

        // Get the GradeSubmission by id
        $gradeSubmission = GradeSubmission::where('id', $submissionId)
            ->where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->first();
            
        if (!$gradeSubmission) {
            return response()->json([
                'error' => 'Submission not found',
                'submission_status' => 'not_found'
            ]);
        }

        // Get the user and check if they are admin/trainer
        $user = auth()->user();
        $isAdminOrTrainer = $user && in_array($user->user_role, ['admin', 'trainer']);
        
        // We no longer block access based on the main submission status
        // All users can see the grades, but the status will be shown per student

        // Get subjects for this submission
        $subjects = $gradeSubmission->subjects()->pluck('name')->toArray();
        // Get all students for this class (distinct to avoid duplicates)
        $students = $gradeSubmission->students()
            ->with(['studentDetail'])
            ->distinct('users.id')
            ->get();
            
        // Get all grades for this submission with student_status
        $gradesRaw = DB::table('grade_submission_subject')
            ->select('*', 'student_status as status')
            ->where('grade_submission_id', $gradeSubmission->id)
            ->get();
            
        // Group grades by user_id for easier access
        $gradesByStudent = [];
        foreach ($gradesRaw as $grade) {
            if (!isset($gradesByStudent[$grade->user_id])) {
                $gradesByStudent[$grade->user_id] = [];
            }
            $gradesByStudent[$grade->user_id][$grade->subject_id] = $grade;
        }

        $studentRows = [];
        $hasGrades = false;
        $allGradesComplete = true;
        
        foreach ($students as $student) {
            $studentId = $student->studentDetail->student_id ?? $student->user_id;
            
            // Skip if we've already processed this student
            if (isset($studentRows[$studentId])) {
                continue;
            }
            
            $row = [
                'student_id' => $studentId,
                'full_name' => $student->user_lname . ', ' . $student->user_fname,
                'grades' => [],
                'average' => '',
                'status' => '',
                'subjects' => $subjects,
                'has_grades' => false,
                'user_id' => $student->user_id  // Keep user_id for reference
            ];
            
            $numericGrades = [];
            $pending = false;
            $hasAnyGrade = false;
            
            foreach ($subjects as $subjectName) {
                $subject = Subject::where('name', $subjectName)->first();
                $gradeObj = $gradesRaw->first(function($g) use ($student, $subject) {
                    return $g->user_id == $student->user_id && $g->subject_id == ($subject ? $subject->id : null);
                });
                
                $grade = $gradeObj ? $gradeObj->grade : '';
                
                // Get both status and student_status
                $status = $gradeObj ? ($gradeObj->status ?? 'pending') : 'pending';
                $studentStatus = $gradeObj ? ($gradeObj->student_status ?? $status) : $status;
                
                // Use student_status if it's set, otherwise fall back to status
                $effectiveStatus = !empty($studentStatus) ? $studentStatus : $status;
                
                // Normalize status to lowercase for comparison
                $normalizedStatus = strtolower($effectiveStatus);
                
                // Create grade object with status
                $gradeData = [
                    'grade' => $grade,
                    'status' => $normalizedStatus === 'approved' ? 'approved' : $effectiveStatus
                ];
                
                // Log the status for debugging
                \Log::debug('Grade status check', [
                    'student_id' => $student->user_id,
                    'subject_id' => $subject ? $subject->id : null,
                    'status' => $status,
                    'student_status' => $studentStatus,
                    'effective_status' => $effectiveStatus,
                    'normalized' => $normalizedStatus,
                    'grade' => $grade,
                    'has_student_status' => isset($gradeObj->student_status),
                    'has_status' => isset($gradeObj->status)
                ]);
                
                // Log the status for debugging
                \Log::debug('Grade status check', [
                    'student_id' => $student->user_id,
                    'subject_id' => $subject ? $subject->id : null,
                    'status' => $status,
                    'normalized' => $normalizedStatus,
                    'grade' => $grade
                ]);
                
                if ($grade !== '') {
                    $hasGrades = true;
                    $hasAnyGrade = true;
                    $row['has_grades'] = true;
                    
                    // Only consider the grade if it's approved or status is not being checked
                    if ($normalizedStatus === 'approved') {
                        if (in_array($grade, ['INC', 'DR', 'NC'])) {
                            $pending = true;
                            $allGradesComplete = false;
                            $gradeData['grade'] = $grade;
                        } elseif (is_numeric($grade)) {
                            $numericGrades[] = floatval($grade);
                        } else {
                            $allGradesComplete = false;
                            $gradeData['grade'] = '';
                        }
                    } else {
                        // If grade exists but not approved, mark as pending
                        $pending = true;
                        $allGradesComplete = false;
                    }
                } else {
                    $allGradesComplete = false;
                    $gradeData['grade'] = '';
                }
                
                $row['grades'][] = $gradeData;
            }
            // Calculate average
            $row['average'] = count($numericGrades) ? number_format(array_sum($numericGrades)/count($numericGrades), 2) : '';
            // Determine status
            if ($pending) {
                $row['status'] = 'Pending';
            } elseif (count($numericGrades) === count($subjects)) {
                $allPassed = true;
                foreach ($numericGrades as $g) {
                    if ($g < $school->passing_grade_min || $g > $school->passing_grade_max) {
                        $allPassed = false;
                        break;
                    }
                }
                $row['status'] = $allPassed ? 'Passed' : 'Failed';
            } else {
                $row['status'] = 'Pending';
            }
            // Use student_id as the key to avoid duplicates
            $studentRows[$studentId] = $row;
        }
        // Convert associative array to indexed array for the response
        $studentRows = array_values($studentRows);
        
        // Check if any grades exist
        $hasAnyGrades = !empty(array_filter($studentRows, function($row) {
            return $row['has_grades'];
        }));
        
        // Add submission info to response
        $response = [
            'students' => $studentRows,
            'subjects' => $subjects,
            'submission' => [
                'term' => $gradeSubmission->term,
                'semester' => $gradeSubmission->semester,
                'academic_year' => $gradeSubmission->academic_year,
                'status' => 'individual_status' // Indicate that status is handled per student
            ],
            'school' => [
                'passing_grade_min' => $school->passing_grade_min,
                'passing_grade_max' => $school->passing_grade_max
            ]
        ];
        
        return response()->json($response);
    }
}
