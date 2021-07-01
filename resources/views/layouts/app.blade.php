<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        {{-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap"> --}}

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="/css/navbar.css">
        <link rel="stylesheet" href="/css/all.css">
        <link rel="stylesheet" href="/semantic-ui/semantic.css">
        <link rel="stylesheet" href="/datetimepicker/jquery.datetimepicker.css">
        <link rel="stylesheet" type="text/css" href="/DataTables/datatables.min.css">
        {{-- <link rel="stylesheet" type="text/css" href="/select2/select2.css"> --}}

        @yield('custom_css')

        {{-- <link rel="stylesheet" href="bootstrap5/bootstrap.min.css"> --}}


        <!-- Scripts -->
        {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}


    </head>
    <body class="font-sans antialiased">
        <div id="loader" class="ui segment" style="position:fixed; height:100vh; width: 100%; z-index: 100000; opacity: 50%; border: 0;">
            <p></p>
            <div class="ui active dimmer">
                <div class="ui loader"></div>
            </div>
        </div>
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
        {{-- <script src="{{ asset('js/app.js') }}" ></script> --}}

        <script src="/js/jquery-3.6.0.js"></script>
        <script src="/js/all.js" ></script>
        <script src="/js/tool.js" ></script>
        {{-- <script src="/select2/select2.js"></script> --}}

        <script src="/semantic-ui/semantic.js" ></script>
        <script src="/datetimepicker/jquery.datetimepicker.full.min.js" ></script>
        <script type="text/javascript" charset="utf8" src="/DataTables/datatables.min.js"></script>
        <script>
            jQuery.datetimepicker.setLocale('zh-TW');
        </script>
        @yield('custom_js')

        {{-- <script src="bootstrap5/bootstrap.min.js" ></script> --}}


        <script>
            var navOpen = false;
            hideLoading()
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
            function showLoading(){
                $("#loader").show();
                // $("#loader").modal("show");
            }
            function hideLoading(){
                // setTimeout(function(){$("#loader").hide(); }, 500);
                // setTimeout(function(){$("#loader").hide(); }, 1000);
                setTimeout(function(){$("#loader").hide(); }, 1500);
            }
            $("#logout").click(function(){
                $("#logout-form").submit();
            })
        </script>
    </body>
</html>
