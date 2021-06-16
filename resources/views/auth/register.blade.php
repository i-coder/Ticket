@extends('layouts.auth')

@section('content')


        <div class="column is-6">
            <div class="">

                <form method="POST" action="{{ route('register') }}" class="box">
                    <img src="{{ asset('images/logo.svg') }}" width="200px" class="bg-white">
                    @csrf
                    <h1>Регистрация в системе</h1>
                    <div class="field">
                        <div class="">
                            <input id="email" type="text" class="input @error('email') is-invalid @enderror" placeholder="Email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="field">
                        <div class="">
                            <input id="f" type="text" class="input @error('f') is-invalid @enderror" placeholder="Фамилия" name="f" value="{{ old('f') }}" required autocomplete="email" autofocus>
                            @error('f')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="field">
                        <div class="">
                            <input id="i" type="text" class="input @error('i') is-invalid @enderror" placeholder="Имя" name="i" value="{{ old('f') }}" required autocomplete="email" autofocus>
                            @error('i')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="field">
                        <div class="">
                            <input id="o" type="text" class="input @error('o') is-invalid @enderror" placeholder="Отчество" name="o" value="{{ old('f') }}" required autocomplete="email" autofocus>
                            @error('o')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="field">
                        <div class="">

                                <input id="password" type="password" class="input @error('password') is-invalid @enderror" placeholder="Новый пароль" name="password" required autocomplete="new-password">

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                        </div>
                    </div>
                    <div class="field">
                        <div class="">
                                <input id="password-confirm" type="password" class="input" placeholder="Повторите пароль" name="password_confirmation" required autocomplete="new-password">
                        </div>
                    </div>


                    <div class="field is-grouped">
                        <div class="control">
                            <button class="button is-success">{{ __('Register') }}</button>
                            <a href="/login" class="button">Авторизация</a>
                        </div>
                    </div>
                </form>


            </div>
        </div>


@endsection
