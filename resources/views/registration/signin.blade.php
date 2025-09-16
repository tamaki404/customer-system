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

            <form action="/login-user" method="POST" class="loginForm" autocomplete="off">
                @csrf
                <div class="form-group">
                    <p>Email address</p>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <p>Password</p>
                    <input type="password" name="password" autocomplete="off" required>
                </div>
                {{-- <a href="/password/forgot">Forgot password?</a> --}}


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

            <span>
                Doesn't have an account? 
                <a href="{{ route('registration.signup') }}">Create account</a>
            </span>

        </div>
    </div>
    <script>
        (function(){
            var images = document.querySelectorAll('.carousel-image');
            if(!images || images.length === 0) return;
            var prevBtn = document.querySelector('.carousel-btn.prev');
            var nextBtn = document.querySelector('.carousel-btn.next');
            var currentIndex = 0;

            function showSlide(index){
                images[currentIndex].classList.remove('active');
                currentIndex = (index + images.length) % images.length;
                images[currentIndex].classList.add('active');
            }

            function nextSlide(){ showSlide(currentIndex + 1); }
            function prevSlide(){ showSlide(currentIndex - 1); }

            var timer = setInterval(nextSlide, 4000);
            function resetTimer(){
                clearInterval(timer);
                timer = setInterval(nextSlide, 4000);
            }

            if(prevBtn){ prevBtn.addEventListener('click', function(){ prevSlide(); resetTimer(); }); }
            if(nextBtn){ nextBtn.addEventListener('click', function(){ nextSlide(); resetTimer(); }); }
        })();
    </script>
</body>
</html>