<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Subject;
use App\Models\ClassModel;
use App\Models\StudentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
                'passing_grade_max' => 'required|numeric|between:1,5',
                'subjects' => 'required|array|min:1',
                'subjects.*.offer_code' => 'required|string',
                'subjects.*.name' => 'required|string',
                'subjects.*.instructor' => 'required|string',
                'subjects.*.schedule' => 'required|string',
                'classes' => 'array',
                'classes.*.class_id' => 'required|string|unique:classes,class_id',
                'classes.*.name' => 'required|string',
                'classes.*.batch' => 'required|string',
                'classes.*.student_ids' => 'required|array',
                'classes.*.student_ids.*' => 'exists:pnph_users,user_id',
            ]);

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
                    $class->name = $classData['name'];
                    $class->school_id = $school->school_id;
                    $class->batch = $classData['batch'];
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
        $school->load('subjects');
        return view('training.schools.edit', compact('school'));
    }

    public function update(Request $request, School $school)
    {
        $request->validate([
            'school_id' => 'required|string|exists:schools,school_id',
            'name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'course' => 'required|string|max:255',
            'semester_count' => 'required|integer|min:1',
            'terms' => 'required|array|min:1',
            'passing_grade_min' => 'required|numeric|between:1,5',
            'passing_grade_max' => 'required|numeric|between:1,5',
            'subjects' => 'required|array|min:1',
            'subjects.*.offer_code' => 'required|string',
            'subjects.*.name' => 'required|string',
            'subjects.*.instructor' => 'required|string',
            'subjects.*.schedule' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $school->update([
                'name' => $request->name,
                'department' => $request->department,
                'course' => $request->course,
                'semester_count' => $request->semester_count,
                'terms' => $request->terms,
                'passing_grade_min' => $request->passing_grade_min,
                'passing_grade_max' => $request->passing_grade_max,
            ]);

            // Delete existing subjects
            $school->subjects()->delete();

            // Create new subjects
            foreach ($request->subjects as $subjectData) {
                $school->subjects()->create([
                    'offer_code' => $subjectData['offer_code'],
                    'name' => $subjectData['name'],
                    'instructor' => $subjectData['instructor'],
                    'schedule' => $subjectData['schedule'],
                ]);
            }

            DB::commit();
            return redirect()->route('training.manage-students')
                ->with('success', 'School updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating school: ' . $e->getMessage())
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
} 