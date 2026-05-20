<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Brick Shot') }}</title>

    @yield('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/icon_top.png') }}">

@if(app()->environment('local'))

<style>

    body::before {
        content: "LOCAL DEVELOPMENT SERVER";
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 28px;

        background: linear-gradient(
            90deg,
            #8b0000,
            #d00000
        );

        color: white;
        font-weight: 800;
        font-size: 13px;
        letter-spacing: 2px;

        display: flex;
        align-items: center;
        justify-content: center;

        z-index: 999999;

        box-shadow: 0 2px 10px rgba(0,0,0,0.35);
    }

    body {
        border-top: 28px solid #8b0000 !important;

        filter: hue-rotate(-25deg);
    }

</style>

@endif

</head>
<body>
    <div id="app">
      

        <main class="py-4">
            @yield('content')
        </main>
    </div>


    <!-- Scripts -->
    
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>  
    @yield('js')  
</body>
</html>
