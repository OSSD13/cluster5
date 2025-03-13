<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

<<<<<<< HEAD
<<<<<<< HEAD
<h1>hello</h1>
=======
    <h1>Information</h1>
    <p>{{ $value }}</p>
=======
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
>>>>>>> 81b3fa3 (style(login): เพิ่ม UI login)

>>>>>>> d4d74f8 (ทmoo)

</style>
<body class="flex items-center justify-center min-h-screen" >
    <div><img src="/assets/img/LogoMyx.png" class="iconLogo" alt="">
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
                <img src="/assets/img/LogoGg.png" class="google-icon">
                Login with Google
            </button>
        </form>
    </div>
</div>
</body>
</html>