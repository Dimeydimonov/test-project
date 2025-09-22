<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LikeResource extends JsonResource
{
    
    public static $wrap = 'like';

    
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'likeable_id' => $this->likeable_id,
            'likeable_type' => $this->likeable_type,
            'created_at' => $this->created_at,
            'created_at_formatted' => $this->created_at?->format('d.m.Y H:i'),
            'created_at_human' => $this->created_at?->diffForHumans(),
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
