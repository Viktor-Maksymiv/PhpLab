<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'owner_id' => $this->user_id,
            'total_amount' => $this->total_amount,
            'status' => $this->status,
            'expires_at' => $this->expires_at?->toDateTimeString(),
            'showtime' => new ShowtimeResource($this->whenLoaded('showtime')),
            'tickets' => TicketResource::collection($this->whenLoaded('tickets')),
            'payment' => $this->whenLoaded('payment'),
        ];
    }
}
