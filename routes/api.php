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
    Route::post('/employee/create', [authApiController::class, 'addEmployee'])->name('employee.verify.default.password');;
    // Add other HOD-specific routes here
});

// Employee-specific routes
Route::prefix('employees')->middleware('employee')->group(function () {
    
    Route::post('/reset-password', [authApiController::class, 'resetPassword']);
   
});
//  Route::post('/employeelogin', [authApiController::class, 'employeeLogin']);
 Route::post('/employees-verify-default-password', [authApiController::class, 'verifyDefaultPassword']);
 Route::get('/allhods', [authApiController::class, 'allhods']);

 Route::post('/employee-reset-password/{default_password}', [authApiController::class, 'employeeResetPassword']);

// Authentication and other routes
Route::post('/register', [authApiController::class, 'register']);
Route::post('/login', [authApiController::class, 'login'])->name('login-user');

Route::post('/logoutuser', [authApiController::class, 'logout']);

Route::post('forgot-password', [authApiController::class, 'forgotPassword']);
Route::post('verify-code', [authApiController::class, 'verifyCode']);
Route::post('reset-password/{code}', [authApiController::class, 'resetPassword']);




Route::get('/test-db', function() {
    try {
        DB::connection()->getPdo();
        return 'Database connection is successful!';
    } catch (\Exception $e) {
        return 'Could not connect to the database: ' . $e->getMessage();
    }
});