<?php

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


       

        // Dashboard Route (Example)
        Route::get('/admin-dashboard', function () {
            return view('admin-dashboard');
        })->middleware('auth')->name('admin.pnph_users.admin-dashboard');

        

        Route::prefix('admin')->name('admin.')->group(function () {
            Route::resource('pnph_users', PNUserController::class);
        });


 
        });


        Route::get('/training', function () {
            return view('training.dashboard');
        });