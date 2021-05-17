<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ isset($title) ? config('app.name') . ' -::- ' . $title : config('app.name') }} </title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet" >
        <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet" >
        <!-- JS -->
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
        <script src="{{ asset('js/pbs.js')}}"></script>
        <!-- Styles -->
        <!-- link rel='stylesheet' href="{{ asset('css/all.css') }}" --> <!-- load font awsome -->
        <link rel="stylesheet" href="//use.fontawesome.com/releases/v5.0.7/css/all.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" >
        <link rel="stylesheet" href="{{ asset('css/pbs-app.css') }}" >
        <script>    
            var allowedFilesTypes = {!! json_encode(explode(',', env('PBS_MIMES')), JSON_HEX_TAG) !!}
        </script>

        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>
    </head>
    <body class="py-6 flex flex-col justify-between items-center min-h-screen">

        <main role="main" class="flex flex-col justify-center items-center" >
            @yield('content')
        </main>
        @include('partials._footer')


    </body>
</html>
