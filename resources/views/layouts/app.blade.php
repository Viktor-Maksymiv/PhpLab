<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Кінотеатр')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">Кінотеатр</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('movies.*') ? 'active' : '' }}" href="{{ route('movies.index') }}">Фільми</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('genres.*') ? 'active' : '' }}" href="{{ route('genres.index') }}">Жанри</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('halls.*') ? 'active' : '' }}" href="{{ route('halls.index') }}">Зали</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('seats.*') ? 'active' : '' }}" href="{{ route('seats.index') }}">Місця</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('showtimes.*') ? 'active' : '' }}" href="{{ route('showtimes.index') }}">Сеанси</a></li>
                    @auth
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">Замовлення</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}" href="{{ route('tickets.index') }}">Квитки</a></li>
                    @endauth
                    @can('viewAny', App\Models\User::class)
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">Користувачі</a></li>
                    @endcan
                </ul>
                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Увійти</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Реєстрація</a></li>
                    @endguest

                    @auth
                        <li class="nav-item">
                            <span class="nav-link text-info">{{ Auth::user()->name }} ({{ Auth::user()->role }})</span>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link nav-link">Вийти</button>
                            </form>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
