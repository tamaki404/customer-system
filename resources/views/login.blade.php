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
<form method="POST" action="/login-user" class="logForm">
    @csrf

    <h2>Welcome back!</h2>
    <p>Lorem ipsum dolor sit amet, consectetur adipisci elit, sed eiusmod</p>
    <span><p>New user?</p> <a href="/register-view">Create account</a></span>

    @if ($errors->has('loginError'))
        <div style="color: red;">{{ $errors->first('loginError') }}</div>
    @endif

    <input type="text" name="username" placeholder="Username" value="{{ old('username') }}" required>
    <input type="password" name="password" placeholder="Password" required>

    <button type="submit">Login</button>
    <a href="/forgot-password">Forgot password</a>
</form>


</body>
</html>