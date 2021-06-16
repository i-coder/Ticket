@extends('layouts.app')

@section('content')
    <div class="">
        <ticket-view-component :ticket='{!! json_encode($ticket) !!}' :performers='{!! json_encode($performers) !!}' :reconciliations= '{!! json_encode($reconciliations) !!}' :files= '{!! json_encode($files) !!}'></ticket-view-component>
    </div>
@endsection
