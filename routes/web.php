<?php

	use App\Http\Controllers\Auth\AuthController;
	use App\Http\Controllers\Auth\ForgotPasswordController;
	use App\Http\Controllers\Auth\ResetPasswordController;
	use App\Http\Controllers\Auth\VerifyEmailController;
	use App\Http\Controllers\GalleryController;
	use App\Http\Controllers\Admin\AdminController;
	use Illuminate\Support\Facades\Route;

	
	Route::get('/', [GalleryController::class, 'index'])->name('home');

	
	Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
	Route::get('/gallery/all', [GalleryController::class, 'all'])->name('gallery.all');
	Route::get('/gallery/category/{category:slug}', [GalleryController::class, 'category'])->name('gallery.category');
	Route::get('/gallery/{artwork:slug}', [GalleryController::class, 'show'])->name('gallery.show');

	
	Route::get('/login', [AuthController::class, 'showLoginForm'])->middleware('guest')->name('login');
	Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
	Route::get('/register', [AuthController::class, 'showRegistrationForm'])->middleware('guest')->name('register');
	Route::post('/register', [AuthController::class, 'register'])->middleware('guest');

	Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->middleware('guest')->name('password.request');
	Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware('guest')->name('password.email');
	Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->middleware('guest')->name('password.reset');
	Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->middleware('guest')->name('password.update');

	
	Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

	Route::get('/email/verify', fn() => view('auth.verify-email'))->middleware(['auth','throttle:6,1'])->name('verification.notice');
	Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])->middleware(['auth','signed','throttle:6,1'])->name('verification.verify');
	Route::post('/email/verification-notification', [VerifyEmailController::class, 'resend'])->middleware(['auth','throttle:6,1'])->name('verification.send');

	
	Route::put('/profile', [AuthController::class, 'updateProfile'])->middleware(['auth','verified'])->name('profile.update');
	Route::put('/password', [AuthController::class, 'updatePassword'])->middleware(['auth','verified'])->name('password.update');
	Route::get('/profile', [AuthController::class, 'profile'])->middleware(['auth','verified'])->name('profile');

	
	Route::get('/admin', [AdminController::class, 'index'])->middleware(['auth','verified','admin'])->name('/admin');

	
	require __DIR__.'/admin.php';
