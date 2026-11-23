@extends('layouts.app')

@section('title', $movie->title)

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <h1>{{ $movie->title }}</h1>
            <p class="text-muted">
                {{ $movie->duration_minutes }} хв · {{ $movie->age_rating }} · прем’єра {{ $movie->release_date->format('d.m.Y') }}
            </p>

            <p>
                Жанри:
                @forelse ($movie->genres as $genre)
                    <span class="badge bg-secondary">{{ $genre->name }}</span>
                @empty
                    <span class="text-muted small">—</span>
                @endforelse
            </p>

            <h4 class="mt-4">Сеанси</h4>
            <ul class="list-group mb-3">
                @forelse ($movie->showtimes as $showtime)
                    <li class="list-group-item">
                        <a href="{{ route('showtimes.show', $showtime) }}">{{ $showtime->start_at->format('d.m.Y H:i') }}</a>
                        — {{ $showtime->hall->name }}, {{ number_format($showtime->base_price, 2) }} грн
                    </li>
                @empty
                    <li class="list-group-item text-muted">Сеансів немає.</li>
                @endforelse
            </ul>

            <a href="{{ route('movies.edit', $movie) }}" class="btn btn-warning">Редагувати</a>
            <a href="{{ route('movies.index') }}" class="btn btn-secondary">До списку</a>
        </div>
    </div>
@endsection
