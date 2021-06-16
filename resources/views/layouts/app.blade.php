<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Ticket') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link href="{{ asset('css/materialdesignicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/all.css') }}" rel="stylesheet">


</head>
<body>
<div id="app" class="">

    <nav class="navbar is-success ">
        <div class="navbar-brand">

            <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false"
               data-target="navbarBasicExample">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
        </div>

        <div id="navbarExampleTransparentExample" class="container navbar-menu">
            <div class="navbar-start">
                <a class="navbar-item" href="/">
                    Главная
                </a>

                <div class="navbar-item has-dropdown is-hoverable">
                    <a class="navbar-link" href="">
                        Список
                    </a>
                    <div class="navbar-dropdown is-boxed">
                        <a class="navbar-item" href="">
                            Архив задач
                        </a>
                    </div>
                </div>
                <div class="navbar-item">
                    <input class="input is-success" type="text" placeholder="Поиск.."/>
                </div>
                <a class="navbar-item" href="/store">
                    <span class="icon is-left mr-1">
                       <i class="fas fa-book" aria-hidden="true"></i>
                    </span>
                    Создать
                </a>
                <a class="navbar-item" href="/approval">
                    <span class="icon is-left mr-1">
                       <i class="fa fa-check-square" aria-hidden="true"></i>
                    </span>
                    На согласование
                </a>
                <a class="navbar-item" href="/you/tickets">

                            <span class="icon is-left mr-1">
                       <i class="fa fa-bell" aria-hidden="true"></i>
                    </span>
                    На исполнение
                </a>
            </div>

            <div class="navbar-end">
                <div class="navbar-item has-dropdown is-hoverable">
                    <a class="navbar-link">
                        Система
                    </a>

                    <div class="navbar-dropdown is-right is-boxed">
                        @auth
                            <a class="navbar-item" href="{{ url('/you/profile') }}">Ваш профиль</a>
                        @else
                            <a class="navbar-item" href="{{ route('login') }}">Войти</a>
                        @endauth

                    </div>
                </div>
            </div>
        </div>
    </nav>


    <div class="container is-fluid">
        @yield('content')
    </div>

</div>
</body>
</html>
