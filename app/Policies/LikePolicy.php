<?php

namespace App\Policies;

use App\Models\Artwork;
use App\Models\Like;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LikePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, Like $like): bool
    {
        return $user->hasRole('admin') ||
               $user->id === $like->user_id || 
               $user->id === $like->likeable->user_id;
    }

    public function create(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }

    public function delete(User $user, Like $like): bool
    {
        return $user->hasRole('admin') || $user->id === $like->user_id;
    }
    public function like(User $user, Artwork $artwork): bool
    {
        if ($user->id === $artwork->user_id) {
            return false;
        }
        
        $alreadyLiked = $artwork->likes()
            ->where('user_id', $user->id)
            ->exists();
            
        return !$alreadyLiked && $user->hasVerifiedEmail();
    }

    public function unlike(User $user, Artwork $artwork): bool
    {
        $like = $artwork->likes()
            ->where('user_id', $user->id)
            ->first();
            
        return (bool) $like;
    }

    public function viewLikers(User $user, Artwork $artwork): bool
    {
        return true;
    }
}
