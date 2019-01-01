@extends('layouts.app')

@section('content')

@foreach($games as $game)
    <h3>{{ $game->name }}</h3>
    <p>
        <a href="#" class="btn btn-info">View Task</a>
        <a href="#" class="btn btn-primary">Edit Task</a>
    </p>
    <hr>
@endforeach

@endsection