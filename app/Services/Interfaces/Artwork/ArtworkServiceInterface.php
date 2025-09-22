<?php

namespace App\Services\Interfaces\Artwork;

use App\Models\Artwork;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

interface ArtworkServiceInterface
{
    
    public function getAllArtworks(array $filters = []): Collection;

    
    public function getArtworkById(int $id): Artwork;

    
    public function createArtwork(array $data, ?UploadedFile $image = null): Artwork;

    
    public function updateArtwork(int $id, array $data, ?UploadedFile $image = null): Artwork;

    
    public function deleteArtwork(int $id): bool;

    
    public function getArtworksByCategory(int $categoryId): Collection;

    
    public function getFeaturedArtworks(): Collection;

    
    public function searchArtworks(string $query): Collection;
}
