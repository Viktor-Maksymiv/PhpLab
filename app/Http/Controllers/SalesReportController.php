<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class SalesReportController extends Controller
{
    private array $tickets = [
        ['id' => 1, 'price' => 180.0, 'status' => 'paid'],
        ['id' => 2, 'price' => 180.0, 'status' => 'reserved'],
        ['id' => 3, 'price' => 220.0, 'status' => 'paid'],
    ];

    public function summary(): JsonResponse
    {
        $paid = array_filter($this->tickets, fn (array $ticket) => $ticket['status'] === 'paid');

        return response()->json([
            'status' => 'success',
            'data' => [
                'tickets_total' => count($this->tickets),
                'tickets_paid' => count($paid),
                'revenue' => (float) array_sum(array_column($paid, 'price')),
            ],
        ], 200);
    }
}
