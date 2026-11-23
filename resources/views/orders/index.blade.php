@extends('layouts.app')

@section('title', 'Замовлення')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Замовлення</h1>
        <a href="{{ route('orders.create') }}" class="btn btn-primary">Додати замовлення</a>
    </div>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Клієнт</th>
                <th>Сеанс</th>
                <th>Квитків</th>
                <th>Сума</th>
                <th>Статус</th>
                <th>Платіж</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->customer->name }}</td>
                    <td>{{ $item->showtime->movie->title }}, {{ $item->showtime->start_at->format('d.m H:i') }}</td>
                    <td>{{ $item->tickets_count }}</td>
                    <td>{{ number_format($item->total_amount, 2) }}</td>
                    <td>{{ $item->status }}</td>
                    <td>{{ $item->payment->status ?? '—' }}</td>
                    <td><x-actions resource="orders" :id="$item->id" /></td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Записів не знайдено.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $items->links('pagination::bootstrap-5') }}
@endsection
