<?php

namespace App\Http\Controllers\Educator;

use App\Http\Controllers\Controller;
use App\Models\PNUser;
use App\Models\School;
use App\Models\ClassModel;
use App\Models\Intervention;
use App\Models\Subject;
use App\Models\GradeSubmission;
use App\Models\GradeSubmissionSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InterventionController extends Controller
{
    /**
     * Display the intervention management page
     */
    public function index(Request $request)
    {
        // Debug: Check what data exists
        $this->debugDataAvailability();

        // Get filter data for dropdowns
        $schools = \App\Models\School::all();
        $classes = collect();
        $submissions = collect();

        // Get classes if school is selected
        if ($request->has('school_id') && $request->school_id) {
            $classes = \App\Models\ClassModel::where('school_id', $request->school_id)->get();
        }

        // Get submissions if class is selected
        if ($request->has('class_id') && $request->class_id) {
            $submissions = GradeSubmission::where('class_id', $request->class_id)
                ->with(['school'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Get filtered interventions
        $interventions = $this->getInterventionData($request);

        return view('educator.intervention', compact('interventions', 'schools', 'classes', 'submissions'));
    }

    /**
     * Debug method to check data availability
     */
    private function debugDataAvailability()
    {
        $gradeSubmissionsCount = GradeSubmission::count();
        $gradeSubmissionSubjectsCount = GradeSubmissionSubject::count();
        $gradesWithDataCount = GradeSubmissionSubject::whereNotNull('grade')->count();

        \Log::info('Intervention Debug Info', [
            'total_grade_submissions' => $gradeSubmissionsCount,
            'total_grade_submission_subjects' => $gradeSubmissionSubjectsCount,
            'grades_with_data' => $gradesWithDataCount,
            'sample_grades' => GradeSubmissionSubject::whereNotNull('grade')->take(5)->get()->toArray()
        ]);
    }

    /**
     * Get intervention data for AJAX requests
     */
    public function getInterventionData(Request $request = null)
    {
        // Build query with filters
        $query = GradeSubmission::with(['school', 'classModel', 'subjects'])
            ->whereIn('status', ['pending', 'submitted', 'approved']);

        // Apply filters if provided
        if ($request) {
            if ($request->has('school_id') && $request->school_id) {
                $query->where('school_id', $request->school_id);
            }

            if ($request->has('class_id') && $request->class_id) {
                $query->where('class_id', $request->class_id);
            }

            if ($request->has('submission_id') && $request->submission_id) {
                $query->where('id', $request->submission_id);
            }
        }

        $gradeSubmissions = $query->get();

        $interventionData = [];

        foreach ($gradeSubmissions as $submission) {
            $school = $submission->school;

            if (!$school) continue;

            // Get grades for this submission grouped by subject
            $grades = GradeSubmissionSubject::where('grade_submission_id', $submission->id)
                ->with(['subject', 'user'])
                ->whereNotNull('grade') // Only include records with actual grades
                ->get()
                ->groupBy('subject_id');

            foreach ($grades as $subjectId => $subjectGrades) {
                $subject = $subjectGrades->first()->subject;
                if (!$subject) continue;

                $passed = 0;
                $failed = 0;
                $inc = 0;
                $dr = 0;
                $nc = 0;
                $totalStudents = 0;

                // Get school's passing grade - handle different grading systems
                $passingGradeMin = $school->passing_grade_min ?? 75;
                $passingGradeMax = $school->passing_grade_max ?? 100;

                foreach ($subjectGrades as $grade) {
                    $totalStudents++;
                    $gradeValue = strtoupper(trim($grade->grade));

                    if ($gradeValue === 'INC') {
                        $inc++;
                    } elseif ($gradeValue === 'DR') {
                        $dr++;
                    } elseif ($gradeValue === 'NC') {
                        $nc++;
                    } elseif (is_numeric($gradeValue)) {
                        $numericGrade = floatval($gradeValue);

                        // Handle different grading systems
                        if ($passingGradeMin <= 5.0) {
                            // 1.0-5.0 grading system (lower is better)
                            if ($numericGrade <= $passingGradeMax) {
                                $passed++;
                            } else {
                                $failed++;
                            }
                        } else {
                            // 0-100 grading system (higher is better)
                            if ($numericGrade >= $passingGradeMin) {
                                $passed++;
                            } else {
                                $failed++;
                            }
                        }
                    } else {
                        // Handle other grade formats (like letter grades)
                        // For now, treat unknown formats as needing intervention
                        $failed++;
                    }
                }

                // Only include subjects that need intervention
                $needsIntervention = ($failed > 0 || $inc > 0 || $dr > 0 || $nc > 0);

                if ($needsIntervention && $totalStudents > 0) {
                    $studentsNeedingIntervention = $failed + $inc + $dr + $nc;

                    // Check if intervention already exists
                    $existingIntervention = Intervention::where([
                        'subject_id' => $subjectId,
                        'school_id' => $submission->school_id,
                        'class_id' => $submission->class_id,
                        'grade_submission_id' => $submission->id
                    ])->first();

                    if (!$existingIntervention) {
                        // Create new intervention record
                        $existingIntervention = Intervention::create([
                            'subject_id' => $subjectId,
                            'school_id' => $submission->school_id,
                            'class_id' => $submission->class_id,
                            'grade_submission_id' => $submission->id,
                            'student_count' => $studentsNeedingIntervention,
                            'status' => 'pending',
                            'created_by' => Auth::user()->user_id ?? 'system'
                        ]);
                    } else {
                        // Update student count if it has changed
                        $existingIntervention->update([
                            'student_count' => $studentsNeedingIntervention,
                            'updated_by' => Auth::user()->user_id ?? 'system'
                        ]);
                    }

                    // Add submission data to the intervention
                    $existingIntervention->semester = $submission->semester;
                    $existingIntervention->term = $submission->term;
                    $existingIntervention->academic_year = $submission->academic_year;
                    $existingIntervention->submission_id = $submission->id;

                    $interventionData[] = $existingIntervention->load(['subject', 'school', 'classModel', 'educatorAssigned']);
                }
            }
        }

        // If this is an AJAX request, return JSON
        if (request()->ajax()) {
            return response()->json([
                'subjects' => collect($interventionData)->map(function($intervention) {
                    return [
                        'id' => $intervention->id,
                        'needs_intervention' => $intervention->student_count,
                        'subject' => $intervention->subject->name ?? 'N/A',
                        'status' => ucfirst($intervention->status),
                        'intervention_date' => $intervention->intervention_date ? $intervention->intervention_date->format('Y-m-d') : null,
                        'instructor' => $intervention->educatorAssigned ?
                            $intervention->educatorAssigned->user_fname . ' ' . $intervention->educatorAssigned->user_lname :
                            'Not Assigned'
                    ];
                })
            ]);
        }

        // Convert to collection and sort
        $collection = collect($interventionData)->sortByDesc('created_at');

        // Apply status filter if provided
        if ($request && $request->has('status') && $request->status) {
            $collection = $collection->filter(function($intervention) use ($request) {
                return $intervention->status === $request->status;
            });
        }

        // Manual pagination for custom data
        $perPage = 5;
        $currentPage = request()->get('page', 1);
        $total = $collection->count();
        $items = $collection->forPage($currentPage, $perPage)->values();

        // Create paginator instance
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );

        // Append query parameters to pagination links
        $paginator->appends(request()->query());

        return $paginator;
    }

    /**
     * Show the intervention update form
     */
    public function update($id)
    {
        $intervention = Intervention::with(['subject', 'school', 'classModel', 'educatorAssigned'])
            ->findOrFail($id);

        // Get all educators for assignment dropdown
        $educators = PNUser::where('user_role', 'Educator')
            ->where('status', 'active')
            ->get();

        return view('educator.intervention-update', compact('intervention', 'educators'));
    }

    /**
     * Update intervention status and assignment
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,done',
            'intervention_date' => 'nullable|date',
            'educator_assigned' => 'nullable|exists:pnph_users,user_id',
            'remarks' => 'nullable|string|max:500'
        ]);

        $intervention = Intervention::findOrFail($id);

        $intervention->update([
            'status' => $request->status,
            'intervention_date' => $request->intervention_date,
            'educator_assigned' => $request->educator_assigned,
            'remarks' => $request->remarks,
            'updated_by' => Auth::user()->user_id
        ]);

        return redirect()->route('educator.intervention')
            ->with('success', 'Intervention updated successfully.');
    }

    /**
     * Create test data for demonstration (temporary method)
     */
    public function createTestData()
    {
        try {
            DB::beginTransaction();

            // Get first available school and class
            $school = \App\Models\School::first();
            $class = \App\Models\ClassModel::first();
            $subject = \App\Models\Subject::first();
            $students = \App\Models\PNUser::where('user_role', 'student')->take(3)->get();

            if (!$school || !$class || !$subject || $students->count() < 1) {
                return response()->json(['error' => 'Missing required data (school, class, subject, or students)']);
            }

            // Create a grade submission
            $submission = GradeSubmission::create([
                'school_id' => $school->school_id,
                'class_id' => $class->class_id,
                'semester' => '1st',
                'term' => 'Prelim',
                'academic_year' => '2024-2025',
                'status' => 'approved'
            ]);

            // Create some failing grades to trigger intervention
            // Use the school's grading system
            $failingGrade = ($school->passing_grade_min <= 5.0) ? '4.5' : '65'; // 4.5 for 1-5 system, 65 for 0-100 system

            foreach ($students as $index => $student) {
                $grade = '';
                switch ($index) {
                    case 0:
                        $grade = $failingGrade; // Failing grade
                        break;
                    case 1:
                        $grade = 'INC'; // Incomplete
                        break;
                    case 2:
                        $grade = 'DR'; // Dropped
                        break;
                    default:
                        $grade = 'NC'; // No Credit
                        break;
                }

                GradeSubmissionSubject::create([
                    'grade_submission_id' => $submission->id,
                    'subject_id' => $subject->id,
                    'user_id' => $student->user_id,
                    'grade' => $grade,
                    'status' => 'approved'
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => 'Test data created successfully',
                'details' => [
                    'submission_id' => $submission->id,
                    'school' => $school->name,
                    'class' => $class->class_name ?? 'N/A',
                    'subject' => $subject->name,
                    'students_count' => $students->count(),
                    'grading_system' => $school->passing_grade_min <= 5.0 ? '1.0-5.0' : '0-100'
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create test data: ' . $e->getMessage()]);
        }
    }

    /**
     * Get classes for a specific school (AJAX)
     */
    public function getClasses(Request $request)
    {
        $classes = \App\Models\ClassModel::where('school_id', $request->school_id)->get();
        return response()->json($classes);
    }

    /**
     * Get submissions for a specific class (AJAX)
     */
    public function getSubmissions(Request $request)
    {
        $submissions = GradeSubmission::where('class_id', $request->class_id)
            ->with(['school'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($submission) {
                return [
                    'id' => $submission->id,
                    'display_name' => $submission->semester . ' - ' . $submission->term . ' (' . $submission->academic_year . ')',
                    'semester' => $submission->semester,
                    'term' => $submission->term,
                    'academic_year' => $submission->academic_year,
                    'status' => $submission->status
                ];
            });

        return response()->json($submissions);
    }
}
