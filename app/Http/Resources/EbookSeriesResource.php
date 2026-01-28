<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EbookSeriesResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'cover_image' => url('storage/' . $this->cover_image),
            'description' => $this->description,
            'created_at' => DateTimeResource::make($this->created_at),
            'updated_at' => $this->updated_at,
        ];
    }
}
