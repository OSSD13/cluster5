<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="w-screen h-screen overflow-hidden flex items-center justify-center bg-cover bg-center" 
    style="background-image: url('/assets/img/bgLogin.jpg'); background-position: 30% center">

    <div class="w-[342px] h-[500px] max-w-sm bg-white/70 backdrop-b-lg p-6 rounded-[8px] shadow-lg">
        <!-- Logo -->
        <img src="/assets/img/LogoMyx.png" class="w-48 mx-auto mb-1" alt="Logo">
        
        <h2 class="text-xl font-bold text-center">Login</h2>

        <!-- Form -->
        <form class="mt-2">
            <label class="self-start block text-gray-700 font-medium ">Email</label>
            <input type="email" placeholder="name@example.com" 
                class="w-full p-2 mt-1 rounded-md shadow-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">

            <label class="block text-gray-700 font-medium mt-3">Password</label>
            <input type="password" placeholder="Your Password" 
                class="w-full p-2 mt-1 rounded-md shadow-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">

            <div class="flex justify-between items-center mt-4">
                <a href="#" class="text-sm text-gray-600 hover:text-blue-600">Forgot password?</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-800">Login</button>
            </div>

            <div class="flex items-center my-4">
                <hr class="flex-grow border-gray-400">
                <span class="px-2 text-gray-600">OR</span>
                <hr class="flex-grow border-gray-400">
            </div>

            <a class="flex items-center justify-center w-full p-2 rounded-md shadow-sm bg-white hover:bg-gray-100" href="{{ route('google-auth') }}">
                <img src="/assets/img/LogoGg.png" class="w-6 h-6 mr-2"> Login with Google
            </a>

        </form>
    </div>
</body>
</html>