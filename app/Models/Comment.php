<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Comment extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'content',
        'user_id',
        'artwork_id',
        'parent_id',
        'is_approved'
    ];

    
    protected $hidden = [
        'user_id',
        'parent_id',
        'artwork_id',
        'updated_at',
        'is_approved'
    ];

    
    protected $casts = [
        'is_approved' => 'boolean',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    
    protected $appends = [
        'is_editable',
        'is_deletable'
    ];

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

    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }

    public function hasReplies(): bool
    {
        return $this->replies()->exists();
    }

    public function getIsEditableAttribute(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        return auth()->id() === $this->user_id || auth()->user()->hasRole('admin');
    }
    public function getIsDeletableAttribute(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        return auth()->id() === $this->user_id || auth()->user()->hasRole('admin');
    }

    public function scopeWithRelations(Builder $query): Builder
    {
        return $query->with(['user', 'replies.user']);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }

    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeReplies(Builder $query): Builder
    {
        return $query->whereNotNull('parent_id');
    }
}
