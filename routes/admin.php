<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ArtworkController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard.alt');
    
    Route::resource('artworks', ArtworkController::class);
    Route::post('artworks/{artwork}/toggle-published', [ArtworkController::class, 'togglePublished'])->name('artworks.toggle-published');
    
    Route::post('artworks/{artwork}/images', [ArtworkController::class, 'uploadImages'])->name('artworks.images.upload');
    
    Route::delete('images/{id}', [ArtworkController::class, 'deleteImageById'])
        ->whereNumber('id')
        ->name('images.delete');
    
    Route::post('images/{id}/delete', [ArtworkController::class, 'deleteImageById'])
        ->whereNumber('id')
        ->name('images.delete.post');
    
    Route::delete('images/{image}', [ArtworkController::class, 'deleteImage']);
    Route::post('artworks/{artwork}/images/order', [ArtworkController::class, 'updateImagesOrder'])->name('artworks.images.order');
    Route::post('artworks/{artwork}/images/primary', [ArtworkController::class, 'setPrimaryImage'])->name('artworks.images.primary');
    
    Route::resource('users', UserController::class);
    Route::post('users/{user}/toggle-role', [UserController::class, 'toggleRole'])->name('users.toggle-role');
    
    Route::get('analytics', [AdminController::class, 'analytics'])->name('analytics');
});
