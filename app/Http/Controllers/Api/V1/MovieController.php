<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\MovieResource;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use OpenApi\Attributes as OA;

class MovieController extends Controller
{
    #[OA\Get(
        path: '/movies',
        summary: 'Отримати список фільмів',
        tags: ['Movies'],
        responses: [
            new OA\Response(response: 200, description: 'Успішне отримання списку'),
        ]
    )]
    public function index(): AnonymousResourceCollection
    {
        return MovieResource::collection(Movie::with('genres')->orderByDesc('id')->paginate(10));
    }

    #[OA\Get(
        path: '/movies/{movie}',
        summary: 'Отримати фільм за ідентифікатором',
        tags: ['Movies'],
        parameters: [new OA\Parameter(name: 'movie', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Успішно'),
            new OA\Response(response: 404, description: 'Не знайдено'),
        ]
    )]
    public function show(Movie $movie): MovieResource
    {
        return new MovieResource($movie->load('genres'));
    }

    #[OA\Post(
        path: '/movies',
        summary: 'Створити фільм',
        tags: ['Movies'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title', 'duration_minutes', 'age_rating', 'release_date'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'Дюна: Частина друга'),
                    new OA\Property(property: 'duration_minutes', type: 'integer', example: 166),
                    new OA\Property(property: 'age_rating', type: 'string', example: '12+'),
                    new OA\Property(property: 'release_date', type: 'string', example: '2024-02-29'),
                    new OA\Property(property: 'genres', type: 'array', items: new OA\Items(type: 'integer'), example: [1, 2]),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Створено'),
            new OA\Response(response: 401, description: 'Не авторизовано'),
            new OA\Response(response: 403, description: 'Заборонено'),
            new OA\Response(response: 422, description: 'Помилка валідації'),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Movie::class);

        $validated = $request->validate($this->rules($request));

        $movie = Movie::create(Arr::except($validated, ['genres']));
        $movie->genres()->sync($validated['genres'] ?? []);
        $movie->load('genres');

        return (new MovieResource($movie))->response()->setStatusCode(201);
    }

    #[OA\Put(
        path: '/movies/{movie}',
        summary: 'Оновити фільм',
        tags: ['Movies'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'movie', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title', 'duration_minutes', 'age_rating', 'release_date'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'Дюна: Частина друга'),
                    new OA\Property(property: 'duration_minutes', type: 'integer', example: 166),
                    new OA\Property(property: 'age_rating', type: 'string', example: '12+'),
                    new OA\Property(property: 'release_date', type: 'string', example: '2024-02-29'),
                    new OA\Property(property: 'genres', type: 'array', items: new OA\Items(type: 'integer'), example: [1, 2]),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Оновлено'),
            new OA\Response(response: 401, description: 'Не авторизовано'),
            new OA\Response(response: 403, description: 'Заборонено'),
            new OA\Response(response: 404, description: 'Не знайдено'),
            new OA\Response(response: 422, description: 'Помилка валідації'),
        ]
    )]
    public function update(Request $request, Movie $movie): MovieResource
    {
        $this->authorize('update', $movie);

        $validated = $request->validate($this->rules($request, $movie));

        $movie->update(Arr::except($validated, ['genres']));

        if (array_key_exists('genres', $validated)) {
            $movie->genres()->sync($validated['genres']);
        }

        $movie->load('genres');

        return new MovieResource($movie);
    }

    #[OA\Delete(
        path: '/movies/{movie}',
        summary: 'Видалити фільм',
        tags: ['Movies'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'movie', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 204, description: 'Видалено'),
            new OA\Response(response: 401, description: 'Не авторизовано'),
            new OA\Response(response: 403, description: 'Заборонено'),
            new OA\Response(response: 404, description: 'Не знайдено'),
        ]
    )]
    public function destroy(Movie $movie): Response
    {
        $this->authorize('delete', $movie);

        $movie->delete();

        return response()->noContent();
    }

    private function rules(Request $request, ?Movie $model = null): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:600'],
            'age_rating' => ['required', 'string', 'in:0+,6+,12+,16+,18+'],
            'release_date' => ['required', 'date'],
            'poster_url' => ['nullable', 'string', 'max:255'],
            'genres' => ['nullable', 'array'],
            'genres.*' => ['integer', 'exists:genres,id'],
        ];
    }
}
