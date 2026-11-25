<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'duration_minutes' => $this->duration_minutes,
            'age_rating' => $this->age_rating,
            'release_date' => $this->release_date?->format('Y-m-d'),
            'poster_url' => $this->poster_url,
            'genres' => GenreResource::collection($this->whenLoaded('genres')),
        ];
    }
}
