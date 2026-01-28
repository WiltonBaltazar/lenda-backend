<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EpisodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'cover_image' => $this->cover_image ? url('storage/' .$this->cover_image) : null,
            'audio_file' => $this->audio_file ? url('storage/'. $this->audio_file) : null,
            'description' => $this->description,
            'guest' => $this->guest,
            'release_date' => $this->release_date,
            'duration' => $this->duration,
            'created_at' => DateTimeResource::make($this->created_at),
        ];
    }

    /**
     * Get additional data that should be return with the resource array.
     */

    public function with(Request $request): array
    {
        return [
            'version' => '1.0',
            'api_url' => url('/api/v1'),
        ];
    }
}
