<?php

	namespace App\Services\Implementations\Artwork;

	use App\Models\Artwork;
	use App\Services\Interfaces\Artwork\ArtworkServiceInterface;
	use Illuminate\Database\Eloquent\Collection;
	use Illuminate\Http\UploadedFile;
	use Illuminate\Pagination\LengthAwarePaginator;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\Storage;
	use Illuminate\Support\Str;

	class ArtworkService implements ArtworkServiceInterface
	{
		public function getAllArtworks(array $filters = [], int $perPage = 15): Collection
		{
			$query = Artwork::with(['categories', 'user', 'images'])
				->withCount(['likes', 'comments']);

			if (!empty($filters['category_ids']) && is_array($filters['category_ids'])) {
				$query->whereHas('categories', fn($q) => $q->whereIn('categories.id', $filters['category_ids']));
			} elseif (!empty($filters['category_id'])) {
				$query->whereHas('categories', fn($q) => $q->where('categories.id', $filters['category_id']));
			}

			if (!empty($filters['search'])) {
				$search = $filters['search'];
				$query->where(fn($q) =>
				$q->where('title', 'like', "%{$search}%")
					->orWhere('description', 'like', "%{$search}%")
					->orWhereHas('user', fn($userQuery) => $userQuery->where('name', 'like', "%{$search}%"))
				);
			}

			if (!empty($filters['featured'])) {
				$query->where('is_featured', true);
			}

			if (isset($filters['is_available'])) {
				$query->where('is_available', $filters['is_available']);
			}

			if (!empty($filters['user_id'])) {
				$query->where('user_id', $filters['user_id']);
			}

			if (!empty($filters['price_from'])) {
				$query->where('price', '>=', $filters['price_from']);
			}

			if (!empty($filters['price_to'])) {
				$query->where('price', '<=', $filters['price_to']);
			}

			if (!empty($filters['year'])) {
				$query->where('year', $filters['year']);
			}

			$sortBy = $filters['sort_by'] ?? 'created_at';
			$sortDirection = $filters['sort_direction'] ?? 'desc';

			switch ($sortBy) {
				case 'title': $query->orderBy('title', $sortDirection); break;
				case 'price': $query->orderBy('price', $sortDirection); break;
				case 'year': $query->orderBy('year', $sortDirection); break;
				case 'likes': $query->orderBy('likes_count', $sortDirection); break;
				case 'comments': $query->orderBy('comments_count', $sortDirection); break;
				default: $query->orderBy('created_at', $sortDirection);
			}

			return $query->paginate($perPage);
		}

		public function getArtworkById(int $id): Artwork
		{
			return Artwork::with([
				'categories', 'user', 'comments.user', 'likes.user',
				'images' => fn($q) => $q->orderBy('order')
			])->findOrFail($id);
		}

		public function getArtworkBySlug(string $slug): Artwork
		{
			return Artwork::with([
				'categories', 'user', 'comments.user', 'likes.user',
				'images' => fn($q) => $q->orderBy('order')
			])->where('slug', $slug)->firstOrFail();
		}

		public function createArtwork(array $data, ?UploadedFile $image = null): Artwork
		{
			DB::beginTransaction();
			try {
				$data['slug'] = $this->generateUniqueSlug($data['title']);
				$data['user_id'] = $data['user_id'] ?? Auth::id();

				if ($image) {
					$data['image_path'] = $this->uploadSingleImage($image);
				}

				$artwork = Artwork::create($data);

				if (!empty($data['categories']) && is_array($data['categories'])) {
					$artwork->categories()->sync($data['categories']);
				}

				DB::commit();
				Log::info('Artwork created successfully', ['artwork_id'=>$artwork->id]);

				return $artwork->load(['categories','user','images']);
			} catch (\Exception $e) {
				DB::rollBack();
				if (!empty($data['image_path'])) {
					Storage::disk('public')->delete($data['image_path']);
				}
				Log::error('Failed to create artwork',['error'=>$e->getMessage()]);
				throw $e;
			}
		}

		public function updateArtwork(Artwork|int $artwork, array $data, ?UploadedFile $image = null): Artwork
		{
			DB::beginTransaction();
			try {
				$oldImagePath = $artwork->image_path;

				if ($image) {
					$data['image_path'] = $this->uploadSingleImage($image);
				}

				if (isset($data['title']) && $artwork->title !== $data['title']) {
					$data['slug'] = $this->generateUniqueSlug($data['title'], $artwork->id);
				}

				$artwork->update($data);

				if (array_key_exists('categories', $data)) {
					$artwork->categories()->sync(is_array($data['categories']) ? $data['categories'] : []);
				}

				if ($image && $oldImagePath && $oldImagePath !== ($data['image_path'] ?? null)) {
					Storage::disk('public')->delete($oldImagePath);
				}

				DB::commit();
				Log::info('Artwork updated successfully',['artwork_id'=>$artwork->id]);
				return $artwork->fresh(['categories','user','images']);
			} catch (\Exception $e) {
				DB::rollBack();
				if (!empty($data['image_path']) && $data['image_path'] !== $oldImagePath) {
					Storage::disk('public')->delete($data['image_path']);
				}
				Log::error('Failed to update artwork',['artwork_id'=>$artwork->id,'error'=>$e->getMessage()]);
				throw $e;
			}
		}

		public function deleteArtwork(Artwork|int $artwork): bool
		{
			DB::beginTransaction();
			try {
				$imagePath = $artwork->image_path;
				if ($imagePath) {
					Storage::disk('public')->delete($imagePath);
				}
				$deleted = $artwork->delete();
				DB::commit();
				Log::info('Artwork deleted successfully',['artwork_id'=>$artwork->id]);
				return $deleted;
			} catch (\Exception $e) {
				DB::rollBack();
				Log::error('Failed to delete artwork',['artwork_id'=>$artwork->id,'error'=>$e->getMessage()]);
				throw $e;
			}
		}

		public function getArtworksByCategory(int $categoryId, int $perPage = 15): Collection
		{
			return Artwork::whereHas('categories', fn($q) => $q->where('categories.id', $categoryId))
				->with(['categories','user','images'])
				->where('is_available', true)
				->latest()
				->paginate($perPage);
		}

		public function getFeaturedArtworks(int $limit = 8): Collection
		{
			return Artwork::where('is_featured', true)
				->where('is_available', true)
				->with(['categories','user','images'])
				->latest()
				->limit($limit)
				->get();
		}

		public function searchArtworks(string $query, int $perPage = 15): Collection
		{
			return Artwork::where(fn($q) =>
			$q->where('title','like',"%{$query}%")
				->orWhere('description','like',"%{$query}%")
				->orWhere('materials','like',"%{$query}%")
				->orWhereHas('user', fn($uq)=>$uq->where('name','like',"%{$query}%"))
				->orWhereHas('categories', fn($cq)=>$cq->where('name','like',"%{$query}%"))
			)->with(['categories','user','images'])
				->where('is_available', true)
				->latest()
				->paginate($perPage);
		}

		public function getPopularArtworks(int $limit = 10): Collection
		{
			return Artwork::withCount(['likes','comments'])
				->where('is_available', true)
				->with(['categories','user','images'])
				->orderByDesc('likes_count')
				->orderByDesc('comments_count')
				->limit($limit)
				->get();
		}

		public function getRecentArtworks(int $limit = 10): Collection
		{
			return Artwork::where('is_available', true)
				->with(['categories','user','images'])
				->latest()
				->limit($limit)
				->get();
		}

		public function getUserArtworks(int $userId, int $perPage = 15, bool $includeUnpublished = false): LengthAwarePaginator
		{
			$query = Artwork::where('user_id', $userId)->with(['categories','user','images']);
			if (!$includeUnpublished) {
				$query->where('is_available', true);
			}
			return $query->latest()->paginate($perPage);
		}

		public function toggleFeatured(Artwork $artwork): bool
		{
			$artwork->update(['is_featured' => !$artwork->is_featured]);
			Log::info('Artwork featured status toggled',['artwork_id'=>$artwork->id,'is_featured'=>$artwork->is_featured]);
			return $artwork->is_featured;
		}

		public function togglePublished(Artwork $artwork): bool
		{
			$artwork->update(['is_available' => !$artwork->is_available]);
			Log::info('Artwork published status toggled',['artwork_id'=>$artwork->id,'is_available'=>$artwork->is_available]);
			return $artwork->is_available;
		}

		protected function uploadSingleImage(UploadedFile $image): string
		{
			$filename = Str::uuid().'.'.$image->getClientOriginalExtension();
			$path = $image->storeAs('artworks', $filename, 'public');
			if (!$path) throw new \Exception('Failed to store image');
			Log::info('Single image uploaded',['filename'=>$filename]);
			return $path;
		}

		protected function generateUniqueSlug(string $title, ?int $excludeId = null): string
		{
			$baseSlug = Str::slug($title);
			if (empty($baseSlug)) $baseSlug = 'artwork-'.Str::random(6);
			$slug = $baseSlug;
			$counter = 1;
			while ($this->slugExists($slug, $excludeId)) {
				$slug = $baseSlug.'-'.$counter;
				$counter++;
			}
			return $slug;
		}

		protected function slugExists(string $slug, ?int $excludeId = null): bool
		{
			$query = Artwork::where('slug', $slug);
			if ($excludeId) $query->where('id','!=',$excludeId);
			return $query->exists();
		}

		public function getArtworkStats(): array
		{
			return [
				'total'=>Artwork::count(),
				'published'=>Artwork::where('is_available',true)->count(),
				'featured'=>Artwork::where('is_featured',true)->count(),
				'with_price'=>Artwork::whereNotNull('price')->where('price','>',0)->count(),
			];
		}

		public function bulkUpdatePublishStatus(array $artworkIds,bool $isPublished): int
		{
			$updated = Artwork::whereIn('id',$artworkIds)->update(['is_available'=>$isPublished]);
			Log::info('Bulk updated artwork publish status',['artwork_ids'=>$artworkIds,'is_published'=>$isPublished,'updated_count'=>$updated]);
			return $updated;
		}

		public function getRelatedArtworks(Artwork $artwork,int $limit=4): Collection
		{
			$categoryIds = $artwork->categories->pluck('id')->toArray();
			$query = Artwork::where('id','!=',$artwork->id)->where('is_available',true)->with(['categories','user','images']);
			if (!empty($categoryIds)) {
				$query->whereHas('categories', fn($q)=>$q->whereIn('categories.id',$categoryIds));
			}
			$artworks = $query->limit($limit)->get();
			if ($artworks->count()<$limit) {
				$needed = $limit - $artworks->count();
				$existingIds = $artworks->pluck('id')->push($artwork->id)->toArray();
				$additional = Artwork::where('user_id',$artwork->user_id)->whereNotIn('id',$existingIds)->where('is_available',true)->with(['categories','user','images'])->limit($needed)->get();
				$artworks = $artworks->merge($additional);
			}
			return $artworks->take($limit);
		}
	}
