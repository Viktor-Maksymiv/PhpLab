<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'owner_id' => $this->user_id,
            'price' => $this->price,
            'qr_code' => $this->qr_code,
            'status' => $this->status,
            'showtime' => new ShowtimeResource($this->whenLoaded('showtime')),
            'seat' => new SeatResource($this->whenLoaded('seat')),
        ];
    }
}
