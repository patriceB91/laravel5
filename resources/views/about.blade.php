@extends('app')


@section('title', 'Hello city | ' . config('app.name') )

@section('content')

        <img src="{{ asset('/images/ibm.jpg') }} " alt="Drapeu Français">

        <p> Build by PB </p>

        <a href="/" class="ml-1 underline">Retour acceuil </a>

@endsection
