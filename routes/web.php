<?php

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PNUserController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\EducatorController;
use App\Http\Controllers\GradeSubmissionController;
use App\Http\Controllers\StudentGradeSubmissionController;
use App\Http\Controllers\StudentController;

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
        Route::get('/dashboard', [EducatorController::class, 'dashboard'])->name('dashboard');

    Route::get('/students-info', [EducatorController::class, 'index'])->name('educator.students.index');
    Route::get('/students/{user_id}/view', [EducatorController::class, 'viewStudent'])->name('students.view');
    Route::get('/students-info', [EducatorController::class, 'index'])->name('students.index');
        


});
 


    
    
    // Training routes

    Route::prefix('training')->name('training.')->middleware(['auth', 'can:training-access'])->group(function () {
        Route::get('/dashboard', [TrainingController::class, 'dashboard'])->name('dashboard');
        Route::get('/students-info', [TrainingController::class, 'index'])->name('students-info');
    
        // Student Information Routes
        Route::get('/students/list', [TrainingController::class, 'getStudentsList'])->name('students.list');
        Route::get('/students', [TrainingController::class, 'index'])->name('students.index');
        Route::get('/students/{user_id}/view', [TrainingController::class, 'view'])->name('students.view');
        Route::get('/students/{user_id}/edit', [TrainingController::class, 'edit'])->name('students.edit');
        Route::put('/students/{user_id}', [TrainingController::class, 'update'])->name('students.update');
        Route::delete('/students/{user_id}', [TrainingController::class, 'destroy'])->name('students.destroy');
    
        // School Management Routes
        Route::get('/manage-students', [SchoolController::class, 'index'])->name('manage-students');
        Route::get('/schools/create', [SchoolController::class, 'create'])->name('schools.create');
        Route::post('/schools', [SchoolController::class, 'store'])->name('schools.store');
        Route::get('/schools/{school}', [SchoolController::class, 'show'])->name('schools.show');
        Route::get('/schools/{school}/edit', [SchoolController::class, 'edit'])->name('schools.edit');
        Route::put('/schools/{school}', [SchoolController::class, 'update'])->name('schools.update');
        Route::delete('/schools/{school}', [SchoolController::class, 'destroy'])->name('schools.destroy');
    
        // Class routes with school context
        Route::get('schools/{school}/classes/create', [ClassController::class, 'create'])->name('classes.create');
        Route::post('schools/{school}/classes', [ClassController::class, 'store'])->name('classes.store');
        Route::get('students/by-batch', [ClassController::class, 'getStudentsList'])->name('students.by-batch');
        Route::resource('classes', ClassController::class)->except(['create', 'store']);


        //Grade submission routes
        Route::get('/grade-submissions', [GradeSubmissionController::class, 'index'])->name('grade-submissions.index');
        Route::get('/grade-submissions/create', [GradeSubmissionController::class, 'create'])->name('grade-submissions.create');
        Route::post('/grade-submissions', [GradeSubmissionController::class, 'store'])->name('grade-submissions.store');
        Route::get('/training/subjects/by-school-and-class', [GradeSubmissionController::class, 'getSubjectsBySchoolAndClass']);
    }); // <-- ✅ properly closed here
    
    



    // Student routes
    Route::prefix('student')->name('student.')->middleware('can:student-access')->group(function () {
        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');


        Route::get('/grade-submissions/{id}', [StudentGradeSubmissionController::class, 'show'])->name('grade-submissions.show');
        Route::post('/grade-submissions/{id}', [StudentGradeSubmissionController::class, 'store'])->name('grade-submissions.store');
    });


});
