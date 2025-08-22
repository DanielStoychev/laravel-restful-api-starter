<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;

// Authentication routes with rate limiting
Route::prefix('auth')->middleware('throttle:10,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// User profile route (outside auth prefix)
Route::get('/user', [AuthController::class, 'user'])->middleware(['auth:sanctum', 'throttle:60,1']);

// Protected API routes with rate limiting
Route::middleware(['auth:sanctum', 'throttle:100,1'])->group(function () {
    // Project CRUD
    Route::apiResource('projects', ProjectController::class);
    
    // Task CRUD
    Route::apiResource('tasks', TaskController::class);
    Route::get('/projects/{project}/tasks', [TaskController::class, 'indexByProject']);
});

// Public routes
Route::get('/health', function () {
    return response()->json(['status' => 'OK', 'timestamp' => now()]);
});
