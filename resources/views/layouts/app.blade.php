<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="/css/navbar.css">
        <link rel="stylesheet" href="/css/all.css">
        <link rel="stylesheet" href="/semantic-ui/semantic.css">
        @yield('custom_css')

        {{-- <link rel="stylesheet" href="bootstrap5/bootstrap.min.css"> --}}


        <!-- Scripts -->
        {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}


    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            {{-- <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header> --}}

            <!-- Page Content -->
            <main id="main" style="margin-left:75px;">
                @yield('content')
            </main>
        </div>
        <script src="/js/jquery-3.5.1.slim.min.js"></script>
        <script src="/js/all.js" ></script>
        <script src="/semantic-ui/semantic.js" ></script>
        @yield('custom_js')

        {{-- <script src="bootstrap5/bootstrap.min.js" ></script> --}}


        <script>
            var navOpen = false;
            function toggleNav(){
                if(navOpen){
                    navOpen = false;
                    $("#nav-btn-arrow").toggleClass('rotated');
                    document.getElementById("my-side-nav").style.width = "75px";
                    document.getElementById("main").style.marginLeft= "75px";
    
                } else {
                    navOpen = true;
                    $("#nav-btn-arrow").toggleClass('rotated');
                    document.getElementById("my-side-nav").style.width = "250px";
                    document.getElementById("main").style.marginLeft = "250px";
                }
            }
            $("#logout").click(function(){
                $("#logout-form").submit();
            })
        </script>
    </body>
</html>
