<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    
    public static $wrap = 'user';

    
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->when($this->showEmail(), $this->email),
            'avatar' => $this->avatar,
            'bio' => $this->bio,
            'website' => $this->website,
            'created_at' => $this->created_at,
            'created_at_formatted' => $this->created_at?->format('d.m.Y H:i'),
            
            
            'artworks_count' => $this->whenCounted('artworks', $this->artworks_count),
            'likes_count' => $this->whenCounted('likes', $this->likes_count),
            'comments_count' => $this->whenCounted('comments', $this->comments_count),
            
            
            'artworks' => ArtworkResource::collection($this->whenLoaded('artworks')),
            'likes' => LikeResource::collection($this->whenLoaded('likes')),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
        ];
    }
    
    
    protected function showEmail(): bool
    {
        $user = auth()->user();
        return $user && ($user->id === $this->id || $user->hasRole('admin'));
    }
    
    
    public function with($request): array
    {
        return [
            'meta' => [
                'version' => '1.0',
                'api_version' => 'v1',
            ],
        ];
    }
}
