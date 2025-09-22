<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\ArtworkController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\LikeController;




Route::prefix('auth')->group(function () {
    
    Route::post('/register', [AuthController::class, 'register']);
    
    
    Route::post('/login', [AuthController::class, 'login']);
    
    
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    
    
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});


Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    
    
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    
    
    Route::put('/profile/password', [AuthController::class, 'updatePassword']);
    
    
    Route::apiResource('artworks', ArtworkController::class);
    
    
    Route::get('categories/{category}/artworks', [ArtworkController::class, 'getByCategory']);
    Route::get('artworks/featured', [ArtworkController::class, 'featured']);
    
    
    Route::post('artworks/{artwork}/like', [LikeController::class, 'like']);
    Route::delete('artworks/{artwork}/like', [LikeController::class, 'unlike']);
    Route::get('artworks/{artwork}/check-like', [LikeController::class, 'check']);
    
    
    Route::middleware(['role:admin'])->group(function () {
        Route::apiResource('categories', CategoryController::class);
    });
    
    
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{category}', [CategoryController::class, 'show']);
    
    
    Route::apiResource('comments', CommentController::class)->except(['index', 'show']);
    Route::get('artworks/{artwork}/comments', [CommentController::class, 'index']);
    Route::get('comments/{comment}/replies', [CommentController::class, 'replies']);
    Route::post('comments/{comment}/reply', [CommentController::class, 'store']);
});


Route::get('public/artworks', [ArtworkController::class, 'index']);
Route::get('public/artworks/{artwork}', [ArtworkController::class, 'show']);
Route::get('public/artworks/category/{category}', [ArtworkController::class, 'getByCategory']);
Route::get('public/artworks/featured', [ArtworkController::class, 'featured']);
Route::get('public/categories', [CategoryController::class, 'index']);
Route::get('public/categories/{category}', [CategoryController::class, 'show']);


Route::get('public/artworks/{artwork}/comments', [CommentController::class, 'index']);
Route::get('public/comments/{comment}/replies', [CommentController::class, 'replies']);


Route::get('public/artworks/{artwork}/likes/count', [\App\Http\Controllers\Api\LikeController::class, 'getLikesCount']);
Route::get('public/artworks/{artwork}/likers', [\App\Http\Controllers\Api\LikeController::class, 'likers']);
