<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Subject;
use App\Models\ClassModel;
use App\Models\StudentDetail;
use App\Models\PNUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = School::with('subjects')->paginate(10);
        return view('training.manage-students', compact('schools'));
    }

    public function show(School $school)
    {
        $school->load('subjects');
        $classes = ClassModel::where('school_id', $school->school_id)
            ->with('students')
            ->get();
        return view('training.schools.show', compact('school', 'classes'));
    }

    public function create()
    {
        $batches = StudentDetail::select('batch')->distinct()->orderBy('batch')->get();
        return view('training.schools.create', compact('batches'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'school_id' => 'required|string|unique:schools,school_id',
                'name' => 'required|string|max:255',
                'department' => 'required|string|max:255',
                'course' => 'required|string|max:255',
                'semester_count' => 'required|integer|min:1',
                'terms' => 'required|array|min:1',
                'passing_grade_min' => 'required|numeric|between:1,5',
                'passing_grade_max' => 'required|numeric|between:1,5|gte:passing_grade_min',
                'failing_grade_min' => 'required|numeric|between:1,5',
                'failing_grade_max' => 'required|numeric|between:1,5|gte:failing_grade_min',
                'subjects' => 'required|array|min:1',
                'subjects.*.offer_code' => 'required|string',
                'subjects.*.name' => 'required|string',
                'subjects.*.instructor' => 'required|string',
                'subjects.*.schedule' => 'required|string',
                'classes' => 'array',
                'classes.*.class_id' => 'required|string|unique:classes,class_id',
                'classes.*.name' => 'required|string',
                'classes.*.student_ids' => 'required|array',
                'classes.*.student_ids.*' => 'exists:pnph_users,user_id',
            ]);

            // Ensure no overlap between passing and failing ranges
            if ($validated['passing_grade_max'] >= $validated['failing_grade_min']) {
                return back()->withErrors(['grade_range' => 'Passing and failing grade ranges must not overlap.'])->withInput();
            }

            DB::beginTransaction();

            // Create school
            $school = School::create([
                'school_id' => $validated['school_id'],
                'name' => $validated['name'],
                'department' => $validated['department'],
                'course' => $validated['course'],
                'semester_count' => $validated['semester_count'],
                'terms' => $validated['terms'],
                'passing_grade_min' => $validated['passing_grade_min'],
                'passing_grade_max' => $validated['passing_grade_max'],
                'failing_grade_min' => $validated['failing_grade_min'],
                'failing_grade_max' => $validated['failing_grade_max'],
            ]);

            // Create subjects
            foreach ($validated['subjects'] as $subjectData) {
                $school->subjects()->create([
                    'offer_code' => $subjectData['offer_code'],
                    'name' => $subjectData['name'],
                    'instructor' => $subjectData['instructor'],
                    'schedule' => $subjectData['schedule'],
                ]);
            }

            // Create classes if provided
            if (isset($validated['classes'])) {
                foreach ($validated['classes'] as $classData) {
                    $class = new ClassModel();
                    $class->class_id = $classData['class_id'];
                    $class->class_name = $classData['name'];
                    $class->school_id = $school->school_id;
                    $class->save();

                    if (isset($classData['student_ids'])) {
                        $class->students()->attach($classData['student_ids']);
                    }
                }
            }

            DB::commit();
            return redirect()->route('training.manage-students')
                ->with('success', 'School created successfully with subjects.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating school: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function edit(School $school)
    {
        $school->load(['subjects', 'classes.students']);
        $batches = StudentDetail::select('batch')->distinct()->orderBy('batch')->get();
        $existingClasses = $school->classes; // Get existing classes
        return view('training.schools.edit', compact('school', 'batches', 'existingClasses'));
    }

    public function update(Request $request, School $school)
    {
        try {
            \Log::info('Starting school update process');
            \Log::info('Request method: ' . $request->method());
            \Log::info('Request URL: ' . $request->url());
            \Log::info('Request data:', $request->all());
            \Log::info('School being updated:', ['school_id' => $school->school_id]);

            // Basic validation
            $validated = $request->validate([
                'school_id' => 'required|string|exists:schools,school_id',
                'name' => 'required|string|max:255',
                'department' => 'required|string|max:255',
                'course' => 'required|string|max:255',
                'semester_count' => 'required|integer|min:1',
                'terms' => 'required|array|min:1',
                'passing_grade_min' => 'required|numeric|between:1,5',
                'passing_grade_max' => 'required|numeric|between:1,5|gte:passing_grade_min',
                'failing_grade_min' => 'required|numeric|between:1,5',
                'failing_grade_max' => 'required|numeric|between:1,5|gte:failing_grade_min',
                'subjects' => 'required|array|min:1',
                'subjects.*.offer_code' => 'required|string',
                'subjects.*.name' => 'required|string',
                'subjects.*.instructor' => 'required|string',
                'subjects.*.schedule' => 'required|string',
                'new_classes' => 'sometimes|array',
                'new_classes.*.class_id' => 'required_with:new_classes|string|unique:classes,class_id',
                'new_classes.*.name' => 'required_with:new_classes|string',
                'new_classes.*.students' => 'array',
                'new_classes.*.students.*' => 'exists:pnph_users,user_id',
            ]);

            \Log::info('Validation passed');

            DB::beginTransaction();

            try {
                // Update school basic info
                $school->update([
                    'name' => $validated['name'],
                    'department' => $validated['department'],
                    'course' => $validated['course'],
                    'semester_count' => $validated['semester_count'],
                    'terms' => $validated['terms'],
                    'passing_grade_min' => $validated['passing_grade_min'],
                    'passing_grade_max' => $validated['passing_grade_max'],
                    'failing_grade_min' => $validated['failing_grade_min'],
                    'failing_grade_max' => $validated['failing_grade_max'],
                ]);

                \Log::info('School basic info updated');

                // Handle subjects
                $existingSubjectIds = $school->subjects()->pluck('id')->toArray();
                $newSubjectIds = [];
                foreach ($validated['subjects'] as $subjectData) {
                    $subject = $school->subjects()->updateOrCreate(
                        ['offer_code' => $subjectData['offer_code']],
                        [
                            'name' => $subjectData['name'],
                            'instructor' => $subjectData['instructor'],
                            'schedule' => $subjectData['schedule']
                        ]
                    );
                    $newSubjectIds[] = $subject->id;
                }
                \Log::info('Subjects updated');

                // Handle new classes
                if (isset($validated['new_classes'])) {
                    foreach ($validated['new_classes'] as $classData) {
                        if (empty($classData['name'])) {
                            \Log::warning('Skipping class due to missing name', $classData);
                            continue;
                        }
                        $class = ClassModel::create([
                            'class_id' => $classData['class_id'],
                            'class_name' => $classData['name'],
                            'school_id' => $school->school_id
                        ]);
                        $studentIds = isset($classData['students']) && is_array($classData['students']) ? $classData['students'] : [];
                        $class->students()->sync($studentIds);
                    }
                    \Log::info('New classes added');
                }

                DB::commit();
                \Log::info('Update completed successfully');

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'School updated successfully',
                        'redirect' => route('training.manage-students')
                    ]);
                }

                return redirect()->route('training.manage-students')
                    ->with('success', 'School updated successfully.');

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error updating school: ' . $e->getMessage());
                \Log::error('Stack trace: ' . $e->getTraceAsString());
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error updating school: ' . $e->getMessage()
                    ], 500);
                }
                
                return back()
                    ->with('error', 'Error updating school: ' . $e->getMessage())
                    ->withInput();
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::error('Validation error:', $e->errors());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating school: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('Request data: ' . json_encode($request->all()));
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating school: ' . $e->getMessage()
                ], 500);
            }
            
            return back()
                ->with('error', 'Error updating school: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(School $school)
    {
        try {
            DB::beginTransaction();
            $school->delete();
            DB::commit();

            return redirect()->route('training.manage-students')
                ->with('success', 'School deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting school: ' . $e->getMessage());
        }
    }

    public function getStudentsList(Request $request)
    {
        $batch = $request->query('batch_id');
        
        $query = PNUser::where('user_role', 'Student')
            ->where('status', 'active')
            ->with('studentDetail');

        if ($batch) {
            $query->whereHas('studentDetail', function($q) use ($batch) {
                $q->where('batch', $batch);
            });
        }

        $students = $query->get()
            ->map(function ($student) {
                $detail = $student->studentDetail;
                return [
                    'user_id' => $student->user_id,
                    'user_lname' => $student->user_lname,
                    'user_fname' => $student->user_fname,
                    'batch' => $detail ? $detail->batch : null,
                    'group' => $detail ? $detail->group : null,
                    'student_number' => $detail ? $detail->student_number : null,
                    'training_code' => $detail ? $detail->training_code : null
                ];
            });

        return response()->json($students);
    }
}