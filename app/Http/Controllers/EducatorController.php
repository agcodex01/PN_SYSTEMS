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

    // Other methods can be copied from TrainingController as needed (like index, view, etc.)
}
