<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;

class SalesReportController extends Controller
{
    public function summary(): JsonResponse
    {
        $paid = Ticket::query()->where('status', 'paid');

        return response()->json([
            'status' => 'success',
            'data' => [
                'tickets_total' => Ticket::query()->count(),
                'tickets_paid' => $paid->count(),
                'revenue' => (float) $paid->sum('price'),
            ],
        ], 200);
    }
}
