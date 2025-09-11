<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ asset('css/registration.css') }}">

    <title>Sign Up</title>
</head>
<body>

<form method="POST" action="/register-user" class="logForm" id="registerForm" enctype="multipart/form-data">
    @csrf
    <div class="companyDetails">
        <img src="{{ asset('assets/sunnyLogo1.png') }}" alt="Owner Image">
        <h2>Create Account</h2>
        <p class="kindly-mess">Please fill out the form below to create your account</p>
        <span>Already have an account? <a href="/login">Sign In</a></span>

         

        
        {{-- company details --}}
        <div class="form-cluster" style="margin-top: 40px;">
            <div class="form-group">
                <p>Company name</p>
                <input type="text" name="store_name" id="store_name" placeholder="" required>
                <small id="store-name-feedback" style="color:red;"></small>
            </div>

            <div class="form-group">
                <p>Address</p>
                <input type="text" name="address" placeholder="Barangay, City, Province" required>
                <small id="address-feedback" style="color:red;"></small>
            </div>
        </div>

        <div class="form-cluster">

            <div class="form-group">
                <p>Email Address</p>
                <input type="email" name="email" placeholder="" required>
                <small id="email-feedback" style="color:red;"></small>
            </div>

            <div class="form-group">
                <p>Representative' Name</p>
                <input type="text" name="name" placeholder="ex: Juan DelaCruz" required>
                <small id="name-feedback" style="color:red;"></small>
            </div>            
        </div>

        <div class="form-cluster">


            <div class="form-group">
                <p>Mobile Number</p>
                <input type="tel" name="mobile" 
                placeholder="ex: 09123456789" 
                pattern="^09\d{9}$" 
                maxlength="11" 
                minlength="11" 
                required
                >
                <small id="mobile-feedback" style="color:red;"></small>               
            </div>

            <div class="form-group">
                <span>Telephone (optional)</span>
                <input type="tel" name="telephone" placeholder="ex: 02-1234567 or 045-1234567" pattern="^0\d{1,3}-\d{6,7}$">
            </div>
        </div>

        <div class="form-cluster">
            <div class="form-group">
                <p>Company image (Optional)</p>
                <input type="file" name="image" id="companyImage" accept="image/*">
                <small id="company-image-feedback" style="color:red; display:block; "></small>
                <p id="file-error" style="color: red; display: none; margin: 0; font-size: 12px; margin: 0;"></p>
            </div>

            <div class="form-group">
                <p>Username</p>
                <input type="text" name="username" id="username" placeholder="" maxlength="15" minlength="4" required>
                <small id="username-feedback" style="color:red;"></small>
            </div>

        </div>

        <div class="form-cluster">
            <div class="form-group">
                <p>Password</p>
                <input type="password" name="password" id="password" placeholder="Password" required>
                <small id="password-feedback" style="color:red;"></small>
            </div>        
            <div class="form-group">
                <p>Confirm password</p>
                <input type="password" name="password_confirmation" id="confirmPassword" placeholder="Confirm Password" required>
                <small id="confirm-password-feedback" style="color:red;"></small>
            </div> 

        </div>

        @if ($errors->any())
            <div style="color: red; margin-bottom: 10px;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <button type="submit" id="submitBtn" class="save-btn">Sign Up</button>

    </div>


    {{-- hidden inputs --}}
    <input type="text" name="user_type" value="Customer" hidden>    
    <input type="text" name="acc_status"  value="Pending" hidden>
    <input type="text" name="action_by" value="Customer" hidden>

    
</form>


<script src="{{asset('js/register.js')}}"></script>
<script src="{{ asset('js/disableBtn.js') }}"></script>
<script src="{{ asset('js/registration/image.js') }}"></script>
<script src="{{ asset('js/confirmation-modal/registration.js') }}"></script>

</body>
</html> 

