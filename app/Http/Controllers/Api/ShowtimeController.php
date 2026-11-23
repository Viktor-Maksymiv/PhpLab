<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Showtime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShowtimeController extends Controller
{
    public function index(): JsonResponse
    {
        $showtimes = Showtime::query()
            ->with(['movie:id,title', 'hall:id,name,format'])
            ->orderBy('start_at')
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $showtimes,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'movie_id' => ['required', 'integer', 'exists:movies,id'],
            'hall_id' => ['required', 'integer', 'exists:halls,id'],
            'start_at' => [
                'required',
                'date',
                'after:now',
                Rule::unique('showtimes', 'start_at')->where('hall_id', $request->input('hall_id')),
            ],
            'base_price' => ['required', 'numeric', 'min:0'],
        ]);

        $showtime = Showtime::create($validated + ['status' => 'scheduled']);
        $showtime->load(['movie:id,title', 'hall:id,name,format']);

        return response()->json([
            'status' => 'created',
            'data' => $showtime,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $showtime = Showtime::with(['movie.genres:id,name', 'hall'])->findOrFail((int) $id);

        return response()->json([
            'status' => 'success',
            'data' => $showtime,
        ], 200);
    }
}
