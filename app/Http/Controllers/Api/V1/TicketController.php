<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketResource;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

class TicketController extends Controller
{
    #[OA\Get(
        path: '/tickets',
        summary: 'Отримати список квитків',
        tags: ['Tickets'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Успішне отримання списку'),
            new OA\Response(response: 401, description: 'Не авторизовано'),
        ]
    )]
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Ticket::class);

        $user = $request->user();

        $tickets = Ticket::with(['showtime.movie', 'seat'])
            ->when(! $user->isStaff(), fn ($query) => $query->where('user_id', $user->id))
            ->orderByDesc('id')
            ->paginate(10);

        return TicketResource::collection($tickets);
    }

    #[OA\Get(
        path: '/tickets/{ticket}',
        summary: 'Отримати квиток за ідентифікатором',
        tags: ['Tickets'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'ticket', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Успішно'),
            new OA\Response(response: 404, description: 'Не знайдено'),
            new OA\Response(response: 401, description: 'Не авторизовано'),
            new OA\Response(response: 403, description: 'Заборонено'),
        ]
    )]
    public function show(Ticket $ticket): TicketResource
    {
        $this->authorize('view', $ticket);

        return new TicketResource($ticket->load(['showtime.movie', 'seat']));
    }

    #[OA\Post(
        path: '/tickets',
        summary: 'Створити квиток',
        tags: ['Tickets'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['order_id', 'showtime_id', 'seat_id', 'price', 'status'],
                properties: [
                    new OA\Property(property: 'order_id', type: 'integer', example: 1),
                    new OA\Property(property: 'showtime_id', type: 'integer', example: 1),
                    new OA\Property(property: 'seat_id', type: 'integer', example: 12),
                    new OA\Property(property: 'price', type: 'number', example: 180.5),
                    new OA\Property(property: 'status', type: 'string', example: 'reserved'),
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
        $this->authorize('create', Ticket::class);

        $validated = $request->validate($this->rules($request));

        $ticket = Ticket::create($validated + [
            'qr_code' => 'QR-'.Str::upper(Str::random(8)),
            'user_id' => $request->user()->id,
        ]);

        $this->syncOrderTotal((int) $ticket->order_id);

        $ticket->load(['showtime.movie', 'seat']);

        return (new TicketResource($ticket))->response()->setStatusCode(201);
    }

    #[OA\Put(
        path: '/tickets/{ticket}',
        summary: 'Оновити квиток',
        tags: ['Tickets'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'ticket', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['order_id', 'showtime_id', 'seat_id', 'price', 'status'],
                properties: [
                    new OA\Property(property: 'order_id', type: 'integer', example: 1),
                    new OA\Property(property: 'showtime_id', type: 'integer', example: 1),
                    new OA\Property(property: 'seat_id', type: 'integer', example: 12),
                    new OA\Property(property: 'price', type: 'number', example: 180.5),
                    new OA\Property(property: 'status', type: 'string', example: 'reserved'),
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
    public function update(Request $request, Ticket $ticket): TicketResource
    {
        $this->authorize('update', $ticket);

        $validated = $request->validate($this->rules($request, $ticket));

        $oldOrderId = (int) $ticket->order_id;

        $ticket->update($validated);

        $this->syncOrderTotal($oldOrderId);
        $this->syncOrderTotal((int) $ticket->order_id);

        $ticket->load(['showtime.movie', 'seat']);

        return new TicketResource($ticket);
    }

    #[OA\Delete(
        path: '/tickets/{ticket}',
        summary: 'Видалити квиток',
        tags: ['Tickets'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'ticket', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 204, description: 'Видалено'),
            new OA\Response(response: 401, description: 'Не авторизовано'),
            new OA\Response(response: 403, description: 'Заборонено'),
            new OA\Response(response: 404, description: 'Не знайдено'),
        ]
    )]
    public function destroy(Ticket $ticket): Response
    {
        $this->authorize('delete', $ticket);

        $ticket->delete();

        return response()->noContent();
    }

    private function syncOrderTotal(int $orderId): void
    {
        Order::query()->whereKey($orderId)->update([
            'total_amount' => Ticket::query()->where('order_id', $orderId)->sum('price'),
        ]);
    }

    private function rules(Request $request, ?Ticket $model = null): array
    {
        $user = $request->user();

        return [
            'order_id' => [
                'required',
                'integer',
                Rule::exists('orders', 'id')->where(function ($query) use ($user) {
                    if (! $user->isStaff()) {
                        $query->where('user_id', $user->id);
                    }
                }),
            ],
            'showtime_id' => ['required', 'integer', 'exists:showtimes,id'],
            'seat_id' => [
                'required',
                'integer',
                'exists:seats,id',
                Rule::unique('tickets', 'seat_id')
                    ->where('showtime_id', $request->input('showtime_id'))
                    ->ignore($model?->id),
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:reserved,paid,used,refunded'],
        ];
    }
}
