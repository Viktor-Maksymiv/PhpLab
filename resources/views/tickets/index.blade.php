@extends('layouts.app')

@section('title', 'Квитки')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Квитки</h1>
        <a href="{{ route('tickets.create') }}" class="btn btn-primary">Додати квиток</a>
    </div>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Замовлення</th>
                <th>Фільм</th>
                <th>Місце</th>
                <th>Ціна</th>
                <th>QR-код</th>
                <th>Статус</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>#{{ $item->order_id }}</td>
                    <td>{{ $item->showtime->movie->title }}</td>
                    <td>{{ $item->seat->hall->name }}, {{ $item->seat->row_number }}-{{ $item->seat->seat_number }}</td>
                    <td>{{ number_format($item->price, 2) }}</td>
                    <td>{{ $item->qr_code }}</td>
                    <td>{{ $item->status }}</td>
                    <td><x-actions resource="tickets" :id="$item->id" /></td>
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
