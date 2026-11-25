@extends('layouts.app')

@section('title', 'Місця')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Місця</h1>
        @can('create', App\Models\Seat::class)
            <a href="{{ route('seats.create') }}" class="btn btn-primary">Додати місце</a>
        @endcan
    </div>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Зал</th>
                <th>Ряд</th>
                <th>Місце</th>
                <th>Тип</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->hall->name }}</td>
                    <td>{{ $item->row_number }}</td>
                    <td>{{ $item->seat_number }}</td>
                    <td>{{ $item->type }}</td>
                    <td><x-actions resource="seats" :model="$item" :show="false" /></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Записів не знайдено.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $items->links('pagination::bootstrap-5') }}
@endsection
