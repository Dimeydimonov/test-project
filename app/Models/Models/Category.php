<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_path',
        'order_column',
        'is_visible',
        'meta_title',
        'meta_description'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'is_visible' => 'boolean',
        'order_column' => 'integer',
    ];

    /**
     * @var array
     */
    protected $appends = ['image_url'];

    /**
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * @return string
     */
    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    public function artworks(): HasMany
    {
        return $this->hasMany(Artwork::class);
    }

    public function publishedArtworks(): HasMany
    {
        return $this->artworks()->where('is_available', true);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * @param  string  $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByColumn($query, $column = 'order_column', $direction = 'asc')
    {
        return $query->orderBy($column, $direction);
    }
}
