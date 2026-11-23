@extends('layouts.app')

@section('title', $hall->name)

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <h1>{{ $hall->name }}</h1>
            <p class="text-muted">
                Формат {{ $hall->format }} · місткість {{ $hall->capacity }} · рядів {{ $hall->rows_count }}
            </p>

            <h4 class="mt-4">Схема місць</h4>
            @forelse ($hall->seats->groupBy('row_number') as $row => $seats)
                <div class="mb-2">
                    <strong>Ряд {{ $row }}:</strong>
                    @foreach ($seats->sortBy('seat_number') as $seat)
                        <span class="badge {{ $seat->type === 'vip' ? 'bg-warning text-dark' : 'bg-secondary' }}">{{ $seat->seat_number }}</span>
                    @endforeach
                </div>
            @empty
                <p class="text-muted">Місця ще не створено.</p>
            @endforelse

            <a href="{{ route('halls.edit', $hall) }}" class="btn btn-warning mt-3">Редагувати</a>
            <a href="{{ route('halls.index') }}" class="btn btn-secondary mt-3">До списку</a>
        </div>
    </div>
@endsection
