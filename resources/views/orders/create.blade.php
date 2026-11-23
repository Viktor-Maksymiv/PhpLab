@extends('layouts.app')

@section('title', 'Створення замовлення')

@section('content')
    <div class="card mx-auto shadow-sm" style="max-width: 720px;">
        <div class="card-header bg-white">
            <h2>Створення замовлення</h2>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">Виправте помилки у формі нижче.</div>
            @endif

            <form action="{{ route('orders.store') }}" method="POST">
                @csrf

                @include('orders._form')

                <button type="submit" class="btn btn-success mt-3">Зберегти</button>
                <a href="{{ route('orders.index') }}" class="btn btn-secondary mt-3">Скасувати</a>
            </form>
        </div>
    </div>
@endsection
