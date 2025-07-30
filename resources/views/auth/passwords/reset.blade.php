
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
        <span><p>{{ old('email', request('email')) }}</p>you can now reset your password</span>
       
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ old('email', request('email')) }}">
            <div class="emailInput">
                <p>New password</p>
                <input type="password" name="password" required placeholder=" ">
            </div>
            <div class="emailInput" style="margin-top: 7px">
                <p>Confirm password</p>
                <input type="password" name="password_confirmation" required placeholder="">
            </div>
            <button type="submit">Reset Password</button>
        </form>


        @if ($errors->has('password'))
            <p class="error-password">{{ $errors->first('password') }}</p>
        @endif
        @if ($errors->has('password_confirmation'))
            <p class="error-password">{{ $errors->first('password_confirmation') }}</p>
        @endif



        <a href="{{ route('login') }}">Back to Login</a>
    </div>

</body>
</html>