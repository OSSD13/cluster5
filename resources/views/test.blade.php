<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Tailwind Navbar</title>
    @vite('resources/css/app.css') <!-- โหลด Tailwind CSS -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100">
<!-- ติดตั้ง Alpine.js -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<!-- Navbar -->
<nav x-data="{ open: false }" class="sticky top-0 w-full h-20 bg-white flex items-center p-2 px-6 rounded-b-lg shadow-md">
    <!-- Logo Section -->
    <div class="flex items-center flex-grow">
        <img src="/assets/img/logo_myLocation.png" alt="Logo" class="h-20 border-r border-gray-800 pr-4">
    </div>

    <!-- Hamburger Menu / Close Button -->
    <div class="flex items-center">
        <button @click="open = !open" class="focus:outline-none">
            <template x-if="!open">
                <div class="space-y-1">
                    <div class="w-6 h-1 bg-black"></div>
                    <div class="w-6 h-1 bg-black"></div>
                    <div class="w-6 h-1 bg-black"></div>
                </div>
            </template>
            <template x-if="open">
                <div class="text-3xl font-bold">&times;</div>
            </template>
        </button>
    </div>
</nav>

<!-- Dropdown Menu -->
<div x-show="open" x-transition.opacity x-transition.scale.origin.top class="absolute top-20 left-0 w-full bg-white shadow-md p-4 flex flex-col items-start">
    <div class="flex items-center space-x-3">
        <div class="bg-gray-300 p-2 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 12c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm0 2c-3.33 0-10 1.67-10 5v3h20v-3c0-3.33-6.67-5-10-5z"/>
            </svg>
        </div>
        <span class="text-lg font-semibold">torlapsayhi@gmail.com</span>
    </div>
    <button class="mt-4 bg-gray-200 px-4 py-2 rounded-lg flex items-center space-x-2">
        <span>Logout</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M10 16l5-4-5-4v3H0v2h10v3zM23 2H10V0h13c.55 0 1 .45 1 1v22c0 .55-.45 1-1 1H10v-2h13V2z"/>
        </svg>
    </button>
</div>


</body>
</html>
