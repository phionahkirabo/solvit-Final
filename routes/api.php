<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authApiController;
use App\Http\Controllers\codecheckcontroller;
use App\Http\Controllers\ProjectCommentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TeamController;


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
    Route::put('/employee/update/{id}', [authApiController::class, 'updateEmployee']);
    Route::get('/employee/show/{id}', [authApiController::class, 'showEmployee']);
    Route::delete('/employee/delete/{id}', [authApiController::class, 'deleteEmployee']);
            //    Routes about project
    Route::get('/allprojects', [ProjectController::class, 'hodsProjectIndex']);
    Route::post('/projects', [ProjectController::class, 'store']);         // Create a new project (HOD only)
    Route::post('/projectsUpdate/{project_id}', [ProjectController::class, 'update']); // Update a project (HOD only)
    Route::delete('/projectsDelete/{project_id}', [ProjectController::class, 'destroy']); // Delete a project (HOD only)
    // route about tasks 
    Route::post('/tasks', [TaskController::class, 'store']); // Create task
    Route::get('/alltasks', [TaskController::class, 'index']); // List all tasks
    Route::get('/tasks/{task_id}', [TaskController::class, 'show']); // Show a task
    Route::put('/tasks/{task_id}', [TaskController::class, 'update']); // Update a task
    Route::delete('/tasks/{task_id}', [TaskController::class, 'destroy']);
    // delete of comment
    Route::delete('comments/{comment_id}', [TaskCommentController::class, 'destroy']);
    // delete on report
    Route::delete('reports/{id}', [ReportController::class, 'destroy']); // Delete a specific report
    //    these routes that are below  are for  the teams
    Route::get('/teamsindex', [TeamController::class, 'index']); // List all teams
    Route::get('/teams/{team_id}', [TeamController::class, 'show']); // Show single team
    Route::post('/teams', [TeamController::class, 'store']); // Create team
    Route::put('/teamsUpdate/{team_id}', [TeamController::class, 'update']); // Update team
    Route::delete('/teams/{team_id}', [TeamController::class, 'destroy']); // Delete team
});
 
// Employee-specific routes
Route::prefix('employees')->middleware('employee')->group(function () {
    // employee resseting new password
    Route::post('/reset-password', [authApiController::class, 'resetPassword']);
    // Routes for Employee actions on projects and comments (protected by 'employee' middleware)
    // Route::get('/projects', [ProjectController::class, 'employeesindex']);          // List all projects (Employee)
    // Route::get('/projects/{project_id}', [ProjectController::class, 'show']);  // Show a specific project (Employee)
    // Route::put('/projects/{project_id}/status', [ProjectController::class, 'updateStatus']); // Employee updates project status
    
   

   
});
   //count on status of all project by each project
Route::get('/employees/projects/status/count', [ProjectController::class, 'countProjectsByStatus']);

//  Route::post('/employeelogin', [authApiController::class, 'employeeLogin']);
 Route::post('/employees-verify-default-password', [authApiController::class, 'verifyDefaultPassword']);
 Route::get('/allhods', [authApiController::class, 'allhods']);
 Route::get('/allemployee', [authApiController::class, 'allemployees']);

 Route::post('/employee-reset-password/{default_password}', [authApiController::class, 'employeeResetPassword']);

// Authentication and other routes
Route::post('/register', [authApiController::class, 'register']);
Route::post('/login', [authApiController::class, 'login'])->name('login-user');

Route::post('/logout', [authApiController::class, 'logout']);

Route::post('forgot-password', [authApiController::class, 'forgotPassword']);
Route::post('verify-code', [authApiController::class, 'verifyCode']);
Route::post('reset-password/{code}', [authApiController::class, 'resetPassword']);
 // Project Comments for Employees
// Route::post('/projects/{project_id}/comments', [ProjectCommentController::class, 'store']); // Employee adds comment to project
// Route::get('/projects/{project_id}/comments', [ProjectCommentController::class, 'index']);  // List comments for a project



// Apply middleware for HOD and employee authentication
Route::middleware(['auth.hod-or-employee'])->group(function () {
    Route::get('tasks/{task_id}/comments/index', [TaskCommentController::class, 'index']);
    Route::post('tasks/{task_id}/comments', [TaskCommentController::class, 'store']);
    Route::get('comments/{comment_id}', [TaskCommentController::class, 'show']);
    Route::put('comments/{comment_id}', [TaskCommentController::class, 'update']);
    

    Route::get('reportsindex', [ReportController::class, 'index']); // Get all reports
    Route::post('reports', [ReportController::class, 'store']); // Create a new report
    Route::get('reports/{id}', [ReportController::class, 'show']); // Get a specific report
    Route::put('reports/{id}', [ReportController::class, 'update']); // Update a specific report
    

});



Route::get('/test-db', function() {
    try {
        DB::connection()->getPdo();
        return 'Database connection is successful!';
    } catch (\Exception $e) {
        return 'Could not connect to the database: ' . $e->getMessage();
    }
});

