<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hall;
use Illuminate\Http\JsonResponse;

class HallController extends Controller
{
    public function index(): JsonResponse
    {
        $halls = Hall::query()->withCount('seats')->orderBy('name')->get();

        return response()->json([
            'status' => 'success',
            'data' => $halls,
        ], 200);
    }

    public function show(string $id): JsonResponse
    {
        $hall = Hall::with('seats')->findOrFail((int) $id);

        return response()->json([
            'status' => 'success',
            'data' => $hall,
        ], 200);
    }
}
