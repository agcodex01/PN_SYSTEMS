<?php

namespace App\Http\Controllers\Educator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\School;
use App\Models\ClassModel;
use App\Models\GradeSubmission;
use App\Models\GradeSubmissionDetail;
use App\Models\Subject;
use App\Models\PNUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InterventionController extends Controller
{
    public function index()
    {
        // Get the first school's passing grade range as default
        $school = School::select('passing_grade_min', 'passing_grade_max')->first();
        
        return view('educator.intervention', [
            'defaultSchool' => $school
        ]);
    }

    public function getInterventionData(Request $request)
    {
        try {
            $schoolId = $request->query('school_id');
            $classId = $request->query('class_id');
            $submissionId = $request->query('submission_id');

            if (!$schoolId || !$classId || !$submissionId) {
                return response()->json(['error' => 'Missing required parameters'], 400);
            }

            $school = School::findOrFail($schoolId);
            $gradeSubmission = GradeSubmission::findOrFail($submissionId);

            // Get all subjects for the class
            $subjects = Subject::where('class_id', $classId)->get();
            $subjectResults = [];

            foreach ($subjects as $subject) {
                $subjectName = $subject->subject_name;
                $passed = 0;
                $failed = 0;
                $inc = 0;
                $dr = 0;
                $nc = 0;
                $totalGrades = 0;
                $remarks = 'No Submission Recorded';

                // Get grades for this subject
                $grades = GradeSubmissionDetail::where('grade_submission_id', $submissionId)
                    ->where('subject_id', $subject->subject_id)
                    ->get();

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

            // Get the educator assigned to this class
            $educator = PNUser::where('class_id', $classId)
                ->where('role', 'educator')
                ->first();

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
                ],
                'educator_name' => $educator ? $educator->first_name . ' ' . $educator->last_name : null
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getInterventionData: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching intervention data'], 500);
        }
    }
} 