<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authApiController;
use App\Http\Controllers\codecheckcontroller;
use App\Http\Controllers\reset_password\forgetpasswordcontroller;
use App\Http\Controllers\reset_password\resetcontroller;

// General API routes
Route::middleware('auth:jwt')->get('/user', function (Request $request) {
    return $request->user();
});

// HOD-specific routes
Route::prefix('hods')->middleware('hod')->group(function () {
    Route::post('/employee/create', [authApiController::class, 'addEmployee']);
    // Add other HOD-specific routes here
});

// Employee-specific routes
Route::prefix('employees')->middleware('employees')->group(function () {
    Route::post('/verify-default-password', [authApiController::class, 'verifyDefaultPassword']);
    Route::post('/reset-password', [authApiController::class, 'resetPassword']);
    Route::post('/employeelogin', [authApiController::class, 'employeeLogin']);
});

// Authentication and other routes
Route::post('/register', [authApiController::class, 'register']);
Route::post('/login', [authApiController::class, 'login'])->name('hod.login');
Route::post('/code', [codecheckcontroller::class, 'codechecker']);
Route::post('/logoutuser', [authApiController::class, 'logout']);
Route::post('/forgotpassword', [forgetpasswordcontroller::class, 'forgetpassword']);
Route::post('/resetpsword', [resetcontroller::class, 'resetpassword']);
