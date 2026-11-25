@extends('layouts.app')

@section('title', 'Фільми')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Фільми</h1>
        @can('create', App\Models\Movie::class)
            <a href="{{ route('movies.create') }}" class="btn btn-primary">Додати фільм</a>
        @endcan
    </div>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Назва</th>
                <th>Тривалість</th>
                <th>Вік</th>
                <th>Жанри</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->title }}</td>
                    <td>{{ $item->duration_minutes }} хв</td>
                    <td>{{ $item->age_rating }}</td>
                    <td>@forelse ($item->genres as $genre)
                            <span class="badge bg-secondary">{{ $genre->name }}</span>
                        @empty
                            <span class="text-muted small">—</span>
                        @endforelse</td>
                    <td><x-actions resource="movies" :model="$item" /></td>
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
