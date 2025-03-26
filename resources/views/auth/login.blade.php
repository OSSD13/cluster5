@extends('layouts.screen')

@section('title', 'Login')

@section('screen')
<div class="w-screen h-screen flex items-center justify-center bg-cover bg-center relative" 
    style="background-image: url('/assets/img/bgLogin.jpg'); background-position: 30% center;">

    <!-- โลโก้ -->
    <div class="absolute top-10 flex flex-col items-center w-full">
        <img src="/assets/img/LogoMyx.png" class="w-48 mb-3" alt="Logo">
    </div>

    <!-- กล่อง Login -->
    <div class="w-[360px] max-w-sm bg-white/80 backdrop-lg p-6 rounded-xl shadow-2xl">
        <h2 class="text-xl font-bold text-gray-100 text-center mb-3">Loginldkf;jksd</h2>

        <!-- ฟอร์ม -->
        <form class="mt-2">
            <label class="block text-gray-700 font-medium">Email</label>
            <input type="email" placeholder="name@example.com" 
                class="w-full p-3 mt-1 rounded-lg border border-gray-300 shadow-md bg-white 
                focus:ring-2 focus:ring-blue-400 focus:outline-none">

            <label class="block text-gray-700 font-medium mt-4">Password</label>
            <input type="password" placeholder="Your Password" 
                class="w-full p-3 mt-1 rounded-lg border border-gray-300 shadow-md bg-white 
                focus:ring-2 focus:ring-blue-400 focus:outline-none">

            <div class="flex justify-between items-center mt-4">
                <a href="#" class="text-sm text-gray-600 hover:text-blue-600">Forgot password?</a>
                <button type="submit" 
                    class="bg-blue-600 text-white px-5 py-2 rounded-lg shadow-md hover:bg-blue-800">
                    Login
                </button>
            </div>

            <div class="flex items-center my-5">
                <hr class="flex-grow border-gray-400">
                <span class="px-3 text-gray-600 text-sm">OR</span>
                <hr class="flex-grow border-gray-400">
            </div>

            <a class="flex items-center justify-center w-full p-3 rounded-lg border border-gray-300 shadow-md bg-white 
                hover:bg-gray-100" href="{{ route('google-auth') }}">
                <img src="/assets/img/LogoGg.png" class="w-6 h-6 mr-2"> Log in with Google
            </a>
        </form>
    </div>
</div>
@endsection
