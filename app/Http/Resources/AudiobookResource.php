<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AudiobookResource extends JsonResource
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
            'cover_image' => url('storage/' . $this->cover_image),
            'chapters' => AudiobookChapterResource::collection($this->audiobookChapters),
            'year' => $this->year,
            'narrator' => $this->narrator,
            'created_at' => DateTimeResource::make($this->created_at),
            'updated_at' => $this->updated_at,
        ];
    }
}
