<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', '') | MyLocation</title>

    {{-- Set base href to support subdirectory routing like /cluster5 --}}
    <base href="{{ url('/') }}/">

    {{-- Styles / Scripts --}}
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Icontify .js --}}
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>

    {{-- Alpine.js --}}
    <script src="//unpkg.com/alpinejs" defer></script>

    {{-- Sweet Alert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Emoji Picker --}}
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>

    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script type="text/javascript"  src="{{ asset('assets/js/zip.js') }}"></script>
    <script type="text/javascript"  src="{{ asset('assets/js/JQL.js') }}"></script>
    <script type="text/javascript"  src="{{ asset('assets/js/typeahead.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/thailand.jquery.css') }}">
    <script type="text/javascript"  src="{{ asset('assets/js/thailand.jquery.js') }}"></script>
    

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Meta --}}
    <meta name="description" content="@yield('description', 'MyLocation')">
    <meta name="keywords" content="@yield('keywords', 'MyLocation')">
    <meta name="author" content="@yield('author', 'MyLocation')">
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <meta name="theme-color" content="#ffffff">

    {{-- Open Graph --}}
    <meta property="og:title" content="@yield('title', 'MyLocation')">
    <meta property="og:description" content="@yield('description', 'MyLocation')">
    <meta property="og:image" content="@yield('image', asset('images/og-image.jpg'))">
    <meta property="og:url" content="@yield('url', url()->full())">
    <meta property="og:type" content="@yield('type', 'website')">
    <meta property="og:site_name" content="@yield('site_name', 'MyLocation')">
    <meta property="og:locale" content="@yield('locale', 'en_US')">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="@yield('twitter_card', 'summary_large_image')">
    <meta name="twitter:site" content="@yield('twitter_site', '@mylocation')">
    <meta name="twitter:creator" content="@yield('twitter_creator', '@mylocation')">
    <meta name="twitter:title" content="@yield('title', 'MyLocation')">
    <meta name="twitter:description" content="@yield('description', 'MyLocation')">
    <meta name="twitter:image" content="@yield('image', asset('images/og-image.jpg'))">

    {{-- Apple Web App --}}
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
</head>

<body class="font-lunasima antialiased bg-gray-200">
    @yield('body')
</body>

@yield('script')

</html>