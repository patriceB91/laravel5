@extends('app')


@section('title', 'Hello city | ' . config('app.name') )

@section('content')

        <img src="{{ asset('/images/ibm.jpg') }} " class="mt-12 rounded-full my-12 shadow-md h-32" alt="Drapeau Français">

        <h2 class="text-lg text-gray-700"> Build with <span class="text-pink-500">&hearts;</span> by PB </h2>

        <P class="mt-5"><a href="/" class="ml-1 underline">Retour acceuil </a></p>

@endsection
