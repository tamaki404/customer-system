
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ asset('css/registration/signin.css') }}">
        <link rel="stylesheet" href="{{ asset('css/registration/choose-account.css') }}">
        <link rel="stylesheet" href="{{ asset('css/info/style.css') }}">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
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
            <h1 title="{{ $user->user_id }}">{{$user->customer->company_name}}</h1>
            <p class="kindly-mess">Kindly choose your account to login to</p>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li style="color: #fd0a00">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            <p class="info-display" style="width: 500px">
                <span class="material-symbols-outlined">info</span>
                <span>
                    All accounts shown here, except for the Admin, already have their permissions configured.
                    To modify or set permissions, navigate to Admin: Menu > Groups > Action: Modify Account, then select the desired permissions and apply the changes
                </span>
            </p>

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
                        @if ($rep->auth_position !== "Admin" AND $rep->cid !== NULL)
                            <form action="{{ route('account.signin-representative') }}" method="POST" class="loginForm" style="justify-content: space-evenly; height: auto; margin-top: 10px;">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ Auth()->user()->user_id }}" required>

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
                                    <input type="hidden" name="auth_position" value="{{ $rep->auth_position }}">
                                    <input type="password" name="cid" placeholder="Enter Representative CID">
                                    <button type="submit">Sign in</button>
                                                                    
                                </div>
                            </form>
                        @elseif ($rep->auth_position === "Admin")
                            <form action="{{ route('account.signin-representative') }}" method="POST" class="loginForm" style="justify-content: space-evenly; height: auto;">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ Auth()->user()->user_id }}" required>

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
                                    <input type="hidden" name="auth_position" value="{{ $rep->auth_position }}">

                                    <input type="password" name="password" placeholder="Enter Admin Password">

                                    <button type="submit">Sign in</button>
                                </div>
                            </form>
                        @endif

                    @endforeach





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

                    
            <form action="{{ route('logout') }}" method="POST" style="display:inline; width: auto;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>


        </div>
        
    </div>

    <script src="{{ asset('js/animations/carousel.js') }}"></script>
    <script src="{{ asset('js/registration/selected_accounts.js') }}"></script>

</body>
</html>