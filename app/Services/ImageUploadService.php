<?php

namespace App\Services;

use App\Models\ArtworkImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    public function uploadArtworkImage(UploadedFile $file, int $artworkId, int $order = 0, bool $isPrimary = false): ArtworkImage
    {
        $filename = $this->generateUniqueFilename($file);
        
        $path = 'artworks/' . $filename;
        
        $storedPath = $file->storeAs('artworks', $filename, 'public');
        
        return ArtworkImage::create([
            'artwork_id' => $artworkId,
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'path' => $storedPath,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'order' => $order,
            'is_primary' => $isPrimary,
        ]);
    }

    public function uploadMultipleImages(array $files, int $artworkId): array
    {
        $uploadedImages = [];
        
        foreach ($files as $index => $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $isPrimary = $index === 0; // Первое изображение делаем главным
                $uploadedImages[] = $this->uploadArtworkImage($file, $artworkId, $index, $isPrimary);
            }
        }
        
        return $uploadedImages;
    }

    public function deleteImage(ArtworkImage $image): bool
    {
        $image->deleteFile();
        
        return $image->delete();
    }

    public function updateImagesOrder(array $imageIds): void
    {
        foreach ($imageIds as $order => $imageId) {
            ArtworkImage::where('id', $imageId)->update(['order' => $order]);
        }
    }

    public function setPrimaryImage(int $imageId, int $artworkId): void
    {
        ArtworkImage::where('artwork_id', $artworkId)->update(['is_primary' => false]);
        
        ArtworkImage::where('id', $imageId)->update(['is_primary' => true]);
    }
    private function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('YmdHis');
        $random = Str::random(8);
        
        return "{$timestamp}_{$random}.{$extension}";
    }
    public function validateImage(UploadedFile $file): array
    {
        $errors = [];
		$allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            $errors[] = 'Недопустимый тип файла. Разрешены только JPEG, PNG, GIF, WebP.';
        }
		$maxSize = 10 * 1024 * 1024; // 10MB в байтах
        if ($file->getSize() > $maxSize) {
            $errors[] = 'Размер файла не должен превышать 10MB.';
        }
		if ($file->isValid()) {
            $imageInfo = getimagesize($file->getPathname());
            if ($imageInfo) {
                $width = $imageInfo[0];
                $height = $imageInfo[1];
				if ($width < 300 || $height < 300) {
                    $errors[] = 'Минимальный размер изображения: 300x300 пикселей.';
                }
				if ($width > 4000 || $height > 4000) {
                    $errors[] = 'Максимальный размер изображения: 4000x4000 пикселей.';
                }
            }
        }
        
        return $errors;
    }
}
