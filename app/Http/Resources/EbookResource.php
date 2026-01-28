<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class EbookResource extends JsonResource
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
            'description' => $this->description,
            'chapters' => $this->chapters,
            'file' =>  url('storage/' . $this->file),
            'year' => $this->year,
            'is_free'=> $this->is_free,
            'ebook_serie_id' => $this->ebook_serie_id,
            'created_at' => DateTimeResource::make($this->created_at),
            'updated_at' => $this->updated_at,
        ];
    }

}
