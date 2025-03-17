<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
        
        @endif
    </head>
    <!-- component -->
<link href="https://fonts.googleapis.com/css2?family=Luckiest+Guy&display=swap" rel="stylesheet">

<style>
.fortnite-btn {
  	background: linear-gradient(#fefb72, #fefca3);
	font-family: 'Luckiest Guy';
}
  
.fortnite-btn-inner {
  	background: linear-gradient(#ede801, #fefb72);
	transform: skew(-5deg);
	color: #343F65;
}
</style>

<button class="fortnite-btn flex items-center justify-center h-32 w-64">
	<span class="fortnite-btn-inner p-3 pt-5 w-11/12 text-5xl truncate">Play</span>
</button>
</html>
