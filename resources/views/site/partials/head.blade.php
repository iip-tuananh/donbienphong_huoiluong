<meta charset="UTF-8" />
<meta name="theme-color" content="#d70018">
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
<meta name='revisit-after' content='2 days' />
<meta name="viewport" content="width=device-width">
<title>@yield('title')</title>
<meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
<meta http-equiv="Content-Language" content="vi" />
<link rel="alternate" href="{{url()->current()}}" hreflang="vi-vn" />
<meta name="description" content="@yield('description')">
<meta name="robots" content="index, follow" />
<meta name="googlebot" content="index, follow">
<meta name="revisit-after" content="1 days" />
<meta name="generator" content="@yield('title')" />
<meta name="rating" content="General">
<meta name="application-name" content="@yield('title')" />
<meta name="theme-color" content="#ed3235" />
<meta name="msapplication-TileColor" content="#ed3235" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-title" content="{{url()->current()}}" />
<link rel="apple-touch-icon-precomposed" href="@yield('image')" sizes="700x700">
<meta property="og:url" content="{{url()->current()}}">
<meta property="og:title" content="@yield('title')">
<meta property="og:description" content="@yield('description')">
<meta property="og:image" content="@yield('image')">
<meta property="og:site_name" content="{{url()->current()}}">
<meta property="og:image:alt" content="@yield('title')">
<meta property="og:type" content="website" />
<meta property="og:locale" content="vi_VN" />
<meta name="twitter:card" content="summary" />
<meta name="twitter:site" content="{{url()->current()}}" />
<meta name="twitter:title" content="@yield('title')" />
<meta name="twitter:description" content="@yield('description')" />
<meta name="twitter:image" content="@yield('image')" />
<meta name="twitter:url" content="" />
<meta itemprop="name" content="@yield('title')">
<meta itemprop="description" content="@yield('description')">
<meta itemprop="image" content="@yield('image')">
<meta itemprop="url" content="">
<link rel="canonical" href="{{\Request::url()}}">
<!-- <link rel="amphtml" href="amp/" /> -->
<link rel="image_src" href="@yield('image')" />
<link rel="image_src" href="@yield('image')" />
<link rel="shortcut icon" href="" type="image/x-icon">
<link rel="icon" href="" type="image/x-icon">
<meta name="csrf-token" content="{{ csrf_token() }}" />
<!--=============== css  ===============-->
<link type="text/css" rel="stylesheet" href="/site/css/plugins.css">
<link type="text/css" rel="stylesheet" href="/site/css/style.css">
<link type="text/css" rel="stylesheet" href="/site/css/color.css">

<style>
    .invalid-feedback {
        margin-bottom: 10px;
        width: 100%;
        color: #dc3545;
    }
</style>

<style>
    /* Ẩn mọi phần tử có ng-cloak cho tới khi Angular biên dịch xong */
    [ng-cloak], [data-ng-cloak], [x-ng-cloak],
    .ng-cloak, .data-ng-cloak, .x-ng-cloak {
        display: none !important;
    }
</style>
