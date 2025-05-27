<?php

namespace App\Http\Controllers\Training;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\GradeSubmission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PNUser;
use App\Models\GradeSubmissionProof;

class GradeSubmissionController extends Controller
{
    public function index(Request $request)
    {
        // Get all schools
        $schools = School::all();
        
        // Get classes for each school
        $classesBySchool = collect();
        foreach($schools as $school) {
            $classesBySchool[$school->school_id] = ClassModel::where('school_id', $school->school_id)->get();
        }
        
        // Get submissions based on filters
        $query = GradeSubmission::query();
        
        // Apply school filter if selected
        if ($request->has('school_id') && $request->school_id) {
            $query->where('school_id', $request->school_id);
        }
        
        // Apply class filter if selected
        if ($request->has('class_id') && $request->class_id) {
            $query->where('class_id', $request->class_id);
        }
        
        // Apply semester/term/year filter if selected
        if ($request->has('filter_key') && $request->filter_key) {
            $filter = explode(',', $request->filter_key);
            if (count($filter) === 3) {
                $query->where('semester', $filter[0])
                    ->where('term', $filter[1])
                    ->where('academic_year', $filter[2]);
            }
        }
        
        // Get submissions with related data
        $submissions = $query->with(['students', 'proofs', 'subjects'])->get();
        
        // Group submissions by school
        $submissionsBySchool = $submissions->groupBy('school_id');

        // Get unique filter options from submissions
        $filterOptions = $submissions->map(function($submission) {
            return $submission->semester . ',' . $submission->term . ',' . $submission->academic_year;
        })->unique()->sortDesc()->values();

        return view('training.grade-submissions.monitor', compact(
            'schools', 
            'classesBySchool',
            'submissionsBySchool',
            'filterOptions'
        ))
        ->with('filter_key', $request->filter_key)
        ->with('school_id', $request->school_id)
        ->with('class_id', $request->class_id);
    }

    public function create(Request $request)
    {
        $schools = School::all();
        $classes = [];
        $subjects = [];
    
        if ($request->has('school_id')) {
            $classes = ClassModel::where('school_id', $request->school_id)->get();
            $subjects = Subject::where('school_id', $request->school_id)->get();
        }
    
        return view('training.grade-submissions.create', compact('schools', 'classes', 'subjects'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'school_id' => 'required|exists:schools,school_id',
                'class_id' => 'required|exists:classes,class_id',
                'semester' => 'required|string',
                'term' => 'required|string',
                'academic_year' => 'required|string',
                'subject_ids' => 'required|array|min:1',
                'subject_ids.*' => 'exists:subjects,id',
            ]);

            DB::beginTransaction();

            // Create the grade submission
            $gradeSubmission = new GradeSubmission();
            $gradeSubmission->school_id = $validated['school_id'];
            $gradeSubmission->class_id = $validated['class_id'];
            $gradeSubmission->semester = $validated['semester'];
            $gradeSubmission->term = $validated['term'];
            $gradeSubmission->academic_year = $validated['academic_year'];
            $gradeSubmission->status = 'pending';
            $gradeSubmission->save();

            // Get students from the class
            $class = ClassModel::where('class_id', $validated['class_id'])->first();
            if (!$class) {
                throw new \Exception('Class not found.');
            }

            $students = $class->students()->where('user_role', 'student')->get();
            if ($students->isEmpty()) {
                throw new \Exception('No students found in the selected class.');
            }

            // Initialize grade records for each student-subject combination
            $gradeRecords = [];
            foreach ($students as $student) {
                foreach ($validated['subject_ids'] as $subjectId) {
                    $gradeRecords[] = [
                        'grade_submission_id' => $gradeSubmission->id,
                        'subject_id' => $subjectId,
                        'user_id' => $student->user_id,
                        'status' => 'pending',
                        'grade' => null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }

            // Insert all grade records in a single query
            DB::table('grade_submission_subject')->insert($gradeRecords);

            // Attach subjects to the grade submission
            // $gradeSubmission->subjects()->attach($validated['subject_ids']); // Removed as redundant

            DB::commit();

            \Log::info('Grade submission created successfully:', [
                'submission_id' => $gradeSubmission->id,
                'students_count' => $students->count(),
                'subjects_count' => count($validated['subject_ids']),
                'grade_records_count' => count($gradeRecords)
            ]);

            return redirect()
                ->route('training.grade-submissions.index')
                ->with('success', 'Grade submission created successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Please check the form for errors.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Grade Submission Creation Error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return back()
                ->withInput()
                ->with('error', 'An error occurred while creating the grade submission: ' . $e->getMessage());
        }
    }

    public function show(GradeSubmission $gradeSubmission)
    {
        $gradeSubmission->load(['school', 'classModel', 'subjects', 'students']);
        return view('training.grade-submissions.show', compact('gradeSubmission'));
    }

    public function monitor(GradeSubmission $gradeSubmission)
    {
        // Debug: Log the grade submission details
        \Log::info('Grade Submission Details:', [
            'id' => $gradeSubmission->id,
            'school_id' => $gradeSubmission->school_id,
            'class_id' => $gradeSubmission->class_id
        ]);

        // Load the students and subjects for this grade submission
        $students = $gradeSubmission->students()
            ->where('user_role', 'student')
            ->get();
            
        $subjects = $gradeSubmission->subjects()->get();

        // Debug: Log students and subjects
        \Log::info('Students and Subjects:', [
            'students_count' => $students->count(),
            'subjects_count' => $subjects->count(),
            'student_ids' => $students->pluck('user_id'),
            'subject_ids' => $subjects->pluck('id')
        ]);

        // Get all grades for this submission with proper joins
        $rawGrades = DB::table('grade_submission_subject')
            ->join('subjects', 'grade_submission_subject.subject_id', '=', 'subjects.id')
            ->where('grade_submission_subject.grade_submission_id', $gradeSubmission->id)
            ->select(
                'grade_submission_subject.user_id',
                'grade_submission_subject.subject_id',
                'grade_submission_subject.grade',
                'grade_submission_subject.status',
                'subjects.name as subject_name'
            )
            ->get();

        // Debug: Log raw grades
        \Log::info('Raw Grades from Database:', [
            'count' => $rawGrades->count(),
            'data' => $rawGrades->toArray()
        ]);

        // Organize grades by user_id and subject_id
        $grades = [];
        foreach ($rawGrades as $grade) {
            if (!isset($grades[$grade->user_id])) {
                $grades[$grade->user_id] = [];
            }
            $grades[$grade->user_id][$grade->subject_id] = (object)[
                'grade' => $grade->grade,
                'status' => $grade->status,
                'subject_name' => $grade->subject_name
            ];
        }

        // Debug: Log processed grades
        \Log::info('Processed Grades:', [
            'count' => count($grades),
            'data' => $grades
        ]);

        // If no subjects found, try to load them from the class
        if ($subjects->isEmpty()) {
            \Log::info('No subjects found via grade_submission_subject. Attempting to load from class.');
            $class = $gradeSubmission->classModel;
            if ($class) {
                \Log::info('Class model loaded for fallback.', ['class_id' => $class->class_id]);
                $subjects = $class->subjects()->get();
                \Log::info('Loaded subjects from class:', [
                    'class_id' => $class->class_id,
                    'subjects_count' => $subjects->count(),
                    'subject_ids' => $subjects->pluck('id')
                ]);
            } else {
                \Log::info('Class model not loaded for fallback.');
            }
        }

        // Load proofs for debugging
        $proofs = $gradeSubmission->proofs()->get();
        \Log::info('Loaded proofs:', [
            'count' => $proofs->count(),
            'data' => $proofs->toArray()
        ]);

        return view('training.grade-submissions.monitor', compact(
            'gradeSubmission',
            'students',
            'subjects',
            'grades'
        ));
    }

    public function recent()
    {
        $recentSubmissions = GradeSubmission::with(['school', 'classModel', 'students'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($submission) {
                return [
                    'id' => $submission->id,
                    'school_name' => $submission->school->name ?? 'N/A',
                    'class_name' => $submission->classModel->class_name ?? 'N/A',
                    'semester' => $submission->semester,
                    'term' => $submission->term,
                    'academic_year' => $submission->academic_year,
                    'status' => $submission->status,
                    'created_at' => $submission->created_at->format('M d, Y h:i A'),
                    'total_students' => $submission->students->count(),
                    'submitted_count' => $submission->students->where('pivot.status', 'submitted')->count(),
                    'approved_count' => $submission->students->where('pivot.status', 'approved')->count(),
                    'pending_count' => $submission->students->where('pivot.status', 'pending')->count(),
                    'rejected_count' => $submission->students->where('pivot.status', 'rejected')->count()
                ];
            });

        return view('training.grade-submissions.recent', compact('recentSubmissions'));
    }

    public function viewStudentSubmission(GradeSubmission $gradeSubmission, User $student)
    {
        $submissions = $gradeSubmission->students()
            ->where('user_id', $student->user_id)
            ->with('subjects')
            ->get();

        $studentGrades = DB::table('grade_submission_subject')
            ->where('grade_submission_id', $gradeSubmission->id)
            ->where('user_id', $student->user_id)
            ->join('subjects', 'grade_submission_subject.subject_id', '=', 'subjects.id')
            ->select('subjects.name as subject_name', 'grade_submission_subject.grade', 'grade_submission_subject.status')
            ->get();

        return view('training.grade-submissions.view-student', compact('gradeSubmission', 'student', 'studentGrades'));
    }

    public function destroy(GradeSubmission $gradeSubmission)
    {
        try {
            $gradeSubmission->delete();
            return redirect()->route('training.grade-submissions.recent')
                ->with('success', 'Grade submission deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while deleting the grade submission.');
        }
    }

    public function getSubjectsBySchoolAndClass(Request $request)
    {
        $schoolId = $request->query('school_id');
        $classId = $request->query('class_id');

        $query = Subject::query();

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        if ($classId) {
            $query->whereHas('classes', function($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }

        $subjects = $query->select('id', 'name', 'offer_code')->get();

        return response()->json($subjects);
    }

    public function updateStatus(Request $request, GradeSubmission $gradeSubmission)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'student_id' => 'required|exists:pnph_users,user_id',
        ]);

        try {
            $student = $gradeSubmission->students()->where('user_id', $request->student_id)->first();

            if (!$student) {
                return response()->json(['error' => 'Student not associated with this grade submission.'], 404);
            }

            DB::table('grade_submission_subject')
                ->where('grade_submission_id', $gradeSubmission->id)
                ->where('user_id', $request->student_id)
                ->update([
                    'status' => $request->status,
                    'updated_at' => now()
                ]);

            return response()->json(['message' => 'Status updated successfully']);
        } catch (\Exception $e) {
            \Log::error('Error updating grade submission status:' . $e->getMessage());
            return response()->json(['error' => 'Failed to update status'], 500);
        }
    }

    public function verify(GradeSubmission $gradeSubmission)
    {
        try {
            $gradeSubmission->update(['status' => 'approved']);

            DB::table('grade_submission_subject')
                ->where('grade_submission_id', $gradeSubmission->id)
                ->update(['status' => 'approved', 'updated_at' => now()]);

            return redirect()->route('training.grade-submissions.index')
                ->with('success', 'Grade submission verified and approved!');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while verifying the grade submission.');
        }
    }

    public function reject(GradeSubmission $gradeSubmission)
    {
        try {
            $gradeSubmission->update(['status' => 'rejected']);

            DB::table('grade_submission_subject')
                ->where('grade_submission_id', $gradeSubmission->id)
                ->update(['status' => 'rejected', 'updated_at' => now()]);

            return redirect()->route('training.grade-submissions.index')
                ->with('success', 'Grade submission rejected!');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while rejecting the grade submission.');
        }
    }

    public function uploadProof(Request $request, GradeSubmission $gradeSubmission, $studentId)
    {
        try {
            $request->validate([
                'proof' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240' // 10MB max
            ]);

            $file = $request->file('proof');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('proofs', $fileName, 'public');

            // Create or update the proof record
            $proof = GradeSubmissionProof::updateOrCreate(
                [
                    'grade_submission_id' => $gradeSubmission->id,
                    'user_id' => $studentId
                ],
                [
                    'file_path' => $filePath,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getClientOriginalExtension()
                ]
            );

            return back()->with('success', 'Proof uploaded successfully!');
        } catch (\Exception $e) {
            \Log::error('Error uploading proof: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while uploading the proof.');
        }
    }

    public function viewProof(GradeSubmission $gradeSubmission, $studentId)
    {
        $proof = $gradeSubmission->proofs()
            ->where('user_id', $studentId)
            ->first();

        if (!$proof) {
            return back()->with('error', 'No proof found for this student.');
        }

        // Get the student's name
        $student = PNUser::where('user_id', $studentId)->first();
        if (!$student) {
            return back()->with('error', 'Student not found.');
        }

        return view('training.grade-submissions.view-proof', compact('gradeSubmission', 'student', 'proof'));
    }

    public function updateProofStatus(Request $request, GradeSubmission $gradeSubmission, $studentId)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,pending'
        ]);

        try {
            $proof = $gradeSubmission->proofs()
                ->where('user_id', $studentId)
                ->first();

            if (!$proof) {
                return back()->with('error', 'No proof found for this student.');
            }

            // Update proof status
            $proof->update([
                'status' => $request->status
            ]);

            // Update the grade submission status for this student
            DB::table('grade_submission_subject')
                ->where('grade_submission_id', $gradeSubmission->id)
                ->where('user_id', $studentId)
                ->update([
                    'status' => $request->status,
                    'updated_at' => now()
                ]);

            \Log::info('Proof status updated:', [
                'proof_id' => $proof->id,
                'student_id' => $studentId,
                'new_status' => $request->status
            ]);

            $message = $request->status === 'approved' 
                ? 'Proof approved successfully.' 
                : 'Proof rejected. Student can resubmit.';

            return back()->with('success', $message);
        } catch (\Exception $e) {
            \Log::error('Error updating proof status: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while updating the proof status.');
        }
    }

    // Temporary method to fix subject associations for a grade submission
    public function fixSubmissionSubjects(GradeSubmission $gradeSubmission)
    {
        try {
            DB::beginTransaction();

            // Get the class associated with the submission
            $class = $gradeSubmission->classModel;
            if (!$class) {
                DB::rollBack();
                return back()->with('error', 'Class not found for this grade submission.');
            }

            // Get subjects associated with the class
            $subjects = $class->subjects()->get();
            if ($subjects->isEmpty()) {
                 // If no subjects in class, try subjects linked to submission (might be the issue)
                 $subjects = $gradeSubmission->subjects()->get();
                 if($subjects->isEmpty()) {
                     DB::rollBack();
                     return back()->with('error', 'No subjects found for the associated class or submission.');
                 }
            }

            // Get students associated with the class
            $students = $class->students()->where('user_role', 'student')->get();
            if ($students->isEmpty()) {
                DB::rollBack();
                return back()->with('error', 'No students found in the associated class.');
            }

            // Safely delete existing entries for this submission in the pivot table
            DB::table('grade_submission_subject')
                ->where('grade_submission_id', $gradeSubmission->id)
                ->delete();

            // Re-initialize and insert grade records for each student-subject combination
            $gradeRecords = [];
            foreach ($students as $student) {
                foreach ($subjects as $subject) {
                    $gradeRecords[] = [
                        'grade_submission_id' => $gradeSubmission->id,
                        'subject_id' => $subject->id,
                        'user_id' => $student->user_id,
                        'status' => 'pending', // Reset status to pending
                        'grade' => null, // Reset grade to null
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }

            // Insert all grade records in a single query
            if (!empty($gradeRecords)) {
                 DB::table('grade_submission_subject')->insert($gradeRecords);
            }

            DB::commit();

            return back()->with('success', 'Subject associations fixed successfully for this grade submission.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error fixing subject associations: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while fixing subject associations: ' . $e->getMessage());
        }
    }
} 