<?php

use Illuminate\Support\Facades\Route;

// require __DIR__.'/api/userAPI.php';
// require __DIR__.'/api/othersAPI.php';

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AdminController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Route::apiResource('tasks', TaskController::class);

    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::Put('/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
    Route::patch('/tasks/reorder', [TaskController::class, 'reorder']);

    Route::get('/admin/user-statistics', [AdminController::class, 'userStatistics']);
    Route::get('/admin/users/{user}/tasks', [AdminController::class, 'userTasks']);


});
