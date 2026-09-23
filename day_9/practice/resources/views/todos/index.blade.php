@extends('layouts.app')
@section('title','Todos')
@section('content')

@foreach ($todos as $todo)
<div>
    {{ $todo['id'] }} - {{ $todo['name'] }}
</div>
@endforeach

@endsection