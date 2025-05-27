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
use App\Http\Controllers\Training\GradeSubmissionController;
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

  Route::prefix('admin')->name('admin.')->middleware('can:admin-access')->group(function () {
        Route::resource('pnph_users', PNUserController::class);
        Route::get('/dashboard', [PNUserController::class, 'dashboard'])->name('dashboard');
    });
    


Route::middleware('auth')->group(function () {


    
    // Change Password Routes
    Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('change-password');
    Route::post('/update-password', [AuthController::class, 'updatePassword'])->name('update-password');

    // Logout Route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Admin routes
  
    // Educator routes
    Route::prefix('educator')->name('educator.')->middleware('can:educator-access')->group(function () {
        Route::get('/dashboard', [EducatorController::class, 'dashboard'])->name('dashboard');

    Route::get('/students-info', [EducatorController::class, 'index'])->name('educator.students.index');
    Route::get('/students/{user_id}/view', [EducatorController::class, 'viewStudent'])->name('students.view');
    Route::get('/students-info', [EducatorController::class, 'index'])->name('students.index');
        


});
 


    
    
    // Training routes

    Route::prefix('training')->name('training.')->middleware(['auth', 'can:training-access'])->group(function () {
        // Analytics routes
     


        
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
        Route::get('/api/students', [SchoolController::class, 'getStudentsList'])->name('api.students.list');
    
        // Class routes with school context
        Route::get('schools/{school}/classes/create', [ClassController::class, 'create'])->name('classes.create');
        Route::post('schools/{school}/classes', [ClassController::class, 'store'])->name('classes.store');
        Route::delete('/classes/{class}', [ClassController::class, 'destroy'])->name('classes.destroy');
        Route::get('students/by-batch', [ClassController::class, 'getStudentsList'])->name('students.by-batch');
        Route::get('api/schools/{school}/classes', [ClassController::class, 'getClassesBySchool'])->name('api.classes.by-school');
        Route::resource('classes', ClassController::class)->except(['create', 'store']);


        //Grade submission routes
        Route::controller(GradeSubmissionController::class)->group(function () {
            Route::get('/grade-submissions', 'index')->name('grade-submissions.index');
            Route::get('/grade-submissions/create', 'create')->name('grade-submissions.create');
            Route::post('/grade-submissions', 'store')->name('grade-submissions.store');
            Route::get('/grade-submissions/recent', 'recent')->name('grade-submissions.recent');
            Route::get('/grade-submissions/monitor', 'monitor')->name('grade-submissions.monitor');
            Route::get('/subjects/by-school-and-class', 'getSubjectsBySchoolAndClass')->name('subjects.by-school-class');
            Route::get('/grade-submissions/{gradeSubmission}', 'show')->name('grade-submissions.show');
            Route::delete('/grade-submissions/{gradeSubmission}', 'destroy')->name('grade-submissions.destroy');
            Route::get('/grade-submissions/{gradeSubmission}/students/{student}', 'viewStudentSubmission')->name('grade-submissions.view');
            Route::post('/grade-submissions/{gradeSubmission}/update-status', 'updateStatus')->name('grade-submissions.update-status');
            Route::put('/grade-submissions/{gradeSubmission}/verify', 'verify')->name('grade-submissions.verify');
            Route::put('/grade-submissions/{gradeSubmission}/reject', 'reject')->name('grade-submissions.reject');
            Route::get('/grade-submissions/{gradeSubmission}/proof/{student}', 'viewProof')->name('grade-submissions.view-proof');
            Route::post('/grade-submissions/{gradeSubmission}/proof/{student}/status', 'updateProofStatus')->name('grade-submissions.update-proof-status');

            // Temporary route to fix subject associations for a submission
            Route::get('/grade-submissions/{gradeSubmission}/fix-subjects', 'fixSubmissionSubjects')->name('grade-submissions.fix-subjects');
        });
    }); // <-- ✅ properly closed here
    
    



    // Student routes
    Route::prefix('student')->name('student.')->middleware('can:student-access')->group(function () {
        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
        Route::get('/grade-submissions/{submissionId}', [StudentController::class, 'showSubmissionForm'])->name('submit-grades.show');
        Route::post('/grade-submissions/{submissionId}', [StudentController::class, 'submitGrades'])->name('submit-grades.store');
        Route::get('/view-submission/{submissionId}', [StudentController::class, 'viewSubmission'])->name('view-submission');
        Route::get('/grade-submissions', [StudentController::class, 'submissionsList'])->name('student.grade-submissions');
    });


});
