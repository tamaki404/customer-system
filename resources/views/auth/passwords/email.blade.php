<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/emailReset.css') }}">

    <title>Reset password</title>
</head>
<body>


    <div class="resetFrame">
        <img src="{{ asset(path: 'assets/sunnyLogo1.png') }}" alt="Logo" class="logo">
        <h2>Password Reset</h2>
        <p>Enter your email address to receive a password reset link.</p>
        <form method="POST" action="{{ route('password.email') }}" id="passwordReset">
            @csrf
            <div class="emailInput">
                <p>Email</p>
                <input type="email" name="email" required placeholder="Used email" required>
            </div>
            <button type="submit" id="submitBtn">Reset password</button>

        </form>

        @if (session('status'))
            <p style="color: green">An email has been sent. Please check your inbox and spam folder.</p>
        @endif
        @if ($errors->has('email'))
            <p class="error-email">{{ $errors->first('email') }}</p>
        @endif


        <a href="{{ route('login') }}" >Back to Login</a>
    </div>

</body>
</html>