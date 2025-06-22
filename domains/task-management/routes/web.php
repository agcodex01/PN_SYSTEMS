<?php

use Illuminate\Support\Facades\Route;
use Pnph\TaskManagement\Http\Controllers\TaskController;

Route::group(['prefix' => 'task-management'], function () {
    Route::resource('tasks', TaskController::class);
});
