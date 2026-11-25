<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\GenreResource;
use App\Models\Genre;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

class GenreController extends Controller
{
    #[OA\Get(
        path: '/genres',
        summary: 'Отримати список жанрів',
        tags: ['Genres'],
        responses: [
            new OA\Response(response: 200, description: 'Успішне отримання списку'),
        ]
    )]
    public function index(): AnonymousResourceCollection
    {
        return GenreResource::collection(Genre::withCount('movies')->orderBy('name')->paginate(10));
    }

    #[OA\Get(
        path: '/genres/{genre}',
        summary: 'Отримати жанр за ідентифікатором',
        tags: ['Genres'],
        parameters: [new OA\Parameter(name: 'genre', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Успішно'),
            new OA\Response(response: 404, description: 'Не знайдено'),
        ]
    )]
    public function show(Genre $genre): GenreResource
    {
        return new GenreResource($genre->loadCount('movies'));
    }

    #[OA\Post(
        path: '/genres',
        summary: 'Створити жанр',
        tags: ['Genres'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'slug'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Драма'),
                    new OA\Property(property: 'slug', type: 'string', example: 'drama'),
                    new OA\Property(property: 'description', type: 'string', example: 'Серйозні історії'),
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
        $this->authorize('create', Genre::class);

        $validated = $request->validate($this->rules($request));

        $genre = Genre::create($validated);

        return (new GenreResource($genre))->response()->setStatusCode(201);
    }

    #[OA\Put(
        path: '/genres/{genre}',
        summary: 'Оновити жанр',
        tags: ['Genres'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'genre', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'slug'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Драма'),
                    new OA\Property(property: 'slug', type: 'string', example: 'drama'),
                    new OA\Property(property: 'description', type: 'string', example: 'Серйозні історії'),
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
    public function update(Request $request, Genre $genre): GenreResource
    {
        $this->authorize('update', $genre);

        $validated = $request->validate($this->rules($request, $genre));

        $genre->update($validated);

        return new GenreResource($genre);
    }

    #[OA\Delete(
        path: '/genres/{genre}',
        summary: 'Видалити жанр',
        tags: ['Genres'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'genre', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 204, description: 'Видалено'),
            new OA\Response(response: 401, description: 'Не авторизовано'),
            new OA\Response(response: 403, description: 'Заборонено'),
            new OA\Response(response: 404, description: 'Не знайдено'),
        ]
    )]
    public function destroy(Genre $genre): Response
    {
        $this->authorize('delete', $genre);

        $genre->delete();

        return response()->noContent();
    }

    private function rules(Request $request, ?Genre $model = null): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100', Rule::unique('genres', 'name')->ignore($model?->id)],
            'slug' => ['required', 'string', 'max:120', Rule::unique('genres', 'slug')->ignore($model?->id)],
            'description' => ['nullable', 'string'],
        ];
    }
}
