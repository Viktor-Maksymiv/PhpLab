<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    public function index(): JsonResponse
    {
        $tickets = Ticket::query()
            ->with(['showtime.movie:id,title', 'seat:id,hall_id,row_number,seat_number'])
            ->orderBy('id')
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $tickets,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'showtime_id' => ['required', 'integer', 'exists:showtimes,id'],
            'seat_id' => [
                'required',
                'integer',
                'exists:seats,id',
                Rule::unique('tickets', 'seat_id')->where('showtime_id', $request->input('showtime_id')),
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:reserved,paid'],
        ]);

        $ticket = Ticket::create($validated + ['qr_code' => 'QR-'.Str::upper(Str::random(8))]);

        Order::query()->whereKey($ticket->order_id)->increment('total_amount', (float) $ticket->price);

        $ticket->load(['showtime.movie:id,title', 'seat:id,hall_id,row_number,seat_number']);

        return response()->json([
            'status' => 'created',
            'data' => $ticket,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $ticket = Ticket::with(['order', 'showtime.movie', 'seat'])->findOrFail((int) $id);

        return response()->json([
            'status' => 'success',
            'data' => $ticket,
        ], 200);
    }
}
