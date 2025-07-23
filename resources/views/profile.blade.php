@extends('layout')

@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <title>Document</title>
</head>
<body>
    <div class="profile-wrapper">
    <div class="profile-card">
        <div class="profile-header">
            <img src="{{ asset('images/' . auth()->user()->image ?? 'default-avatar.png') }}" class="avatar" alt="Avatar">
            <div class="profile-info">
                <h2 class="profile-email">{{ auth()->user()->store_name }}</h2>
                <p class="profile-role">{{ ucfirst(auth()->user()->user_type) }}</p>
            </div>
        </div>

        <div class="profile-body">
            <h3>For other changes, kindly email for the support team via email detailing your concern.</h3>
            @if(auth()->user()->user_type === 'Customer')
            <form action="/edit-profile" method="POST" class="profile-form">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label>Representative</label>
                        <input type="text" name="username" value="Nicole Tumpag" disabled>
                    </div>             
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" value="{{ auth()->user()->username }}" disabled>
                    </div>
                    <div class="form-group">
                        <label>Company</label>
                        <input type="text" name="company" value="{{ auth()->user()->store_name }}" disabled>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" name="address" value="Rodriguez, Rizal">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="xeultu@gmail.com" disabled>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group full">
                        <label>Phone Number</label>
                        <input type="tel" name="number" value="+63912345678">
                    </div>
                </div>
                <button class="btn-submit" type="submit">Save Changes</button>
            </form>

            <a href="/update-password" class="updatePassword">Update Password</a>

            @elseif(auth()->user()->user_type === 'staff')
            <div class="info-box">
                <p>Your profile is managed by the IT or HR department. Contact them for changes.</p>
                <button class="btn-disabled" disabled>Editing Locked</button>
            </div>
            @endif
        </div>
    </div>
</div>
</body>
</html>



@endsection
