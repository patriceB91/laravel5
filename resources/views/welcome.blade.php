@extends('app')

@section('title')
    Hello city
@endsection


@section('content')

        <img src="{{ asset('/images/drapeau-francais.jpg') }}" alt="Drapeau Français">

        <h1> Hello from PB </h1>

        <p> It's about time {{ date('d/m/Y') }} </p>

@endsection
