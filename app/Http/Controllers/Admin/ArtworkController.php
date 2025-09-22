<?php

	namespace App\Http\Controllers\Admin;

	use App\Http\Controllers\Controller;
	use App\Models\Artwork;
	use App\Models\ArtworkImage;
	use App\Models\Category;
	use App\Services\Interfaces\Artwork\ArtworkServiceInterface;
	use App\Services\ImageUploadService;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\Validator;

	class ArtworkController extends Controller
	{
		private ArtworkServiceInterface $artworkService;
		private ImageUploadService $imageUploadService;

		public function __construct(
			ArtworkServiceInterface $artworkService,
			ImageUploadService $imageUploadService
		) {
			$this->artworkService = $artworkService;
			$this->imageUploadService = $imageUploadService;
		}

		
		
		
		public function index(Request $request)
		{
			$query = Artwork::with(['user', 'categories'])
				->withCount(['likes', 'comments']);

			if ($request->filled('search')) {
				$search = $request->search;
				$query->where(function($q) use ($search) {
					$q->where('title', 'like', "%{$search}%")
						->orWhere('description', 'like', "%{$search}%")
						->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"));
				});
			}

			if ($request->filled('status')) {
				if ($request->status === 'published') $query->where('is_available', true);
				elseif ($request->status === 'draft') $query->where('is_available', false);
			}

			if ($request->filled('category')) {
				$categoryId = $request->category;
				$query->whereHas('categories', fn($q) => $q->where('categories.id', $categoryId));
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
			$validated = $this->validateArtworkData($request);

			$validated['user_id'] = Auth::id();
			$validated['is_available'] = $request->boolean('is_published');

			DB::beginTransaction();
			try {
				$artwork = $this->artworkService->createArtwork($validated);

				if ($request->hasFile('images')) {
					$this->imageUploadService->uploadMultipleImages($request->file('images'), $artwork->id);
				}

				DB::commit();

				return redirect()->route('admin.artworks.index')
					->with('success', 'Произведение успешно создано!');

			} catch (\Exception $e) {
				DB::rollBack();
				Log::error('Artwork creation failed', ['error' => $e->getMessage()]);
				return redirect()->back()->with('error', 'Ошибка при создании произведения: ' . $e->getMessage())->withInput();
			}
		}

		
		
		
		public function show(Artwork $artwork)
		{
			$artwork->load(['user', 'categories', 'comments.user', 'likes.user', 'images']);
			return view('admin.artworks.show', compact('artwork'));
		}

		
		
		
		public function edit(Artwork $artwork)
		{
			$categories = Category::active()->orderBy('name')->get();
			$selectedCategories = $artwork->categories->pluck('id')->toArray();
			$artwork->load('images');
			return view('admin.artworks.edit', compact('artwork', 'categories', 'selectedCategories'));
		}

		
		
		
		public function update(Request $request, Artwork $artwork)
		{
			$validated = $this->validateArtworkData($request, $artwork->id);
			$validated['is_available'] = $request->boolean('is_published');

			DB::beginTransaction();
			try {
				$this->artworkService->updateArtwork($artwork, $validated);

				if ($request->hasFile('images')) {
					$this->imageUploadService->uploadMultipleImages($request->file('images'), $artwork->id);
				}

				DB::commit();
				return redirect()->route('admin.artworks.index')->with('success', 'Произведение успешно обновлено!');
			} catch (\Exception $e) {
				DB::rollBack();
				Log::error('Artwork update failed', ['artwork_id' => $artwork->id, 'error' => $e->getMessage()]);
				return redirect()->back()->with('error', 'Ошибка при обновлении: ' . $e->getMessage())->withInput();
			}
		}

		
		
		
		public function destroy(Artwork $artwork)
		{
			DB::beginTransaction();
			try {
				$this->imageUploadService->deleteArtworkImages($artwork);
				$this->artworkService->deleteArtwork($artwork);
				DB::commit();
				return redirect()->route('admin.artworks.index')->with('success', 'Произведение успешно удалено!');
			} catch (\Exception $e) {
				DB::rollBack();
				Log::error('Artwork deletion failed', ['artwork_id' => $artwork->id, 'error' => $e->getMessage()]);
				return redirect()->back()->with('error', 'Ошибка при удалении: ' . $e->getMessage());
			}
		}

		
		
		
		public function uploadImages(Request $request, Artwork $artwork)
		{
			$validated = $request->validate([
				'images' => 'required|array|max:10',
				'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240',
			]);

			try {
				$files = $request->file('images');
				$uploaded = $this->imageUploadService->uploadMultipleImages($files, $artwork->id);
				return response()->json([
					'success' => true,
					'count' => count($uploaded),
					'message' => 'Изображения загружены',
				]);
			} catch (\Exception $e) {
				Log::error('Images upload failed', ['artwork_id' => $artwork->id, 'error' => $e->getMessage()]);
				return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
			}
		}

		
		
		
		public function deleteImage(ArtworkImage $image)
		{
			try {
				$this->imageUploadService->deleteImage($image);
				return response()->json(['success' => true, 'message' => 'Изображение удалено']);
			} catch (\Exception $e) {
				Log::error('Delete image failed', ['image_id' => $image->id, 'error' => $e->getMessage()]);
				return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
			}
		}

		
		public function deleteImageById(int $id)
		{
			$image = ArtworkImage::find($id);
			if (!$image) {
				return response()->json(['success' => false, 'message' => 'Изображение не найдено'], 404);
			}
			return $this->deleteImage($image);
		}

		
		
		
		public function updateImagesOrder(Request $request, Artwork $artwork)
		{
			$validated = $request->validate([
				'images' => 'required|array',
				'images.*' => 'integer|exists:artwork_images,id',
			]);

			try {
				$this->imageUploadService->updateImagesOrder($validated['images']);
				return response()->json(['success' => true, 'message' => 'Порядок изображений обновлен']);
			} catch (\Exception $e) {
				Log::error('Update images order failed', ['artwork_id' => $artwork->id, 'error' => $e->getMessage()]);
				return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
			}
		}

		
		
		
		public function setPrimaryImage(Request $request, Artwork $artwork)
		{
			$validated = $request->validate([
				'image_id' => 'required|integer|exists:artwork_images,id',
			]);

			try {
				$this->imageUploadService->setPrimaryImage($validated['image_id'], $artwork->id);
				return response()->json(['success' => true, 'message' => 'Главное изображение установлено']);
			} catch (\Exception $e) {
				Log::error('Set primary image failed', ['artwork_id' => $artwork->id, 'error' => $e->getMessage()]);
				return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
			}
		}

		
		
		
		public function togglePublished(Artwork $artwork)
		{
			try {
				$artwork->update(['is_available' => !$artwork->is_available]);
				$status = $artwork->is_available ? 'опубликовано' : 'снято с публикации';
				return response()->json(['success' => true, 'message' => "Произведение {$status}", 'is_published' => $artwork->is_available]);
			} catch (\Exception $e) {
				Log::error('Toggle status failed', ['artwork_id' => $artwork->id, 'error' => $e->getMessage()]);
				return response()->json(['success' => false, 'message' => 'Ошибка при изменении статуса'], 500);
			}
		}

		
		
		
		private function validateArtworkData(Request $request, ?int $artworkId = null): array
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
				'categories' => 'nullable|array',
				'categories.*' => 'exists:categories,id',
				'images' => 'nullable|array|max:10',
				'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240',
				'is_published' => 'boolean'
			];

			$messages = [
				'title.required' => 'Название произведения обязательно.',
				'title.max' => 'Название не должно превышать 255 символов.',
				'description.max' => 'Описание не должно превышать 5000 символов.',
				'year.integer' => 'Год должен быть числом.',
				'year.min' => 'Год не может быть меньше 1000.',
				'year.max' => 'Год не может быть больше ' . (date('Y') + 1),
				'width.numeric' => 'Ширина должна быть числом.',
				'height.numeric' => 'Высота должна быть числом.',
				'price.numeric' => 'Цена должна быть числом.',
				'categories.*.exists' => 'Выбранная категория не существует.',
				'images.*.image' => 'Файл должен быть изображением.',
				'images.*.mimes' => 'Поддерживаемые форматы: JPEG, PNG, JPG, GIF, WEBP.',
				'images.*.max' => 'Размер изображения не должен превышать 10 МБ.',
			];

			return Validator::make($request->all(), $rules, $messages)->validate();
		}
	}
