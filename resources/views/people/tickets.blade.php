@extends('layouts.app')

@section('content')
    <div class="">
        <people-table-all-ticket-component
            :user_id='{!! json_encode($user_id) !!}'
            :users='{!! json_encode($users) !!}'
            :work='{!! json_encode($work) !!}'
            :ispl='{!! json_encode($ispl) !!}'
            :all='{!! json_encode($all) !!}'
        ></people-table-all-ticket-component>
    </div>
@endsection
