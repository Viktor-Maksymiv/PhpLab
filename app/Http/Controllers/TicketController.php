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
    public function index(): View
    {
        $items = Ticket::with(['showtime.movie', 'seat.hall'])->orderByDesc('id')->paginate(10);

        return view('tickets.index', compact('items'));
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load(['order.customer', 'showtime.movie', 'showtime.hall', 'seat']);

        return view('tickets.show', compact('ticket'));
    }

    public function create(): View
    {
        return view('tickets.create', $this->options());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules($request));

        $ticket = Ticket::create($validated + ['qr_code' => 'QR-'.Str::upper(Str::random(8))]);

        $this->syncOrderTotal((int) $ticket->order_id);

        return redirect()->route('tickets.index')->with('success', 'Квиток успішно створено!');
    }

    public function edit(Ticket $ticket): View
    {
        return view('tickets.edit', ['ticket' => $ticket] + $this->options());
    }

    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validate($this->rules($request, $ticket));

        $oldOrderId = (int) $ticket->order_id;

        $ticket->update($validated);

        $this->syncOrderTotal($oldOrderId);
        $this->syncOrderTotal((int) $ticket->order_id);

        return redirect()->route('tickets.index')->with('success', 'Квиток успішно оновлено!');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        $orderId = (int) $ticket->order_id;

        $ticket->delete();

        $this->syncOrderTotal($orderId);

        return redirect()->route('tickets.index')->with('success', 'Квиток успішно видалено!');
    }

    private function options(): array
    {
        return [
            'orders' => FormOptions::orders(),
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
        return [
            'order_id' => ['required', 'integer', 'exists:orders,id'],
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
