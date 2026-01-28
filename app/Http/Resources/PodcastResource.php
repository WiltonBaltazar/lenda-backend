<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PodcastResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return 
        [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,  
            'episodes' => EpisodeResource::collection($this->episodes),
            'created_at' => DateTimeResource::make($this->created_at),
            'updated_at' => $this->updated_at
        ];
    }
}
