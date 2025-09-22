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
    Route::delete('images/{image}', [ArtworkController::class, 'deleteImage'])->name('images.delete');
    Route::post('artworks/{artwork}/images/order', [ArtworkController::class, 'updateImagesOrder'])->name('artworks.images.order');
    Route::post('artworks/{artwork}/images/primary', [ArtworkController::class, 'setPrimaryImage'])->name('artworks.images.primary');
    
    Route::resource('users', UserController::class);
    Route::post('users/{user}/toggle-role', [UserController::class, 'toggleRole'])->name('users.toggle-role');
    
    Route::get('analytics', [AdminController::class, 'analytics'])->name('analytics');
    
    Route::get('debug/upload', function() {
        return view('admin.debug-upload');
    })->name('debug.upload');
    
    Route::post('debug/artwork-upload', function(\Illuminate\Http\Request $request) {
        \Log::info('=== ДИАГНОСТИКА ЗАГРУЗКИ ИЗОБРАЖЕНИЙ ===');
        \Log::info('Request method: ' . $request->method());
        \Log::info('Request URL: ' . $request->url());
        \Log::info('Request data: ', $request->except(['images']));
        \Log::info('Has files: ' . ($request->hasFile('images') ? 'YES' : 'NO'));
        
        if ($request->hasFile('images')) {
            $images = $request->file('images');
            \Log::info('Images count: ' . count($images));
            
            foreach ($images as $index => $image) {
                \Log::info("Image {$index}: " . $image->getClientOriginalName() . ' (' . $image->getSize() . ' bytes)');
            }
        }
        
        return response()->json([
            'status' => 'debug',
            'has_files' => $request->hasFile('images'),
            'files_count' => $request->hasFile('images') ? count($request->file('images')) : 0,
            'data' => $request->except(['images'])
        ]);
    })->name('debug.artwork-upload');
});
