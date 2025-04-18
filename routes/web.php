<?php

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PNUserController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TrainingController;

Route::get('/', function () {
    return view('welcome');
});

// Login Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login');

//Forgot Password Routes
Route::get('/forgot-password', function () {
    return view('forgotpassword');
})->name('forgot-password');
Route::post('/forgot-password/verify', [AuthController::class, 'verifyForgotPassword'])->name('forgot-password.verify');

// Reset password (after forgot password verification)
Route::get('/reset-password', [AuthController::class, 'showResetPasswordForm'])->name('reset-password');
Route::post('/reset-password/update', [AuthController::class, 'resetPassword'])->name('reset-password.update');

Route::middleware('auth')->group(function () {
    // Change Password Routes
    Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('change-password');
    Route::post('/update-password', [AuthController::class, 'updatePassword'])->name('update-password');

    // Logout Route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware('can:admin-access')->group(function () {
        Route::resource('pnph_users', PNUserController::class);
        Route::get('/dashboard', [PNUserController::class, 'dashboard'])->name('dashboard');
    });
    
    // Educator routes
    Route::prefix('educator')->name('educator.')->middleware('can:educator-access')->group(function () {
        Route::get('/dashboard', function () {
            return view('educator.dashboard', ['title' => 'Educator Dashboard']);
        })->name('dashboard');
    });
    
    // Training routes
    Route::prefix('training')->name('training.')->middleware('can:training-access')->group(function () {
        Route::get('/dashboard', function () {
            return view('training.dashboard', ['title' => 'Training Dashboard']);
        })->name('dashboard');

        // Student Information Routes
        Route::get('/students', [TrainingController::class, 'index'])->name('students.index');
        Route::get('/students/{user_id}/edit', [TrainingController::class, 'edit'])->name('students.edit');
        Route::put('/students/{user_id}', [TrainingController::class, 'update'])->name('students.update');
        Route::delete('/students/{user_id}', [TrainingController::class, 'destroy'])->name('students.destroy');

        // Manage Students
        Route::get('/manage-students', [TrainingController::class, 'manageStudents'])->name('manage-students');
        
        // Grade Submission
        Route::get('/grade-submission', [TrainingController::class, 'gradeSubmission'])->name('grade-submission');
        Route::post('/grade-submission', [TrainingController::class, 'submitGrade'])->name('submit-grade');
        
        // Analytics
        Route::get('/analytics', [TrainingController::class, 'analytics'])->name('analytics');
        
        // Intervention Status
        Route::get('/intervention-status', [TrainingController::class, 'interventionStatus'])->name('intervention-status');
        
        // Profile
        Route::get('/profile', [TrainingController::class, 'profile'])->name('profile');
        Route::put('/profile', [TrainingController::class, 'updateProfile'])->name('update-profile');
    });
    
    // Student routes
    Route::prefix('student')->name('student.')->middleware('can:student-access')->group(function () {
        Route::get('/dashboard', function () {
            return view('student.dashboard', ['title' => 'Student Dashboard']);
        })->name('dashboard');
    });
});
