<?php

namespace App\Http\Controllers;

use App\Models\PNUser;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    public function dashboard()
    {
        return view('training.dashboard', ['title' => 'Training Dashboard']);
    }

    public function index()
    {
        $query = PNUser::where('user_role', 'Student')
            ->where('status', 'active');

        // Apply batch filter if selected
        if (request('batch')) {
            $query->where('batch', request('batch'));
        }

        // Get unique batches for the filter dropdown
        $batches = PNUser::where('user_role', 'Student')
            ->where('status', 'active')
            ->distinct()
            ->pluck('batch')
            ->filter()
            ->sort()
            ->values();

        $students = $query->paginate(10);

        return view('training.students-info', compact('students', 'batches'));
    }

    public function edit($user_id)
    {
        $student = PNUser::where('user_id', $user_id)->firstOrFail();
        return view('training.edit-student', compact('student'));
    }

    public function view($user_id)
    {
        $student = PNUser::where('user_id', $user_id)->firstOrFail();
        return view('training.view-student', compact('student'));
    }

    public function update(Request $request, $user_id)
    {
        $student = PNUser::where('user_id', $user_id)->firstOrFail();

        $request->validate([
            'student_id' => 'required',
            'user_lname' => 'required',
            'user_fname' => 'required',
            'user_mInitial' => 'nullable',
            'user_suffix' => 'nullable',
            'batch' => 'required',
            'user_email' => 'required|email|unique:pnph_users,user_email,' . $user_id . ',user_id',
        ]);

        $student->update([
            'student_id' => $request->student_id,
            'user_lname' => $request->user_lname,
            'user_fname' => $request->user_fname,
            'user_mInitial' => $request->user_mInitial,
            'user_suffix' => $request->user_suffix,
            'batch' => $request->batch,
            'user_email' => $request->user_email,
        ]);

        return redirect()->route('training.students.index')
            ->with('success', 'Student information updated successfully.');
    }

    public function destroy($user_id)
    {
        $student = PNUser::where('user_id', $user_id)->firstOrFail();
        $student->update(['status' => 'inactive']);

        return redirect()->route('training.students.index')
            ->with('success', 'Student deactivated successfully.');
    }

    public function manageStudents()
    {
        $students = PNUser::where('user_role', 'Student')
            ->paginate(10);

        return view('training.manage-students', [
            'title' => 'Manage Students',
            'students' => $students
        ]);
    }
} 