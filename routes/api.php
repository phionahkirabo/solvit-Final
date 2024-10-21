<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authApiController;
use App\Http\Controllers\codecheckcontroller;
use App\Http\Controllers\ProjectCommentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;

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

    Route::get('/allprojects', [ProjectController::class, 'hodsindex']);
    Route::post('/projects', [ProjectController::class, 'store']);         // Create a new project (HOD only)
    Route::post('/projectsUpdate/{project_id}', [ProjectController::class, 'update']); // Update a project (HOD only)
    Route::delete('/projectsDelete/{project_id}', [ProjectController::class, 'destroy']); // Delete a project (HOD only)
    // route about tasks 
    Route::post('/tasks', [TaskController::class, 'store']); // Create task
    Route::get('/alltasks', [TaskController::class, 'index']); // List all tasks
    Route::get('/tasks/{task_id}', [TaskController::class, 'show']); // Show a task
    Route::put('/tasks/{task_id}', [TaskController::class, 'update']); // Update a task
    Route::delete('/tasks/{task_id}', [TaskController::class, 'destroy']);
});
 
// Employee-specific routes
Route::prefix('employees')->middleware('employee')->group(function () {
    // employee resseting new password
    Route::post('/reset-password', [authApiController::class, 'resetPassword']);
    // Routes for Employee actions on projects and comments (protected by 'employee' middleware)
    Route::get('/projects', [ProjectController::class, 'employeesindex']);          // List all projects (Employee)
    Route::get('/projects/{project_id}', [ProjectController::class, 'show']);  // Show a specific project (Employee)
    Route::put('/projects/{project_id}/status', [ProjectController::class, 'updateStatus']); // Employee updates project status
     Route::get('/projects/{project_id}/comments', [ProjectCommentController::class, 'index']);  // List comments
    Route::post('/projects/{project_id}/addComments', [ProjectCommentController::class, 'store']); // Add a comment
    Route::get('/projects/{project_id}/comments/{comment_id}', [ProjectCommentController::class, 'show']);  // Show a comment
    Route::put('/projects/{project_id}/comments/{comment_id}', [ProjectCommentController::class, 'update']); // Update a comment
    Route::delete('/projects/{project_id}/comments/{comment_id}', [ProjectCommentController::class, 'destroy']); // Delete a comment

   
});
   //count on status of all project by each project
Route::get('/employees/projects/status/count', [ProjectController::class, 'countProjectsByStatus']);

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

Route::middleware(['auth:hod,employee'])->group(function () {
    Route::prefix('tasks/{task_id}')->group(function () {
    Route::post('/comments', [ProjectCommentController::class, 'store']);  // Add a task comment
    Route::get('/comments', [ProjectCommentController::class, 'index']);   // List comments for a task
    Route::get('/comments/{comment_id}', [ProjectCommentController::class, 'show']); // Show a specific comment
    Route::put('/comments/{comment_id}', [ProjectCommentController::class, 'update']); // Update a task comment
    Route::delete('/comments/{comment_id}', [ProjectCommentController::class, 'destroy']); // Delete a task comment
    });
});

Route::get('/test-db', function() {
    try {
        DB::connection()->getPdo();
        return 'Database connection is successful!';
    } catch (\Exception $e) {
        return 'Could not connect to the database: ' . $e->getMessage();
    }
});

