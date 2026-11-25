<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowtimeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'start_at' => $this->start_at?->format('Y-m-d H:i'),
            'base_price' => $this->base_price,
            'status' => $this->status,
            'movie' => new MovieResource($this->whenLoaded('movie')),
            'hall' => new HallResource($this->whenLoaded('hall')),
        ];
    }
}
