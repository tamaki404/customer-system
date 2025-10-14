
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ asset('css/registration/signin.css') }}">
        <link rel="stylesheet" href="{{ asset('css/registration/choose-account.css') }}">

    <title>Choose account</title>
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
            <h1>{{$user->supplier->company_name}}</h1>
            <p class="kindly-mess">Kindly choose your account to login to</p>

            {{-- <form action="{{ route('account.signin-representative') }}" method="POST" style="justify-content:space-evenly;" class="loginForm" autocomplete="off">
                @csrf --}}
                {{-- choose account to login to --}}
                {{-- @foreach ($reps as $rep)
                    <div class="rep-group">
                        @php
                            $imgSrc = auth()->user()->image 
                                ? ('data:' . auth()->user()->image_mime_type . ';base64,' . base64_encode(auth()->user()->image))
                                : asset('images/default-avatar.png');
                        @endphp
                        <img src="{{ $imgSrc }}" class="rep-img" alt="Profile Image">
                        <p>
                            <span class="rep-name">{{ $rep->rep_lastname }}, {{ $rep->rep_firstname }}, {{ $rep->rep_middlename }}</span>
                            <span class="rep-pos">{{ $rep->auth_position }}</span>
                        </p>
                        
                    </div>               
                @endforeach --}}
@foreach ($reps as $rep)
    <form action="{{ route('account.signin-representative') }}" method="POST" class="loginForm">
        @csrf
        <div class="rep-group" onclick="showLoginForm(this)">
            @php
                $imgSrc = auth()->user()->image 
                    ? ('data:' . auth()->user()->image_mime_type . ';base64,' . base64_encode(auth()->user()->image))
                    : asset('images/default-avatar.png');
            @endphp
            <img src="{{ $imgSrc }}" class="rep-img" alt="Profile Image">
            <p>
                <span class="rep-name">{{ $rep->rep_lastname }}, {{ $rep->rep_firstname }} {{ $rep->rep_middlename }}</span>
                <span class="rep-pos">{{ $rep->auth_position }}</span>
            </p>
        </div>

        <div class="rep-inputs" style="display: none;">
            <input type="hidden" name="rep_id" value="{{ $rep->rep_id }}">
            <input type="hidden" name="user_id" value="{{ Auth()->user()->user_id }}">

            @if (strtolower($rep->auth_position) === 'admin')
                <input type="password" name="password" placeholder="Enter Admin Password">
            @else
                <input type="text" name="cid" placeholder="Enter Representative CID">
            @endif

            <button type="submit">Sign in</button>
        </div>
    </form>
@endforeach

<script>
    function showLoginForm(selected) {
        // Hide inputs of all forms
        document.querySelectorAll('.loginForm .rep-inputs').forEach(inputs => {
            inputs.style.display = 'none';
        });

        // Show inputs of the selected form
        const form = selected.closest('.loginForm');
        const inputs = form.querySelector('.rep-inputs');
        inputs.style.display = 'block';
    }
</script>



                {{-- display account picked --}}
                {{-- if auth_position === Admin,  use the password input tag and let them input user->password that === $request->password in the backend,
                and elseif auth_position !==, use name="cid" input tag and match cid to representative->cid to log in to this account. 
                theyre already authethicated but this step of log in is just for permissions set in representatives->permissions --}}
                {{-- <div>
                    <input type="hidden" name="rep_id" value="{{ $rep->rep_id }}">
                    <input type="hidden" name="user_id" value="{{ Auth()->user()->user_id }}">

                        <p>
                            <span class="rep-name">{{ $rep->rep_lastname }}, {{ $rep->rep_firstname }}, {{ $rep->rep_middlename }}</span>
                            <span class="rep-pos">{{ $rep->auth_position }}</span>
                        </p>                    
                        <input type="text" name="cid" value="{{ $rep->cid }}">
                        <input type="password" name="password" value="{{ $rep->cid }}">

                    <button type="submit">Sign in</button>
                </div> --}}

            {{-- </form> --}}


        </div>
    </div>

    <script src="{{ asset('js/animations/carousel.js') }}"></script>

</body>
</html>