<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authApiController;
use App\Http\Controllers\codecheckcontroller;
use App\Http\Controllers\ProjectCommentController;
use App\Http\Controllers\ProjectController;

use App\Http\Controllers\reset_password\forgetpasswordcontroller;
use App\Http\Controllers\reset_password\resetcontroller;

// General API routes
Route::middleware('auth:jwt')->get('/user', function (Request $request) {
    return $request->user();
});

// HOD-specific routes
Route::prefix('hods')->middleware('hod')->group(function () {
    // Add other HOD-specific routes on adding new projects
    Route::post('/employee/create', [authApiController::class, 'addEmployee'])->name('employee.verify.default.password');

    // Add other HOD-specific routes on projects
    Route::post('/projects', [ProjectController::class, 'store']);         // Create a new project (HOD only)
    Route::put('/projects/{project_id}', [ProjectController::class, 'update']); // Update a project (HOD only)
    Route::delete('/projects/{project_id}', [ProjectController::class, 'destroy']); // Delete a project (HOD only)
});

// Employee-specific routes
Route::prefix('employees')->middleware('employee')->group(function () {
    // employee resseting new password
    Route::post('/reset-password', [authApiController::class, 'resetPassword']);
    // Routes for Employee actions on projects and comments (protected by 'employee' middleware)
    Route::get('/projects', [ProjectController::class, 'index']);          // List all projects (Employee)
    Route::get('/projects/{project_id}', [ProjectController::class, 'show']);  // Show a specific project (Employee)
    Route::put('/projects/{project_id}/status', [ProjectController::class, 'updateStatus']); // Employee updates project status

   
});
//  Route::post('/employeelogin', [authApiController::class, 'employeeLogin']);
 Route::post('/employees-verify-default-password', [authApiController::class, 'verifyDefaultPassword']);
 Route::get('/allhods', [authApiController::class, 'allhods']);

 Route::post('/employee-reset-password/{default_password}', [authApiController::class, 'employeeResetPassword']);

// Authentication and other routes
Route::post('/register', [authApiController::class, 'register']);
Route::post('/login', [authApiController::class, 'login'])->name('login-user');

Route::post('/logout', [authApiController::class, 'logout']);

Route::post('forgot-password', [authApiController::class, 'forgotPassword']);
Route::post('verify-code', [authApiController::class, 'verifyCode']);
Route::post('reset-password/{code}', [authApiController::class, 'resetPassword']);
 // Project Comments for Employees
Route::post('/projects/{project_id}/comments', [ProjectCommentController::class, 'store']); // Employee adds comment to project
Route::get('/projects/{project_id}/comments', [ProjectCommentController::class, 'index']);  // List comments for a project


Route::get('/test-db', function() {
    try {
        DB::connection()->getPdo();
        return 'Database connection is successful!';
    } catch (\Exception $e) {
        return 'Could not connect to the database: ' . $e->getMessage();
    }
});

