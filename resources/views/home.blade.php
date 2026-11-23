@extends('layouts.app')

@section('title', 'Головна сторінка')

@section('content')
    <div class="p-5 mb-4 bg-light rounded-3 shadow-sm">
        <h1 class="display-5 fw-bold">Електронна система продажу квитків до кінотеатру</h1>
        <p class="col-md-8 fs-4">Лабораторна робота з вивчення шаблонізатора Blade та рендерингу на боці сервера (SSR).</p>
        <a href="{{ route('movies.index') }}" class="btn btn-primary btn-lg">Перейти до афіші</a>
        <a href="{{ route('tickets.index') }}" class="btn btn-outline-primary btn-lg">Квитки</a>
    </div>

    <div class="row g-3">
        <div class="col-md-4"><div class="card h-100"><div class="card-body"><h5>Афіша</h5><p class="text-muted">Фільми, жанри та сеанси.</p><a href="{{ route('showtimes.index') }}">Розклад сеансів</a></div></div></div>
        <div class="col-md-4"><div class="card h-100"><div class="card-body"><h5>Продаж</h5><p class="text-muted">Замовлення, квитки та оплата.</p><a href="{{ route('orders.index') }}">Замовлення</a></div></div></div>
        <div class="col-md-4"><div class="card h-100"><div class="card-body"><h5>Довідники</h5><p class="text-muted">Зали, місця та користувачі.</p><a href="{{ route('halls.index') }}">Зали</a></div></div></div>
    </div>
@endsection
