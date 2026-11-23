<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $orders = Order::query()
            ->with(['customer:id,name,email', 'showtime.movie:id,title', 'tickets', 'payment'])
            ->latest('id')
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $orders,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'showtime_id' => ['required', 'integer', 'exists:showtimes,id'],
        ]);

        $order = Order::create($validated + [
            'total_amount' => 0,
            'status' => 'pending',
            'expires_at' => now()->addMinutes(10),
        ]);

        $order->load(['customer:id,name,email', 'showtime.movie:id,title']);

        return response()->json([
            'status' => 'created',
            'data' => $order,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $order = Order::with(['customer:id,name,email', 'showtime.movie:id,title', 'tickets', 'payment'])
            ->findOrFail((int) $id);

        return response()->json([
            'status' => 'success',
            'data' => $order,
        ], 200);
    }

    public function pay(Request $request, string $id): JsonResponse
    {
        $order = Order::findOrFail((int) $id);

        $validated = $request->validate([
            'method' => ['required', 'string', 'in:card,cash,apple_pay'],
        ]);

        $order->payment()->updateOrCreate([], [
            'amount' => $order->total_amount,
            'method' => $validated['method'],
            'transaction_id' => 'TX-'.Str::upper(Str::random(10)),
            'paid_at' => now(),
            'status' => 'success',
        ]);

        $order->update(['status' => 'paid']);
        $order->load('payment');

        return response()->json([
            'status' => 'created',
            'data' => $order,
        ], 201);
    }
}
