<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Like extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'user_id',
        'likeable_id',
        'likeable_type',
    ];

    
    protected $hidden = [
        'likeable_type',
        'likeable_id',
        'updated_at',
    ];

    
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeWithUser($query)
    {
        return $query->with('user');
    }

    public function scopeForModel($query, $model)
    {
        return $query->where('likeable_type', $model);
    }
}
