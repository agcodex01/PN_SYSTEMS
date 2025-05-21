<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class StudentController extends Controller
{
    public function dashboard()
{
    $notifications = Notification::where('user_id', auth()->user()->user_id)
        ->where('is_read', false)
        ->with('gradeSubmission')
        ->get();

    return view('student.dashboard', compact('notifications'));
}
}
