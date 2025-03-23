<<<<<<< HEAD
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
<<<<<<< HEAD
    <link rel="stylesheet" href="styles.css">
</head>
<style>
    body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: url('/assets/img/bgLogin.jpg') no-repeat center center/cover;
        }
.bgLogin {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: -1;
    filter: blur(10px);
    background-size: cover;
    background-position: center;
}

.login-box {
    background: rgba(255, 255, 255, 0.7);
    padding: 2rem;
    border-radius: 1rem;
    box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
    width: 24rem;
    margin: 30px;
}

/* สไตล์ของ Label */
.label {
    display: block;
    color: #4a5568; /* text-gray-700 */
    font-weight: 600;
}

/* สไตล์ของ Input Field */
.input-email {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
    margin-top: 0.25rem;
    outline: none;
    transition: border 0.2s, box-shadow 0.2s;
}
.input-password {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
    margin-top: 0.25rem;
    outline: none;
    transition: border 0.2s, box-shadow 0.2s;
}

.input-field:focus {
    border-color: #3b82f6; /* blue-400 */
    box-shadow: 0 0 5px rgba(59, 130, 246, 0.5);
}

/* Forgot Password Link */
.forgot-password {
    font-size: 0.875rem;
    color: #3C3C3C;

    transition: color 0.2s;
}

.forgot-password:hover {
    color: #2563eb;
}

/* ปุ่ม Login */
.login-btn {
    background: #2563eb;
    color: white;
    padding: 0.5rem 1.5rem;
    border-radius: 0.5rem;
    transition: background 0.2s;
    box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
}

.login-btn:hover {
    background: #1e40af;
}

/* เส้นคั่น OR */
.separator {
    display: flex;
    align-items: center;
    margin: 1.5rem 0;
}

.line {
    flex-grow: 1;
    border-top: 3px solid #D9D9D9;
}

.or-text {
    padding: 0 0.75rem;
    color: #6b7280;
}

/* ปุ่ม Google */
.google-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    padding: 0.5rem;
    transition: background 0.2s;
    background: white;
    box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
}

.google-btn:hover {
    background: #f3f4f6;
}

.google-icon {
    width: 1.5rem;
    height: 1.5rem;
    margin-right: 0.75rem;


}
.iconLogo {
    width: 269px;
    height: 132px;
    margin: 0 auto 10px auto; /* จัดให้อยู่กึ่งกลางแนวนอน */
    display: block; /* ทำให้ margin auto มีผล */
}


</style>
<body class="flex items-center justify-center min-h-screen" >
    <div><img src="/assets/img/LogoMyx.png" class="iconLogo" alt="">
    <div class="login-box">

        <h2 class="text-2xl font-bold text-center">Login</h2>

        <form class="mt-4" action="/login" method="POST">
            @csrf
            <label class="label">Email</label>
            <input name="email" type="email" placeholder="name@example.com" class="input-email">

            <label class="label mt-4">Password</label>
            <input name="password" type="password" placeholder="Your Password" class="input-password">

            <div class="flex justify-between items-center mt-4">
                <a href="#" class="forgot-password">Forgot password?</a>
                <button class="login-btn">Login</button>
            </div>
            @if (session('error'))
                <div class="text-red-500 text-sm mt-2">
                    {{ session('error') }}
                </div>
            @endif

            <div class="separator">
                <hr class="line">
                <span class="or-text">OR</span>
                <hr class="line">
            </div>

            <a class="google-btn" href="{{  route('google-auth') }}">
                <img src="/assets/img/LogoGg.png" class="google-icon">
                Login with Google
            </a>
        </form>
    </div>
</div>
=======
</head>
<body class="w-screen h-screen overflow-hidden flex items-center justify-center bg-cover bg-center" 
    style="background-image: url('/assets/img/bgLogin.jpg'); background-position: 30% center">
=======
@extends('layouts.screen')
>>>>>>> f9d4b34 (fix(login):แก้ไขเลย์เอ้า2)

@section('title', 'Login')

@section('screen')
<div class="w-screen h-screen flex items-center justify-center bg-cover bg-center relative" 
    style="background-image: url('/assets/img/bgLogin.jpg'); background-position: 30% center;">

    <!-- โลโก้ -->
    <div class="absolute top-40 flex flex-col items-center">
        <img src="/assets/img/LogoMyx.png" class="w-48 mb-3" alt="Logo">
    </div>

    <!-- กล่อง Login -->
    <div class="w-[360px] max-w-sm bg-white/80 backdrop-lg p-6 rounded-xl shadow-2xl">
        <h2 class="text-xl font-bold text-gray-900 text-center mb-3">Login</h2>

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
<<<<<<< HEAD
>>>>>>> 014d5eb (fix(login):แก้ไขสวยๆ)
</body>
</html>
<<<<<<< HEAD
=======
=======
</div>
@endsection
>>>>>>> f9d4b34 (fix(login):แก้ไขเลย์เอ้า2)
>>>>>>> 53db682 (fix(login):แก้ไขเลย์เอ้า2)
