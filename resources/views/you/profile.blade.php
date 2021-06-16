@extends('layouts.app')

@section('content')


    <div class="column is-offset-one-fifths is-4">
        <h3 class="title">You profile</h3>
        @if (Session::has('message'))
            <div class="box">{{ Session::get('message') }}</div>
        @endif


        <div class="notification ">
            {{$model->f}} {{$model->i}} {{$model->o}}
        </div>


        <form method="POST" action="{{ route('you/profile/edit') }}">
            @csrf

            <div class="form-group row">
                <label for="name" class="label">First name</label>
                <div class="col-md-6">
                    <input id="f" type="text" class="input" name="f" value="{{ old('name') }}" required>
                </div>
            </div>

            <div class="form-group row">
                <label for="name" class="label">Last name</label>
                <div class="col-md-6">
                    <input id="i" type="text" class="input" name="i" value="{{ old('name') }}" required>
                </div>
            </div>

            <div class="form-group row">
                <label for="name" class="label">Middle name</label>
                <div class="col-md-6">
                    <input id="o" type="text" class="input" name="o" value="{{ old('name') }}" required>
                </div>
            </div>


            <div class="form-group row mt-3">
                <div class="col-md-6 offset-md-4">
                    <button type="submit" class="button is-success">
                        Save profile
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
