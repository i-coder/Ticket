@extends('layouts.app')

@section('content')
    <div class="">
        <ticket-show-component
            :procentispl='{!! json_encode($procentispl) !!}'
            :zakaz='{!! json_encode($zakaz) !!}'
            :sogl='{!! json_encode($sogl) !!}'
            :ispl='{!! json_encode($ispl) !!}'
            :auth='{!! json_encode(Auth::id()) !!}'
            :customer='{!! json_encode($customer) !!}'
            :comments='{!! json_encode($comments) !!}'
            :user='{!! json_encode($user) !!}'
            :ticket='{!! json_encode($ticket) !!}'
            :performers='{!! json_encode($performers) !!}'
            :reconciliations= '{!! json_encode($reconciliations) !!}'
            :files= '{!! json_encode($files) !!}'></ticket-show-component>
    </div>
@endsection
