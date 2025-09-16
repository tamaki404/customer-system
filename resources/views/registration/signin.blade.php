<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ asset('css/registration/signin.css') }}">

    <title>Login</title>
</head>
<body>

    <div class="loginPage" >
        <div class="left">
            <div class="carousel" aria-label="Promotional images" role="region">
                <img src="{{ asset('assets/sunny-chickens.jpg') }}" alt="Happy chickens at the farm" class="carousel-image active">
                <img src="{{ asset('assets/eggTray.jpg') }}" alt="Fresh egg trays" class="carousel-image">
                <img src="{{ asset('assets/store_bg.jpg') }}" alt="Store background" class="carousel-image">
                <button class="carousel-btn prev" aria-label="Previous slide" type="button">&#10094;</button>
                <button class="carousel-btn next" aria-label="Next slide" type="button">&#10095;</button>
            </div>
        </div>
        <div class="right">
            <img src="{{ asset('assets/sunnyLogo1.png') }}" alt="Owner Image">
            <h1>Welcome👋!</h1>
            <p class="kindly-mess">Please log in to your account below</p>

            <form action="{{ route('account.signin') }}" method="POST" class="loginForm" autocomplete="off">
                @csrf
                
                <div class="form-group">
                    <p>Email address</p>
                    <input type="email" name="email_address" value="{{ old('email_address') }}" required>
                    @error('email_address')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <p>Password</p>
                    <input type="password" name="password" autocomplete="off" required>
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="error-display">
                    @if ($errors->has('loginError'))
                        <p>{{ $errors->first('loginError') }}</p>
                    @endif

                    @if(session('error'))
                        <p style="color: red">{{ session('error') }}</p>
                    @endif

                    @if(session('success'))
                        <p style="color: green">{{ session('success') }}</p>
                    @endif

                    
                </div>

                <button type="submit">Sign in</button>
            </form>


            <span>
                Doesn't have an account? 
                <a href="{{ route('registration.signup') }}">Create account</a>
            </span>

        </div>
    </div>

    <script src="{{ asset('js/animations/carousel.js') }}"></script>

</body>
</html>