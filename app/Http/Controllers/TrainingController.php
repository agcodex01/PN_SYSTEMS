<?php

namespace App\Http\Controllers;

use App\Models\PNUser;
use App\Models\School;
use App\Models\ClassModel;
use App\Models\StudentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingController extends Controller
{

    public function dashboard()
    {
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

        // Get recent items
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

        return view('training.dashboard', [
            'title' => 'Training Dashboard',
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



    public function index()
    {
        $students = PNUser::where('user_role', 'Student')
            ->where('status', 'active')
            ->with('studentDetail')
            ->paginate(10);

        return view('training.students-info', compact('students'));
    }







    public function edit($user_id)
    {
        $student = PNUser::with('studentDetail')
            ->where('user_id', $user_id)
            ->firstOrFail();
        return view('training.edit-student', compact('student'));
    }

    public function view($user_id)
    {
        $student = PNUser::with('studentDetail')
            ->where('user_id', $user_id)
            ->firstOrFail();
        return view('training.view-student', compact('student'));
    }

    public function update(Request $request, $user_id)
    {
        $student = PNUser::where('user_id', $user_id)->firstOrFail();

        $request->validate([
            'user_lname' => 'required',
            'user_fname' => 'required',
            'user_mInitial' => 'nullable',
            'user_suffix' => 'nullable',
            'user_email' => 'required|email|unique:pnph_users,user_email,' . $user_id . ',user_id',
            'batch' => 'required|digits:4',
            'group' => 'required|size:2',
            'student_number' => 'required|digits:4',
            'training_code' => 'required|size:2',
            'gender' => 'required|in:Male,Female',
        ]);

        // Update user information
        $student->update([
            'user_lname' => $request->user_lname,
            'user_fname' => $request->user_fname,
            'user_mInitial' => $request->user_mInitial,
            'user_suffix' => $request->user_suffix,
            'user_email' => $request->user_email,
        ]);

        // Generate student ID
        $studentId = $request->batch . $request->group . $request->student_number . $request->training_code;

        // Update or create student details
        StudentDetail::updateOrCreate(
            ['user_id' => $user_id],
            [
                'student_id' => $studentId,
                'batch' => $request->batch,
                'group' => $request->group,
                'student_number' => $request->student_number,
                'training_code' => $request->training_code,
                'gender' => $request->gender,
            ]
        );

        return redirect()->route('training.students.index')
            ->with('success', 'Student information updated successfully.');
    }

    public function destroy($user_id)
    {
        try {
            $user = PNUser::findOrFail($user_id);
            $user->update(['status' => 'inactive']);
            return redirect()->route('training.students.index')
                ->with('success', 'Student deactivated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deactivating student: ' . $e->getMessage());
        }
    }

    public function getStudentsList()
    {
        try {
            $students = PNUser::where('user_role', 'Student')
                ->where('status', 'active')
                ->with('studentDetail')
                ->get()
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
                })
                ->filter(function ($student) {
                    return $student['batch'] !== null;
                });

            return response()->json($students);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}



//Training Dashboard Analytics 
public function dashboard()
{
    // Count students per batch (include both active and inactive)
    $batchCounts = StudentDetail::whereHas('user', function ($q) {
            $q->where('user_role', 'Student');
        })
        ->select('batch')
        ->selectRaw('count(*) as count')
        ->groupBy('batch')
        ->orderBy('batch')
        ->pluck('count', 'batch');

    return view('training.dashboard', [
        'title' => 'Training Dashboard',
        'batchCounts' => $batchCounts
    ]);
}

} 




