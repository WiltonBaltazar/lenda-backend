<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
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
            'price' => $this->price,
            'duration_days' => $this->duration_days,
             'formatted_price' => number_format($this->price, 2, '.', ','),
            'slug' => $this->slug,
            'bg_color' => $this->bg_color,
            'text_color' => $this->text_color,
            'description' => $this->description,
        ];
    }
}
