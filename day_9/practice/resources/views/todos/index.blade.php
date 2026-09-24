@extends('layouts.app')

@section('name','Todos')

@section('content')


<h4>Basic Loop:</h4>

@foreach ($todos as $todo)
<div>
    {{ $todo['id'] }} - {{ $todo['name'] }}
</div>
@endforeach


<h4>Loop with Key:</h4>
@foreach ($todos as $key => $todo)
<div>
    {{ $key }} - {{ $todo['name'] }}
</div>
@endforeach

<h4>Loop properties:</h4>
@foreach ($todos as $todo)
    @if ($loop->first)
        <strong>First Todo</strong>
    @endif

    <p>{{ $todo['name'] }}</p>
@endforeach

@endsection