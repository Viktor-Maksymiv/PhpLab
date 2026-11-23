@extends('layouts.app')

@section('title', 'Жанри')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Жанри</h1>
        <a href="{{ route('genres.create') }}" class="btn btn-primary">Додати жанр</a>
    </div>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Назва</th>
                <th>Slug</th>
                <th>Фільмів</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->slug }}</td>
                    <td>{{ $item->movies_count }}</td>
                    <td><x-actions resource="genres" :id="$item->id" :show="false" /></td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Записів не знайдено.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $items->links('pagination::bootstrap-5') }}
@endsection
