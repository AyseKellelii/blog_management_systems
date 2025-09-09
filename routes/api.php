<?php

use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AuthController;
use http\Client\Request;
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


    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/author/notifications', [NotificationController::class, 'index']);
    });
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/author/comments', [CommentController::class, 'authorComments']);
    });


    // Post CRUD
    Route::get('/author/posts', [PostController::class, 'authorPosts']);
    Route::apiResource('/posts', PostController::class)->except(['show']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::patch('/posts/{post}/toggle-status', [PostController::class, 'toggleStatus']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);


    Route::middleware('auth:sanctum')->group(function () {
        // Kullanıcı yorumları
        Route::get('/posts/published', [PostController::class, 'publishedPosts']); //yayınlanmış yazılar
        Route::get('/posts/{post}', [CommentController::class, 'singlePostWithComments']);
        Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
        Route::get('/posts/{post}/comments', [CommentController::class, 'index']);
        Route::patch('/comments/{comment}/approve', [CommentController::class, 'approve']);
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

        Route::get('/my-comments', [CommentController::class, 'myComments']);
        Route::put('/comments/{comment}', [CommentController::class, 'update']);
        Route::delete('/comments/{comment}/delete-own', [CommentController::class, 'deleteOwn']);

        // Admin paneli
        // routes/api.php
        Route::get('/my-notifications', function (Request $request) {
            return $request->user()->notifications()->latest()->get();
        });
        Route::get('/admin/posts', [PostController::class, 'adminPosts']);
        Route::get('/admin/comments', [CommentController::class, 'allComments']);
        Route::get('/admin/comments/pending', [CommentController::class, 'pendingComments']);
        Route::put('/admin/comments/{id}/approve', [CommentController::class, 'approve']);
        Route::delete('/admin/comments/{comment}/delete', [CommentController::class, 'adminDelete']);
    });
});
