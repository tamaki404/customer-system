<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ asset('css/login.css') }}">

    <title>Sign Up</title>
</head>
<body>
    <form method="POST" action="/register-user" class="logForm">
        @csrf
        <h2>Create Account</h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipisci elit, sed eiusmod</p>
        <span><p>Already have an account?</p> <a href="/">Sign In</a></span>
          <input type="text" placeholder="Username" name="username">
          <input type="password" placeholder="Password" name="password">
          <button>Sign Up</button>
    </form>
</body>
</html>