<?php

namespace App\Services\Interfaces\Artwork;

use App\Models\Artwork;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

interface ArtworkServiceInterface
{
    /**
     * @param array $filters
     * @return Collection
     */
    public function getAllArtworks(array $filters = []): Collection;

    /**
     * @param int $id
     * @return Artwork
     */
    public function getArtworkById(int $id): Artwork;

    /**
     * @param array $data
     * @param UploadedFile|null $image
     * @return Artwork
     */
    public function createArtwork(array $data, ?UploadedFile $image = null): Artwork;

    /**
     * @param int $id
     * @param array $data
     * @param UploadedFile|null $image
     * @return Artwork
     */
    public function updateArtwork(int $id, array $data, ?UploadedFile $image = null): Artwork;

    /**
     * @param int $id
     * @return bool
     */
    public function deleteArtwork(int $id): bool;

    /**
     * @param int $categoryId
     * @return Collection
     */
    public function getArtworksByCategory(int $categoryId): Collection;

    /**
     * @return Collection
     */
    public function getFeaturedArtworks(): Collection;

    /**
     * @param string $query
     * @return Collection
     */
    public function searchArtworks(string $query): Collection;
}
