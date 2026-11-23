@extends('layouts.app')

@section('title', 'Зали')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Зали</h1>
        <a href="{{ route('halls.create') }}" class="btn btn-primary">Додати зал</a>
    </div>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Назва</th>
                <th>Формат</th>
                <th>Місць у залі</th>
                <th>Місткість</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->format }}</td>
                    <td>{{ $item->seats_count }}</td>
                    <td>{{ $item->capacity }}</td>
                    <td><x-actions resource="halls" :id="$item->id" /></td>
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
