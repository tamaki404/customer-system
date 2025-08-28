@extends('layout')

@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fadein.css') }}">
    <title>Document</title>
</head>
<body>
    <div class="profile-wrapper" style="">
    <div class="profile-card" style="overflow-x: auto">
        <div class="profile-header">
            @php
                $isBase64 = !empty(auth()->user()->image_mime);
                $imgSrc = $isBase64 ? ('data:' . auth()->user()->image_mime . ';base64,' . auth()->user()->image) : asset('images/' . (auth()->user()->image ?? 'default-avatar.png'));
            @endphp
            <img src="{{ $imgSrc }}" class="avatar" alt="Avatar">
            <div class="profile-info">
                <h2 class="profile-store" style="color: #333">{{ auth()->user()->store_name }}</h2>
                @if (auth()->user()->user_type === 'Staff')
                <h2 class="profile-store" style="color:#333">{{ auth()->user()->name }}</h2>
                @endif

                <p class="profile-role">{{ ucfirst(auth()->user()->user_type) }}</p>
                <p style="font-size: 12px; margin: 0; color: #888;">ID: {{ auth()->user()->id }}</p>
            </div>
        </div>
        <script src="{{ asset('js/fadein.js') }}"></script>
        <div class="profile-body" style="height: auto">
            @if(auth()->user()->user_type === 'Customer')
            <p class="info-help">Your company’s information is required to ensure accurate identification and processing of transactions.</p>

            <div class="info-box">
                <p style="font-weight: bold;">For other changes, kindly email the support team or admin detailing your concern.</p>
                <p class="acc_creation">
                    Account created on {{ auth()->user()->created_at->format('F j, Y') }}
                </p>
                <form action="/edit-profile" method="POST" id="submitForm" class="profile-form" >
                    @csrf
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" value="{{auth()->user()->name}}" style=" width:300px;"  >
                        </div>             
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" value="{{ auth()->user()->username }}" style=" width:200px;" disabled title="This field is not editable">
                        </div>

                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="address"  value="{{auth()->user()->address}}" style=" width:300px;">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email"  value="{{auth()->user()->email}}" disabled style=" width:240px;" title="This field is not editable">
                        </div>
                        <div class="formSection">
                            <div class="form-group" style="width: auto;">
                                <label>Phone Number</label>
                                <input type="tel" name="mobile"  value="{{auth()->user()->mobile}}" style=" width:250px;">
                            </div>
                            <div class="form-group">
                                <label>Telephone</label>
                                <input type="text" name="telephone" value="{{ auth()->user()->telephone }}"   style=" width:250px;">
                            </div>
                        </div>

                    <button class="editProfileBtn"  id="submitBtn" style="font-style: 13px;" type="submit">Save Changes</button>

                </form>
                <p class="updatePassword">To update password, use the forgot password on login</p>


            </div>

            @elseif(auth()->user()->user_type === 'Staff')
            <div class="info-box">
                <p class="acc_creation">
                    Account created on {{ auth()->user()->created_at->format('F j, Y') }}
                </p>
                <form action="/edit-profile" method="POST" class="profile-form">
                @csrf
                <div class="form-row">
                    <div class="form-group" >
                        <label>Name</label>
                        <input type="text" name="username" value="{{ auth()->user()->name}}" disabled style="width: 150px;">
                    </div>  
                    <div class="form-group" >
                        <label>Username</label>
                        <input type="text" name="username" value="{{ auth()->user()->username }}" disabled style="width: 300px;">
                    </div>             
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" name="email" value="{{ auth()->user()->email }}" disabled style="width: 300px;">
                    </div>

                </div>



            </form>

                <p class="updatePassword">You're not allowed to edit, kindly contact the admin for any changes.</p>


            </div>
           
            @elseif(auth()->user()->user_type === 'Admin')
            <div class="info-box">
                <p style="font-size: 15px;">Please review and update your profile details. Ensure your contact information is accurate.</p>
                <form action="/edit-profile" method="POST" id="submitForm" class="profile-form">
                @csrf
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="username" value="{{ auth()->user()->name }}" style=" width:300px;">
                    </div>             
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" value="{{ auth()->user()->username }}"  style=" width:200px;" disabled>
                    </div>
                    <div class="form-group">
                        <label>Company</label>
                        <input type="text" style="company" name="company" value="{{ auth()->user()->store_name }}"  style=" width:400px;">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email"  value="{{ auth()->user()->email }}" placeholder="Working email address"  style=" width:240px;" disabled>
                    </div>

                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="number" value="{{ auth()->user()->mobile }}" placeholder="ex: 09123456789" maxlength="10"  style=" width:150px;">
                    </div>
                    


                <button class="editProfileBtn" id="submitBtn" type="submit">Save changes</button>
                
            </form>
                <p class="updatePassword">To update password, use the forgot password on login</p>
            

            </div>
            @endif
        </div>
    </div>
    </div>

    
</body>
</html>



<script src="{{ asset('js/disableBtn.js') }}"></script>


@endsection
