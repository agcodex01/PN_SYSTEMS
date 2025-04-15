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



Route::middleware('auth')->group(function () {

        // Change Password Routes
        Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->middleware('auth')->name('change-password');
        Route::post('/update-password', [AuthController::class, 'updatePassword'])->middleware('auth')->name('update-password');


        // Logout Route
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // Admin Routes for CRUD
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::resource('pnph_users', PNUserController::class);
        });
        


        

        // Admin routes
        Route::get('/admin-dashboard', function () {
            if (Gate::allows('admin-access')) {
                return view('admin.dashboard', ['title' => 'Admin Dashboard']); // Corrected view path
            }

            return redirect('/login')->withErrors(['error' => 'Unauthorized access']);
        })->name('admin.dashboard');    

        // Educator routes
        Route::get('/educator-dashboard', function () {
            if (Gate::allows('educator-access')) {
                return view('educator.dashboard', ['title' => 'Educator Dashboard']); // Corrected view path
            }

            return redirect('/login')->withErrors(['error' => 'Unauthorized access']);
        })->name('educator.dashboard');

        // Training routes
        Route::get('/training-dashboard', function () {
            if (Gate::allows('training-access')) {
                return view('training.dashboard', ['title' => 'Training Dashboard']); // Corrected view path
            }

            return redirect('/login')->withErrors(['error' => 'Unauthorized access']);
        })->name('training.dashboard');

        // Student routes
        Route::get('/student-dashboard', function () {
            if (Gate::allows('student-access')) {
                return view('student.dashboard', ['title' => 'Student Dashboard']); // Corrected view path
            }

            return redirect('/login')->withErrors(['error' => 'Unauthorized access']);
        })->name('student.dashboard');


            












        Route::get('/training', function () {
            return view('training.dashboard');
        });


 });

 

