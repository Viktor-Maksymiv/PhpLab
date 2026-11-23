@extends('layouts.app')

@section('title', 'Створення жанру')

@section('content')
    <div class="card mx-auto shadow-sm" style="max-width: 720px;">
        <div class="card-header bg-white">
            <h2>Створення жанру</h2>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">Виправте помилки у формі нижче.</div>
            @endif

            <form action="{{ route('genres.store') }}" method="POST">
                @csrf

                @include('genres._form')

                <button type="submit" class="btn btn-success mt-3">Зберегти</button>
                <a href="{{ route('genres.index') }}" class="btn btn-secondary mt-3">Скасувати</a>
            </form>
        </div>
    </div>
@endsection
