@extends('layouts.app')

@section('title', 'Сеанси')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Сеанси</h1>
        @can('create', App\Models\Showtime::class)
            <a href="{{ route('showtimes.create') }}" class="btn btn-primary">Додати сеанс</a>
        @endcan
    </div>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Фільм</th>
                <th>Зал</th>
                <th>Початок</th>
                <th>Ціна</th>
                <th>Статус</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->movie->title }}</td>
                    <td>{{ $item->hall->name }}</td>
                    <td>{{ $item->start_at->format('d.m.Y H:i') }}</td>
                    <td>{{ number_format($item->base_price, 2) }}</td>
                    <td>{{ $item->status }}</td>
                    <td><x-actions resource="showtimes" :model="$item" /></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Записів не знайдено.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $items->links('pagination::bootstrap-5') }}
@endsection
