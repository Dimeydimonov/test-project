<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\ArtworkImage;
use App\Models\Category;
use App\Services\Interfaces\ArtworkServiceInterface;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ArtworkController extends Controller
{
    private ArtworkServiceInterface $artworkService;

    public function __construct(ArtworkServiceInterface $artworkService)
    {
        $this->artworkService = $artworkService;
    }

    public function index(Request $request)
    {
        $query = Artwork::with(['user', 'categories'])
            ->withCount(['likes', 'comments']);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'published') {
                $query->where('is_available', true);
            } elseif ($status === 'draft') {
                $query->where('is_available', false);
            }
        }

        if ($request->filled('category')) {
            $categoryId = $request->get('category');
            $query->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });
        }

        $artworks = $query->latest()->paginate(15);
        $categories = Category::orderBy('name')->get();

        return view('admin.artworks.index', compact('artworks', 'categories'));
    }

    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('admin.artworks.create', compact('categories'));
    }

    public function store(Request $request)
    {
        error_log('ARTWORK CONTROLLER STORE METHOD CALLED!');
        file_put_contents('/tmp/artwork_debug.log', date('Y-m-d H:i:s') . " - Store method called\n", FILE_APPEND);
        
        \Log::info('=== НАЧАЛО ОБРАБОТКИ ЗАПРОСА ОТ ПОЛЬЗОВАТЕЛЯ ===');
        \Log::info('Request method: ' . $request->method());
        \Log::info('Request URL: ' . $request->url());
        \Log::info('Request Content-Type: ' . $request->header('Content-Type'));
        \Log::info('Request has files: ' . ($request->hasFile('images') ? 'YES' : 'NO'));
        \Log::info('All request files: ', array_keys($request->allFiles()));
        \Log::info('Request all data (except images): ', $request->except(['images']));
        
        // Проверяем все возможные способы получения файлов
        if ($request->hasFile('images')) {
            $images = $request->file('images');
            \Log::info('Images received via images field: ' . count($images));
            foreach ($images as $index => $image) {
                \Log::info("Image {$index}: " . $image->getClientOriginalName() . ' (' . $image->getSize() . ' bytes)');
            }
        } else {
            \Log::warning('НЕТ ФАЙЛОВ В ПОЛЕ images!');
            
            $allFiles = $request->allFiles();
            if (!empty($allFiles)) {
                \Log::info('Но есть другие файлы: ', array_keys($allFiles));
                foreach ($allFiles as $fieldName => $files) {
                    \Log::info("Поле {$fieldName} содержит файлы: " . (is_array($files) ? count($files) : 1));
                }
            } else {
                \Log::error('ВООБЩЕ НЕТ ФАЙЛОВ В ЗАПРОСЕ! Форма не отправляет файлы!');
            }
        }
        
        \Log::info('Request size: ' . strlen($request->getContent()) . ' bytes');
        
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                if ($file->getError() !== UPLOAD_ERR_OK) {
                    \Log::error("Upload error for image {$index}: " . $file->getErrorMessage());
                }
            }
        }

        $validator = $this->validateArtworkData($request);

        if ($validator->fails()) {
            \Log::warning('Валидация не прошла', [
                'errors' => $validator->errors()->toArray(),
                'input' => $request->except(['images']) // Исключаем изображения из лога
            ]);
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        \Log::info('Валидация прошла успешно');

        try {
            $data = $request->all();
            $data['user_id'] = Auth::id();
            $data['is_available'] = $request->boolean('is_published');
            if (!isset($data['image_path'])) {
                $data['image_path'] = '';
            }

            \Log::info('Создаем произведение', [
                'title' => $data['title'] ?? 'НЕ ЗАДАНО',
                'user_id' => $data['user_id'],
                'has_images' => $request->hasFile('images')
            ]);

            $artwork = $this->artworkService->createArtwork($data, null);

            \Log::info('Произведение создано', ['artwork_id' => $artwork->id]);

            if ($request->hasFile('images')) {
                $images = $request->file('images');
                $this->processUploadedImages($artwork, $images);

                $firstImage = $artwork->images()->orderBy('order_column')->first();
                if ($firstImage) {
                    $artwork->update(['image_path' => $firstImage->file_path]);
                    \Log::info('Установлено главное изображение', ['image_path' => $firstImage->file_path]);
                } else {
                    \Log::warning('Изображения были загружены, но не найдено ни одного в БД');
                }
            } else {
                \Log::info('Изображения не были загружены');
            }

            $successMessage = 'Произведение успешно создано!';
            if ($request->hasFile('images')) {
                $imagesCount = $artwork->images()->count();
                $successMessage .= " Загружено изображений: {$imagesCount}";
            }

            return redirect()->route('admin.artworks.index')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            \Log::error('Ошибка при создании произведения: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return redirect()->back()
                ->with('error', 'Ошибка при создании произведения: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Artwork $artwork)
    {
        $artwork->load(['user', 'categories', 'comments.user', 'likes.user']);
        return view('admin.artworks.show', compact('artwork'));
    }

    public function edit(Artwork $artwork)
    {
        $categories = Category::active()->orderBy('name')->get();
        $selectedCategories = $artwork->categories->pluck('id')->toArray();
        
        return view('admin.artworks.edit', compact('artwork', 'categories', 'selectedCategories'));
    }


    public function update(Request $request, Artwork $artwork)
    {
        $validator = $this->validateArtworkData($request, $artwork->id);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->all();
            $data['is_available'] = $request->boolean('is_published');

            $image = $request->hasFile('image') ? $request->file('image') : null;
            
            $this->artworkService->updateArtwork($artwork, $data, $image);

            return redirect()->route('admin.artworks.index')
                ->with('success', 'Произведение успешно обновлено!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Ошибка при обновлении произведения: ' . $e->getMessage())
                ->withInput();
        }
    }


    public function destroy(Artwork $artwork)
    {
        try {
            $this->artworkService->deleteArtwork($artwork);
            
            return redirect()->route('admin.artworks.index')
                ->with('success', 'Произведение успешно удалено!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Ошибка при удалении произведения: ' . $e->getMessage());
        }
    }


    public function togglePublished(Artwork $artwork)
    {
        try {
            $artwork->update(['is_available' => !$artwork->is_available]);
            
            $status = $artwork->is_available ? 'опубликовано' : 'снято с публикации';
            
            return response()->json([
                'success' => true,
                'message' => "Произведение успешно {$status}!",
                'is_published' => $artwork->is_available
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при изменении статуса: ' . $e->getMessage()
            ], 500);
        }
    }

    private function validateArtworkData(Request $request, ?int $artworkId = null): \Illuminate\Validation\Validator
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'size' => 'nullable|string|max:100',
            'width' => 'nullable|numeric|min:0|max:9999.99',
            'height' => 'nullable|numeric|min:0|max:9999.99',
            'materials' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0|max:999999.99',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:102400',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'is_published' => 'boolean'
        ];

        $messages = [
            'title.required' => 'Название произведения обязательно для заполнения.',
            'title.max' => 'Название произведения не должно превышать 255 символов.',
            'description.max' => 'Описание не должно превышать 5000 символов.',
            'year.integer' => 'Год должен быть числом.',
            'year.min' => 'Год не может быть меньше 1000.',
            'year.max' => 'Год не может быть больше текущего года.',
            'width.numeric' => 'Ширина должна быть числом.',
            'width.min' => 'Ширина не может быть отрицательной.',
            'width.max' => 'Ширина не может превышать 9999.99 см.',
            'height.numeric' => 'Высота должна быть числом.',
            'height.min' => 'Высота не может быть отрицательной.',
            'height.max' => 'Высота не может превышать 9999.99 см.',
            'price.numeric' => 'Цена должна быть числом.',
            'price.min' => 'Цена не может быть отрицательной.',
            'price.max' => 'Цена не может превышать $999,999.99.',
            'images.array' => 'Изображения должны быть массивом.',
            'images.max' => 'Можно загрузить максимум 10 изображений.',
            'images.*.image' => 'Каждый файл должен быть изображением.',
            'images.*.mimes' => 'Изображения должны быть в формате: jpeg, png, jpg, gif, webp.',
            'images.*.max' => 'Размер каждого изображения не должен превышать 10 МБ.',
            'categories.array' => 'Категории должны быть массивом.',
            'categories.*.exists' => 'Выбранная категория не существует.'
        ];

        return Validator::make($request->all(), $rules, $messages);
    }


    private function processUploadedImages(Artwork $artwork, array $images)
    {
        \Log::info('Начинаем обработку изображений', [
            'artwork_id' => $artwork->id,
            'images_count' => count($images)
        ]);

        foreach ($images as $index => $image) {
            try {
                if (!$image->isValid()) {
                    \Log::warning('Изображение невалидно', [
                        'index' => $index,
                        'error' => $image->getError()
                    ]);
                    continue;
                }

                $filename = time() . '_' . $index . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                
                \Log::info('Сохраняем изображение', [
                    'filename' => $filename,
                    'original_name' => $image->getClientOriginalName(),
                    'size' => $image->getSize()
                ]);

                $path = $image->storeAs('artworks', $filename, 'public');
                
                if (!$path) {
                    \Log::error('Не удалось сохранить изображение', [
                        'filename' => $filename
                    ]);
                    continue;
                }
                
                \Log::info('Изображение сохранено в Laravel storage', ['path' => $path]);
                
                $sourcePath = storage_path('app/public/' . $path);
                $publicStoragePath = public_path('storage/' . $path);
                
                $publicStorageDir = dirname($publicStoragePath);
                if (!is_dir($publicStorageDir)) {
                    mkdir($publicStorageDir, 0755, true);
                    \Log::info('Создана директория в public/storage', ['dir' => $publicStorageDir]);
                }
                
                if (copy($sourcePath, $publicStoragePath)) {
                    \Log::info('Изображение скопировано в public/storage', ['public_path' => $publicStoragePath]);
                } else {
                    \Log::error('Не удалось скопировать изображение в public/storage', [
                        'source' => $sourcePath,
                        'destination' => $publicStoragePath
                    ]);
                }
                
                $artworkImage = ArtworkImage::create([
                    'artwork_id' => $artwork->id,
                    'file_path' => 'artworks/' . $filename,
                    'original_name' => $image->getClientOriginalName(),
                    'mime_type' => $image->getMimeType(),
                    'file_size' => $image->getSize(),
                    'order_column' => $index + 1,
                    'is_primary' => $index === 0,
                ]);
                
                \Log::info('Запись изображения создана в БД', ['image_id' => $artworkImage->id]);
                
            } catch (\Exception $e) {
                \Log::error('Ошибка при обработке изображения', [
                    'index' => $index,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
    }

    public function uploadImages(Request $request, Artwork $artwork)
    {
        $request->validate([
            'images' => 'required|array|max:10',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240'
        ]);

        $imageUploadService = new ImageUploadService();
        $uploadedImages = [];
        $errors = [];

        foreach ($request->file('images') as $index => $file) {
            $validationErrors = $imageUploadService->validateImage($file);
            if (!empty($validationErrors)) {
                $errors[] = "Изображение " . ($index + 1) . ": " . implode(', ', $validationErrors);
                continue;
            }

            try {
                $isPrimary = $index === 0 && $artwork->images()->count() === 0;
                $order = $artwork->images()->max('order') + 1 + $index;
                
                $uploadedImages[] = $imageUploadService->uploadArtworkImage(
                    $file, 
                    $artwork->id, 
                    $order, 
                    $isPrimary
                );
            } catch (\Exception $e) {
                $errors[] = "Ошибка загрузки изображения " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'errors' => $errors,
                'uploaded' => count($uploadedImages)
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Изображения успешно загружены',
            'images' => $uploadedImages->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->url,
                    'filename' => $image->filename,
                    'original_name' => $image->original_name,
                    'is_primary' => $image->is_primary,
                    'order' => $image->order
                ];
            })
        ]);
    }

    public function deleteImage(ArtworkImage $image)
    {
        try {
            $imageUploadService = new ImageUploadService();
            $imageUploadService->deleteImage($image);

            return response()->json([
                'success' => true,
                'message' => 'Изображение успешно удалено'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении изображения: ' . $e->getMessage()
            ], 500);
        }
    }


    public function updateImagesOrder(Request $request, Artwork $artwork)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|integer|exists:artwork_images,id'
        ]);

        try {
            $imageUploadService = new ImageUploadService();
            $imageUploadService->updateImagesOrder($request->images);

            return response()->json([
                'success' => true,
                'message' => 'Порядок изображений обновлен'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении порядка: ' . $e->getMessage()
            ], 500);
        }
    }


    public function setPrimaryImage(Request $request, Artwork $artwork)
    {
        $request->validate([
            'image_id' => 'required|integer|exists:artwork_images,id'
        ]);

        try {
            $imageUploadService = new ImageUploadService();
            $imageUploadService->setPrimaryImage($request->image_id, $artwork->id);

            return response()->json([
                'success' => true,
                'message' => 'Главное изображение установлено'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при установке главного изображения: ' . $e->getMessage()
            ], 500);
        }
    }
}
