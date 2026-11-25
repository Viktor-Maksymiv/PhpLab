<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\FormOptions;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Order::class);

        $user = $request->user();

        $items = Order::with(['customer', 'showtime.movie', 'payment'])
            ->withCount('tickets')
            ->when(! $user->isStaff(), fn ($query) => $query->where('user_id', $user->id))
            ->orderByDesc('id')
            ->paginate(10);

        return view('orders.index', compact('items'));
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        $order->load(['customer', 'showtime.movie', 'showtime.hall', 'tickets.seat', 'payment']);

        return view('orders.show', compact('order'));
    }

    public function create(): View
    {
        $this->authorize('create', Order::class);

        return view('orders.create', $this->options());
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Order::class);

        $validated = $request->validate($this->rules());

        $user = $request->user();

        $validated['user_id'] = $user->isStaff() ? ($validated['user_id'] ?? $user->id) : $user->id;

        Order::create($validated + [
            'total_amount' => 0,
            'expires_at' => now()->addMinutes(10),
        ]);

        return redirect()->route('orders.index')->with('success', 'Замовлення успішно створено!');
    }

    public function edit(Order $order): View
    {
        $this->authorize('update', $order);

        return view('orders.edit', ['order' => $order] + $this->options());
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        $validated = $request->validate($this->rules());

        if (! $request->user()->isStaff() || empty($validated['user_id'])) {
            unset($validated['user_id']);
        }

        $order->update($validated);

        return redirect()->route('orders.index')->with('success', 'Замовлення успішно оновлено!');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $this->authorize('delete', $order);

        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Замовлення успішно видалено!');
    }

    public function pay(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('pay', $order);

        $validated = $request->validate([
            'method' => ['required', 'string', 'in:card,cash,apple_pay'],
        ]);

        $order->payment()->updateOrCreate([], [
            'amount' => $order->total_amount,
            'method' => $validated['method'],
            'transaction_id' => 'TX-'.Str::upper(Str::random(10)),
            'paid_at' => now(),
            'status' => 'success',
        ]);

        $order->update(['status' => 'paid']);

        return redirect()->route('orders.show', $order)->with('success', 'Оплату успішно проведено!');
    }

    private function options(): array
    {
        return ['users' => FormOptions::users(), 'showtimes' => FormOptions::showtimes()];
    }

    private function rules(): array
    {
        return [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'showtime_id' => ['required', 'integer', 'exists:showtimes,id'],
            'status' => ['required', 'string', 'in:pending,paid,cancelled'],
        ];
    }
}
