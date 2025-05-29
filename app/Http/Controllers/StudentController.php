<?php

namespace App\Http\Controllers;

use App\Models\GradeSubmission;
use App\Models\GradeSubmissionProof;
use App\Models\GradeSubmissionSubject;
use App\Models\PNUser;
use App\Models\Subject;
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
            },
            'proofs' => function($query) use ($user) {
                $query->where('user_id', $user->user_id);
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
                },
                'proofs' => function($query) use ($user) {
                    $query->where('user_id', $user->user_id);
                }
            ])
            ->firstOrFail();

        // Check if the submission is approved - if yes, don't allow editing
        if ($gradeSubmission->status === 'approved') {
            return redirect()->route('student.dashboard')->with('error', 'This submission has already been approved and cannot be modified.');
        }

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

        // Get the latest proof if it exists
        $proof = $gradeSubmission->proofs->first();

        return view('student.submission_form', compact('gradeSubmission', 'subjects', 'proof'));
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
            ->with(['proofs' => function($query) use ($user) {
                $query->where('user_id', $user->user_id);
            }])
            ->firstOrFail();

        // Check if submission is already approved
        if ($gradeSubmission->status === 'approved') {
            return redirect()->route('student.dashboard')->with('error', 'This submission has already been approved and cannot be modified.');
        }

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
                    // Check if it's a valid numeric grade (1.0-5.0)
                    if (is_numeric($value)) {
                        $value = floatval($value);
                        if ($value < 1.0 || $value > 5.0) {
                            $fail('The numeric grade must be between 1.0 and 5.0.');
                        }
                    }
                    // Check if it's a valid special grade (INC, NC, DR)
                    else if (!in_array(strtoupper($value), ['INC', 'NC', 'DR'])) {
                        $fail('The grade must be between 1.0-5.0 or one of: INC, NC, DR.');
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

            // Get the grade submission
            $gradeSubmission = GradeSubmission::findOrFail($submissionId);
            
            // Update grades and status in the pivot table
            foreach ($validated['grades'] as $subjectId => $grade) {
                // If it's a numeric grade and doesn't have a decimal point, add .0
                if (is_numeric($grade) && strpos($grade, '.') === false) {
                    $grade = floatval($grade) . '.0';
                }
                
                // Update or create the grade submission subject record
                $result = DB::table('grade_submission_subject')
                    ->updateOrInsert(
                        [
                            'grade_submission_id' => $submissionId,
                            'subject_id' => $subjectId,
                            'user_id' => $user->user_id
                        ],
                        [
                            'grade' => $grade,
                            'status' => 'submitted',
                            'updated_at' => now()
                        ]
                    );
            }
            
            // Update the main grade submission status to 'submitted' if it was 'rejected' or 'pending'
            if (in_array($gradeSubmission->status, ['rejected', 'pending', 'submitted'])) {
                $gradeSubmission->update(['status' => 'submitted']);
                
                // Also update all related grade_submission_subject records to 'submitted'
                DB::table('grade_submission_subject')
                    ->where('grade_submission_id', $submissionId)
                    ->where('user_id', $user->user_id)
                    ->update(['status' => 'submitted']);
            }

            // Log the submission
            \Log::info('Grade submission updated:', [
                'submission_id' => $submissionId,
                'user_id' => $user->user_id,
                'subjects_updated' => count($validated['grades'])
            ]);

            // Handle file upload - store in student-specific folder
            $file = $request->file('proof');
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            // Get student details for folder name
            $student = PNUser::with('studentDetail')->findOrFail($user->user_id);
            $studentId = $student->studentDetail->student_id ?? $student->user_id; // Fallback to user_id if student_id is not available
            $folderName = $student->user_lname . '_' . $studentId;
            $folderName = preg_replace('/[^a-zA-Z0-9_]/', '_', $folderName); // Sanitize folder name
            
            $filePath = $file->storeAs("proofs/{$folderName}", $fileName, 'public');

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
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            \Log::error('Database error submitting grades: ' . $e->getMessage());
            return redirect()->route('student.dashboard')->with('error', 'Database error: ' . $e->getMessage());
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::error('Validation error submitting grades: ' . $e->getMessage());
            return redirect()->route('student.dashboard')->with('error', 'Validation error: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error submitting grades: ' . $e->getMessage());
            return redirect()->route('student.dashboard')->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function viewSubmission($submissionId)
    {
        $user = Auth::user();

        // Get the grade submission
        $gradeSubmission = GradeSubmission::where('id', $submissionId)
            ->whereHas('students', function($query) use ($user) {
                $query->where('grade_submission_subject.user_id', $user->user_id);
            })
            ->with([
                'classModel',
                'subjects'
            ])
            ->firstOrFail();

        // Get the student's subject entries for this submission
        $studentSubjectEntries = DB::table('grade_submission_subject')
            ->join('subjects', 'grade_submission_subject.subject_id', '=', 'subjects.id')
            ->where('grade_submission_id', $submissionId)
            ->where('user_id', $user->user_id)
            ->select(
                'subjects.name as subject_name',
                'grade_submission_subject.grade',
                'grade_submission_subject.status'
            )
            ->get();

        // Get the proof for this submission
        $proof = GradeSubmissionProof::where('grade_submission_id', $submissionId)
            ->where('user_id', $user->user_id)
            ->first();

        return view('student.view_submission', compact('gradeSubmission', 'studentSubjectEntries', 'proof'));
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