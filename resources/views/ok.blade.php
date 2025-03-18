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
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> d77ac94 (Add new component layout and route for test view)
<<!-- component -->
<!-- This is an example component -->
<div class="flex flex-col">
  <div>
      <button type="button" class="bg-blue-600 text-white p-2 rounded  leading-none flex items-center">
          Notifications <span class="bg-white p-1 rounded text-blue-600 text-xs ml-2">4</span>
      </button>
  </div>
  <div class="mt-8 flex">
    <button type="button" class="mr-2 bg-blue-600 text-white p-2 rounded  leading-none flex items-center">
      	New
    </button>
    <button type="button" class="mr-2 bg-red-600 text-white p-2 rounded  leading-none flex items-center">
      	New
    </button>
    
    <button type="button" class="mr-2 bg-orange-600 text-white p-2 rounded  leading-none flex items-center">
      	New
    </button>
    
    <button type="button" class="mr-2 bg-green-600 text-white p-2 rounded  leading-none flex items-center">
      	New
    </button>
    
    <button type="button" class="bg-white text-black p-2 rounded  leading-none flex items-center">
      	New
    </button>
  </div>
<<<<<<< HEAD
  
  
  <div class="mt-8 flex">
    <button type="button" class="rounded-full px-4 mr-2 bg-blue-600 text-white p-2 rounded  leading-none flex items-center">
      	New
    </button>
    <button type="button" class="rounded-full px-4 mr-2 bg-red-600 text-white p-2 rounded  leading-none flex items-center">
      	New
    </button>
    
    <button type="button" class="rounded-full px-4 mr-2 bg-orange-600 text-white p-2 rounded  leading-none flex items-center">
      	New
    </button>
    
    <button type="button" class="rounded-full px-4 mr-2 bg-green-600 text-white p-2 rounded  leading-none flex items-center">
      	New
    </button>
    
    <button type="button" class="rounded-full px-4 bg-white text-black p-2 rounded  leading-none flex items-center">
      	New
    </button>
  </div>
</div>
=======
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
>>>>>>> 30d3bf4 (Chg)
=======
  
  
  <div class="mt-8 flex">
    <button type="button" class="rounded-full px-4 mr-2 bg-blue-600 text-white p-2 rounded  leading-none flex items-center">
      	New
    </button>
    <button type="button" class="rounded-full px-4 mr-2 bg-red-600 text-white p-2 rounded  leading-none flex items-center">
      	New
    </button>
    
    <button type="button" class="rounded-full px-4 mr-2 bg-orange-600 text-white p-2 rounded  leading-none flex items-center">
      	New
    </button>
    
    <button type="button" class="rounded-full px-4 mr-2 bg-green-600 text-white p-2 rounded  leading-none flex items-center">
      	New
    </button>
    
    <button type="button" class="rounded-full px-4 bg-white text-black p-2 rounded  leading-none flex items-center">
      	New
    </button>
  </div>
</div>
>>>>>>> d77ac94 (Add new component layout and route for test view)
</html>
