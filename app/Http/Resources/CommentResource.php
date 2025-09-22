<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    
    public static $wrap = 'comment';

    
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'is_approved' => $this->is_approved,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_at_formatted' => $this->created_at?->format('d.m.Y H:i'),
            'created_at_human' => $this->created_at?->diffForHumans(),
            'updated_at_formatted' => $this->updated_at?->format('d.m.Y H:i'),
            'updated_at_human' => $this->updated_at?->diffForHumans(),
            
            
            'user' => new UserResource($this->whenLoaded('user')),
            'replies' => self::collection($this->whenLoaded('replies')),
            'parent' => new self($this->whenLoaded('parent')),
            'artwork' => new ArtworkResource($this->whenLoaded('artwork')),
            
            
            'replies_count' => $this->whenCounted('replies', $this->replies_count),
            
            
            'is_editable' => $this->when(
                auth()->check() && 
                (auth()->user()->can('update', $this->resource) || 
                 auth()->user()->hasRole('admin')),
                true
            ),
            'is_deletable' => $this->when(
                auth()->check() && 
                (auth()->user()->can('delete', $this->resource) || 
                 auth()->user()->hasRole('admin')),
                true
            ),
        ];
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
