<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar'
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'deleted_at',
        'email_verified_at',
        'updated_at'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => 'string'
    ];

    /**
     * @var array
     */
    protected $appends = [
        'photo_url'
    ];

    public function artworks(): HasMany
    {
        return $this->hasMany(Artwork::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function getPhotoUrlAttribute(): string
    {
        return $this->avatar 
            ? asset('storage/avatars/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    public function likedArtworks(): HasManyThrough
    {
        return $this->hasManyThrough(Artwork::class, Like::class, 'user_id', 'id', 'id', 'artwork_id');
    }

}
