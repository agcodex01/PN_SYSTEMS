<?php

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PNUserController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\AuthController;

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
        Route::get('/dashboard', function () {
            return view('admin.dashboard', ['title' => 'Admin Dashboard']);
        })->name('dashboard');
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
    });
    
    // Student routes
    Route::prefix('student')->name('student.')->middleware('can:student-access')->group(function () {
        Route::get('/dashboard', function () {
            return view('student.dashboard', ['title' => 'Student Dashboard']);
        })->name('dashboard');
    });
});
