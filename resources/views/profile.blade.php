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
                <h2 class="profile-store">{{ auth()->user()->store_name }}</h2>
                <p class="profile-role">{{ ucfirst(auth()->user()->user_type) }}</p>
            </div>
        </div>

        <div class="profile-body">
            
            @if(auth()->user()->user_type === 'Customer')
            <div class="info-box">
                <p style="font-size: 15px;">For other changes, kindly email the suppoort team or admin detailing your concern.</p>

                <form action="/edit-profile" method="POST" class="profile-form">
                    @csrf
                        <div class="form-group">
                            <label>Name</label>
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
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="address" value="Rodriguez, Rizal">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="xeultu@gmail.com" disabled>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="number" value="+63912345678">
                        </div>
                    <button class="btn-submit" style="font-style: 13px;" type="submit">Save Changes</button>
                </form>

                <a href="/update-password" class="updatePassword">Update Password</a>
            </div>

            @elseif(auth()->user()->user_type === 'Staff')
            <div class="info-box">
                <p style="font-size: 15px;">Please review and update your profile details. For other changes, kindly contact the admin.</p>

                <form action="/edit-profile" method="POST" class="profile-form">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label>Name</label>
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
                        <input type="text" name="address" value="Rodriguez, Rizal" disabled>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="xeultu@gmail.com" disabled>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group full">
                        <label>Phone Number</label>
                        <input type="tel" name="number" value="+63912345678" disabled>
                    </div>
                </div>

                <p>You're not allowed to edit</p>
            </form>
            </div>
           
            @elseif(auth()->user()->user_type === 'Admin')
            <div class="info-box">
                <p style="font-size: 15px;">Please review and update your profile details. Ensure your contact information is accurate.</p>
                <form action="/edit-profile" method="POST" class="profile-form">
                @csrf
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="username" value="Nicole Tumpag" style=" width:300px;">
                    </div>             
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" value="{{ auth()->user()->username }}"  style=" width:200px;" disabled>
                    </div>
                    <div class="form-group">
                        <label>Company</label>
                        <input type="text" name="company" value="{{ auth()->user()->store_name }}"  style=" width:400px;">
                    </div>
                    {{-- <div class="form-group">
                        <label>Address</label>
                        <input type="text" name="address" value="Rodriguez, Rizal">
                    </div> --}}
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email"  value="{{ auth()->user()->email }}" placeholder="Working email address"  style=" width:240px;" disabled>
                    </div>

                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="number" value="{{ auth()->user()->mobile }}" placeholder="ex: 09123456789" maxlength="10"  style=" width:150px;">
                    </div>
                    


                <button class="editProfileBtn" type="submit">Save changes</button>
                
            </form>
            <a href="" class="updatePass">Update Password</a>
            

            </div>
            @endif
        </div>
    </div>
    </div>
</body>
</html>



@endsection
