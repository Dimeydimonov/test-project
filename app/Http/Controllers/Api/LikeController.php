<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\Like;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LikeController extends Controller
{
    
    public function like(Artwork $artwork): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $existingLike = $artwork->likes()->where('user_id', $user->id)->first();
            
            if ($existingLike) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Вы уже поставили лайк этому произведению'
                ], 422);
            }
            
            $like = new Like([
                'user_id' => $user->id,
                'likeable_id' => $artwork->id,
                'likeable_type' => get_class($artwork)
            ]);
            
            $artwork->likes()->save($like);
            
            $artwork->loadCount('likes');
            
            return response()->json([
                'status' => 'success',
                'message' => 'Лайк успешно поставлен',
                'likes_count' => $artwork->likes_count,
                'is_liked' => true
            ]);
        } catch (\Exception $e) {
            Log::error('Error liking artwork: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Произошла ошибка при добавлении лайка'
            ], 500);
        }
    }

    
    
    public function unlike(Artwork $artwork): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $like = $artwork->likes()
                ->where('user_id', $user->id)
                ->first();
                
            if (!$like) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Вы еще не ставили лайк этому произведению'
                ], 422);
            }
            
            $like->delete();
            
            $artwork->loadCount('likes');
            
            return response()->json([
                'status' => 'success',
                'message' => 'Лайк успешно убран',
                'likes_count' => $artwork->likes_count,
                'is_liked' => false
            ]);
        } catch (\Exception $e) {
            Log::error('Error unliking artwork: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Произошла ошибка при удалении лайка'
            ], 500);
        }
    }

    
    
    public function check(Artwork $artwork): JsonResponse
    {
        try {
            $user = Auth::user();
            $isLiked = $user ? $artwork->likes()->where('user_id', $user->id)->exists() : false;
            
            return response()->json([
                'status' => 'success',
                'is_liked' => $isLiked,
                'likes_count' => $artwork->likes()->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking like status: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Произошла ошибка при проверке статуса лайка'
            ], 500);
        }
    }

    
    public function getLikesCount(Artwork $artwork): JsonResponse
    {
        try {
            $count = $artwork->likes()->count();
            
            return response()->json([
                'status' => 'success',
                'likes_count' => $count
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting likes count: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Произошла ошибка при получении количества лайков'
            ], 500);
        }
    }

    
    public function likers(Artwork $artwork): JsonResponse
    {
        try {
            $likers = $artwork->likes()
                ->with('user')
                ->latest()
                ->get()
                ->pluck('user')
                ->filter();
            
            return response()->json([
                'status' => 'success',
                'data' => $likers->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'avatar' => $user->getFirstMediaUrl('avatar', 'thumb'),
                        'profile_url' => route('profile.show', $user->id)
                    ];
                })
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting likers: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Произошла ошибка при получении списка пользователей, поставивших лайк'
            ], 500);
        }
    }

    
}
