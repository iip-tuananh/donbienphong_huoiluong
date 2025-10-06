<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('page_title') - {{ $config->web_title }}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon/favicon-16x16.png') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap&subset=vietnamese" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('libs/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('libs/mmenu/jquery.mmenu.css') }}">
    <link rel="stylesheet" href="{{ asset('libs/mmenu/jquery.mmenu.all.css') }}">
    {{-- Front Awesome --}}
    <script src="https://kit.fontawesome.com/f13bb303f7.js" crossorigin="anonymous"></script>
    <!-- css front -->
    <link rel="stylesheet" href="{{ asset('css/mega-menu.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mmenu_custom.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('libs/sweetalert/css/sweetalert.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,300;0,400;0,700;1,400&display=swap" rel="stylesheet">
    @yield('css')
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>

<body>
    <div class="page">
        @include('front.blocks.header')
        @include('front.blocks.menu')
        <main id="main_content">
            <div class="main-content-wrap">
                @yield('slide')
                @yield('content')
            </div>
        </main>
        @include('front.blocks.footer')
    </div>
    <!-- ./wrapper -->
    <!-- jQuery -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <!-- Mmenu -->
    <script src="{{ asset('plugins/mmenu/jquery.mmenu.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="{{ asset('libs/bootstrap/bootstrap.min.js') }}"></script>

    <!-- overlayScrollbars -->
    <script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous"></script>
    <script type="text/javascript">
        toastr.options.escapeHtml = false;
        @if(Session::has('message'))
        toastr.{{Session::get('alert-type', 'info')}}("{{Session::get('message')}}");
        @endif
        @if(Session::has('token'))
        localStorage.setItem('{{ env("prefix") }}-token', "{{Session::get('token')}}")
        @endif
        @if(Session::has('logout'))
        localStorage.removeItem('{{ env("prefix") }}-token');
        @endif
        var CSRF_TOKEN = "{{ csrf_token() }}";
        @if (Auth::check())
        const DEFAULT_USER = {
            id: "{{ Auth::user()->id }}",
            fullname: "{{ Auth::user()->name }}"
        };
        const USERS = @json(\App\Model\Common\User::getMembers());
        @endIf
    </script>
    <script>
        $(document).ready(function () {
            /* ================ Menu Mobile ================ */
            $("#bs-navbar").mmenu({
            }, {
                clone: true
            });
            $('.mm-listview').removeClass('ruby-menu')
            var API = $("#mm-bs-navbar").data("mmenu");

            $("#hamburger").on("click", function () {
                API.open();
            });
        });
    </script>
    @if ($config->click_call == 1)
    @include('front.blocks.click-call')
    @endif
    @if ($config->facebook_chat == 1)
    @include('front.blocks.facebookchat')
    @endif
    @if ($config->zalo_chat == 1)
    @include('front.blocks.zalo')
    @endif
    @yield('script')

</body>

</html>