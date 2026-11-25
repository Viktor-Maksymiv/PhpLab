<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Ticket;
use App\Support\FormOptions;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Ticket::class);

        $user = $request->user();

        $items = Ticket::with(['showtime.movie', 'seat.hall'])
            ->when(! $user->isStaff(), fn ($query) => $query->where('user_id', $user->id))
            ->orderByDesc('id')
            ->paginate(10);

        return view('tickets.index', compact('items'));
    }

    public function show(Ticket $ticket): View
    {
        $this->authorize('view', $ticket);

        $ticket->load(['order.customer', 'showtime.movie', 'showtime.hall', 'seat']);

        return view('tickets.show', compact('ticket'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Ticket::class);

        return view('tickets.create', $this->options($request));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Ticket::class);

        $validated = $request->validate($this->rules($request));

        $ticket = Ticket::create($validated + [
            'qr_code' => 'QR-'.Str::upper(Str::random(8)),
            'user_id' => $request->user()->id,
        ]);

        $this->syncOrderTotal((int) $ticket->order_id);

        return redirect()->route('tickets.index')->with('success', 'Квиток успішно створено!');
    }

    public function edit(Request $request, Ticket $ticket): View
    {
        $this->authorize('update', $ticket);

        return view('tickets.edit', ['ticket' => $ticket] + $this->options($request));
    }

    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('update', $ticket);

        $validated = $request->validate($this->rules($request, $ticket));

        $oldOrderId = (int) $ticket->order_id;

        $ticket->update($validated);

        $this->syncOrderTotal($oldOrderId);
        $this->syncOrderTotal((int) $ticket->order_id);

        return redirect()->route('tickets.index')->with('success', 'Квиток успішно оновлено!');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        $this->authorize('delete', $ticket);

        $orderId = (int) $ticket->order_id;

        $ticket->delete();

        $this->syncOrderTotal($orderId);

        return redirect()->route('tickets.index')->with('success', 'Квиток успішно видалено!');
    }

    private function options(Request $request): array
    {
        return [
            'orders' => FormOptions::orders($request->user()),
            'showtimes' => FormOptions::showtimes(),
            'seats' => FormOptions::seats(),
        ];
    }

    private function syncOrderTotal(int $orderId): void
    {
        Order::query()->whereKey($orderId)->update([
            'total_amount' => Ticket::query()->where('order_id', $orderId)->sum('price'),
        ]);
    }

    private function rules(Request $request, ?Ticket $ticket = null): array
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
                    ->ignore($ticket?->id),
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:reserved,paid,used,refunded'],
        ];
    }
}
