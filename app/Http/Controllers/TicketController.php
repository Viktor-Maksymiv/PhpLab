<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    private array $items = [
        ['id' => 1, 'showtime_id' => 101, 'seat_id' => 15, 'price' => 180.0, 'status' => 'paid'],
        ['id' => 2, 'showtime_id' => 101, 'seat_id' => 16, 'price' => 180.0, 'status' => 'reserved'],
        ['id' => 3, 'showtime_id' => 102, 'seat_id' => 7, 'price' => 220.0, 'status' => 'paid'],
    ];

    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->items,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'showtime_id' => ['required', 'integer', 'min:1'],
            'seat_id' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:reserved,paid'],
        ]);

        $newItem = [
            'id' => count($this->items) + 1,
            'showtime_id' => (int) $validated['showtime_id'],
            'seat_id' => (int) $validated['seat_id'],
            'price' => (float) $validated['price'],
            'status' => $validated['status'],
            'qr_code' => 'QR-'.Str::upper(Str::random(8)),
        ];

        $this->items[] = $newItem;

        return response()->json([
            'status' => 'created',
            'data' => $newItem,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $itemId = (int) $id;

        foreach ($this->items as $item) {
            if ($item['id'] === $itemId) {
                return response()->json(['status' => 'success', 'data' => $item], 200);
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Квиток із зазначеним ідентифікатором не знайдено.',
        ], 404);
    }
}
