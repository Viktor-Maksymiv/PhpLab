<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class OrderController extends Controller
{
    #[OA\Get(
        path: '/orders',
        summary: 'Отримати список замовлень',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Успішне отримання списку'),
            new OA\Response(response: 401, description: 'Не авторизовано'),
        ]
    )]
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Order::class);

        $user = $request->user();

        $orders = Order::with(['showtime.movie', 'tickets', 'payment'])
            ->when(! $user->isStaff(), fn ($query) => $query->where('user_id', $user->id))
            ->orderByDesc('id')
            ->paginate(10);

        return OrderResource::collection($orders);
    }

    #[OA\Get(
        path: '/orders/{order}',
        summary: 'Отримати замовлення за ідентифікатором',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'order', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Успішно'),
            new OA\Response(response: 404, description: 'Не знайдено'),
            new OA\Response(response: 401, description: 'Не авторизовано'),
            new OA\Response(response: 403, description: 'Заборонено'),
        ]
    )]
    public function show(Order $order): OrderResource
    {
        $this->authorize('view', $order);

        return new OrderResource($order->load(['showtime.movie', 'tickets', 'payment']));
    }

    #[OA\Post(
        path: '/orders',
        summary: 'Створити замовлення',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['showtime_id', 'status'],
                properties: [
                    new OA\Property(property: 'showtime_id', type: 'integer', example: 1),
                    new OA\Property(property: 'status', type: 'string', example: 'pending'),
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
        $this->authorize('create', Order::class);

        $validated = $request->validate($this->rules($request));

        $validated['user_id'] = $request->user()->id;

        $order = Order::create($validated + ['total_amount' => 0, 'expires_at' => now()->addMinutes(10)]);
        $order->load(['showtime.movie', 'tickets', 'payment']);

        return (new OrderResource($order))->response()->setStatusCode(201);
    }

    #[OA\Put(
        path: '/orders/{order}',
        summary: 'Оновити замовлення',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'order', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['showtime_id', 'status'],
                properties: [
                    new OA\Property(property: 'showtime_id', type: 'integer', example: 1),
                    new OA\Property(property: 'status', type: 'string', example: 'pending'),
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
    public function update(Request $request, Order $order): OrderResource
    {
        $this->authorize('update', $order);

        $validated = $request->validate($this->rules($request, $order));

        $order->update($validated);
        $order->load(['showtime.movie', 'tickets', 'payment']);

        return new OrderResource($order);
    }

    #[OA\Delete(
        path: '/orders/{order}',
        summary: 'Видалити замовлення',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'order', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 204, description: 'Видалено'),
            new OA\Response(response: 401, description: 'Не авторизовано'),
            new OA\Response(response: 403, description: 'Заборонено'),
            new OA\Response(response: 404, description: 'Не знайдено'),
        ]
    )]
    public function destroy(Order $order): Response
    {
        $this->authorize('delete', $order);

        $order->delete();

        return response()->noContent();
    }

    private function rules(Request $request, ?Order $model = null): array
    {
        return [
            'showtime_id' => ['required', 'integer', 'exists:showtimes,id'],
            'status' => ['required', 'string', 'in:pending,paid,cancelled'],
        ];
    }
}
