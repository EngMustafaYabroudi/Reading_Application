<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ReadingGroupController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\UserController;

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

// Api Sanctum Authentication 

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', [RegisterController::class, 'register']); 
    Route::group(['middleware' => 'auth:sanctum'], function() {
      Route::get('logout', [LogoutController::class, 'logout']);    
    });
});

// 
Route::group(['middleware' => 'auth:sanctum'], function() {
  Route::apiResource('user', UserController::class);
  Route::apiResource('role', RoleController::class);
  Route::apiResource('permission', PermissionController::class);
  Route::post('author/{id}',[ AuthorController::class,'update']);
  Route::apiResource('author', AuthorController::class)->except('update');
  Route::apiResource('book', BookController::class);
  Route::apiResource('readingGroup', ReadingGroupController::class);
  Route::apiResource('review', ReviewController::class);
});


  
//// Content-Type: application/json