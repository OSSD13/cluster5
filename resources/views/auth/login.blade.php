<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
<<<<<<< Updated upstream
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
=======
    <link rel="stylesheet" href="styles.css"> 
</head>
<style>
  
.login-box {
    background: rgba(255, 255, 255, 0.7);
    padding: 2rem;
    border-radius: 1rem;
    box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
    width: 24rem;
}

/* สไตล์ของ Label */
.label {
    display: block;
    color: #4a5568; /* text-gray-700 */
    font-weight: 600;
}
>>>>>>> Stashed changes

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
    color: #6b7280;

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
    border-top: 1px solid #d1d5db;
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
}

.google-btn:hover {
    background: #f3f4f6;
}

.google-icon {
    width: 1.5rem;
    height: 1.5rem;
    margin-right: 0.75rem;
    box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
}

</style>
<body class="flex items-center justify-center min-h-screen" >
    <img src="/Applications/XAMPP/xamppfiles/htdocs/88823665-camp-66/login.jpg" alt="">
    <div class="login-box">
        <h2 class="text-2xl font-bold text-center">Login</h2>

        <form class="mt-4">
            <label class="label">Email</label>
            <input type="email" placeholder="name@example.com" class="input-email">

            <label class="label mt-4">Password</label>
            <input type="password" placeholder="Your Password" class="input-password">

            <div class="flex justify-between items-center mt-4">
                <a href="#" class="forgot-password">Forgot password?</a>
                <button class="login-btn">Login</button>
            </div>

            <div class="separator">
                <hr class="line">
                <span class="or-text">OR</span>
                <hr class="line">
            </div>

            <button class="google-btn">
                <img src="https://www.google.com/imgres?q=login%20with%20google%20logo&imgurl=https%3A%2F%2Fw7.pngwing.com%2Fpngs%2F937%2F156%2Fpng-transparent-google-logo-google-search-google-account-redes-search-engine-optimization-text-service-thumbnail.png&imgrefurl=https%3A%2F%2Fwww.pngwing.com%2Fen%2Fsearch%3Fq%3Dgoogle%2BAccount&docid=dfljTXCHCwVsNM&tbnid=GO4v_Lbi9z93zM&vet=12ahUKEwiLr9OXmoaMAxV2zDgGHfQFOoEQM3oECH8QAA..i&w=360&h=360&hcb=2&ved=2ahUKEwiLr9OXmoaMAxV2zDgGHfQFOoEQM3oECH8QAA" class="google-icon">
                Log in with Google
            </button>
        </form>
    </div>
</body>
</html>