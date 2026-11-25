@extends('layouts.app')

@section('title', 'Замовлення #'.$order->id)

@section('content')
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h1>Замовлення #{{ $order->id }}</h1>
            <dl class="row">
                <dt class="col-sm-3">Клієнт</dt>
                <dd class="col-sm-9">{{ $order->customer->name }} ({{ $order->customer->email }})</dd>
                <dt class="col-sm-3">Сеанс</dt>
                <dd class="col-sm-9">{{ $order->showtime->movie->title }}, {{ $order->showtime->start_at->format('d.m.Y H:i') }}, {{ $order->showtime->hall->name }}</dd>
                <dt class="col-sm-3">Статус</dt>
                <dd class="col-sm-9">{{ $order->status }}</dd>
                <dt class="col-sm-3">Сума</dt>
                <dd class="col-sm-9">{{ number_format($order->total_amount, 2) }} грн</dd>
            </dl>

            @can('update', $order)
                <a href="{{ route('orders.edit', $order) }}" class="btn btn-warning">Редагувати</a>
            @endcan
            @can('create', App\Models\Ticket::class)
                <a href="{{ route('tickets.create') }}" class="btn btn-primary">Додати квиток</a>
            @endcan
            <a href="{{ route('orders.index') }}" class="btn btn-secondary">До списку</a>
        </div>
    </div>

    <h4>Квитки</h4>
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Місце</th>
                <th>Ціна</th>
                <th>QR-код</th>
                <th>Статус</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($order->tickets as $ticket)
                <tr>
                    <td><a href="{{ route('tickets.show', $ticket) }}">{{ $ticket->id }}</a></td>
                    <td>ряд {{ $ticket->seat->row_number }}, місце {{ $ticket->seat->seat_number }}</td>
                    <td>{{ number_format($ticket->price, 2) }}</td>
                    <td>{{ $ticket->qr_code }}</td>
                    <td>{{ $ticket->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Квитків ще немає.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h4 class="mt-4">Оплата</h4>
    @if ($order->payment)
        <div class="alert alert-success">
            Оплачено {{ number_format($order->payment->amount, 2) }} грн, спосіб: {{ $order->payment->method }},
            транзакція {{ $order->payment->transaction_id }}.
        </div>
    @else
        @can('pay', $order)
            <form action="{{ route('orders.pay', $order) }}" method="POST" class="card card-body shadow-sm" style="max-width: 420px;">
                @csrf

                <x-form.select name="method" label="Спосіб оплати" :options="['card' => 'Картка', 'cash' => 'Готівка', 'apple_pay' => 'Apple Pay']" />

                <button type="submit" class="btn btn-success">Оплатити {{ number_format($order->total_amount, 2) }} грн</button>
            </form>
        @endcan
    @endif
@endsection
