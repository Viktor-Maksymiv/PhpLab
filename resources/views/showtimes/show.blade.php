@extends('layouts.app')

@section('title', 'Сеанс')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <h1>{{ $showtime->movie->title }}</h1>
            <dl class="row">
                <dt class="col-sm-3">Початок</dt>
                <dd class="col-sm-9">{{ $showtime->start_at->format('d.m.Y H:i') }}</dd>
                <dt class="col-sm-3">Зал</dt>
                <dd class="col-sm-9">{{ $showtime->hall->name }} ({{ $showtime->hall->format }})</dd>
                <dt class="col-sm-3">Ціна</dt>
                <dd class="col-sm-9">{{ number_format($showtime->base_price, 2) }} грн</dd>
                <dt class="col-sm-3">Статус</dt>
                <dd class="col-sm-9">{{ $showtime->status }}</dd>
                <dt class="col-sm-3">Продано квитків</dt>
                <dd class="col-sm-9">{{ $showtime->tickets_count }} із {{ $showtime->hall->capacity }}</dd>
                <dt class="col-sm-3">Жанри</dt>
                <dd class="col-sm-9">
                    @forelse ($showtime->movie->genres as $genre)
                        <span class="badge bg-secondary">{{ $genre->name }}</span>
                    @empty
                        <span class="text-muted small">—</span>
                    @endforelse
                </dd>
            </dl>

            @can('update', $showtime)
                <a href="{{ route('showtimes.edit', $showtime) }}" class="btn btn-warning">Редагувати</a>
            @endcan
            <a href="{{ route('showtimes.index') }}" class="btn btn-secondary">До списку</a>
        </div>
    </div>
@endsection
