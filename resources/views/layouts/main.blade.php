@extends('layouts.screen')

@section('screen')
<div class="min-h-screen h-full w-full flex flex-col">
    <!-- Top Navbar -->
    <div x-data="{ open: false }">
        <nav :class="{'rounded-b-lg': !open}"
            class="sticky top-0 w-full h-20 bg-white flex items-center p-2 px-6 z-10 transition-all ease-out duration-10">
            <!-- Logo Section -->
            <div class="flex items-center flex-grow">
                <div class="flex items-center border-r border-gray-400 h-12 pr-4">
                    <img src="{{ asset('assets/img/logo_myLocation.png') }}" alt="Logo" class="h-20">
                </div>
            </div>

            <!-- Hamburger Menu / Close Button -->
            <button @click="open = !open" class="relative z-50">
                <div x-show="!open" x-cloak class="space-y-1">
                    <div class="w-6 h-1 bg-black"></div>
                    <div class="w-6 h-1 bg-black"></div>
                    <div class="w-6 h-1 bg-black"></div>
                </div>
                <svg x-show="open" xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24">
                    <path fill="currentColor"
                        d="m12 13.4l-4.9 4.9q-.275.275-.7.275t-.7-.275t-.275-.7t.275-.7l4.9-4.9l-4.9-4.9q-.275-.275-.275-.7t.275-.7t.7-.275t.7.275l4.9 4.9l4.9-4.9q.275-.275.7-.275t.7.275t.275.7t-.275.7L13.4 12l4.9 4.9q.275.275.275.7t-.275.7t-.7.275t-.7-.275z" />
                </svg>
            </button>
        </nav>

        <!-- Dropdown Menu -->
        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-5" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-5"
            class="absolute top-20 left-0 w-full bg-white shadow-md p-4 flex flex-col items-center space-y-4 z-40 rounded-b-lg">

            <!-- User Info -->
            <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path fill="currentColor"
                        d="M6.5 7.5a5.5 5.5 0 1 1 11 0a5.5 5.5 0 0 1-11 0M3 19a5 5 0 0 1 5-5h8a5 5 0 0 1 5 5v3H3z" />
                </svg>
                <span style="font-weight:bold;">{{ session()->get('user')->email }}</span>
            </div>

            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-gray-200 px-4 py-2 flex items-center space-x-2 rounded-md">
                    <span>Logout</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <path fill="currentColor"
                            d="M5 21q-.825 0-1.412-.587T3 19V5q0-.825.588-1.412T5 3h6q.425 0 .713.288T12 4t-.288.713T11 5H5v14h6q.425 0 .713.288T12 20t-.288.713T11 21zm12.175-8H10q-.425 0-.712-.288T9 12t.288-.712T10 11h7.175L15.3 9.125q-.275-.275-.275-.675t.275-.7t.7-.313t.725.288L20.3 11.3q.3.3.3.7t-.3.7l-3.575 3.575q-.3.3-.712.288t-.713-.313q-.275-.3-.262-.712t.287-.688z" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 p-4 px-4">
        @yield('content')
    </main>

    <!-- Bottom Navbar -->
    <footer
    class="sticky bottom-0 left-0 z-10 w-full h-19.5 p-1 bg-gray-100 shadow flex items-center justify-around rounded-t-lg flex-shrink-0">
    @php
        $basePath = trim(parse_url(config('app.url'), PHP_URL_PATH), '/'); // will be "cluster5" or ""
        $prefix = $basePath ? "/$basePath" : '';

        $navItems = [
            ['name' => 'หน้าหลัก', 'path' => '/', 'icon' => 'icon-[material-symbols--home]'],
            ['name' => 'แผนที่', 'path' => '/map', 'icon' => 'icon-[material-symbols--map]', 'startsWith' => true],
            ['name' => 'สาขา', 'path' => '/branch', 'icon' => 'icon-[ri--building-fill]', 'startsWith' => true],
            ['name' => 'สถานที่สนใจ', 'path' => '/poi', 'icon' => 'icon-[material-symbols--star-rounded]', 'startsWith' => true],
            ['name' => 'สมาชิก', 'path' => '/user', 'icon' => 'icon-[tdesign--member-filled]', 'startsWith' => true],
        ];
    @endphp

@php
    $navItems = [
        ['name' => 'หน้าหลัก', 'path' => '/', 'icon' => 'icon-[material-symbols--home]'],
        ['name' => 'แผนที่', 'path' => '/map', 'icon' => 'icon-[material-symbols--map]', 'startsWith' => true],
        ['name' => 'สาขา', 'path' => '/branch', 'icon' => 'icon-[ri--building-fill]', 'startsWith' => true],
        ['name' => 'สถานที่สนใจ', 'path' => '/poi', 'icon' => 'icon-[material-symbols--star-rounded]', 'startsWith' => true],
        ['name' => 'สมาชิก', 'path' => '/user', 'icon' => 'icon-[tdesign--member-filled]', 'startsWith' => true],
    ];
@endphp

@foreach ($navItems as $item)
    @php
        $fullPath = $item['path'];

        $currentPath = request()->path();
        $relativePath = ltrim($fullPath, '/');

        $isActive =
            ($item['path'] === '/' && $currentPath === '/') ||
            (isset($item['startsWith']) && $item['startsWith']
                ? str_starts_with($currentPath, trim($relativePath, '/'))
                : $currentPath === trim($relativePath, '/'));
    @endphp

    <a href="{{ url($fullPath) }}"
        class="flex flex-col items-center text-center w-1/5 {{ $isActive ? 'text-black' : 'text-gray-500' }}">
        <span class="{{ $item['icon'] }} w-7 h-8"></span>
        <span class="text-sm truncate w-full">{{ $item['name'] }}</span>
    </a>
@endforeach

</footer>

</div>
@endsection
