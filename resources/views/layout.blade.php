<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout_final.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif !important; }</style>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Favicon: ICO for best compatibility, SVG for modern browsers -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" href="{{ asset('faviconSVG.svg') }}" type="image/svg+xml">

    <title>Sunny&Scrambles</title>
</head>
<body>


@auth




 <div class="mainFrame">
    <div class="sideAccess">
        <button class="userProfile">
            <div class="imgFrame"> <img src="{{ asset('images/' . auth()->user()->image) }}" alt="Customer Image"></div>
            <div class="nameFrame">
                @if(auth()->user()->user_type === 'Admin')
                <p class="userName">{{ auth()->user()->store_name }}</p>
                @elseif(auth()->user()->user_type === 'Customer')
                <p class="userName">{{ auth()->user()->store_name }}</p>
                @elseif(auth()->user()->user_type === 'Staff')
                <p class="userName">{{ auth()->user()->username }}</p>
                @endif
                <p class="userTitle">{{ auth()->user()->user_type }}</p> 
            </div>
        </button>
        {{-- <div class="searchFrame">
            <div class="searchCon">
                <span class="material-symbols-outlined">search</span>
                <input type="text" placeholder="Search...">
            </div>

        </div> --}}
        <div class="sideMenu">
            @php
                $currentRoute = Route::currentRouteName();
            @endphp
            @if(auth()->user()->user_type === 'Admin')
            <a class="buttonDiv{{ $currentRoute == 'dashboard' ? ' active' : '' }}" href="{{ route('dashboard') }}">
                <span class="material-symbols-outlined">dashboard</span>
                <p>Dashboard</p>
            </a>
            <a href="{{ route('profile') }}" class="buttonDiv{{ $currentRoute == 'profile' ? ' active' : '' }}">
                <span class="material-symbols-outlined">person</span>
                <p>Profile</p>
            </a>
            <a class="buttonDiv{{ $currentRoute == 'receipts' ? ' active' : '' }}" href="{{ route('receipts') }}">
                <span class="material-symbols-outlined">receipt</span>
                <p>Receipts</p>
            </a>

            <a class="buttonDiv{{ $currentRoute == 'staffs' ? ' active' : '' }}" href="{{ route('staffs') }}">
                <span class="material-symbols-outlined">person</span>
                <p>Staffs</p>
            </a>
            <a class="buttonDiv{{ $currentRoute == 'customers' ? ' active' : '' }}" href="{{ route('customers') }}">
                <span class="material-symbols-outlined">groups</span>
                <p>Customers</p>
            </a>
            {{-- <button class="buttonDiv">
                <span class="material-symbols-outlined">assignment</span>
                <p>Reports</p>
            </button> --}}



                  
            @php
                $currentRoute = Route::currentRouteName();
            @endphp
            @elseif(auth()->user()->user_type === 'Staff')
            <a class="buttonDiv{{ $currentRoute == 'dashboard' ? ' active' : '' }}" href="{{ route('dashboard') }}">
                <span class="material-symbols-outlined">dashboard</span>
                <p>Dashboard</p>
            </a>
            <a class="buttonDiv{{ $currentRoute == 'profile' ? ' active' : '' }}" href="{{ route('profile') }}">
                <span class="material-symbols-outlined">person</span>
                <p>Profile</p>
            </a>
            <a class="buttonDiv{{ $currentRoute == 'receipts' ? ' active' : '' }}" href="{{ route('receipts') }}">
                <span class="material-symbols-outlined">receipt</span>
                <p>Receipts</p>
            </a>
   
            <a class="buttonDiv{{ $currentRoute == 'staffs' ? ' active' : '' }}" href="{{ route('staffs') }}">
                <span class="material-symbols-outlined">person</span>
                <p>Staffs</p>
            </a>

            <a class="buttonDiv{{ $currentRoute == 'customers' ? ' active' : '' }}" href="{{ route('customers') }}">
                <span class="material-symbols-outlined">groups</span>
                <p>Customers</p>
            </a>


            @else



            <a class="buttonDiv{{ $currentRoute == 'dashboard' ? ' active' : '' }}" href="{{ route('dashboard') }}">
                <span class="material-symbols-outlined">dashboard</span>
                <p>Dashboard</p>
            </a>

            <a class="buttonDiv{{ $currentRoute == 'profile' ? ' active' : '' }}" href="{{ route('profile') }}">
                <span class="material-symbols-outlined">person</span>
                <p>Profile</p>
            </a>
            <a class="buttonDiv{{ $currentRoute == 'receipts' ? ' active' : '' }}" href="{{ route('receipts') }}">
                <span class="material-symbols-outlined">receipt</span>
                <p>Receipts</p>
            </a>


  
            <a class="buttonDiv{{ $currentRoute == 'help' ? ' active' : '' }}" href="{{ route('help') }}">
                <span class="material-symbols-outlined">help</span>
                <p>Help</p>
            </a>


            @endif
         
        </div>

        @if(auth()->user()->user_type === 'Customer')

   
        @endif



        <div class="deskFrame">
            <p class="inquiry">INQUIRIES</p>
            <p>For any inquiries, please contact us at rplai_riza@gmail.com or call us at 09123456789</p>
        </div>
        <div class="logoutFrame">
            <form action="/logout-user" method="post">
                @csrf
                <button class="logoutButton">Logout</button>
            </form>
        </div>

        <div class="ownFrame" style="">
            <p>OWNED BY</p>
            <img src="{{ asset('assets/sunnyLogo1.png') }}" alt="Owner Image" width="100" class="ownerImage">
        </div>
    </div>
    <div class="showScreen">

        @yield('content')

    </div>

</div>





@else

login first!

@endauth



    
</body>
</html>