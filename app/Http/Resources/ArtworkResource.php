<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArtworkResource extends JsonResource
{
    /**
     * @var string|null
     */
    public static $wrap = 'artwork';

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'year' => $this->year,
            'size' => $this->size,
            'materials' => $this->materials,
            'price' => $this->price,
            'image_url' => $this->image_url,
            'image_alt' => $this->image_alt,
            'is_available' => $this->is_available,
            'is_featured' => $this->is_featured,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            'user' => new UserResource($this->whenLoaded('user')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            

            'likes_count' => $this->whenCounted('likes', $this->likes_count),
            'is_liked' => $this->when(
                $this->relationLoaded('likes') && auth()->check(),
                fn () => $this->is_liked
            ),
            
            'comments_count' => $this->whenCounted('comments', $this->comments_count),
            
            'created_at_formatted' => $this->created_at?->format('d.m.Y H:i'),
            'updated_at_formatted' => $this->updated_at?->format('d.m.Y H:i'),
        ];
    }
    
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
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
