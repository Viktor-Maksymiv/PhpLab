<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'hall_id' => $this->hall_id,
            'row_number' => $this->row_number,
            'seat_number' => $this->seat_number,
            'type' => $this->type,
            'hall' => new HallResource($this->whenLoaded('hall')),
        ];
    }
}
