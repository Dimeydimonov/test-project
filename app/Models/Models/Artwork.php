<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;
use App\Models\ArtworkImage;

class Artwork extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'year',
        'size',
        'width',
        'height',
        'materials',
        'price',
        'image_path',
        'image_alt',
        'thumbnail_path',
        'category_id',
        'user_id',
        'views_count',
        'likes_count',
        'is_available',
        'is_featured',
        'order_column'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'views_count' => 'integer',
        'likes_count' => 'integer',
        'order_column' => 'integer',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'price' => 'decimal:2',
        'year' => 'integer',
    ];

    /**
     * @var array
     */
    protected $appends = ['image_url', 'thumbnail_url'];

    /**
     * @return string
     */
    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    /**
     * @return string
     */
    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail_path ? asset('storage/' . $this->thumbnail_path) : $this->image_url;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'artwork_category');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ArtworkImage::class)->orderBy('order_column');
    }

    public function likedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * @param  string  $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByColumn($query, $column = 'created_at', $direction = 'desc')
    {
        return $query->orderBy($column, $direction);
    }

    /**
     * @return $this
     */
    public function incrementViewCount()
    {
        $this->increment('views_count');
        return $this;
    }
}
