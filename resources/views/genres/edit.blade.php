@extends('layouts.app')

@section('title', 'Редагування жанру')

@section('content')
    <div class="card mx-auto shadow-sm" style="max-width: 720px;">
        <div class="card-header bg-white">
            <h2>Редагування жанру #{{ $genre->id }}</h2>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">Виправте помилки у формі нижче.</div>
            @endif

            <form action="{{ route('genres.update', $genre) }}" method="POST">
                @csrf
                @method('PUT')

                @include('genres._form')

                <button type="submit" class="btn btn-primary mt-3">Оновити</button>
                <a href="{{ route('genres.index') }}" class="btn btn-secondary mt-3">Скасувати</a>
            </form>
        </div>
    </div>
@endsection
