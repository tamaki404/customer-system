@extends('layouts.main')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/views/customer.css') }}">
@endpush



@section('content')


    <div class="modal fade" id="modify-action" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('staff.modify') }}" enctype="multipart/form-data">
            <!-- Debug: Route URL -->
            <!-- Route URL: {{ route('staff.modify') }} -->
            @csrf
            
            @if ($errors->any())
                <div class="alert alert-danger" style="margin: 10px;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li style="font-size: 14px;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if (session('success'))
                <div class="alert alert-success" style="margin: 10px;">{{ session('success') }}</div>
            @endif

            <!-- Hidden field for staff ID -->
            <input type="hidden" name="staff_id" value="{{ $staff->staff_id }}">
            
            
            <div class="modal-header">
                <p class="modal-title" id="requestActionLabel">Staff modify action</p>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <p class="note-notify">
                    <span class="material-symbols-outlined"> info </span>
                    <span>Any changes will be logged.</span>
                </p>
                
                <div class="modal-option-groups">
                    <p>Profile picture</p>
                    <input type="file" name="new_image" accept="image/*" class="@error('new_image') is-invalid @enderror">
                    @error('new_image')
                        <div class="invalid-feedback" style="color: #dc3545; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="modal-option-groups">
                    <p>Fullname</p>
                    <div class="form-group">
                        <input type="text" name="lastname" placeholder="Lastname" value="{{ old('lastname', $staff->lastname) }}" class="@error('lastname') is-invalid @enderror">
                        @error('lastname')
                            <div class="invalid-feedback" style="color: #dc3545; font-size: 12px;">{{ $message }}</div>
                        @enderror
                        <input type="text" name="firstname" placeholder="Firstname" value="{{ old('firstname', $staff->firstname) }}" class="@error('firstname') is-invalid @enderror">
                        @error('firstname')
                            <div class="invalid-feedback" style="color: #dc3545; font-size: 12px;">{{ $message }}</div>
                        @enderror
                        <input type="text" name="middlename" placeholder="Middlename" value="{{ old('middlename', $staff->middlename) }}" class="@error('middlename') is-invalid @enderror">
                        @error('middlename')
                            <div class="invalid-feedback" style="color: #dc3545; font-size: 12px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="modal-option-groups">
                    <p>Contact</p>
                    <input type="text" placeholder="Mobile no." maxlength="11" name="mobile_no" value="{{ old('mobile_no', $staff->mobile_no) }}" class="@error('mobile_no') is-invalid @enderror">
                    @error('mobile_no')
                        <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                    <input type="text" placeholder="Telephone no." maxlength="11" name="telephone_no" value="{{ old('telephone_no', $staff->telephone_no) }}" class="@error('telephone_no') is-invalid @enderror">
                    @error('telephone_no')
                        <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="modal-option-groups">
                    <p>Account security</p>
                    <div class="form-group">
                        <input type="text" name="email_address" maxlength="50" placeholder="Email address" value="{{ old('email_address', $staff->user->email_address) }}" class="@error('email_address') is-invalid @enderror">
                        @error('email_address')
                            <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <p style="margin: 5px">Fill this if user wants their password changed</p>
                        <input type="password" name="password" placeholder="Password" minlength="6" class="@error('password') is-invalid @enderror">
                        @error('password')
                            <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                        <input type="password" name="password_confirmation" minlength="6" placeholder="Confirm password" class="@error('password_confirmation') is-invalid @enderror">
                        @error('password_confirmation')
                            <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                        <div id="staff-password-requirements" style="margin-top: 5px; font-size: 12px;">
                            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                <span id="staff-length-check" style="color: #ccc;"> Minimum of 6 -</span>
                                <span id="staff-match-check" style="color: #ccc;"> Matched confirm password </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Submit action</button>
            </div>
        </form>
    </div>
    </div>


@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

   <div class="content-bg" >
        <div class="content-header">
            <div class="contents-display">
                <p>
                    <a href="{{ route('staffs.list') }}">< Staffs list</a>
                </p>
            </div>

            <div class="title-actions">
                <p class="heading">Staff's profile</p>

                <div>
                    <button data-bs-toggle="modal" data-bs-target="#modify-action" class="btn-transition">Modify account</button>
                </div>

            </div>


        </div>

        <div class="content-body" style="padding: 10px; border: none; height: auto;">
            <div class="profile-upper">
                @php
                    $imgSrc = $staff->user->image 
                        ? ('data:' . $staff->user->image_mime_type . ';base64,' . base64_encode($staff->user->image))
                        : asset('assets/default-image.jpg');
                @endphp
                <img class="supplier-image" src="{{ $imgSrc }}" alt="Profile Image">   
            

            </div>

            <div class="profile-mid">

                <div class="authorized-staffs" style="margin-top: 10px">
                    <p style="margin-bottom: 5px">Handled suppliers</p>
                    <di class="rep-sign-tables" style="width: 100%; display: flex; flex-direction: row; gap: 5px;">
                        <div class="authorized-rep">
                            <table style="width:100%; border-collapse:collapse; border: 1px solid #f7f7fa;">
                                <thead style="background-color: #f9f9f9;">
                                    <tr style="background:#f7f7fa; text-align: center; height: 30px">
                                        <th>#</th>
                                        <th>Supplier ID</th>
                                        <th>Company name</th>
                                        <th>Order</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($suppliers as $supplier)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{$supplier->supplier_id}}</td>
                                            <td>{{$supplier->company_name}}</td>
                                            <td>--</td>
                                            <td>--</td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>

                    </di>


                </div>

            </div>

         
       
        </div>


   </div>
@endsection



@push('scripts')
    <script src="{{ asset('js/global/password.js') }}"></script>
    <script>
        // Auto-hide success/error messages after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            });
        }, 5000);

        // Form validation enhancement
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[action="{{ route('staff.modify') }}"]');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const password = form.querySelector('input[name="password"]').value;
                    const passwordConfirmation = form.querySelector('input[name="password_confirmation"]').value;
                    
                    if (password && password !== passwordConfirmation) {
                        e.preventDefault();
                        alert('Password and password confirmation do not match.');
                        return false;
                    }
                });
            }
        });
    </script>
@endpush