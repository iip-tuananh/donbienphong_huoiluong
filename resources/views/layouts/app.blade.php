<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Đăng Nhập - {{ $config->web_title }}</title>
    <!-- Fonts -->
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,300;0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA==" crossorigin="anonymous" />
    <link href="{{ asset('css/custom.css') }}?version={{ env('APP_VERSION', '1') }}" rel="stylesheet">
</head>

<body>
@yield('content')

<!-- jQuery -->
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
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
</body>

</html>
