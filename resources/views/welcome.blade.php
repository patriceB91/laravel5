@extends('app', )

@section('title')
    Hello city
@endsection


@section('content')

        <img src="{{ asset('/images/drapeau-francais.jpg') }}" class="rounded shadow-md h-32" alt="Drapeau Français">

        <h1 class="text-indigo-600 text-3xl sm:text-5xl font-semibold mt-5"> Hello from PB </h1>

        <p class="text-lg text-gray-800"> It's about time {{ date('d/m/Y') }} </p>

@endsection
