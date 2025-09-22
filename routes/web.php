<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\GalleryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

Route::get('/test', function () {
    return 'Laravel работает!';
});

Route::get('/', [GalleryController::class, 'index'])->name('home');

Route::prefix('gallery')->name('gallery.')->group(function () {
    Route::get('/', [GalleryController::class, 'index'])->name('index');
    
    Route::get('/all', [GalleryController::class, 'all'])->name('all');
    
    Route::get('/category/{category:slug}', [GalleryController::class, 'category'])->name('category');
    
    Route::get('/{artwork:slug}', [GalleryController::class, 'show'])->name('show');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);

    Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);

    Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');
    Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->middleware(['throttle:6,1'])->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', [VerifyEmailController::class, 'resend'])
        ->middleware(['throttle:6,1'])
        ->name('verification.send');
});

Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [AuthController::class, 'updatePassword'])->name('password.update');
});

Route::any('/test-emergency', function() {
    return response()->json([
        'status' => 'EMERGENCY_SUCCESS',
        'message' => 'Laravel работает! Проблема НЕ в Laravel!',
        'timestamp' => date('Y-m-d H:i:s'),
        'method' => request()->method()
    ]);
});

Route::post('/admin/artworks', function(Request $request) {
    error_log('🚀 SIMPLE TEST ROUTE CALLED!');
    file_put_contents('/tmp/artwork_debug.log', date('Y-m-d H:i:s') . " - 🚀 SIMPLE TEST ROUTE CALLED!\n", FILE_APPEND);
    
    file_put_contents('/tmp/artwork_debug.log', "📝 Request data: " . json_encode($request->all()) . "\n", FILE_APPEND);
    file_put_contents('/tmp/artwork_debug.log', "📁 Has files: " . ($request->hasFile('images') ? 'YES' : 'NO') . "\n", FILE_APPEND);
    
    if ($request->hasFile('images')) {
        $files = $request->file('images');
        file_put_contents('/tmp/artwork_debug.log', "📸 Files count: " . count($files) . "\n", FILE_APPEND);
    }
    
    return response()->json([
        'status' => 'success',
        'message' => 'Запрос дошел до сервера!',
        'data' => $request->except(['images']),
        'has_files' => $request->hasFile('images'),
        'files_count' => $request->hasFile('images') ? count($request->file('images')) : 0
    ]);
});

require __DIR__.'/admin.php';

Route::get('/test-image-upload', function () {
    return view('test-image-upload');
});

Route::post('/test-image-upload', function (Request $request) {
    try {
        \Log::info('Тест загрузки изображения начат');
        
        if (!$request->hasFile('test_image')) {
            return response()->json(['error' => 'Файл не загружен'], 400);
        }
        
        $file = $request->file('test_image');
        \Log::info('Файл получен', [
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime' => $file->getMimeType(),
            'valid' => $file->isValid()
        ]);
        
        if (!$file->isValid()) {
            return response()->json(['error' => 'Файл невалиден: ' . $file->getError()], 400);
        }
        
        $filename = 'test_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('artworks', $filename, 'public');
        
        \Log::info('Файл сохранен', ['path' => $path]);
        
        $fullPath = storage_path('app/public/' . $path);
        $exists = file_exists($fullPath);
        
        return response()->json([
            'success' => true,
            'path' => $path,
            'full_path' => $fullPath,
            'exists' => $exists,
            'size_on_disk' => $exists ? filesize($fullPath) : 0
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Ошибка при тестовой загрузке', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
