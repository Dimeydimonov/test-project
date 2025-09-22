<?php

namespace App\Services\Implementations;

use App\Models\Models\Artwork;
use App\Services\Interfaces\ArtworkServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArtworkService implements ArtworkServiceInterface
{
    public function getAllArtworks(int $perPage = 12): LengthAwarePaginator
    {
        return Artwork::with(['user', 'categories'])
            ->where('is_available', true)
            ->withCount(['likes', 'comments'])
            ->latest()
            ->paginate($perPage);
    }

    public function getArtworkById(int $id): ?Artwork
    {
        return Artwork::with(['user', 'categories', 'comments.user', 'likes'])
            ->withCount(['likes', 'comments'])
            ->find($id);
    }

    public function getArtworkBySlug(string $slug): ?Artwork
    {
        return Artwork::with(['user', 'categories', 'comments.user', 'likes'])
            ->withCount(['likes', 'comments'])
            ->where('slug', $slug)
            ->where('is_available', true)
            ->first();
    }

    public function createArtwork(array $data, ?UploadedFile $image = null): Artwork
    {
        $data['slug'] = $this->generateUniqueSlug($data['title']);

        if ($image) {
            $data['image_path'] = $this->uploadArtworkImage($image);
        } else {
            $data['image_path'] = '';
        }

        if (!isset($data['category_id'])) {
            $data['category_id'] = null;
        }

        $artwork = Artwork::create($data);

        if (isset($data['categories']) && is_array($data['categories'])) {
            $artwork->categories()->sync($data['categories']);
        }

        return $artwork->load(['user', 'categories']);
    }

    public function updateArtwork(Artwork $artwork, array $data, ?UploadedFile $image = null): Artwork
    {
        if (isset($data['title']) && $data['title'] !== $artwork->title) {
            $data['slug'] = $this->generateUniqueSlug($data['title'], $artwork->id);
        }

        if ($image) {
            if ($artwork->image_path) {
                $this->deleteArtworkImage($artwork->image_path);
            }
            $data['image_path'] = $this->uploadArtworkImage($image);
        }

        $artwork->update($data);

        if (isset($data['categories']) && is_array($data['categories'])) {
            $artwork->categories()->sync($data['categories']);
        }

        return $artwork->load(['user', 'categories']);
    }

    public function deleteArtwork(Artwork $artwork): bool
    {
        if ($artwork->image_path) {
            $this->deleteArtworkImage($artwork->image_path);
        }

        $artwork->categories()->detach();

        return $artwork->delete();
    }

    public function getPopularArtworks(int $limit = 8): \Illuminate\Database\Eloquent\Collection
    {
        return Artwork::with(['user', 'categories'])
            ->where('is_available', true)
            ->withCount(['likes', 'comments'])
            ->orderBy('likes_count', 'desc')
            ->orderBy('views', 'desc')
            ->take($limit)
            ->get();
    }

    public function getRecentArtworks(int $limit = 8): \Illuminate\Database\Eloquent\Collection
    {
        return Artwork::with(['user', 'categories'])
            ->where('is_available', true)
            ->withCount(['likes', 'comments'])
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getArtworksByCategory(int $categoryId, int $perPage = 12): LengthAwarePaginator
    {
        return Artwork::with(['user', 'categories'])
            ->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('categories.id', $categoryId);
            })
            ->where('is_available', true)
            ->withCount(['likes', 'comments'])
            ->latest()
            ->paginate($perPage);
    }

    public function searchArtworks(string $query, int $perPage = 12): LengthAwarePaginator
    {
        return Artwork::with(['user', 'categories'])
            ->where('is_available', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhereHas('user', function ($userQuery) use ($query) {
                      $userQuery->where('name', 'like', "%{$query}%");
                  });
            })
            ->withCount(['likes', 'comments'])
            ->latest()
            ->paginate($perPage);
    }

    public function uploadArtworkImage(UploadedFile $image): string
    {
        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('artworks', $filename, 'public');
        
        return $path;
    }

    public function deleteArtworkImage(string $imagePath): bool
    {
        return Storage::disk('public')->delete($imagePath);
    }

    private function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $query = Artwork::where('slug', $slug);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
