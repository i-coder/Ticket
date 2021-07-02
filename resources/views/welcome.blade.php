@extends('layouts.app')

@section('content')
    <ticket-table-all-component
        :users='{!! json_encode($users) !!}'
    ></ticket-table-all-component>
    <people-table-all-component></people-table-all-component>
@endsection
