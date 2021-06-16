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

    <section class="hero is-success is-fullheight">
        <div class="hero-body">
            <div class="container">
                <div class="columns">
                    @yield('content')
                    <div class="column">

                        <h1 class="title">Система сбора и контроля</h1>
                        <h2 class="subtitle">
                            - задачи<br> - заявки<br> - согласования<br> - служебки<br><br>

                            <p>Версия: 0.0.1</p>
                        </h2>
                    </div>
                </div>

            </div>

        </div>
    </section>

</div>
</body>
</html>
