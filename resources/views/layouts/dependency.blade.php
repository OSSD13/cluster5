<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', '') | MyLocation</title>


    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    {{-- Sweet Aleart --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    {{-- jquery --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Meta --}}
    <meta name="description" content="@yield('description', 'MyLocation')">
    <meta name="keywords" content="@yield('keywords', 'MyLocation')">
    <meta name="author" content="@yield('author', 'MyLocation')">
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#ffffff">

    {{-- Open Graph --}}
    <meta property="og:title" content="@yield('title', 'MyLocation')">
    <meta property="og:description" content="@yield('description', 'MyLocation')">
    <meta property="og:image" content="@yield('image', asset('images/og-image.jpg'))">
    <meta property="og:url" content="@yield('url', url()->current())">
    <meta property="og:type" content="@yield('type', 'website')">
    <meta property="og:site_name" content="@yield('site_name', 'MyLocation')">
    <meta property="og:locale" content="@yield('locale', 'en_US')">

    {{-- Twitter --}}
    <meta name="twitter:card" content="@yield('twitter_card', 'summary_large_image')">
    <meta name="twitter:site" content="@yield('twitter_site', '@mylocation')">
    <meta name="twitter:creator" content="@yield('twitter_creator', '@mylocation')">
    <meta name="twitter:title" content="@yield('title', 'MyLocation')">
    <meta name="twitter:description" content="@yield('description', 'MyLocation')">
    <meta name="twitter:image" content="@yield('image', asset('images/og-image.jpg'))">

    {{-- Apple --}}
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">

</head>

<body class="font-lunasima antialiased bg-gray-300">
    @yield('body')
</body>

@yield('script')

</html>
