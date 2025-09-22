<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Comment extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'content',
        'user_id',
        'artwork_id',
        'parent_id',
        'likes_count',
        'is_approved'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'is_approved' => 'boolean',
        'likes_count' => 'integer',
    ];

    /**
     * @var array
     */
    protected $touches = ['artwork'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function artwork(): BelongsTo
    {
        return $this->belongsTo(Artwork::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * @return bool
     */
    public function hasReplies(): bool
    {
        return $this->replies()->exists();
    }

    /**
     * @return $this
     */
    public function incrementLikeCount()
    {
        $this->increment('likes_count');
        return $this;
    }

    /**
     * @return $this
     */
    public function decrementLikeCount()
    {
        $this->decrement('likes_count');
        return $this;
    }
}
