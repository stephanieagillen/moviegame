@extends('layouts.app')


@section('content')
<game :game="{{ json_encode($game) }}"></game>

@endsection
