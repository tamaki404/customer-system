<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

    <title>Dashboard</title>
</head>
<body>


@auth
<!-- <h1>Welcome, {{ auth()->user()->username }}!</h1>


<form action="/logout-user" method="post">
    @csrf
    <button>logout</button>
</form>
<button>Manage staffs</button>
<button>Customers</button>
<button>Receipts</button>
<button>Profile</button>
<button>Reports</button>
<button>Help</button> -->

<div class="mainFrame">
    <div class="sideAccess">
        <button class="userProfile">
            <div class="imgFrame"> <img src="{{ asset('images/' . auth()->user()->image) }}" alt="Customer Image" width="100"></div>
            <div class="nameFrame"><p class="userName">Nicole Tumpag</p><p class="userTitle">{{ auth()->user()->user_type }}</p> </div>
        </button>
        <div class="searchFrame">
            <div class="searchCon">
                <span class="material-symbols-outlined">search</span>
                <input type="text" placeholder="Search...">
            </div>

        </div>
        <div class="sideMenu">
            @if(auth()->user()->user_type === 'Admin')
            <button class="buttonDiv">
                <span class="material-symbols-outlined">person</span>
                <p>Profile</p>
            </button>
            <button class="buttonDiv">
                <span class="material-symbols-outlined">dashboard</span>
                <p>Dashboard</p>
            </button>
            <button class="buttonDiv">
                <span class="material-symbols-outlined">confirmation_number</span>
                <p>Tickets</p>
            </button>
            <button class="buttonDiv">
                <span class="material-symbols-outlined">dynamic_feed</span>
                <p>Feed</p>
            </button>     
            <a class="buttonDiv" href="{{ route('staffs') }}">
                <span class="material-symbols-outlined">person</span>
                <p>Staffs</p>
            </a>
            <button class="buttonDiv">
                <span class="material-symbols-outlined">groups</span>
                <p>Customers</p>
            </button>
            <button class="buttonDiv">
                <span class="material-symbols-outlined">assignment</span>
                <p>Reports</p>
            </button>

            @elseif(auth()->user()->user_type === 'Staff')
            <button class="buttonDiv">
                <span class="material-symbols-outlined">person</span>
                <p>Profile</p>
            </button>
            <button class="buttonDiv">
                <span class="material-symbols-outlined">confirmation_number</span>
                <p>Tickets</p>
            </button>
            <button class="buttonDiv">
                <span class="material-symbols-outlined">dynamic_feed</span>
                <p>Feed</p>
            </button>        
            <button class="buttonDiv">
                <span class="material-symbols-outlined">groups</span>
                <p>Customers</p>
            </button>


            @else
            <a class="buttonDiv" href="{{ route('profile') }}">
                <span class="material-symbols-outlined">person</span>
                <p>Profile</p>
            </a>

            <a class="buttonDiv" href="{{ route('tickets') }}">
                <span class="material-symbols-outlined">confirmation_number</span>
                <p>Tickets Sent</p>
            </a>

            <button class="buttonDiv">
                <span class="material-symbols-outlined">dynamic_feed</span>
                <p>Feed</p>
            </button>

            <button class="buttonDiv">
                <span class="material-symbols-outlined">assignment</span>
                <p>Filed Reports</p>
            </button>
            <button class="buttonDiv">
                <span class="material-symbols-outlined">help</span>
                <p>Help</p>
            </button>


            @endif
         
        </div>

        @if(auth()->user()->user_type === 'Customer')

        <div class="deskFrame">
            <p>INQUIRIES</p>
            <p>For any inquiries, please contact us at rplai_riza@gmail.com or call us at 09123456789</p>
        </div>
   
        @endif



        <div class="logoutFrame">
            <form action="/logout-user" method="post">
                @csrf
                <button class="logoutButton">Logout</button>
            </form>
        </div>

        <div class="ownFrame">
            <p>OWNED BY</p>
            <img src="{{ asset('assets/rplai_logo.svg') }}" alt="Owner Image" width="100">
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