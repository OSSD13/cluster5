@extends('layouts.screen')

@section('screen')
    <div class="min-h-screen h-full w-full flex flex-col">
        <!-- Top Navbar -->
        <nav class="sticky top-0 w-[100%] h-20 bg-white flex items-center p-2 px-6 rounded-b-lg z-10">
            <!-- Logo Section -->
            <div class="flex items-center flex-grow">
                <img src="/assets/img/logo_myLocation.png" alt="Logo" class="h-20 border-r-1 border-gray-800 pr-4">
            </div>

            <!-- Hamburger Menu -->
            <div class="flex items-center">
                <div class="space-y-1">
                    <div class="w-6 h-1 bg-black"></div>
                    <div class="w-6 h-1 bg-black"></div>
                    <div class="w-6 h-1 bg-black"></div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1 p-4 px-7">
            @yield('content')
        </main>

        <!-- Bottom Navbar -->
        <footer
            class="sticky bottom-0 left-0 z-10 w-full h-20 p-1 bg-gray-100 shadow flex items-center justify-around rounded-t-lg flex-shrink-0">
            {{-- class="sticky bottom-0 w-full h-20 p-1 bg-gray-100 shadow flex items-center justify-around rounded-t-lg flex-shrink-0"> --}}
            @php
                $navItems = [
                    ['name' => 'หน้าหลัก', 'path' => '/', 'icon' => 'icon-[material-symbols--home]'],
                    ['name' => 'แผนที่', 'path' => '/map', 'icon' => 'icon-[material-symbols--map]', 'startsWith' => true],
                    ['name' => 'สาขา', 'path' => '/branch', 'icon' => 'icon-[ri--building-fill]', 'startsWith' => true],
                    ['name' => 'สถานที่สนใจ', 'path' => '/poi', 'icon' => 'icon-[material-symbols--star-rounded]', 'startsWith' => true],
                    ['name' => 'จัดการสมาชิก', 'path' => '/user', 'icon' => 'icon-[tdesign--member-filled]', 'startsWith' => true],
                ];
            @endphp

            @foreach ($navItems as $item)
                @php
                    $isActive =
                        ($item['path'] === '/' && request()->path() === '/') ||
                        (isset($item['startsWith']) && $item['startsWith'] ? str_starts_with(request()->path(), ltrim($item['path'], '/')) : request()->is(ltrim($item['path'], '/')));
                @endphp
                <a href="{{ $item['path'] }}"
                    class="flex flex-col items-center text-center w-1/5 {{ $isActive ? 'text-black' : 'text-gray-500' }}">
                    <span class="{{ $item['icon'] }} w-9 h-9"></span>
                    <span class="text-sm truncate w-full">{{ $item['name'] }}</span>
                </a>
            @endforeach
        </footer>
    </div>

@endsection
