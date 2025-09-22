<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_path',
        'order',
        'is_active',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
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

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = $category->generateUniqueSlug();
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = $category->generateUniqueSlug();
            }
        });
    }

    /**
     * @return string
     */
    public function generateUniqueSlug()
    {
        $slug = Str::slug($this->name);
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

    public function artworks()
    {
        return $this->belongsToMany(Artwork::class, 'artwork_category');
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $column
     * @param  string  $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByColumn($query, $column = 'order', $direction = 'asc')
    {
        return $query->orderBy($column, $direction);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
    }
}
