<?php

namespace App\Http\Controllers\Educator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\School;
use App\Models\ClassModel;
use App\Models\GradeSubmission;
use App\Models\GradeSubmissionDetail;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PNUser;

class AnalyticsController extends Controller
{
    public function showClassGrades()
    {
        // Get the first school's passing grade range as default
        $school = School::select('passing_grade_min', 'passing_grade_max')->first();
        
        return view('educator.analytics.class-grades', [
            'defaultSchool' => $school
        ]);
    }

    public function showSubjectProgress()
    {
        // Get the first school's passing grade range as default
        $school = School::select('passing_grade_min', 'passing_grade_max')->first();
        
        return view('educator.analytics.subject-progress', [
            'defaultSchool' => $school
        ]);
    }

    public function showSubjectIntervention()
    {
        // Get the first school's passing grade range as default
        $school = School::select('passing_grade_min', 'passing_grade_max')->first();
        
        return view('educator.analytics.subject-intervention', [
            'defaultSchool' => $school
        ]);
    }

    public function showClassProgress()
    {
        // Get the first school's passing grade range as default
        $school = School::select('passing_grade_min', 'passing_grade_max')->first();

        return view('educator.analytics.class-progress', [
            'defaultSchool' => $school
        ]);
    }





    public function getSchools()
    {
        Log::info('Educator Analytics: getSchools method called.');
        $schools = School::select('school_id as id', 'name')->get();
        Log::info('Educator Analytics: Schools fetched: ' . $schools->toJson());
        return response()->json($schools);
    }

    public function getClassesBySchool($schoolId)
    {
        $classes = ClassModel::where('school_id', $schoolId)
            ->select('class_id as id', 'class_name as name')
            ->orderBy('class_name')
            ->get();
        return response()->json($classes);
    }

    public function getClassSubmissions($schoolId, $classId)
    {
        $submissions = GradeSubmission::where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($submission) {
                return [
                    'id' => $submission->id,
                    'label' => sprintf(
                        '%s | %s | %s',
                        $submission->semester,
                        $submission->term,
                        $submission->academic_year
                    ),
                    'status' => $submission->status,
                    'has_incomplete_grades' => DB::table('grade_submission_subject')
                        ->where('grade_submission_id', $submission->id)
                        ->whereNull('grade')
                        ->exists()
                ];
            });
            
        \Log::info('Submissions query:', [
            'school_id' => $schoolId,
            'class_id' => $classId,
            'count' => $submissions->count(),
            'submissions' => $submissions->toArray()
        ]);
        
        return response()->json($submissions);
    }

    public function fetchClassGrades(Request $request)
    {
        try {
            $schoolId = $request->query('school_id');
            $classId = $request->query('class_id');
            $submissionId = $request->query('submission_id');

            if (!$schoolId || !$classId || !$submissionId) {
                return response()->json([]);
            }

            // Get school passing grade
            $school = School::where('school_id', $schoolId)->first();
            if (!$school) {
                return response()->json([]);
            }

            // Get the GradeSubmission by id with eager loading
            $gradeSubmission = GradeSubmission::with(['classModel'])
                ->where('id', $submissionId)
                ->where('school_id', $schoolId)
                ->where('grade_submissions.class_id', $classId)
                ->first();
            
            if (!$gradeSubmission) {
                return response()->json([
                    'error' => 'Submission not found',
                    'submission_status' => 'not_found'
                ]);
            }

            // Get all detailed grades for this submission
            $grades = DB::table('grade_submission_subject')
                ->join('pnph_users', 'grade_submission_subject.user_id', '=', 'pnph_users.user_id')
                ->leftJoin('student_details', 'pnph_users.user_id', '=', 'student_details.user_id')
                ->join('subjects', 'grade_submission_subject.subject_id', '=', 'subjects.id')
                ->where('grade_submission_subject.grade_submission_id', $gradeSubmission->id)
                ->select(
                    'pnph_users.user_id as student_id',
                    'pnph_users.user_fname',
                    'pnph_users.user_lname',
                    'student_details.gender',
                    'subjects.name as subject_name',
                    'grade_submission_subject.grade',
                    'grade_submission_subject.student_status as status' // Assuming this is the remarks
                )
                ->get();
            
            // Group grades by student
            $groupedGrades = $grades->groupBy(function($item) {
                return $item->student_id; // Group by student_id to easily access student_id
            });

            $studentResults = [];

            foreach ($groupedGrades as $studentId => $studentGrades) {
                $student = $studentGrades->first(); // Get student details from the first grade of the student
                $student_name = $student->user_fname . ' ' . $student->user_lname;
                $student_gender = $student->gender ?? 'N/A';
                $subjects_data = [];
                $total_grade = 0;
                $graded_subjects_count = 0;
                $has_failed = false;
                $has_incomplete = false;
                $has_pending = false;

                foreach ($studentGrades as $grade) {
                    $remarks = '';
                    if ($grade->status === 'approved') {
                        if (is_numeric($grade->grade)) {
                            $numeric_grade = (float) $grade->grade;
                            if ($numeric_grade >= $school->passing_grade_min && $numeric_grade <= $school->passing_grade_max) {
                                $remarks = 'Passed';
                                $total_grade += $numeric_grade;
                                $graded_subjects_count++;
                            } else {
                                $remarks = 'Failed';
                                $has_failed = true;
                                $total_grade += $numeric_grade;
                                $graded_subjects_count++;
                            }
                        } else {
                            // Handle non-numeric grades like INC, DR, NC for approved submissions
                            $remarks = $grade->grade; 
                            if ($grade->grade === 'INC' || $grade->grade === 'NC') {
                                $has_incomplete = true;
                            } else if ($grade->grade === 'DR') {
                                $has_failed = true;
                            }
                        }
                    } else if ($grade->status === 'pending' || $grade->status === 'pending_approval') {
                        $remarks = 'Pending';
                        $has_pending = true;
                    } else if ($grade->status === 'rejected') {
                        $remarks = 'Failed'; // Assuming rejected means failed status
                        $has_failed = true;
                    } else if ($grade->grade === null || $grade->grade === '') {
                        $remarks = 'Incomplete Submission';
                        $has_incomplete = true;
                    } else {
                        $remarks = $grade->status; // Fallback for other statuses
                    }

                    $subjects_data[] = [
                        'subject_name' => $grade->subject_name,
                        'grade' => $grade->grade ?? 'N/A',
                        'remarks' => $remarks
                    ];
                }

                $average_grade = $graded_subjects_count > 0 ? $total_grade / $graded_subjects_count : 0;

                $overall_status = 'Passed';
                if ($has_failed) {
                    $overall_status = 'Failed';
                } elseif ($has_incomplete) {
                    $overall_status = 'Incomplete Submission';
                } elseif ($has_pending) {
                    $overall_status = 'Pending';
                }

                $studentResults[] = [
                    'student_id' => $studentId, // Include student_id
                    'student_name' => $student_name,
                    'gender' => $student_gender,
                    'section' => 'N/A', // Hardcode 'N/A' as 'class_student.section' is not directly fetched
                    'subjects' => $subjects_data,
                    'average_grade' => $average_grade,
                    'overall_status' => $overall_status,
                ];
            }

            $response = [
                'grades' => $studentResults,
                'school' => [
                    'name' => $school->name,
                    'passing_grade_min' => $school->passing_grade_min,
                    'passing_grade_max' => $school->passing_grade_max
                ],
                'submission' => [
                    'term' => $gradeSubmission->term,
                    'semester' => $gradeSubmission->semester,
                    'academic_year' => $gradeSubmission->academic_year,
                ],
                'class_name' => $gradeSubmission->classModel->class_name ?? 'Unknown Class'
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Error in fetchClassGrades: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'An internal server error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function fetchSubjectProgressData(Request $request)
    {
        try {
            $schoolId = $request->input('school_id');
            $classId = $request->input('class_id');
            $submissionId = $request->input('submission_id');

            // Get school passing grade
            $school = School::find($schoolId);
            if (!$school) {
                return response()->json(['error' => 'School not found'], 404);
            }

            // Get the GradeSubmission by id with eager loading
            $gradeSubmission = GradeSubmission::with(['classModel', 'subjects'])
                ->where('id', $submissionId)
                ->where('school_id', $schoolId)
                ->where('class_id', $classId)
                ->first();
            
            if (!$gradeSubmission) {
                return response()->json([
                    'error' => 'Submission not found',
                    'submission_status' => 'not_found'
                ]);
            }

            // Get all grades for this submission
            $gradesRaw = DB::table('grade_submission_subject')
                ->join('subjects', 'grade_submission_subject.subject_id', '=', 'subjects.id')
                ->where('grade_submission_subject.grade_submission_id', $gradeSubmission->id)
                ->select(
                    'grade_submission_subject.*',
                    'subjects.name as subject_name'
                )
                ->get();

            // Group grades by subject
            $subjectResults = [];
            $groupedGrades = $gradesRaw->groupBy('subject_name');

            foreach ($groupedGrades as $subjectName => $grades) {
                $passed = 0;
                $failed = 0;
                $inc = 0;
                $dr = 0;
                $nc = 0;
                $totalGrades = 0;
                $remarks = '';

                foreach ($grades as $grade) {
                    $totalGrades++;
                    if ($grade->grade === 'INC') {
                        $inc++;
                    } elseif ($grade->grade === 'DR') {
                        $dr++;
                    } elseif ($grade->grade === 'NC') {
                        $nc++;
                    } elseif (is_numeric($grade->grade)) {
                        if ($grade->grade >= $school->passing_grade_min) {
                            $passed++;
                        } else {
                            $failed++;
                        }
                    }
                }

                if ($totalGrades > 0) {
                    // If any student has Failed, INC, DR, or NC, mark as 'Need Intervention'
                    if ($failed > 0 || $inc > 0 || $dr > 0 || $nc > 0) {
                        $remarks = 'Need Intervention';
                    } else {
                        // Only mark as 'No Need Intervention' if all students have passed
                        $remarks = 'No Need Intervention';
                    }
                }
                
                $subjectResults[] = [
                    'subject' => $subjectName,
                    'passed' => $passed,
                    'failed' => $failed,
                    'inc' => $inc,
                    'dr' => $dr,
                    'nc' => $nc,
                    'remarks' => $remarks
                ];
            }

            return response()->json([
                'subjects' => $subjectResults,
                'submission' => [
                    'term' => $gradeSubmission->term,
                    'semester' => $gradeSubmission->semester,
                    'academic_year' => $gradeSubmission->academic_year,
                ],
                'school' => [
                    'name' => $school->name,
                    'passing_grade_min' => $school->passing_grade_min,
                    'passing_grade_max' => $school->passing_grade_max
                ],
                'class_name' => $gradeSubmission->classModel->class_name ?? 'Unknown Class'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in fetchSubjectProgressData: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'An internal server error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function fetchSubjectInterventionData(Request $request)
    {
        try {
            $schoolId = $request->input('school_id');
            $classId = $request->input('class_id');
            $submissionId = $request->input('submission_id');

            // Get school passing grade
            $school = School::find($schoolId);
            if (!$school) {
                return response()->json(['error' => 'School not found'], 404);
            }

            // Get the GradeSubmission by id with eager loading
            $gradeSubmission = GradeSubmission::with(['classModel', 'subjects'])
                ->where('id', $submissionId)
                ->where('school_id', $schoolId)
                ->where('grade_submissions.class_id', $classId)
                ->first();
            
            if (!$gradeSubmission) {
                return response()->json([
                    'error' => 'Submission not found',
                    'submission_status' => 'not_found'
                ]);
            }

            // Get all subjects for the class
            $subjects = Subject::whereHas('classes', function ($query) use ($classId) {
                $query->where('class_subject.class_id', $classId);
            })->get();

            // Get all grades for this submission
            $gradesRaw = DB::table('grade_submission_subject')
                ->join('subjects', 'grade_submission_subject.subject_id', '=', 'subjects.id')
                ->where('grade_submission_subject.grade_submission_id', $gradeSubmission->id)
                ->select(
                    'grade_submission_subject.*',
                    'subjects.name as subject_name'
                )
                ->get();

            // Group grades by subject
            $subjectResults = [];
            $groupedGrades = $gradesRaw->groupBy('subject_name');

            foreach ($groupedGrades as $subjectName => $grades) {
                $passed = 0;
                $failed = 0;
                $inc = 0;
                $dr = 0;
                $nc = 0;
                $totalGrades = 0;
                $remarks = '';

                foreach ($grades as $grade) {
                    $totalGrades++;
                    if ($grade->grade === 'INC') {
                        $inc++;
                    } elseif ($grade->grade === 'DR') {
                        $dr++;
                    } elseif ($grade->grade === 'NC') {
                        $nc++;
                    } elseif (is_numeric($grade->grade)) {
                        if ($grade->grade >= $school->passing_grade_min) {
                            $passed++;
                        } else {
                            $failed++;
                        }
                    }
                }

                if ($totalGrades > 0) {
                    // If any student has Failed, INC, DR, or NC, mark as 'Need Intervention'
                    if ($failed > 0 || $inc > 0 || $dr > 0 || $nc > 0) {
                        $remarks = 'Need Intervention';
                    } else {
                        // Only mark as 'No Need Intervention' if all students have passed
                        $remarks = 'No Need Intervention';
                    }
                }
                
                $subjectResults[] = [
                    'subject' => $subjectName,
                    'passed' => $passed,
                    'failed' => $failed,
                    'inc' => $inc,
                    'dr' => $dr,
                    'nc' => $nc,
                    'remarks' => $remarks
                ];
            }

            return response()->json([
                'subjects' => $subjectResults,
                'submission' => [
                    'term' => $gradeSubmission->term,
                    'semester' => $gradeSubmission->semester,
                    'academic_year' => $gradeSubmission->academic_year,
                ],
                'school' => [
                    'name' => $school->name,
                    'passing_grade_min' => $school->passing_grade_min,
                    'passing_grade_max' => $school->passing_grade_max
                ],
                'class_name' => $gradeSubmission->classModel->class_name ?? 'Unknown Class'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in fetchSubjectInterventionData: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'An internal server error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function fetchClassProgressData(Request $request)
    {
        try {
            $schoolId = $request->input('school_id');
            $classId = $request->input('class_id');
            $submissionId = $request->input('submission_id');

            Log::info('Fetching class progress data:', [
                'school_id' => $schoolId,
                'class_id' => $classId,
                'submission_id' => $submissionId
            ]);

            // Get school passing grade
            $school = School::find($schoolId);
            if (!$school) {
                Log::warning('School not found for ID: ' . $schoolId);
                return response()->json(['error' => 'School not found'], 404);
            }

            // Get the GradeSubmission by id with eager loading
            $gradeSubmission = GradeSubmission::with(['classModel'])
                ->where('id', $submissionId)
                ->where('school_id', $schoolId)
                ->where('class_id', $classId)
                ->first();
            
            if (!$gradeSubmission) {
                Log::warning('Submission not found:', [
                    'submission_id' => $submissionId,
                    'school_id' => $schoolId,
                    'class_id' => $classId
                ]);
                return response()->json([
                    'error' => 'Submission not found',
                    'submission_status' => 'not_found'
                ]);
            }

            // Get students for this class
            $students = PNUser::select('pnph_users.user_id', 'pnph_users.user_fname', 'pnph_users.user_lname')
                ->join('class_student', 'pnph_users.user_id', '=', 'class_student.user_id')
                ->where('class_student.class_id', $classId)
                ->where('pnph_users.user_role', 'Student')
                ->where('pnph_users.status', 'active')
                ->orderBy('pnph_users.user_lname')
                ->orderBy('pnph_users.user_fname')
                ->get();

            Log::info('Students found:', [
                'count' => $students->count(),
                'class_id' => $classId
            ]);

            if ($students->isEmpty()) {
                Log::warning('No students found for the given criteria', [
                    'school_id' => $schoolId,
                    'class_id' => $classId
                ]);
                return response()->json([
                    'error' => 'No students found for this class',
                    'total_students' => 0
                ]);
            }

            // Calculate class progress
            $totalStudents = count($students);
            $passedStudents = 0;
            $failedStudents = 0;
            $incompleteStudents = 0;
            $noGradesStudents = 0;

            foreach ($students as $student) {
                $grades = DB::table('grade_submission_subject')
                    ->where('grade_submission_id', $submissionId)
                    ->where('user_id', $student->user_id)
                    ->get();

                Log::info('Student grades:', [
                    'student_id' => $student->user_id,
                    'grades_count' => $grades->count()
                ]);

                if ($grades->isEmpty()) {
                    $noGradesStudents++;
                    continue;
                }

                $hasIncomplete = false;
                $hasFailed = false;
                $validGradesCount = 0;
                $totalGrade = 0;

                foreach ($grades as $grade) {
                    if ($grade->grade === 'INC') {
                        $hasIncomplete = true;
                    } elseif ($grade->grade === 'NC' || $grade->grade === 'DR') {
                        $hasFailed = true;
                    } elseif (is_numeric($grade->grade)) {
                        $validGradesCount++;
                        $totalGrade += floatval($grade->grade);
                    }
                }

                if ($hasIncomplete) {
                    $incompleteStudents++;
                } elseif ($hasFailed || ($validGradesCount > 0 && ($totalGrade / $validGradesCount) < $school->passing_grade_min)) {
                    $failedStudents++;
                } else {
                    $passedStudents++;
                }
            }

            // Calculate percentages
            $passedPercentage = $totalStudents > 0 ? ($passedStudents / $totalStudents) * 100 : 0;
            $failedPercentage = $totalStudents > 0 ? ($failedStudents / $totalStudents) * 100 : 0;
            $incompletePercentage = $totalStudents > 0 ? ($incompleteStudents / $totalStudents) * 100 : 0;
            $noGradesPercentage = $totalStudents > 0 ? ($noGradesStudents / $totalStudents) * 100 : 0;

            $chartData = [
                'labels' => ['Passed', 'Failed', 'Pending', 'No Grades Submitted'],
                'data' => [
                    round($passedPercentage, 2),
                    round($failedPercentage, 2),
                    round($incompletePercentage, 2),
                    round($noGradesPercentage, 2)
                ],
                'counts' => [
                    'Passed' => $passedStudents,
                    'Failed' => $failedStudents,
                    'Pending' => $incompleteStudents,
                    'No Grades Submitted' => $noGradesStudents
                ],
                'total_students' => $totalStudents,
                'class_name' => $gradeSubmission->classModel->class_name ?? 'Unknown Class',
                'submission_details' => [
                    'term' => $gradeSubmission->term,
                    'semester' => $gradeSubmission->semester,
                    'academic_year' => $gradeSubmission->academic_year
                ]
            ];

            Log::info('Class progress data prepared:', $chartData);

            return response()->json($chartData);

        } catch (\Exception $e) {
            Log::error('Error in fetchClassProgressData: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'school_id' => $schoolId ?? null,
                'class_id' => $classId ?? null,
                'submission_id' => $submissionId ?? null
            ]);
            return response()->json(['error' => 'An internal server error occurred: ' . $e->getMessage()], 500);
        }
    }
} 