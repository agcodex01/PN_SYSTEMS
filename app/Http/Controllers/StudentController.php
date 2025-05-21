<?php

namespace App\Http\Controllers;

use App\Models\GradeSubmission;
use App\Models\GradeSubmissionProof;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $filterKey = $request->query('filter_key');

        $gradeSubmissionsQuery = GradeSubmission::whereHas('students', function($query) use ($user) {
            $query->where('grade_submission_subject.user_id', $user->user_id);
        })
        ->with([
            'classModel',
            'subjects',
            'students' => function($query) use ($user) {
                $query->where('grade_submission_subject.user_id', $user->user_id);
            }
        ])
        ->orderBy('created_at', 'desc');

        if ($filterKey) {
            $gradeSubmissionsQuery->where(DB::raw("CONCAT(semester, ' ', term, ' ', academic_year)"), $filterKey);
        }

        $gradeSubmissions = $gradeSubmissionsQuery->get();

        // For filter dropdown
        $filterOptions = GradeSubmission::whereHas('students', function($query) use ($user) {
            $query->where('grade_submission_subject.user_id', $user->user_id);
        })
        ->select(DB::raw("CONCAT(semester, ' ', term, ' ', academic_year) AS filter_key"))
        ->distinct()
        ->pluck('filter_key')
        ->sortDesc()
        ->values()
        ->all();

        // Transform the data to avoid the ambiguous user_id issue
        $gradeSubmissions->each(function ($submission) use ($user) {
            $submission->subjects->each(function ($subject) use ($submission, $user) {
                $studentSubmission = DB::table('grade_submission_subject')
                    ->where('grade_submission_id', $submission->id)
                    ->where('user_id', $user->user_id)
                    ->where('subject_id', $subject->id)
                    ->first();
                $subject->student_submission = $studentSubmission;
            });
        });

        return view('student.dashboard', compact('gradeSubmissions', 'filterOptions', 'filterKey'));
    }

    public function showSubmissionForm($submissionId)
    {
        $user = Auth::user();

        // Fetch the grade submission and eager load classModel and students
        $gradeSubmission = GradeSubmission::where('id', $submissionId)
            ->whereHas('students', function($query) use ($user) {
                $query->where('grade_submission_subject.user_id', $user->user_id);
            })
            ->with([
                'classModel',
                'subjects',
                'students' => function($query) use ($user) {
                    $query->where('grade_submission_subject.user_id', $user->user_id);
                }
            ])
            ->firstOrFail();

        // Get all subjects and their grades for this student
        $subjects = DB::table('subjects')
            ->join('grade_submission_subject as gss', 'subjects.id', '=', 'gss.subject_id')
            ->where('gss.grade_submission_id', $submissionId)
            ->where('gss.user_id', $user->user_id)
            ->select('subjects.*', 'gss.grade', 'gss.status')
            ->get();

        // If no subjects found, try getting them directly from the grade submission
        if ($subjects->isEmpty()) {
            $subjects = $gradeSubmission->subjects;
        }

        return view('student.submission_form', compact('gradeSubmission', 'subjects'));
    }

    public function submitGrades(Request $request, $submissionId)
    {
        $user = Auth::user();

        \Log::info('Starting grade submission process:', [
            'submission_id' => $submissionId,
            'user_id' => $user->user_id
        ]);

        // Find the grade submission and verify the student is associated
        $gradeSubmission = GradeSubmission::where('id', $submissionId)
            ->whereHas('students', function($query) use ($user) {
                $query->where('grade_submission_subject.user_id', $user->user_id);
            })
            ->firstOrFail();

        \Log::info('Found grade submission:', [
            'submission_id' => $gradeSubmission->id,
            'school_id' => $gradeSubmission->school_id,
            'class_id' => $gradeSubmission->class_id
        ]);

        // Validate the submitted grades and proof
        $validated = $request->validate([
            'grades' => 'required|array',
            'grades.*' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!is_numeric($value) && !in_array(strtoupper($value), ['INC', 'NC', 'DR'])) {
                        $fail('The grade must be a number between 1.0-5.0 or one of: INC, NC, DR.');
                    }
                    if (is_numeric($value) && ($value < 1.0 || $value > 5.0)) {
                        $fail('The numeric grade must be between 1.0 and 5.0.');
                    }
                },
            ],
            'proof' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240' // 10MB max
        ]);

        \Log::info('Validated grades:', [
            'grades' => $validated['grades']
        ]);

        try {
            DB::beginTransaction();

            // Update grades and status in the pivot table (this will always replace old grades, including rejected ones)
            foreach ($validated['grades'] as $subjectId => $grade) {
                $result = DB::table('grade_submission_subject')
                    ->where('grade_submission_id', $submissionId)
                    ->where('subject_id', $subjectId)
                    ->where('user_id', $user->user_id)
                    ->update([
                        'grade' => $grade,
                        'status' => 'submitted',
                        'updated_at' => now()
                    ]);

                \Log::info('Updated grade for subject:', [
                    'subject_id' => $subjectId,
                    'grade' => $grade,
                    'result' => $result
                ]);
            }

            // Handle file upload
            $file = $request->file('proof');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('proofs', $fileName, 'public');

            // Create or update the proof record
            $proof = GradeSubmissionProof::updateOrCreate(
                [
                    'grade_submission_id' => $submissionId,
                    'user_id' => $user->user_id
                ],
                [
                    'file_path' => $filePath,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientOriginalExtension(),
                    'status' => 'pending'
                ]
            );

            \Log::info('Proof uploaded:', [
                'proof_id' => $proof->id,
                'file_path' => $filePath
            ]);

            // Verify the grades were stored
            $storedGrades = DB::table('grade_submission_subject')
                ->where('grade_submission_id', $submissionId)
                ->where('user_id', $user->user_id)
                ->get();

            \Log::info('Stored grades verification:', [
                'count' => $storedGrades->count(),
                'grades' => $storedGrades->toArray()
            ]);

            DB::commit();

            return redirect()->route('student.dashboard')->with('success', 'Grades and proof submitted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error submitting grades: ' . $e->getMessage());
            return redirect()->route('student.dashboard')->with('error', 'An error occurred while submitting grades.');
        }
    }

    public function viewSubmission($submissionId)
    {
        $user = Auth::user();
        $gradeSubmission = GradeSubmission::where('id', $submissionId)
            ->whereHas('students', function($query) use ($user) {
                $query->where('grade_submission_subject.user_id', $user->user_id);
            })
            ->with([
                'classModel',
                'subjects',
                'students' => function($query) use ($user) {
                    $query->where('grade_submission_subject.user_id', $user->user_id);
                }
            ])
            ->firstOrFail();

        // Get all subjects and their grades for this student
        $studentSubjectEntries = DB::table('subjects')
            ->join('grade_submission_subject as gss', 'subjects.id', '=', 'gss.subject_id')
            ->where('gss.grade_submission_id', $submissionId)
            ->where('gss.user_id', $user->user_id)
            ->select('subjects.name as subject_name', 'gss.grade', 'gss.status')
            ->get();

        return view('student.view_submission', compact('gradeSubmission', 'studentSubjectEntries'));
    }

    public function submissionsList(Request $request)
    {
        $user = Auth::user();
        $filterKey = $request->query('filter_key');

        $gradeSubmissionsQuery = GradeSubmission::whereHas('students', function($query) use ($user) {
            $query->where('grade_submission_subject.user_id', $user->user_id);
        })
        ->with([
            'classModel',
            'subjects',
            'students' => function($query) use ($user) {
                $query->where('grade_submission_subject.user_id', $user->user_id);
            }
        ])
        ->orderBy('created_at', 'desc');

        if ($filterKey) {
            $gradeSubmissionsQuery->where(DB::raw("CONCAT(semester, ' ', term, ' ', academic_year)"), $filterKey);
        }

        $gradeSubmissions = $gradeSubmissionsQuery->get();

        // For filter dropdown
        $filterOptions = GradeSubmission::whereHas('students', function($query) use ($user) {
            $query->where('grade_submission_subject.user_id', $user->user_id);
        })
        ->select(DB::raw("CONCAT(semester, ' ', term, ' ', academic_year) AS filter_key"))
        ->distinct()
        ->pluck('filter_key')
        ->sortDesc()
        ->values()
        ->all();

        return view('student.grade_submissions_list', compact('gradeSubmissions', 'filterOptions', 'filterKey'));
    }
} 