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
    <form action="/login-user" class="logForm">
        <h2>Login</h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipisci elit, sed eiusmod</p>
        <span><p>New user?</p> <a href="/register-user">Create account</a></span>
          <input type="text" placeholder="Username" name="username">
          <input type="password" placeholder="Password" name="password">
          <button>Login</button>
          <a href="/forgot-password">Forgot password</a>
    </form>
</body>
</html>