<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SeatResource;
use App\Models\Seat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

class SeatController extends Controller
{
    #[OA\Get(
        path: '/seats',
        summary: 'Отримати список місць',
        tags: ['Seats'],
        responses: [
            new OA\Response(response: 200, description: 'Успішне отримання списку'),
        ]
    )]
    public function index(): AnonymousResourceCollection
    {
        return SeatResource::collection(Seat::with('hall')->orderBy('hall_id')->orderBy('row_number')->orderBy('seat_number')->paginate(15));
    }

    #[OA\Get(
        path: '/seats/{seat}',
        summary: 'Отримати місце за ідентифікатором',
        tags: ['Seats'],
        parameters: [new OA\Parameter(name: 'seat', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Успішно'),
            new OA\Response(response: 404, description: 'Не знайдено'),
        ]
    )]
    public function show(Seat $seat): SeatResource
    {
        return new SeatResource($seat->load('hall'));
    }

    #[OA\Post(
        path: '/seats',
        summary: 'Створити місце',
        tags: ['Seats'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['hall_id', 'row_number', 'seat_number', 'type'],
                properties: [
                    new OA\Property(property: 'hall_id', type: 'integer', example: 1),
                    new OA\Property(property: 'row_number', type: 'integer', example: 3),
                    new OA\Property(property: 'seat_number', type: 'integer', example: 7),
                    new OA\Property(property: 'type', type: 'string', example: 'vip'),
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
        $this->authorize('create', Seat::class);

        $validated = $request->validate($this->rules($request));

        $seat = Seat::create($validated);
        $seat->load('hall');

        return (new SeatResource($seat))->response()->setStatusCode(201);
    }

    #[OA\Put(
        path: '/seats/{seat}',
        summary: 'Оновити місце',
        tags: ['Seats'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'seat', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['hall_id', 'row_number', 'seat_number', 'type'],
                properties: [
                    new OA\Property(property: 'hall_id', type: 'integer', example: 1),
                    new OA\Property(property: 'row_number', type: 'integer', example: 3),
                    new OA\Property(property: 'seat_number', type: 'integer', example: 7),
                    new OA\Property(property: 'type', type: 'string', example: 'vip'),
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
    public function update(Request $request, Seat $seat): SeatResource
    {
        $this->authorize('update', $seat);

        $validated = $request->validate($this->rules($request, $seat));

        $seat->update($validated);
        $seat->load('hall');

        return new SeatResource($seat);
    }

    #[OA\Delete(
        path: '/seats/{seat}',
        summary: 'Видалити місце',
        tags: ['Seats'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'seat', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 204, description: 'Видалено'),
            new OA\Response(response: 401, description: 'Не авторизовано'),
            new OA\Response(response: 403, description: 'Заборонено'),
            new OA\Response(response: 404, description: 'Не знайдено'),
        ]
    )]
    public function destroy(Seat $seat): Response
    {
        $this->authorize('delete', $seat);

        $seat->delete();

        return response()->noContent();
    }

    private function rules(Request $request, ?Seat $model = null): array
    {
        return [
            'hall_id' => ['required', 'integer', 'exists:halls,id'],
            'row_number' => ['required', 'integer', 'min:1', 'max:50'],
            'seat_number' => [
                'required',
                'integer',
                'min:1',
                'max:50',
                Rule::unique('seats', 'seat_number')
                    ->where('hall_id', $request->input('hall_id'))
                    ->where('row_number', $request->input('row_number'))
                    ->ignore($model?->id),
            ],
            'type' => ['required', 'string', 'in:standard,vip,sofa'],
        ];
    }
}
