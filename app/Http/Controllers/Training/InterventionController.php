<?php

namespace App\Http\Controllers\Training;

use App\Http\Controllers\Controller;
use App\Models\Intervention;
use App\Models\School;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\GradeSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InterventionController extends Controller
{
    /**
     * Display intervention list for training (view-only)
     */
    public function index(Request $request)
    {
        try {
            // Get all interventions with relationships
            $query = Intervention::with([
                'subject', 
                'school', 
                'classModel', 
                'gradeSubmission',
                'educatorAssigned'
            ]);

            // Apply filters if provided
            if ($request->filled('school_id')) {
                $query->where('school_id', $request->school_id);
            }

            if ($request->filled('class_id')) {
                $query->where('class_id', $request->class_id);
            }

            if ($request->filled('subject_id')) {
                $query->where('subject_id', $request->subject_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('submission_id')) {
                $query->where('grade_submission_id', $request->submission_id);
            }

            // Get interventions ordered by most recent with pagination
            $interventions = $query->orderBy('created_at', 'desc')->paginate(5);

            // Append query parameters to pagination links
            $interventions->appends(request()->query());

            // Get filter options
            $schools = School::orderBy('name')->get();
            $classes = ClassModel::orderBy('class_name')->get();
            $subjects = Subject::orderBy('name')->get();

            // Get submissions for dropdown
            $submissions = GradeSubmission::with(['school', 'classModel'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($submission) {
                    return [
                        'id' => $submission->id,
                        'display_name' => ($submission->school->name ?? 'Unknown School') . ' - ' .
                                       ($submission->classModel->class_name ?? 'Unknown Class') . ' - ' .
                                       $submission->semester . ' ' . $submission->term . ' (' . $submission->academic_year . ')',
                        'school_name' => $submission->school->name ?? 'Unknown School',
                        'class_name' => $submission->classModel->class_name ?? 'Unknown Class',
                        'semester' => $submission->semester,
                        'term' => $submission->term,
                        'academic_year' => $submission->academic_year
                    ];
                });

            Log::info('Training Intervention Index', [
                'total_interventions' => $interventions->count(),
                'filters_applied' => $request->only(['school_id', 'class_id', 'subject_id', 'status'])
            ]);

            return view('training.intervention.index', compact(
                'interventions',
                'schools',
                'classes',
                'subjects',
                'submissions'
            ));

        } catch (\Exception $e) {
            Log::error('Training Intervention Index Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to load interventions: ' . $e->getMessage());
        }
    }

    /**
     * Get classes for a specific school (AJAX)
     */
    public function getClasses($school_id)
    {
        try {
            $classes = ClassModel::where('school_id', $school_id)
                ->orderBy('class_name')
                ->get(['class_id', 'class_name']);

            return response()->json($classes);

        } catch (\Exception $e) {
            Log::error('Training Get Classes Error', [
                'school_id' => $school_id,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to load classes'], 500);
        }
    }

    /**
     * Get subjects for a specific school and class (AJAX)
     */
    public function getSubjects(Request $request)
    {
        try {
            // Get subjects that have interventions for the selected school/class
            $query = Subject::whereHas('interventions', function($q) use ($request) {
                if ($request->filled('school_id')) {
                    $q->where('school_id', $request->school_id);
                }
                if ($request->filled('class_id')) {
                    $q->where('class_id', $request->class_id);
                }
            });

            $subjects = $query->orderBy('name')->get(['id', 'name']);

            return response()->json($subjects);

        } catch (\Exception $e) {
            Log::error('Training Get Subjects Error', [
                'school_id' => $request->school_id,
                'class_id' => $request->class_id,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to load subjects'], 500);
        }
    }

    /**
     * Get submissions for a specific school and class (AJAX)
     */
    public function getSubmissions(Request $request)
    {
        try {
            $query = GradeSubmission::with(['school', 'classModel']);

            if ($request->filled('school_id')) {
                $query->where('school_id', $request->school_id);
            }

            if ($request->filled('class_id')) {
                $query->where('class_id', $request->class_id);
            }

            $submissions = $query->orderBy('created_at', 'desc')
                ->get()
                ->map(function($submission) {
                    return [
                        'id' => $submission->id,
                        'display_name' => $submission->semester . ' ' . $submission->term . ' (' . $submission->academic_year . ')',
                        'semester' => $submission->semester,
                        'term' => $submission->term,
                        'academic_year' => $submission->academic_year
                    ];
                });

            return response()->json($submissions);

        } catch (\Exception $e) {
            Log::error('Training Get Submissions Error', [
                'school_id' => $request->school_id,
                'class_id' => $request->class_id,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to load submissions'], 500);
        }
    }
}
