<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShowtimeResource;
use App\Models\Showtime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

class ShowtimeController extends Controller
{
    #[OA\Get(
        path: '/showtimes',
        summary: 'Отримати список сеансів',
        tags: ['Showtimes'],
        responses: [
            new OA\Response(response: 200, description: 'Успішне отримання списку'),
        ]
    )]
    public function index(): AnonymousResourceCollection
    {
        return ShowtimeResource::collection(Showtime::with(['movie', 'hall'])->orderBy('start_at')->paginate(15));
    }

    #[OA\Get(
        path: '/showtimes/{showtime}',
        summary: 'Отримати сеанс за ідентифікатором',
        tags: ['Showtimes'],
        parameters: [new OA\Parameter(name: 'showtime', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Успішно'),
            new OA\Response(response: 404, description: 'Не знайдено'),
        ]
    )]
    public function show(Showtime $showtime): ShowtimeResource
    {
        return new ShowtimeResource($showtime->load(['movie', 'hall']));
    }

    #[OA\Post(
        path: '/showtimes',
        summary: 'Створити сеанс',
        tags: ['Showtimes'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['movie_id', 'hall_id', 'start_at', 'base_price', 'status'],
                properties: [
                    new OA\Property(property: 'movie_id', type: 'integer', example: 1),
                    new OA\Property(property: 'hall_id', type: 'integer', example: 1),
                    new OA\Property(property: 'start_at', type: 'string', example: '2026-12-01 18:00:00'),
                    new OA\Property(property: 'base_price', type: 'number', example: 180.5),
                    new OA\Property(property: 'status', type: 'string', example: 'scheduled'),
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
        $this->authorize('create', Showtime::class);

        $this->normalizeStartAt($request);

        $validated = $request->validate($this->rules($request));

        $showtime = Showtime::create($validated);
        $showtime->load(['movie', 'hall']);

        return (new ShowtimeResource($showtime))->response()->setStatusCode(201);
    }

    #[OA\Put(
        path: '/showtimes/{showtime}',
        summary: 'Оновити сеанс',
        tags: ['Showtimes'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'showtime', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['movie_id', 'hall_id', 'start_at', 'base_price', 'status'],
                properties: [
                    new OA\Property(property: 'movie_id', type: 'integer', example: 1),
                    new OA\Property(property: 'hall_id', type: 'integer', example: 1),
                    new OA\Property(property: 'start_at', type: 'string', example: '2026-12-01 18:00:00'),
                    new OA\Property(property: 'base_price', type: 'number', example: 180.5),
                    new OA\Property(property: 'status', type: 'string', example: 'scheduled'),
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
    public function update(Request $request, Showtime $showtime): ShowtimeResource
    {
        $this->authorize('update', $showtime);

        $this->normalizeStartAt($request);

        $validated = $request->validate($this->rules($request, $showtime));

        $showtime->update($validated);
        $showtime->load(['movie', 'hall']);

        return new ShowtimeResource($showtime);
    }

    #[OA\Delete(
        path: '/showtimes/{showtime}',
        summary: 'Видалити сеанс',
        tags: ['Showtimes'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'showtime', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 204, description: 'Видалено'),
            new OA\Response(response: 401, description: 'Не авторизовано'),
            new OA\Response(response: 403, description: 'Заборонено'),
            new OA\Response(response: 404, description: 'Не знайдено'),
        ]
    )]
    public function destroy(Showtime $showtime): Response
    {
        $this->authorize('delete', $showtime);

        $showtime->delete();

        return response()->noContent();
    }

    private function normalizeStartAt(Request $request): void
    {
        $value = $request->input('start_at');

        if (is_string($value) && strtotime($value) !== false) {
            $request->merge(['start_at' => date('Y-m-d H:i:s', strtotime($value))]);
        }
    }

    private function rules(Request $request, ?Showtime $model = null): array
    {
        return [
            'movie_id' => ['required', 'integer', 'exists:movies,id'],
            'hall_id' => ['required', 'integer', 'exists:halls,id'],
            'start_at' => [
                'required',
                'date',
                Rule::unique('showtimes', 'start_at')
                    ->where('hall_id', $request->input('hall_id'))
                    ->ignore($model?->id),
            ],
            'base_price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:scheduled,cancelled,finished'],
        ];
    }
}
