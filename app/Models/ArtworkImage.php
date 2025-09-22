<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Artwork;
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
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'size' => 'integer',
        'order' => 'integer',
    ];

    public function artwork()
    {
        return $this->belongsTo(Artwork::class);
    }

    
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function deleteFile(): void
    {
        if ($this->path) {
            Storage::disk('public')->delete($this->path);
        }
    }

    public function getUrlAttribute(): ?string
    {
        return $this->path ? Storage::url($this->path) : null;
    }
}
