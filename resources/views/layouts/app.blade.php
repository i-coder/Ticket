<?php

use App\Performer;
use App\Reconciliation;
use Illuminate\Support\Facades\Auth;

$recon = Reconciliation::where('subdivision_id', '=', Auth::id())->get();
$iSogl = 0;
foreach ($recon as $item) {
    if (count($item->getStatus) == 0) {
        $iSogl = $iSogl+1;
    }
}

$recon = Performer::where('user_id', '=', Auth::id())->get();//исполнитель
$iIspl = 0;

foreach ($recon as $item) {
    if (count($item->getStatus) == 0) {
        $iIspl = $iIspl + 1;
    }
}

?>

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
    <link href="{{ asset('css/datepicker.css') }}" rel="stylesheet">

    <style>
        body {
            font-size: 13px !important;
        }
    </style>
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
                        <a class="navbar-item" href="/ViewAllArchive">
                            Архив задач
                        </a>
                    </div>
                </div>

                <a class="navbar-item" href="/store">
                    Создать
                </a>
                <a class="navbar-item" href="/approval">

                    <div class="tags has-addons">
                        <span class="tag">Входящие </span>
                        <span class="tag is-info">на согласование</span>
                        <span class="tag is-danger">{{$iSogl}}</span>
                    </div>

                </a>
                <a class="navbar-item" href="/you/tickets">

                    <div class="tags has-addons">
                        <span class="tag">Входящие </span>
                        <span class="tag is-info">на исполнение</span>
                        <span class="tag is-danger">{{$iIspl}}</span>
                    </div>

                </a>
                <a class="navbar-item" href="/outgoing">

                    <div class="tags has-addons">
                        <span class="tag">Исходящие</span>
                    </div>

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
