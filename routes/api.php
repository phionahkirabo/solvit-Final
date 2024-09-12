<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authApiController;
use App\Http\Controllers\reset_password\forgetpasswordcontroller;
use App\Http\Controllers\reset_password\resetcontroller;
use App\Http\Controllers\codecheckcontroller;

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
Route::group([
    'prefix'=>'hods',
    'middleware'=>'hod',
    function(){

    }
]
);
Route::group([
    'prefix'=>'hods',
    'middleware'=>'hod',
    function(){

    }
]
);

Route::post('/register', [authApiController::class, 'register']);
Route::post('/login', [authApiController::class, 'login']);
Route::post('/code', [codecheckcontroller::class, 'codechecker']);
Route::post('/logoutuser', [authApiController::class, 'logout']);
Route::post('/forgotpassword', [forgetpasswordcontroller::class, 'forgetpassword']);
Route::post('/resetpsword', [resetcontroller::class, 'resetpassword']);
