<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\ArtworkController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\LikeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Публичные маршруты аутентификации
Route::prefix('auth')->group(function () {
    // Регистрация
    Route::post('/register', [AuthController::class, 'register']);
    
    // Вход
    Route::post('/login', [AuthController::class, 'login']);
    
    // Запрос на восстановление пароля
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    
    // Сброс пароля
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Защищенные маршруты
Route::middleware('auth:sanctum')->group(function () {
    // Получение текущего пользователя
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Выход
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    
    // Обновление профиля
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    
    // Смена пароля
    Route::put('/profile/password', [AuthController::class, 'updatePassword']);
    
    // Маршруты для управления произведениями
    Route::apiResource('artworks', ArtworkController::class);
    
    // Дополнительные маршруты для произведений
    Route::get('categories/{category}/artworks', [ArtworkController::class, 'getByCategory']);
    Route::get('artworks/featured', [ArtworkController::class, 'featured']);
    
    // Like/Unlike routes
    Route::post('artworks/{artwork}/like', [LikeController::class, 'like']);
    Route::delete('artworks/{artwork}/like', [LikeController::class, 'unlike']);
    Route::get('artworks/{artwork}/check-like', [LikeController::class, 'check']);
    
    // Маршруты для управления категориями (только для администраторов)
    Route::middleware(['role:admin'])->group(function () {
        Route::apiResource('categories', CategoryController::class);
    });
    
    // Публичный доступ к списку категорий
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{category}', [CategoryController::class, 'show']);
    
    // Маршруты для работы с комментариями
    Route::apiResource('comments', CommentController::class)->except(['index', 'show']);
    Route::get('artworks/{artwork}/comments', [CommentController::class, 'index']);
    Route::get('comments/{comment}/replies', [CommentController::class, 'replies']);
    Route::post('comments/{comment}/reply', [CommentController::class, 'store']);
});

// Публичные маршруты для просмотра произведений
Route::get('public/artworks', [ArtworkController::class, 'index']);
Route::get('public/artworks/{artwork}', [ArtworkController::class, 'show']);
Route::get('public/artworks/category/{category}', [ArtworkController::class, 'getByCategory']);
Route::get('public/artworks/featured', [ArtworkController::class, 'featured']);
Route::get('public/categories', [CategoryController::class, 'index']);
Route::get('public/categories/{category}', [CategoryController::class, 'show']);

// Публичные маршруты для комментариев
Route::get('public/artworks/{artwork}/comments', [CommentController::class, 'index']);
Route::get('public/comments/{comment}/replies', [CommentController::class, 'replies']);

// Публичные маршруты для лайков
Route::get('public/artworks/{artwork}/likes/count', [\App\Http\Controllers\Api\LikeController::class, 'getLikesCount']);
Route::get('public/artworks/{artwork}/likers', [\App\Http\Controllers\Api\LikeController::class, 'likers']);
