@extends('layouts.app')

@section('title', 'Квиток #'.$ticket->id)

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <h1>Квиток #{{ $ticket->id }}</h1>
            <dl class="row">
                <dt class="col-sm-3">Фільм</dt>
                <dd class="col-sm-9">{{ $ticket->showtime->movie->title }}</dd>
                <dt class="col-sm-3">Сеанс</dt>
                <dd class="col-sm-9">{{ $ticket->showtime->start_at->format('d.m.Y H:i') }}, {{ $ticket->showtime->hall->name }}</dd>
                <dt class="col-sm-3">Місце</dt>
                <dd class="col-sm-9">ряд {{ $ticket->seat->row_number }}, місце {{ $ticket->seat->seat_number }}</dd>
                <dt class="col-sm-3">Замовлення</dt>
                <dd class="col-sm-9"><a href="{{ route('orders.show', $ticket->order) }}">#{{ $ticket->order->id }}</a>, {{ $ticket->order->customer->name }}</dd>
                <dt class="col-sm-3">Ціна</dt>
                <dd class="col-sm-9">{{ number_format($ticket->price, 2) }} грн</dd>
                <dt class="col-sm-3">QR-код</dt>
                <dd class="col-sm-9"><code>{{ $ticket->qr_code }}</code></dd>
                <dt class="col-sm-3">Статус</dt>
                <dd class="col-sm-9">{{ $ticket->status }}</dd>
            </dl>

            <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-warning">Редагувати</a>
            <a href="{{ route('tickets.index') }}" class="btn btn-secondary">До списку</a>
        </div>
    </div>
@endsection
