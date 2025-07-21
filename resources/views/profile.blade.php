@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">

    <title>Profile</title>
</head>
<body>

<div class="profileFrame">
         <h2>Edit Profile</h2>
         <p>You can edit or change your details here</p>

        @if(auth()->user()->user_type === 'Customer')

        <form action="/edir-profile">
            <input type="text" name="username" value="{{auth()->user()->username}}">
            <input type="text" name="company" value="Xeultu deves .inc">
            <input type="text" name="address" value="Rodriguez, Rizal">
            <input type="email" name="email" value="xeultu@gmail.com">
            <input type="phone" name="number" value="+63912345678">


            <button>Update Profile</button>
        </form>

        @elseif(auth()->user()->user_type === 'staff')
        <div class="">
            <div class=""> <img src="{{ asset('images/' . auth()->user()->image) }}" alt="Customer Image" width="100"></div>
            <div class=""><p class="userName">Nicole Tumpag</p><p class="userTitle">{{ auth()->user()->user_type }}</p> </div>
             
            <p>Profile image and name are given by the IT admin or hr, contact them for changes</p>
            <button>Edit</button>
        </div>

        @endif
</div>



</body>
</html>


 
@endsection
