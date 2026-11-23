@extends('layouts.app')

@section('title', 'Створення сеансу')

@section('content')
    <div class="card mx-auto shadow-sm" style="max-width: 720px;">
        <div class="card-header bg-white">
            <h2>Створення сеансу</h2>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">Виправте помилки у формі нижче.</div>
            @endif

            <form action="{{ route('showtimes.store') }}" method="POST">
                @csrf

                @include('showtimes._form')

                <button type="submit" class="btn btn-success mt-3">Зберегти</button>
                <a href="{{ route('showtimes.index') }}" class="btn btn-secondary mt-3">Скасувати</a>
            </form>
        </div>
    </div>
@endsection
