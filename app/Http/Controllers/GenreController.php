<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function index(): JsonResponse
    {
        $genres = Genre::query()->withCount('movies')->orderBy('name')->get();

        return response()->json([
            'status' => 'success',
            'data' => $genres,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100', 'unique:genres,name'],
            'slug' => ['required', 'string', 'max:120', 'unique:genres,slug'],
            'description' => ['nullable', 'string'],
        ]);

        $genre = Genre::create($validated);

        return response()->json([
            'status' => 'created',
            'data' => $genre,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $genre = Genre::with('movies:id,title')->findOrFail((int) $id);

        return response()->json([
            'status' => 'success',
            'data' => $genre,
        ], 200);
    }
}
