<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ asset('css/login.css') }}">

    <title>Sign Up</title>
</head>
<body>
<form method="POST" action="/register-user" class="logForm" id="registerForm" enctype="multipart/form-data">
    @csrf
    <h2>Create Account</h2>
    {{-- <p>Lorem ipsum dolor sit amet, consectetur adipisci elit, sed eiusmod</p> --}}
    <span><p>Already have an account?</p> <a href="/">Sign In</a></span>


    {{-- company details --}}
    <span>Company name</span>
    <input type="text" name="store_name" id="store_name" placeholder="" required>
    <small id="store-name-feedback" style="color:red;"></small>
    <span>Address</span>
    <input type="text" name="address" placeholder="Barangay, City, Province" required>
    <span>Company image</span>
    <input type="file" name="image" id="companyImage" accept="image/*" required>
    <small id="company-image-feedback" style="color:red; display:block; min-height:1.2em;"></small>
    <span>Representative' Name</span>
    <input type="text" name="name" placeholder="ex: Juan DelaCruz" required>
    <span>Email Address</span>
    <input type="email" name="email" placeholder="" required>
    <span>Mobile Number (optional)</span>
    <input type="tel" name="mobile" 
       placeholder="ex: 09123456789" 
       pattern="^09\d{9}$" 
       maxlength="11" 
       minlength="11" 
    >
    <span>Telephone (optional)</span>
    <input type="tel" name="telephone" 
    
       placeholder="ex: 02-1234567 or 045-1234567" 
       pattern="^0\d{1,3}-\d{6,7}$"
    >

    
    {{-- security --}}
    <span>Username</span>
    <input type="text" name="username" id="username" placeholder="" maxlength="15" minlength="4" required>
    <small id="username-feedback" style="color:red;"></small>
<span>Password</span>
<input type="password" name="password" id="password" placeholder="Password" required>
<small id="password-feedback" style="color:red;"></small>
<span>Confirm password</span>
<input type="password" name="password_confirmation" id="confirmPassword" placeholder="Confirm Password" required>
<small id="confirm-password-feedback" style="color:red;"></small>
    {{-- <input type="password" name="password_confirmation" id="confirmPassword" required> --}}
   

    {{-- hidden inputs --}}
    <input type="text" name="user_type" value="Customer" hidden>    
    <input type="text" name="acc_status"  value="Pending" hidden>
    <input type="text" name="action_by" value="Customer" hidden>

    <button type="submit">Sign Up</button>
    
</form>
@if ($errors->any())
    <div style="color: red; margin-bottom: 10px;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<script src="{{asset('js/register.js')}}"></script>

</body>
</html>