<?php

use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// Auth işlemleri
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Authenticated kullanıcı işlemleri
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Kategori CRUD
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);


    // Post CRUD
    Route::get('/author/posts', [PostController::class, 'authorPosts']);
    Route::apiResource('/posts', PostController::class );
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::patch('/posts/{post}/status', [PostController::class, 'toggleStatus']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);

});
