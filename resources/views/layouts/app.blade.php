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
        <link rel="icon" href="/image/chef-icon-white.png">
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
            <main id="main" style="padding-left: 2.5rem; padding-right: 1rem;">
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
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            hideLoading();

            // document.cookie = "sidebar=close;";
            // const cookie = document.cookie
            // .split('; ')
            // .find(row => row.startsWith('sidebar='));

            // .split('=')[1];
            if(getCookie("sidebar") == undefined){
                setCookie("sidebar", "close", 1);
            } else {
                const cookeValue = getCookie("sidebar");

                $(".sidenav").addClass("notransition");
                $("#main").addClass("notransition");

                if(cookeValue == "close"){
                    $("#nav-btn-arrow").removeClass('rotated');
                    document.getElementById("my-side-nav").style.width = "75px";
                    document.getElementById("main").style.marginLeft= "75px";
                    $("#navigation_name").hide();
                    $("#navigation_branch_container").hide();
                } else {
                    $("#nav-btn-arrow").addClass('rotated');
                    document.getElementById("my-side-nav").style.width = "300px";
                    document.getElementById("main").style.marginLeft = "300px";
                    $("#navigation_name").show();
                    $("#navigation_branch_container").show();
                }

                setTimeout(function(){
                    $(".sidenav").removeClass("notransition");
                    $("#main").removeClass("notransition");
                }, 200)
            }

            // alert(cookie);
            function getCookie(c_name){
                const cookie = document.cookie
                .split('; ')
                .find(row => row.startsWith(c_name + '='));
                if(cookie == undefined){
                    return undefined;
                } else {
                    return cookie.split('=')[1];
                }
            }

            function setCookie(c_name,value,exdays){
                var exdate=new Date();
                exdate.setDate(exdate.getDate() + exdays);
                var c_value=escape(value) + ((exdays==null)
                                            ? "" : "; expires="+exdate.toUTCString())
                                            + "; path=/";
                document.cookie=c_name + "=" + c_value;
            }
            function deleteAllCookies() {
                var cookies = document.cookie.split(";");

                for (var i = 0; i < cookies.length; i++) {
                    var cookie = cookies[i];
                    var eqPos = cookie.indexOf("=");
                    var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
                    document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
                }
            }

            function toggleNav(){
                if(getCookie("sidebar") == "open"){
                    $("#navigation_branch_container").hide();
                    $("#navigation_name").hide();

                    $("#nav-btn-arrow").toggleClass('rotated');
                    document.getElementById("my-side-nav").style.width = "75px";
                    document.getElementById("main").style.marginLeft= "75px";
                    setCookie("sidebar", "close", 1);
                } else {
                    setTimeout(function(){
                        $("#navigation_branch_container").show();
                        $("#navigation_name").show();
                    }, 250);

                    $("#nav-btn-arrow").toggleClass('rotated');
                    document.getElementById("my-side-nav").style.width = "300px";
                    document.getElementById("main").style.marginLeft = "300px";

                    setCookie("sidebar", "open", 1);
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
            $("#app_branch").change(function(){
                let data = {
                    "branch_id": $(this).val()
                };
                $.ajax({
                    type: "POST",
                    url: "/users/updateUserBranch",
                    contentType: "application/json",
                    dataType: "json",
                    beforeSend: showLoading,
                    complete: hideLoading,
                    data: JSON.stringify(data),
                    success: function(response) {
                        if(response["status"] != "200"){
                            alert("系統錯誤");
                        }

                        if(response["error"].length > 0){
                            for(let i = 0; i < response["error"].length; i++){
                                alert(response["error"][i]);
                            }
                        } else {
                            location.reload();
                        }
                    },
                    error: function(response) {
                        // console.log(response);
                    }
                });
                console.log($(this).val());
            })
        </script>
    </body>
</html>
