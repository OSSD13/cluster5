@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')

<!-- ช่องค้นหา -->
<div class="">
    <div class="flex items-center bg-white rounded-lg p-2">
        <input type="text" placeholder="ค้นหา" class="flex-grow p-2 outline-none">
        <button class="text-gray-500 px-2"><i class="fas fa-search"></i></button>
    </div>

    <div class="mt-2 flex space-x-2">
        <button class="font-bold bg-blue-500 text-white px-4 py-2 rounded-lg w-full ">วิเคราะห์</button>
        <select class="bg-white px-4 py-2 rounded-lg">
            <option>1.0 km</option>
            <option>2.0 km</option>
            <option>5.0 km</option>
        </select>
    </div>
</div>

    @for ($i = 0; $i < 1; $i++)
        <p>{{ session('user') }}</p>
    @endfor
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="cursor-pointer bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Logout
        </button>
        {{-- <button type="submit" class="submit">Logout</button> --}}
    </form>
@endsection