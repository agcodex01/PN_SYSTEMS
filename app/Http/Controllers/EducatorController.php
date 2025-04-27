<?php

namespace App\Http\Controllers;

use App\Models\PNUser;
use App\Models\School;
use App\Models\ClassModel;
use App\Models\StudentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EducatorController extends Controller
{
    public function dashboard()
    {
        // Similar logic as Training, but here we fetch the data for Educator's view
        $schoolsCount = \App\Models\School::count();
        $classesCount = \App\Models\ClassModel::count();
        $studentsCount = PNUser::where('user_role', 'Student')->where('status', 'active')->count();
        
        // Get gender distribution from student_details table
        $maleCount = \App\Models\StudentDetail::where('gender', 'Male')->count();
        $femaleCount = \App\Models\StudentDetail::where('gender', 'Female')->count();
        
        // Get students count by batch
        $batchCounts = StudentDetail::select('batch')
            ->selectRaw('count(*) as count')
            ->groupBy('batch')
            ->orderBy('batch')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->batch => $item->count];
            });

        // Get gender distribution by batch
        $genderByBatch = [];
        foreach ($batchCounts->keys() as $batch) {
            $genderByBatch[$batch] = [
                'male' => StudentDetail::where('batch', $batch)
                    ->where('gender', 'Male')
                    ->count(),
                'female' => StudentDetail::where('batch', $batch)
                    ->where('gender', 'Female')
                    ->count()
            ];
        }

        // Get recent items for educator dashboard
        $recentStudents = PNUser::where('user_role', 'Student')
            ->where('status', 'active')
            ->with('studentDetail')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentSchools = School::orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentClasses = ClassModel::with('school')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('educator.dashboard', [
            'title' => 'Educator Dashboard',
            'schoolsCount' => $schoolsCount,
            'classesCount' => $classesCount,
            'studentsCount' => $studentsCount,
            'maleCount' => $maleCount,
            'femaleCount' => $femaleCount,
            'batchCounts' => $batchCounts,
            'genderByBatch' => $genderByBatch,
            'recentStudents' => $recentStudents,
            'recentSchools' => $recentSchools,
            'recentClasses' => $recentClasses
        ]);
    }

    


    public function studentsInfo(Request $request)
{
    // Get all unique batch numbers to display in the dropdown
    $batches = StudentDetail::distinct()->pluck('batch');
    
    // Get students, filter by batch if a batch is selected
    $students = PNUser::where('user_role', 'Student')
        ->where('status', 'active')
        ->with('studentDetail')
        ->when($request->has('batch') && $request->batch != '', function ($query) use ($request) {
            return $query->whereHas('studentDetail', function ($q) use ($request) {
                $q->where('batch', $request->batch);
            });
        })
        ->paginate(10);

    // Get the role of the currently logged-in user
    $userRole = Auth::user()->user_role;

    // Return the educator version of the student info view
    return view('educator.students-info', compact('students', 'batches', 'userRole'));
}



public function index(Request $request)
{
    $batches = StudentDetail::distinct()->pluck('batch');

    $students = PNUser::where('user_role', 'Student')
        ->where('status', 'active')
        ->with('studentDetail')
        ->when($request->has('batch') && $request->batch != '', function ($query) use ($request) {
            return $query->whereHas('studentDetail', function ($q) use ($request) {
                $q->where('batch', $request->batch);
            });
        })
        ->paginate(10);

    $userRole = Auth::user()->user_role;

    return view('educator.students-info', compact('students', 'batches', 'userRole'));
}

public function viewStudent($user_id)
{
    $student = PNUser::where('user_id', $user_id)
        ->where('user_role', 'Student')
        ->with('studentDetail')
        ->firstOrFail();

    return view('educator.view-student', compact('student'));
}









}

