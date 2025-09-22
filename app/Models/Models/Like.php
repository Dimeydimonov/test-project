<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Like extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'artwork_id',
        'comment_id'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function artwork(): BelongsTo
    {
        return $this->belongsTo(Artwork::class);
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForArtwork($query)
    {
        return $query->whereNotNull('artwork_id');
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForComment($query)
    {
        return $query->whereNotNull('comment_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function likeable()
    {
        return $this->morphTo();
    }
}
