@extends('layouts.app')

@section('content')
    <div class="">
        <people-table-all-ticket-component :user_id='{!! json_encode($user_id) !!}'></people-table-all-ticket-component>
    </div>
@endsection
