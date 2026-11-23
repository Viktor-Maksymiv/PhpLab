@extends('layouts.app')

@section('title', 'Редагування залу')

@section('content')
    <div class="card mx-auto shadow-sm" style="max-width: 720px;">
        <div class="card-header bg-white">
            <h2>Редагування залу #{{ $hall->id }}</h2>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">Виправте помилки у формі нижче.</div>
            @endif

            <form action="{{ route('halls.update', $hall) }}" method="POST">
                @csrf
                @method('PUT')

                @include('halls._form')

                <button type="submit" class="btn btn-primary mt-3">Оновити</button>
                <a href="{{ route('halls.index') }}" class="btn btn-secondary mt-3">Скасувати</a>
            </form>
        </div>
    </div>
@endsection
