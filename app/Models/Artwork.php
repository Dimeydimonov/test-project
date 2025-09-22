<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Category;
use App\Models\ArtworkImage;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Artwork extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var array<int, string>
     */

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'artwork_category');
    }
    protected $fillable = [
        'title',
        'slug',
        'description',
        'year',
        'size',
        'materials',
        'price',
        'image_path',
        'image_alt',
        'is_available',
        'is_featured',
        'category_id',
        'user_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'float',
        'year' => 'integer',
    ];

    /**
     * @var array
     */
    protected $appends = ['image_url'];

    /**
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($artwork) {
            if (empty($artwork->slug)) {
                $artwork->slug = $artwork->generateUniqueSlug();
            }
        });

        static::updating(function ($artwork) {
            if ($artwork->isDirty('title') && empty($artwork->slug)) {
                $artwork->slug = $artwork->generateUniqueSlug();
            }
        });
    }

    /**
     * @return string
     */
    public function generateUniqueSlug()
    {
        $slug = Str::slug($this->title);
        $count = static::where('slug', 'LIKE', "{$slug}%")->count();
        
        return $count ? "{$slug}-{$count}" : $slug;
    }

    /**
     * @return string|null
     */
    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function rootComments()
    {
        return $this->comments()->whereNull('parent_id');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * @return bool
     */
    public function getIsLikedAttribute(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        return $this->likes()->where('user_id', auth()->id())->exists();
    }

    /**
     * @return int
     */
    public function getLikesCountAttribute(): int
    {
        return $this->likes()->count();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvailable($query)
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
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
    }

    public function images(): HasMany
    {
        return $this->hasMany(ArtworkImage::class)->ordered();
    }

    public function primaryImage()
    {
        return $this->images()->primary()->first();
    }

    public function getMainImageUrlAttribute(): ?string
    {
        $primaryImage = $this->primaryImage();
        if ($primaryImage) {
            return $primaryImage->url;
        }

        $firstImage = $this->images()->first();
        if ($firstImage) {
            return $firstImage->url;
        }
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }
}
