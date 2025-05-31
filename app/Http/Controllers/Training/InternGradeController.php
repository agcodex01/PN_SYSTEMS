<?php

namespace App\Http\Controllers\Training;

use App\Http\Controllers\Controller;
use App\Models\InternGrade;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InternGradeController extends Controller
{
    public function index(Request $request)
    {
        $query = InternGrade::with(['intern', 'school', 'class', 'intern.studentDetail'])
            ->select([
                'intern_grades.*',
                DB::raw('CONCAT(pnph_users.user_fname, " ", pnph_users.user_lname) as intern_name'),
                'schools.name as school_name',
                'classes.class_name',
                'student_details.student_id as student_id'
            ])
            ->join('pnph_users', 'intern_grades.intern_id', '=', 'pnph_users.user_id')
            ->join('schools', 'intern_grades.school_id', '=', 'schools.school_id')
            ->join('classes', 'intern_grades.class_id', '=', 'classes.class_id')
            ->leftJoin('student_details', 'pnph_users.user_id', '=', 'student_details.user_id');

        if ($request->filled('school_filter')) {
            $query->where('intern_grades.school_id', $request->school_filter);
        }

        $internGrades = $query->get();
        $schools = School::all();

        $groupedGrades = $internGrades->groupBy('class_id');

        return view('training.intern.index', compact('groupedGrades', 'schools'));
    }

    public function create()
    {
        $schools = School::all();
        return view('training.intern.create', compact('schools'));
    }

    public function store(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'school_id' => 'required|exists:schools,school_id',
                'class_id' => 'required|exists:classes,class_id',
                'intern_id' => 'required|exists:pnph_users,user_id',
                'company_name' => 'required|string',
                'grades' => 'required|array',
                'grades.ict_learning_competency' => 'required|integer|min:1|max:4',
                'grades.twenty_first_century_skills' => 'required|integer|min:1|max:4',
                'grades.expected_outputs_deliverables' => 'required|integer|min:1|max:4',
                'remarks' => 'nullable|string|max:500'
            ]);

            // Log the incoming request data
            \Log::info('Creating intern grade with data:', [
                'request_data' => $request->all(),
                'validated_data' => $validated
            ]);

            // Check if a grade already exists for this intern in this class
            $existingGrade = InternGrade::where('intern_id', $validated['intern_id'])
                ->where('class_id', $validated['class_id'])
                ->first();

            if ($existingGrade) {
                \Log::warning('Grade already exists for intern', [
                    'intern_id' => $validated['intern_id'],
                    'class_id' => $validated['class_id']
                ]);
                return redirect()->route('training.intern-grades.index')->with('error', 'A grade already exists for this intern in this class.');
            }

            DB::beginTransaction();

            try {
                // Create the intern grade
                $internGrade = new InternGrade();
                $internGrade->intern_id = $validated['intern_id'];
                $internGrade->school_id = $validated['school_id'];
                $internGrade->class_id = $validated['class_id'];
                $internGrade->company_name = $validated['company_name'];
                $internGrade->ict_learning_competency = $validated['grades']['ict_learning_competency'];
                $internGrade->twenty_first_century_skills = $validated['grades']['twenty_first_century_skills'];
                $internGrade->expected_outputs_deliverables = $validated['grades']['expected_outputs_deliverables'];
                $internGrade->remarks = $validated['remarks'] ?? null;
                $internGrade->created_by = auth()->id();
                $internGrade->updated_by = auth()->id();

                // Calculate final grade BEFORE saving
                $internGrade->calculateFinalGrade();

                // Log the grade calculation
                \Log::info('Calculated final grade:', [
                    'grades' => [
                        'ict' => $internGrade->ict_learning_competency,
                        'skills' => $internGrade->twenty_first_century_skills,
                        'outputs' => $internGrade->expected_outputs_deliverables
                    ],
                    'final_grade' => $internGrade->final_grade
                ]);

                // Save after calculating final grade
                $internGrade->save();

                DB::commit();

                \Log::info('Successfully created intern grade', [
                    'intern_grade_id' => $internGrade->id,
                    'intern_id' => $internGrade->intern_id,
                    'class_id' => $internGrade->class_id
                ]);

                // Redirect to index instead of returning JSON
                return redirect()->route('training.intern-grades.index')->with('success', 'Intern grade submitted successfully.');

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Database error while creating intern grade: ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

        } catch (\Exception $e) {
            \Log::error('Error creating intern grade: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return redirect()->route('training.intern-grades.index')->with('error', 'Failed to submit intern grade. Please try again.');
        }
    }

    public function edit(InternGrade $internGrade)
    {
        $schools = School::all();
        return view('training.intern.edit', compact('internGrade', 'schools'));
    }

    public function update(Request $request, InternGrade $internGrade)
    {
        $validated = $request->validate([
            'grades.ict_learning_competency' => 'required|integer|min:1|max:4',
            'grades.twenty_first_century_skills' => 'required|integer|min:1|max:4',
            'grades.expected_outputs_deliverables' => 'required|integer|min:1|max:4',
            'remarks' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $internGrade->ict_learning_competency = $validated['grades']['ict_learning_competency'];
            $internGrade->twenty_first_century_skills = $validated['grades']['twenty_first_century_skills'];
            $internGrade->expected_outputs_deliverables = $validated['grades']['expected_outputs_deliverables'];
            $internGrade->remarks = $validated['remarks'] ?? null;
            $internGrade->updated_by = Auth::id();

            // Recalculate final grade
            $internGrade->final_grade = $internGrade->calculateFinalGrade();
            $internGrade->save();

            DB::commit();

            return redirect()
                ->route('training.intern-grades.index')
                ->with('success', 'Intern grade has been updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating intern grade: ' . $e->getMessage());
            return back()->with('error', 'Failed to update intern grade. Please try again.');
        }
    }

    public function destroy(InternGrade $internGrade)
    {
        try {
            $internGrade->delete();
            return redirect()
                ->route('training.intern-grades.index')
                ->with('success', 'Intern grade has been deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting intern grade: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete intern grade. Please try again.');
        }
    }

    public function getInternsBySchoolAndClass(Request $request, $schoolId)
    {
        try {
            Log::info('Fetching students for school and class', [
                'school_id' => $schoolId,
                'class_id' => $request->class_id,
                'request_data' => $request->all()
            ]);

            // First, verify the school exists
            $school = \App\Models\School::find($schoolId);
            if (!$school) {
                Log::error('School not found', ['school_id' => $schoolId]);
                return response()->json(['error' => 'School not found'], 404);
            }

            // Then, verify the class exists if class_id is provided
            if ($request->has('class_id')) {
                $class = \App\Models\ClassModel::where('class_id', $request->class_id)
                    ->where('school_id', $schoolId)
                    ->first();
                if (!$class) {
                    Log::error('Class not found or does not belong to school', [
                        'class_id' => $request->class_id,
                        'school_id' => $schoolId
                    ]);
                    return response()->json(['error' => 'Class not found'], 404);
                }
            }

            // Build the query using joins instead of whereHas for better performance
            $query = \App\Models\PNUser::select('pnph_users.user_id', 'pnph_users.user_fname', 'pnph_users.user_lname')
                ->join('class_student', 'pnph_users.user_id', '=', 'class_student.user_id')
                ->join('classes', function($join) use ($schoolId, $request) {
                    $join->on('classes.id', '=', 'class_student.class_id')
                        ->where('classes.school_id', '=', $schoolId);
                    if ($request->has('class_id')) {
                        $join->where('classes.class_id', '=', $request->class_id);
                    }
                })
                ->where('pnph_users.user_role', 'student')
                ->where('pnph_users.status', 'active')
                ->orderBy('pnph_users.user_lname')
                ->orderBy('pnph_users.user_fname')
                ->distinct();

            Log::info('Query details', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);

            $students = $query->get();

            Log::info('Query results', [
                'student_count' => $students->count(),
                'students' => $students->toArray()
            ]);

            if ($students->isEmpty()) {
                Log::warning('No students found for the given criteria', [
                    'school_id' => $schoolId,
                    'class_id' => $request->class_id
                ]);
            }

            return response()->json($students);
        } catch (\Exception $e) {
            Log::error('Error fetching students: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'school_id' => $schoolId,
                'class_id' => $request->class_id
            ]);
            return response()->json(['error' => 'Failed to fetch students'], 500);
        }
    }

    public function progress()
    {
        // Fetch all intern grades with relevant fields
        $grades = \App\Models\InternGrade::select(
                'final_grade',
                'ict_learning_competency',
                'twenty_first_century_skills',
                'expected_outputs_deliverables'
            )
            ->whereNotNull('final_grade') // Only consider graded interns
            ->get();

        // Initialize data structure
        $data = [
            'ICT Learning Competency' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
            '21st Century Skills' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
            'Expected Outputs/Deliverables' => [1 => 0, 2 => 0, 3 => 0, 4 => 0],
        ];

        // Map final grade to status key (1->1, 2->2, 3->3, 4->4)
        foreach ($grades as $grade) {
            $roundedFinalGrade = round($grade->final_grade);

            if ($roundedFinalGrade >= 1 && $roundedFinalGrade <= 4) {
                 // Increment counts for each subject based on the intern's final grade status
                 // We are checking if the subject grade itself is not null (meaning it was graded)
                 // and associating it with the final overall status.
                if ($grade->ict_learning_competency !== null) {
                    $data['ICT Learning Competency'][$roundedFinalGrade]++;
                }
                if ($grade->twenty_first_century_skills !== null) {
                    $data['21st Century Skills'][$roundedFinalGrade]++;
                }
                if ($grade->expected_outputs_deliverables !== null) {
                    $data['Expected Outputs/Deliverables'][$roundedFinalGrade]++;
                }
            }
        }

        // Prepare data for Chart.js
        $chartData = [
            'labels' => array_keys($data), // Subjects on X-axis
            'datasets' => [
                [
                    'label' => 'Fully Achieved (Grade 1)',
                    'data' => [ $data['ICT Learning Competency'][1], $data['21st Century Skills'][1], $data['Expected Outputs/Deliverables'][1] ],
                    'backgroundColor' => '#22bbea', // Color for Grade 1
                    'borderColor' => 'rgb(80, 80, 80)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Partially Achieved (Grade 2)',
                    'data' => [ $data['ICT Learning Competency'][2], $data['21st Century Skills'][2], $data['Expected Outputs/Deliverables'][2] ],
                    'backgroundColor' => 'rgb(0, 157, 34)', // Color for Grade 2
                    'borderColor' => 'rgb(80, 80, 80)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Barely Achieved (Grade 3)',
                    'data' => [ $data['ICT Learning Competency'][3], $data['21st Century Skills'][3], $data['Expected Outputs/Deliverables'][3] ],
                    'backgroundColor' => '#ff9933', // Color for Grade 3
                    'borderColor' => 'rgb(81, 81, 81)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'No Achievement (Grade 4)',
                    'data' => [ $data['ICT Learning Competency'][4], $data['21st Century Skills'][4], $data['Expected Outputs/Deliverables'][4] ],
                    'backgroundColor' => 'rgb(204, 1, 1)',  // Color for Grade 4
                    'borderColor' => 'rgb(80, 80, 80)',
                    'borderWidth' => 1
                ]
            ]
        ];

        // Pass data to the view
        return view('training.analytics.intern-grades-progress', compact('chartData'));
    }
} 