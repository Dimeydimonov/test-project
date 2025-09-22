<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ArtworkImage extends Model
{
    protected $fillable = [
        'artwork_id',
        'filename',
        'original_name',
        'path',
        'mime_type',
        'size',
        'order',
        'is_primary'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'size' => 'integer',
        'order' => 'integer',
    ];

    public function artwork(): BelongsTo
    {
        return $this->belongsTo(Artwork::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }

    public function getFullPathAttribute(): string
    {
        return storage_path('app/public/' . $this->path);
    }

    public function deleteFile(): bool
    {
        if (Storage::disk('public')->exists($this->path)) {
            return Storage::disk('public')->delete($this->path);
        }
        return true;
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('id');
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
}
