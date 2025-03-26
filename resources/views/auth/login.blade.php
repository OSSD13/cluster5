<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="flex items-center justify-center min-h-screen bg-cover bg-center" style="background-image: url('/assets/img/bgLogin.jpg');">

    <div class="login-box w-[360px] max-w-sm bg-white/80 backdrop-lg p-6 rounded-xl shadow-2xl">
        <h2 class="text-2xl font-bold text-center mb-3">Login</h2>

       

        <form class="mt-2" action="/login" method="POST">
            @csrf
            <label class="block text-gray-700 font-medium">Email</label>
            <input name="email" type="email" placeholder="name@example.com" 
                class="w-full p-3 mt-1 rounded-lg border border-gray-300 shadow-md bg-white focus:ring-2 focus:ring-blue-400 focus:outline-none">

            <label class="block text-gray-700 font-medium mt-4">Password</label>
            <input name="password" type="password" placeholder="Your Password" 
                class="w-full p-3 mt-1 rounded-lg border border-gray-300 shadow-md bg-white focus:ring-2 focus:ring-blue-400 focus:outline-none">

            <div class="flex justify-between items-center mt-4">
                <a href="#" class="text-sm text-gray-600 hover:text-blue-600">Forgot password?</a>
                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg shadow-md hover:bg-blue-800">
                    Login
                </button>
            </div>

            <div class="flex items-center my-5">
                <hr class="flex-grow border-gray-400">
                <span class="px-3 text-gray-600 text-sm">OR</span>
                <hr class="flex-grow border-gray-400">
            </div>

            <a class="flex items-center justify-center w-full p-3 rounded-lg border border-gray-300 shadow-md bg-white hover:bg-gray-100" href="{{ route('google-auth') }}">
                <img src="/assets/img/LogoGg.png" class="w-6 h-6 mr-2"> Log in with Google
            </a>
        </form>
    </div>

</body>
</html>
