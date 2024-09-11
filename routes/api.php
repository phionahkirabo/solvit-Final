<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authApiController;
use App\Http\Controllers\reset_password\forgetpasswordcontroller;
use App\Http\Controllers\reset_password\resetcontroller;

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

Route::middleware('auth:jwt')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [authApiController::class, 'register']);
Route::post('/login', [authApiController::class, 'login']);
Route::post('/logoutuser', [authApiController::class, 'logout']);
Route::post('/forgotpassword', [forgetpasswordcontroller::class, 'forgetpassword']);
Route::post('/resetpswd', [resetcontroller::class, 'resetpassword']);
