<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\HallResource;
use App\Models\Hall;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

class HallController extends Controller
{
    #[OA\Get(
        path: '/halls',
        summary: 'Отримати список залів',
        tags: ['Halls'],
        responses: [
            new OA\Response(response: 200, description: 'Успішне отримання списку'),
        ]
    )]
    public function index(): AnonymousResourceCollection
    {
        return HallResource::collection(Hall::withCount('seats')->orderBy('name')->paginate(10));
    }

    #[OA\Get(
        path: '/halls/{hall}',
        summary: 'Отримати зал за ідентифікатором',
        tags: ['Halls'],
        parameters: [new OA\Parameter(name: 'hall', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Успішно'),
            new OA\Response(response: 404, description: 'Не знайдено'),
        ]
    )]
    public function show(Hall $hall): HallResource
    {
        return new HallResource($hall->loadCount('seats'));
    }

    #[OA\Post(
        path: '/halls',
        summary: 'Створити зал',
        tags: ['Halls'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'capacity', 'format', 'rows_count'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Зал 1'),
                    new OA\Property(property: 'capacity', type: 'integer', example: 40),
                    new OA\Property(property: 'format', type: 'string', example: 'IMAX'),
                    new OA\Property(property: 'rows_count', type: 'integer', example: 5),
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
        $this->authorize('create', Hall::class);

        $validated = $request->validate($this->rules($request));

        $hall = Hall::create($validated);

        return (new HallResource($hall))->response()->setStatusCode(201);
    }

    #[OA\Put(
        path: '/halls/{hall}',
        summary: 'Оновити зал',
        tags: ['Halls'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'hall', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'capacity', 'format', 'rows_count'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Зал 1'),
                    new OA\Property(property: 'capacity', type: 'integer', example: 40),
                    new OA\Property(property: 'format', type: 'string', example: 'IMAX'),
                    new OA\Property(property: 'rows_count', type: 'integer', example: 5),
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
    public function update(Request $request, Hall $hall): HallResource
    {
        $this->authorize('update', $hall);

        $validated = $request->validate($this->rules($request, $hall));

        $hall->update($validated);

        return new HallResource($hall);
    }

    #[OA\Delete(
        path: '/halls/{hall}',
        summary: 'Видалити зал',
        tags: ['Halls'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'hall', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 204, description: 'Видалено'),
            new OA\Response(response: 401, description: 'Не авторизовано'),
            new OA\Response(response: 403, description: 'Заборонено'),
            new OA\Response(response: 404, description: 'Не знайдено'),
        ]
    )]
    public function destroy(Hall $hall): Response
    {
        $this->authorize('delete', $hall);

        $hall->delete();

        return response()->noContent();
    }

    private function rules(Request $request, ?Hall $model = null): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:50', Rule::unique('halls', 'name')->ignore($model?->id)],
            'capacity' => ['required', 'integer', 'min:1', 'max:1000'],
            'format' => ['required', 'string', 'in:2D,3D,IMAX,4DX'],
            'rows_count' => ['required', 'integer', 'min:1', 'max:50'],
        ];
    }
}
