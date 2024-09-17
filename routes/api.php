<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authApiController;
use App\Http\Controllers\reset_password\forgetpasswordcontroller;
use App\Http\Controllers\reset_password\resetcontroller;
use App\Http\Controllers\codecheckcontroller;
use App\Http\Controllers\HODController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route for getting the authenticated user with 'auth:jwt' middleware
Route::middleware('auth:jwt')->get('/user', function (Request $request) {
    return $request->user();
});

// Group routes for HODs with 'hod' middleware
Route::group([
    'prefix' => 'hods',
    'middleware' => 'hod',
], function () {
    Route::post('/employee/create', [HODController::class, 'addEmployee']);
    // Add other HOD-specific routes here
});

// Group routes for Employees with 'employees' middleware
Route::group([
    'prefix' => 'employees',
    'middleware' => 'employees',
], function () {
    // Add employee-specific routes here
});

// HOD authentication routes
Route::post('/register', [authApiController::class, 'register']);
Route::post('/login', [authApiController::class, 'login']);
Route::post('/code', [codecheckcontroller::class, 'codechecker']);
Route::post('/logoutuser', [authApiController::class, 'logout']);
Route::post('/forgotpassword', [forgetpasswordcontroller::class, 'forgetpassword']);
Route::post('/resetpsword', [resetcontroller::class, 'resetpassword']);

// Employee API routes
Route::post('/employee/verify/{id}', [EmployeeController::class, 'verifyDefaultPassword']);
Route::post('/employee/login', [AuthController::class, 'employeeLogin']);
