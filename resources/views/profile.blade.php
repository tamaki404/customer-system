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
    <link rel="stylesheet" href="{{ asset('css/confirmation-modal/profile.css') }}">

</head>
<body>

    <!-- confirmation modal -->
    <div class="modal fade" id="confirmModal" style="display: none;"  tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"  style="justify-self: center; align-self: center; ">
            <div class="modal-content" style="border-top: 4px solid #ffde59;">
                <div class="modal-header">
                    <h5 class="modal-title" style="padding: 0; margin: 0;">Confirm action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" style="border: none; font-size: 14px;">
                    Are you sure you want to commit changes?
                </div>

                <div class="modal-footer" style="padding: 5px">
                    <button type="button" id="cancelBtn" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmSaveBtn" class="btn" style="background: #ffde59; font-weight: bold; font-size: 14px;">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- change password modal -->
    <div class="modal fade" id="changePasswordModal" style="display: none;" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-top: 4px solid #ffde59;">
                <div class="modal-header">
                    <h5 class="modal-title" style="padding: 0; margin: 0;">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('password.change') }}" id="changePasswordForm">
                    @csrf
                    <div class="modal-body" style="border: none; font-size: 14px; display: grid; gap: 10px;">
                        <div class="form-group" style="width: auto;">
                            <label>Current Password</label>
                            <input type="password" name="current_password" id="current_password" required style="width: 100%;">
                            <span id="current_password_error" style="color: #ea1f21; font-size: 12px; display: none;"></span>
                        </div>
                        <div class="form-group" style="width: auto;">
                            <label>New Password</label>
                            <input type="password" name="password" id="new_password" required minlength="8" style="width: 100%;">
                            <span id="new_password_error" style="color: #ea1f21; font-size: 12px; display: none;"></span>
                        </div>
                        <div class="form-group" style="width: auto;">
                            <label>Confirm New Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8" style="width: 100%;">
                            <span id="password_confirmation_error" style="color: #ea1f21; font-size: 12px; display: none;"></span>
                        </div>
                    </div>
                    <div class="modal-footer" style="padding: 5px">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="submitChangePassword" class="btn" style="background: #ffde59; font-weight: bold; font-size: 14px;">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="profile-wrapper" style=" z-index: 1;">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show position-fixed" 
                style="top: 20px; right: 20px; z-index: 9999; font-size: 14px; border-radius: 10px;">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show position-fixed" 
                style="top: 20px; right: 20px; z-index: 9999; font-size: 14px; border-radius: 10px;">
                {{ session('error') }}
            </div>
        @endif

        <div class="profile-card" style="overflow-x: auto">
            <div class="profile-header">

                @php
                    $isBase64 = !empty(auth()->user()->image_mime);
                    $imgSrc = $isBase64 
                        ? ('data:' . auth()->user()->image_mime . ';base64,' . auth()->user()->image) 
                        : asset('images/' . (auth()->user()->image ?? 'default-avatar.png'));
                @endphp

                <form action="{{ url('/update-image') }}" method="POST" id="customer_edit" class="profile-image-wrapper" enctype="multipart/form-data">
                    @csrf
                    <img src="{{ $imgSrc }}" class="avatar" alt="Avatar" id="profileImagePreview">
                    <input type="hidden" name="id" value="{{ auth()->user()->id }}">

                    <input type="file" id="companyImage" name="image" accept="image/*" hidden>

                    <button type="button" class="edit-avatar-btn" id="editAvatarBtn">
                        <span class="material-symbols-outlined">edit</span>
                    </button>

                    <div class="avatar-actions" id="avatarActions" style="display: none;">
                        <button type="submit" class="avatar-btn" id="saveImageBtn">Save</button>
                        <button type="button" class="avatar-btn cancel" id="cancelImageBtn">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                    <p id="file-error" style="color: red; display: none; margin: 0; font-size: 12px; margin: 0;"></p>

                </form>







                <div class="profile-info">
                    <h2 class="profile-store" style="color: #333">{{ auth()->user()->store_name }}</h2>
                    @if (auth()->user()->user_type === 'Staff')
                    <h2 class="profile-store" style="color:#333">{{ auth()->user()->name }}</h2>
                    @endif

                    <p class="profile-role">{{ ucfirst(auth()->user()->user_type) }}</p>
                    <p style="font-size: 12px; margin: 0; color: #888;">ID: {{ auth()->user()->id }}</p>
                </div>
            </div>
            <div class="profile-body" style="height: auto">
                @if(auth()->user()->user_type === 'Customer')
                <p class="info-help">Your company’s information is required to ensure accurate identification and processing of transactions.</p>

                <div class="info-box">
                    <p style="font-weight: bold;">For other changes, kindly email the support team or admin detailing your concern.</p>
                    <p class="acc_creation">
                        Account created on {{ auth()->user()->created_at->format('F j, Y') }}
                    </p>
                    <form action="/edit-profile" method="POST" id="customer_edit" class="profile-form" >
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
                                    <label>Telephone (Optional)</label>
                                    <input type="text" name="telephone" value="{{ auth()->user()->telephone }}"   style=" width:250px;">
                                </div>
                            </div>

                            <button class="editProfileBtn" id="customerSaveChanges" type="button" data-bs-toggle="modal" data-bs-target="#confirmModal">
                                Save Changes
                            </button>
                    </form>
                    <div class="change-password" style="margin-top: 20px;">
                        <button type="button" class="editProfileBtn openChangePasswordBtn" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</button>
                    </div>


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
                            <input type="text" name="name" value="{{ auth()->user()->name }}" style=" width:300px;">
                        </div>             
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" value="{{ auth()->user()->username }}"  style=" width:200px;" disabled>
                        </div>
          
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email"  value="{{ auth()->user()->email }}" placeholder="Working email address"  style=" width:240px;" disabled>
                        </div>

                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="number" value="{{ auth()->user()->mobile }}" placeholder="ex: 09123456789" maxlength="10"  style=" width:150px;">
                        </div>
                        
                        <button class="editProfileBtn" id="adminSaveChanges" type="button" 
                                data-bs-toggle="modal" data-bs-target="#confirmModal">
                            Save Changes
                        </button>
                        
                    </form>
                    <div class="change-password" style="margin-top: 20px;">
                        <button type="button" class="editProfileBtn openChangePasswordBtn" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</button>
                    </div>
                

                </div>
                @endif
            </div>
        </div>



    </div>

    
</body>
</html>

<script src="{{ asset('js/disableBtn.js') }}"></script>
<script src="{{ asset('js/confirmation-modal/profile.js') }}"></script>
<script src="{{ asset('js/profile/2mb.js') }}"></script>
<script src="{{ asset('js/fadein.js') }}"></script>
<script>
    (function(){
        var openButtons = document.querySelectorAll('.openChangePasswordBtn');
        openButtons.forEach(function(btn){
            btn.addEventListener('click', function(){
                btn.disabled = true;
                setTimeout(function(){ btn.disabled = false; }, 800);
            });
        });

        var form = document.getElementById('changePasswordForm');
        var submitBtn = document.getElementById('submitChangePassword');
        var currentInput = document.getElementById('current_password');
        var newInput = document.getElementById('new_password');
        var confirmInput = document.getElementById('password_confirmation');
        var currentErr = document.getElementById('current_password_error');
        var newErr = document.getElementById('new_password_error');
        var confirmErr = document.getElementById('password_confirmation_error');

        function setError(el, span, message){
            if(!span) return;
            if(message){
                span.textContent = message;
                span.style.display = 'block';
                el && el.classList && el.classList.add('is-invalid');
            } else {
                span.textContent = '';
                span.style.display = 'none';
                el && el.classList && el.classList.remove('is-invalid');
            }
        }

        function validateNewPassword(){
            var val = newInput ? newInput.value : '';
            if(val.length < 8){
                setError(newInput, newErr, 'Password must be at least 8 characters.');
                return false;
            }
            setError(newInput, newErr, '');
            return true;
        }

        function validateConfirmation(){
            var val = confirmInput ? confirmInput.value : '';
            if(val !== (newInput ? newInput.value : '')){
                setError(confirmInput, confirmErr, 'Passwords do not match.');
                return false;
            }
            setError(confirmInput, confirmErr, '');
            return true;
        }

        function validateCurrentPasswordDebounced(){
            if(!currentInput) return;
            var value = currentInput.value;
            if(!value){ setError(currentInput, currentErr, 'Current password is required.'); return; }
            setError(currentInput, currentErr, '');
            fetch('{{ route('password.checkCurrent') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ current_password: value })
            }).then(function(res){ return res.json(); })
            .then(function(data){
                if(!data.valid){
                    setError(currentInput, currentErr, data.message || 'Current password is incorrect.');
                } else {
                    setError(currentInput, currentErr, '');
                }
            }).catch(function(){
            });
        }

        var debounceTimer;
        if(currentInput){
            currentInput.addEventListener('input', function(){
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(validateCurrentPasswordDebounced, 350);
            });
            currentInput.addEventListener('blur', validateCurrentPasswordDebounced);
        }
        if(newInput){ newInput.addEventListener('input', validateNewPassword); }
        if(confirmInput){ confirmInput.addEventListener('input', validateConfirmation); }

        if(form && submitBtn){
            form.addEventListener('submit', function(){
                var ok = true;
                if(currentErr && currentErr.style.display === 'block'){ ok = false; }
                if(!validateNewPassword()) ok = false;
                if(!validateConfirmation()) ok = false;
                if(!ok){
                    if(currentErr && currentErr.style.display === 'block'){ currentInput && currentInput.focus(); return false; }
                    if(newErr && newErr.style.display === 'block'){ newInput && newInput.focus(); return false; }
                    if(confirmErr && confirmErr.style.display === 'block'){ confirmInput && confirmInput.focus(); return false; }
                    return false;
                }
                submitBtn.disabled = true;
                submitBtn.textContent = 'Updating...';
            });
        }
    })();
</script>




@endsection


