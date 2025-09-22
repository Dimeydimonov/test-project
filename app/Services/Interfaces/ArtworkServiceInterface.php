<?php

namespace App\Services\Interfaces;

use App\Models\Models\Artwork;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

interface ArtworkServiceInterface
{
    public function getAllArtworks(int $perPage = 12): LengthAwarePaginator;

    public function getArtworkById(int $id): ?Artwork;

    public function getArtworkBySlug(string $slug): ?Artwork;

    public function createArtwork(array $data, ?UploadedFile $image = null): Artwork;

    public function updateArtwork(Artwork $artwork, array $data, ?UploadedFile $image = null): Artwork;

    public function deleteArtwork(Artwork $artwork): bool;

    public function getPopularArtworks(int $limit = 8): \Illuminate\Database\Eloquent\Collection;

    public function getRecentArtworks(int $limit = 8): \Illuminate\Database\Eloquent\Collection;

    public function getArtworksByCategory(int $categoryId, int $perPage = 12): LengthAwarePaginator;

    public function searchArtworks(string $query, int $perPage = 12): LengthAwarePaginator;

    public function uploadArtworkImage(UploadedFile $image): string;

    public function deleteArtworkImage(string $imagePath): bool;
}
