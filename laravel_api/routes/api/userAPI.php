<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
 use App\Http\Controllers\AuthController;


Route::get('/users', [UserController::class, 'index']);


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->get('me', [AuthController::class, 'me']);
