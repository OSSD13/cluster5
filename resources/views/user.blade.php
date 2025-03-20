@extends('layouts.main')

@section('title', 'User')

@section('content')
    @for ($i = 0; $i < 15; $i++)
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
