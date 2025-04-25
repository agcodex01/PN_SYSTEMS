<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\ClassModel;
use App\Models\PNUser;
use App\Models\StudentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClassController extends Controller
{
    public function index(School $school)
    {
        $classes = ClassModel::with(['students', 'school'])
            ->where('school_id', $school->school_id)
            ->get();
        return view('training.classes.index', compact('classes', 'school'));
    }

    public function create(School $school)
    {
        $students = PNUser::where('user_role', 'student')
            ->with('studentDetail')
            ->get();
        return view('training.classes.create', compact('school', 'students'));
    }

    public function store(Request $request, School $school)
    {
        try {
            $validated = $request->validate([
                'class_id' => 'required|string|unique:classes,class_id',
                'class_name' => 'required|string'
            ]);

            $class = new ClassModel();
            $class->class_id = $validated['class_id'];
            $class->class_name = $validated['class_name'];
            $class->school_id = $school->school_id;
            $class->save();

            // If there are student IDs, attach them to the class
            if ($request->has('student_ids')) {
                $class->students()->attach($request->student_ids);
            }

            return redirect()
                ->route('training.classes.index', ['school' => $school->school_id])
                ->with('success', 'Class created successfully.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error creating class: ' . $e->getMessage());
        }
    }

    public function edit(ClassModel $class)
    {
        $class->load(['school', 'students']);
        $students = PNUser::where('user_role', 'student')->get();
        return view('training.classes.edit', compact('class', 'students'));
    }

    public function update(Request $request, ClassModel $class)
    {
        try {
            $validated = $request->validate([
                'class_id' => 'required|string|unique:classes,class_id,' . $class->id,
                'class_name' => 'required|string',
            ]);

            $class->update([
                'class_id' => $validated['class_id'],
                'class_name' => $validated['class_name'],
            ]);

            // Update students
            if ($request->has('student_ids')) {
                $class->students()->sync($request->student_ids);
            } else {
                $class->students()->detach();
            }

            return redirect()->route('training.schools.show', ['school' => $class->school_id])
                ->with('success', 'Class updated successfully.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error updating class: ' . $e->getMessage());
        }
    }

    public function show(ClassModel $class)
    {
        $class->load(['school', 'students.studentDetail']);
        return view('training.classes.show', compact('class'));
    }

    public function destroy(ClassModel $class)
    {
        $class->delete();
        return redirect()->route('training.classes.index')
            ->with('success', 'Class deleted successfully.');
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
                    'batch' => $detail->batch,
                    'group' => $detail->group,
                    'student_number' => $detail->student_number,
                    'training_code' => $detail->training_code
                ];
            });

        return response()->json($students);
    }
}