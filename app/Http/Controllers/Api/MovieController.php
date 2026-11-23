<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class MovieController extends Controller
{
    public function index(): JsonResponse
    {
        $movies = Movie::query()
            ->with('genres:id,name')
            ->orderByDesc('release_date')
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $movies,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:1', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:600'],
            'age_rating' => ['required', 'string', 'in:0+,6+,12+,16+,18+'],
            'release_date' => ['required', 'date'],
            'poster_url' => ['nullable', 'string', 'max:255'],
            'genres' => ['nullable', 'array'],
            'genres.*' => ['integer', 'exists:genres,id'],
        ]);

        $movie = Movie::create(Arr::except($validated, ['genres']));

        $movie->genres()->sync($validated['genres'] ?? []);
        $movie->load('genres:id,name');

        return response()->json([
            'status' => 'created',
            'data' => $movie,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $movie = Movie::with(['genres:id,name', 'showtimes:id,movie_id,hall_id,start_at,base_price'])
            ->findOrFail((int) $id);

        return response()->json([
            'status' => 'success',
            'data' => $movie,
        ], 200);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $movie = Movie::findOrFail((int) $id);

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'min:1', 'max:255'],
            'duration_minutes' => ['sometimes', 'integer', 'min:1', 'max:600'],
            'age_rating' => ['sometimes', 'string', 'in:0+,6+,12+,16+,18+'],
            'release_date' => ['sometimes', 'date'],
            'poster_url' => ['nullable', 'string', 'max:255'],
            'genres' => ['sometimes', 'array'],
            'genres.*' => ['integer', 'exists:genres,id'],
        ]);

        $movie->update(Arr::except($validated, ['genres']));

        if (array_key_exists('genres', $validated)) {
            $movie->genres()->sync($validated['genres']);
        }

        $movie->load('genres:id,name');

        return response()->json([
            'status' => 'success',
            'data' => $movie,
        ], 200);
    }
}
