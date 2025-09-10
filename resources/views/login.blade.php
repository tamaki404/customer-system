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

    
    <div class="loginPage" >
        <div class="left">
            <img src="{{ asset('assets/sunny-chickens.jpg') }}" alt="Owner Image"  class="ownerImage">
        </div>
        <div class="right">
            <img src="{{ asset('assets/sunnyLogo1.png') }}" alt="Owner Image">
            <h1>Welcome back👋!</h1>
            <p class="kindly-mess">Please log in to your account below</p>



            <form action="/login-user" method="POST" class="loginForm" autocomplete="off">
                @csrf
                <div class="form-group">
                    <p>Username or email</p>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <p>Password</p>
                    <input type="password" name="password" autocomplete="off" required>
                </div>
                <a href="/password/forgot">Forgot password?</a>


                <div class="error-display">     
                    @if ($errors->has('loginError'))
                            <p>{{ $errors->first('loginError') }}</p>
                    @endif

                    @if(session('error'))
                            <p>{{ session('error') }}</p>
                    @endif

                    @if(session('success'))
                            <p style="color: green">{{ session('success') }}</p>
                    @endif

                </div>

                <button type="submit">Sign in</button>

                
                
            </form>



            <span>Doesn't have an ccount? <a href="/register-view">Create account</a></span>

            
        </div>
        
    </div>


</body>
</html>