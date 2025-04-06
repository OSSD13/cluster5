<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Maps URL Converter</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- andypf/json-viewer -->
    <script src="https://cdn.jsdelivr.net/npm/@andypf/json-viewer@2.1.10/dist/iife/index.js"></script>
</head>
<body>

    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Sign in to start your session</p>
            
            @if(session('error'))
                <h3 class="text-danger">{{ session('error') }}</h3>
            @endif
            
            <form action="{{ url('/login') }}" method="post">
                @csrf
                <div class="input-group mb-3">
                <input type="email" name="email" value="{{ isset($email) ? $email : "" }}"
                    class="form-control" placeholder="Email" />
                    <div class="input-group-text"><span class="bi bi-envelope"></span></div>
                </div>
                
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" />
                    <div class="input-group-text"><span class="bi bi-lock-fill"></span></div>
                </div>
                
                <div class="row">
                    <div class="col-8">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" />
                            <label class="form-check-label" for="flexCheckDefault"> Remember Me </label>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </div>
                </div>
            </form>
        </div> <!-- ปิด card-body -->
    </div> <!-- ปิด card -->

</body>
</html>
