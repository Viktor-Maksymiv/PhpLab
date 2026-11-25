@extends('layouts.app')

@section('title', 'Реєстрація')

@section('content')
    <div class="card mx-auto shadow-sm" style="max-width: 450px;">
        <div class="card-header bg-white">
            <h3 class="mb-0">Реєстрація</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('register') }}" method="POST">
                @csrf

                <x-form.input name="name" label="Імʼя" autofocus />
                <x-form.input name="email" label="Електронна пошта" type="email" />
                <x-form.input name="password" label="Пароль" type="password" />
                <x-form.input name="password_confirmation" label="Підтвердження пароля" type="password" />

                <button type="submit" class="btn btn-success w-100">Зареєструватися</button>
            </form>
        </div>
    </div>
@endsection
