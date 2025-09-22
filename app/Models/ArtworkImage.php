<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Models\Artwork;

class ArtworkImage extends Model
{
    protected $fillable = [
        'artwork_id',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'order_column',
        'is_primary'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'file_size' => 'integer',
        'order_column' => 'integer'
    ];

    public function artwork()
    {
        return $this->belongsTo(Artwork::class);
    }
}
