<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();
        return 'Database connection is working!';
    } catch (\Exception $e) {
        return 'Could not connect to the database. Please check your configuration. Error: ' . $e->getMessage();
    }
});

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
        
        // Тест сохранения
        $filename = 'test_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('artworks', $filename, 'public');
        
        \Log::info('Файл сохранен', ['path' => $path]);
        
        // Проверяем, что файл действительно сохранился
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
