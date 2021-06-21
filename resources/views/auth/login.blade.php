@extends('layouts.auth')

@section('content')

    <div class="column is-offset-one-fifths is-4">



                    <form method="POST" action="{{ route('login') }}" class="box">
                        <img src="{{ asset('images/logo.svg') }}" width="200px" class="bg-white">
                        @csrf
                        <h1>Авторизация в системе СЭД</h1>
                        <div class="field">
                            <label for="email" class="label">Email</label>
                            <div class="">
                                 <input id="email" type="text" class="input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="field">
                            <label for="password" class="label">Пароль</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="input @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        Запомнить меня
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="field is-grouped">
                            <div class="control">
                                <button class="button is-success">Войти</button>
                                <a href="/register" class="button">Регистрация</a>
                            </div>
                        </div>
                    </form>


    </div>

@endsection
