<?php
namespace App\Http\Controllers;

use App\Models\School;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\GradeSubmission;
use Illuminate\Http\Request;

class GradeSubmissionController extends Controller
{


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
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,school_id',
            'class_id' => 'required|exists:classes,class_id',
            'semester' => 'required|string',
            'term' => 'required|string',
            'academic_year' => 'required|string',
            'subject_ids' => 'required|array|min:1',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        GradeSubmission::create([
            'school_id' => $validated['school_id'],
            'class_id' => $validated['class_id'],
            'semester' => $validated['semester'],
            'term' => $validated['term'],
            'academic_year' => $validated['academic_year'],
            'subject_ids' => $validated['subject_ids'],
        ]);

        return redirect()->route('grade-submissions.index')
            ->with('success', 'Grade submission created successfully!');
    }

    public function index()
    {
        // Fetch all grade submissions from the database
        $gradeSubmissions = GradeSubmission::all();

        return view('training.grade-submissions.index', compact('gradeSubmissions'));
    }

    public function getSubjectsBySchoolAndClass(Request $request)
    {
        $request->validate([
            'school_id' => 'required|integer|exists:schools,school_id',
            'class_id' => 'required|integer|exists:classes,class_id',
        ]);
        
        // Fetch subjects that belong to the selected school and are associated with the selected class
        $subjects = Subject::where('school_id', $request->school_id)
            ->whereHas('classes', function ($query) use ($request) {
                $query->where('class_id', $request->class_id);
            })
            ->get(['id', 'name', 'offer_code']); // Fetch only necessary fields
    
        return response()->json($subjects);
    }
}
