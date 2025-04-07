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


<div x-data="{ open: false }" class="relative">
    <!-- Navbar -->
    <nav class="bg-white shadow-md p-4 flex justify-between items-center">
        <!-- Logo -->
        <img src="logo.png" class="h-10" alt="Logo">

        <!-- ปุ่ม Toggle (☰ → ✖) -->
        <button @click="open = !open" class="text-2xl z-50 relative">
            <span x-show="!open">☰</span>
            <span x-show="open">✖</span>
        </button>
    </nav>

    <!-- Dropdown Menu -->
    <div x-show="open" x-transition.duration.300ms
        class="absolute top-0 left-0 w-full bg-white shadow-md p-4 flex flex-col items-center space-y-4 h-auto z-40">
        
        <!-- User Info -->
        <div class="flex items-center space-x-2 mt-8">
            <span class="text-2xl">👤</span>
            <span>torlapsayhi@gmail.com</span>
        </div>

        <!-- Logout Button -->
        <a href="/logout" class="bg-gray-200 px-4 py-2 rounded flex items-center space-x-2">
            <span>Logout</span>
            <span>↗</span>
        </a>
    </div>
</div>






</body>
</html>
