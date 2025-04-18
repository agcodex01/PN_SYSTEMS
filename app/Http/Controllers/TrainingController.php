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
        $students = PNUser::where('user_role', 'Student')
            ->where('status', 'active')
            ->paginate(10);

        return view('training.students-info', compact('students'));
    }

    public function edit($user_id)
    {
        $student = PNUser::where('user_id', $user_id)->firstOrFail();
        return view('training.edit-student', compact('student'));
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
        ]);

        $student->update($request->all());

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