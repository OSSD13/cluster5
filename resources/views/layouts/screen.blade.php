@extends('layouts.dependency')

@section('body')
<div class="w-full flex justify-center text-black transition bg-white">
    <div class="h-full w-full min-h-screen max-w-[28rem] bg-white flex flex-nowrap relative break-words whitespace-normal ">
        <div class="w-full min-w-screen bg-primary-dark h-64 rounded-b-[6rem] ">
            @yield('screen')
        </div>
    </div>
</div>
@endsection
