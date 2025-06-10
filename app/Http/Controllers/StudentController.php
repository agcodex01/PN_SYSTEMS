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
    public function profile()
    {
        $user = Auth::user();
        
        // Load student details
        $user->load('studentDetail');
        
        // Get classes for the user directly from the ClassModel
        $classes = \App\Models\ClassModel::whereHas('students', function($query) use ($user) {
            $query->where('class_student.user_id', $user->user_id);
        })->whereNotIn('class_name', ['C2026', 'C2027'])
        ->get();
        
        // Add the classes to the user object for the view
        $user->setRelation('classes', $classes);
        
        return view('student.profile', compact('user'));
    }
    public function grades(Request $request)
    {
        $user = Auth::user();
        
        // Get unique terms and years for the filter dropdowns
        $terms = $this->getSortedTerms();
            
        $years = DB::table('grade_submissions')
            ->distinct()
            ->pluck('academic_year')
            ->filter()
            ->sortBy(function($year) {
                // Extract the start year from academic year format (e.g., "2023-2024" -> 2023)
                if (preg_match('/^(\d{4})-\d{4}$/', $year, $matches)) {
                    return (int)$matches[1];
                }
                return 9999; // Default for invalid formats (put them at the end)
            })
            ->values();
        
        // Build the base query for filtered results (used for table/chart)
        $query = DB::table('grade_submission_subject')
            ->join('subjects', 'grade_submission_subject.subject_id', '=', 'subjects.id')
            ->join('grade_submissions', 'grade_submission_subject.grade_submission_id', '=', 'grade_submissions.id')
            ->where('grade_submission_subject.user_id', $user->user_id);
        
        // Apply filters if provided
        if ($request->filled('term')) {
            $query->where('grade_submissions.term', $request->term);
        }
        
        if ($request->filled('academic_year')) {
            $query->where('grade_submissions.academic_year', $request->academic_year);
        }
        
        // Get the filtered subjects with grades (for table/chart)
        $subjectsWithGrades = $query
            ->select(
                'grade_submission_subject.*', 
                'subjects.name as subject_name', 
                'subjects.offer_code as subject_code',
                'grade_submissions.term',
                'grade_submissions.academic_year',
                'grade_submissions.semester'
            )
            ->orderBy('grade_submissions.academic_year', 'desc')
            ->orderBy('grade_submissions.term')
            ->orderBy('subjects.name')
            ->get();

        // Get total grade status counts for the status cards (unfiltered)
        $statusCounts = [
            'pass' => 0,
            'fail' => 0,
            'inc' => 0,
            'nc' => 0,
            'dr' => 0
        ];

        // Get all subjects for the user (unfiltered) for status cards
        $allSubjects = DB::table('grade_submission_subject')
            ->join('subjects', 'grade_submission_subject.subject_id', '=', 'subjects.id')
            ->join('grade_submissions', 'grade_submission_subject.grade_submission_id', '=', 'grade_submissions.id')
            ->where('grade_submission_subject.user_id', $user->user_id)
            ->select('grade_submission_subject.status')
            ->get();

        // Count subjects by status (unfiltered)
        foreach ($allSubjects as $subject) {
            $status = strtolower($subject->status);
            if (array_key_exists($status, $statusCounts)) {
                $statusCounts[$status]++;
            }
        }

        // Get subject statuses for the chart
        $subjectStatuses = DB::table('grade_submission_subject')
            ->join('grade_submissions', 'grade_submission_subject.grade_submission_id', '=', 'grade_submissions.id')
            ->where('grade_submission_subject.user_id', $user->user_id)
            ->when($request->filled('term'), function($query) use ($request) {
                return $query->where('grade_submissions.term', $request->term);
            })
            ->when($request->filled('academic_year'), function($query) use ($request) {
                return $query->where('grade_submissions.academic_year', $request->academic_year);
            })
            ->pluck('grade_submission_subject.status', 'grade_submission_subject.id')
            ->toArray();

        return view('student.grades', compact(
            'subjectsWithGrades', 
            'statusCounts',
            'terms',
            'years',
            'request',
            'subjectStatuses'
        ));
    }

    protected function getSortedTerms()
    {
        $terms = DB::table('grade_submissions')
            ->distinct()
            ->pluck('term')
            ->filter()
            ->sortBy(function($term) {
                $order = [
                    'prelim' => 1,
                    'midterm' => 2,
                    'semi-final' => 3,
                    'final' => 4
                ];
                return $order[strtolower($term)] ?? 999;
            })
            ->values();
            
        return $terms;
    }

    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $filterKey = $request->query('filter_key');

        // Get unique terms and years for the filter dropdowns
        $terms = $this->getSortedTerms();
        $years = DB::table('grade_submissions')
            ->distinct()
            ->pluck('academic_year')
            ->filter()
            ->sortBy(function($year) {
                // Extract the start year from academic year format (e.g., "2023-2024" -> 2023)
                if (preg_match('/^(\d{4})-\d{4}$/', $year, $matches)) {
                    return (int)$matches[1];
                }
                return 9999; // Default for invalid formats (put them at the end)
            })
            ->values();

        // Get all grade submissions for the user, with optional term/year filter
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

        // Apply term/year filter if provided
        if ($request->filled('term')) {
            $gradeSubmissionsQuery->where('term', $request->term);
        }
        if ($request->filled('academic_year')) {
            $gradeSubmissionsQuery->where('academic_year', $request->academic_year);
        }

        if ($filterKey) {
            $gradeSubmissionsQuery->where(DB::raw("CONCAT(semester, ' ', term, ' ', academic_year)"), $filterKey);
        }

        $gradeSubmissions = $gradeSubmissionsQuery->get();

        // Gather all subjects for the user
        $allSubjects = collect();
        foreach ($gradeSubmissions as $submission) {
            foreach ($submission->students as $student) {
                if ($student->pivot) {
                    $allSubjects->push($student->pivot);
                }
            }
        }

        $statusCounts = [
            'pass' => 0,
            'fail' => 0,
            'inc' => 0,
            'nc' => 0,
            'dr' => 0
        ];

        foreach ($allSubjects as $subject) {
            $status = strtolower($subject->status ?? '');
            $grade = $subject->grade ?? null;

            if (is_numeric($grade)) {
                if ($grade >= 3.0 && $grade <= 5.0) {
                    $statusCounts['pass']++;
                } elseif ($grade > 0 && $grade < 3.0) {
                    $statusCounts['fail']++;
                }
            } elseif ($status === 'inc') {
                $statusCounts['inc']++;
            } elseif ($status === 'nc') {
                $statusCounts['nc']++;
            } elseif ($status === 'dr') {
                $statusCounts['dr']++;
            }
        }

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

        return view('student.dashboard', compact(
            'gradeSubmissions', 
            'filterOptions', 
            'filterKey',
            'statusCounts',
            'terms',
            'years'
        ));
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