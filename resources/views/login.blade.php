<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ asset('css/login.css') }}">

    <title>Login</title>
</head>
<body>
        <!-- <div class="texts">
            <span><h2>Rizal Poultry</h2> <h4>&</h4></span> 
            <h3>Livestock association inc.</h3>
            <p>We’re more than just poultry — we support Filipino farmers, promote sustainability, and help ensure food security for every household.</p>
            
        </div> -->
        <form method="POST" action="/login-user" class="logForm">
            @csrf

            <p>Goodmorning!</p>
            <p>Hello, Please enter your details</p>

            @if ($errors->has('loginError'))
                <div style="color: red;">{{ $errors->first('loginError') }}</div>
            @endif

            <input type="text" name="username" placeholder="Username" value="{{ old('username') }}" required>
            <input type="password" name="password" placeholder="Password" required>
            <a href="/password/forgot" class="forgotPass">Forgot password</a>

            <button type="submit">Login</button>

            <span><p>New user?</p> <a href="/register-view">Create account</a></span>

            @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif


        </form>




</body>
</html>