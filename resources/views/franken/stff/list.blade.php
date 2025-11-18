@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{asset('css/staffs/list.css')}}">

@endpush

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger" style="margin: 10px;">
            <h6 style="margin-bottom: 10px; font-weight: bold;">Validation Errors:</h6>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li style="font-size: 14px;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('success') || session('error'))
        <div 
            id="flash-message"
            class="flash-message 
                {{ session('success') ? 'alert-success' : 'alert-danger' }}">
            <strong>
                {{ session('success') ? 'Success:' : ' Error:' }}
            </strong>
            {{ session('success') ?? session('error') }}
        </div>


    @endif



    <div class="modal fade" id="add-staff-modal" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content"  method="POST" action="{{ route('registration.staff.register') }}" enctype="multipart/form-data">
                @csrf
            
                <div class="modal-header">
                    <p class="modal-title" id="requestActionLabel">Add staff form</p>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <p class="note-notify">
                        <span class="material-symbols-outlined"> info </span>
                        <span>The staff will receive a confirmation email once the request is submitted.</span>
                    </p>

                    <div class="modal-option-groups">
                        <p>Profile picture</p>
                        <input type="file" name="image" accept="image/*" required class="@error('image') is-invalid @enderror">
                        @error('image')
                            <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="modal-option-groups">
                        <p>Fullname</p>
                        <div class="form-group">
                            <input type="text" name="lastname" placeholder="Lastname" required class="@error('lastname') is-invalid @enderror" value="{{ old('lastname') }}">
                            @error('lastname')
                                <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                            <input type="text" name="firstname" placeholder="Firstname" required class="@error('firstname') is-invalid @enderror" value="{{ old('firstname') }}">
                            @error('firstname')
                                <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                            <input type="text" name="middlename" placeholder="Middlename" class="@error('middlename') is-invalid @enderror" value="{{ old('middlename') }}">
                            @error('middlename')
                                <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="modal-option-groups">
                        <p>Staff role</p>
                        <select name="role_type" id="" class="@error('role_type') is-invalid @enderror">
                            <option value="">Select role...</option>
                            <option value="sales_representative" {{ old('role_type') == 'sales_representative' ? 'selected' : '' }}>Sales representative</option>
                            <option value="inventory_staff" {{ old('role_type') == 'inventory_staff' ? 'selected' : '' }}>Inventory staff</option>
                            <option value="procurement_officer" {{ old('role_type') == 'procurement_officer' ? 'selected' : '' }}>Procurement officer</option>
                            <option value="warehouse_staff" {{ old('role_type') == 'warehouse_staff' ? 'selected' : '' }}>Warehouse staff</option>
                            <option value="accounting_staff" {{ old('role_type') == 'accounting_staff' ? 'selected' : '' }}>Accounting staff</option>
                            <option value="system_administrator" {{ old('role_type') == 'system_administrator' ? 'selected' : '' }}>System administrator</option>
                        </select>
                        @error('role_type')
                            <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="modal-option-groups">
                        <p>Contact</p>
                        <input type="text" placeholder="Mobile no." maxlength="11" name="mobile_no" required class="@error('mobile_no') is-invalid @enderror" value="{{ old('mobile_no') }}">
                        @error('mobile_no')
                            <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                        <input type="text" placeholder="Telephone no." maxlength="11" name="telephone_no" class="@error('telephone_no') is-invalid @enderror" value="{{ old('telephone_no') }}">
                        @error('telephone_no')
                            <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                        @enderror

                    </div>

                    <div class="modal-option-groups">
                        <p>Account security</p>
                        <div class="form-group">
                            <input type="text" name="email_address" maxlength="50" placeholder="Email address" required class="@error('email_address') is-invalid @enderror" value="{{ old('email_address') }}">
                            @error('email_address')
                                <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" id="staff-register-password" placeholder="Password" minlength="6" required class="@error('password') is-invalid @enderror">
                            @error('password')
                                <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                            <input type="password" name="password_confirmation" id="staff-register-password-confirmation" minlength="6" placeholder="Confirm password" required class="@error('password_confirmation') is-invalid @enderror">
                            @error('password_confirmation')
                                <div class="invalid-feedback" style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                            <div id="staff-register-password-requirements" style="margin-top: 5px; font-size: 12px;">
                                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                    <span id="staff-register-length-check" style="color: #ccc;">✓ Minimum of 6 characters</span>
                                    <span id="staff-register-number-check" style="color: #ccc;">✓ Contains a number</span>
                                    <span id="staff-register-special-check" style="color: #ccc;">✓ Contains special character</span>
                                    <span id="staff-register-match-check" style="color: #ccc;">✓ Passwords match</span>
                                </div>
                            </div>
                            <div id="staff-register-password-error" style="color: #dc3545; font-size: 12px; margin-top: 5px; display: none;"></div>
                        </div>
                    </div>

                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="add-staff-submit">Add staff</button>
                </div>

                <input type="hidden" name="action_by" value="{{ auth()->user()->user_id }}">
            
            </form>
        </div>
    </div>

    <div class="content-bg" >
        <div class="content-header">
            <div class="contents-display">
                <form action="{{ route('purchaseorders.list') }}" id="text-search" class="search-text-con" method="GET">
                    <input type="text" name="search" class="search-bar"
                        placeholder="Search by PO ID, Customer, Status"
                        value="{{ request('search') }}"
                        style="outline:none;"
                    >
                    <button type="submit" class="search-btn"><span class="material-symbols-outlined">search</span></button>
                </form>
                <form action="{{ route('purchaseorders.list') }}" class="date-search" id="from-to-date" method="GET">
                    <p>Date range</p>
                    <div class="from-to-picker">
                        <div class="month-div">
                            <span>From</span>
                            <input type="date" name="from_date" class="input-date"
                                value="{{ request('from_date', now()->startOfMonth()->format('Y-m-d')) }}"
                                onchange="this.form.submit()">
                        </div>
                        <div class="month-div">
                            <span>To</span>
                            <input type="date" name="to_date" class="input-date"
                                value="{{ request('to_date', now()->endOfMonth()->format('Y-m-d')) }}"
                                onchange="this.form.submit()">
                        </div>
                    </div>
                </form>
            </div>
            <div style="display: flex; flex-direction: row; justify-content: space-between; margin: 5px;">
                <div class="heading" style="display: flex; flex-direction: row; justify-content: space-between; margin-top: 10px;">
                    <p class="heading">Staffs list</p>
                </div>
                <div>
                @if ( auth()->user()->role === 'Admin')
                    <button class="add-staff-btn btn-transition" data-bs-toggle="modal" data-bs-target="#add-staff-modal">
                        <span style="font-size: 15px; margin: 0" class="material-symbols-outlined">add</span>
                        Add staffs
                     </button>
                @endif
            </div>

        </div>

        <div class="content-body" style="background: #fff">
            <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                <thead style="background-color: #fff;">
                    <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                        <th>#</th>
                        <th>Staff ID</th>
                        <th>Name</th>
                        <th>Email address</th>
                        <th>Number of clients</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>                                
                    @foreach ($staffs as $staff)
                        <tr>
                            <td style="text-align: center; padding: 8px;">{{ $loop->iteration }}</td>
                            <td style="text-align: center; padding: 8px;">{{ $staff->staff_id }}</td>
                            <td style="text-align: center; padding: 8px;">{{ $staff->lastname }}, {{ $staff->firstname }} {{ $staff->middlename }}</td>
                            <td style="text-align: center; padding: 8px;">{{ $staff->user->email_address }}</td>
                            {{-- <td style="text-align: center; padding: 8px;">{{ $staff->clients_count }}</td> --}}
                            <td>--</td>
                            <td style="text-align: center; padding: 8px;">{{ $staff->status }}</td>
                            <td>
                                <div class="dropdown" style="display:flex; align-items: center; justify-content: center;">
                                    <button class="" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 13px; ">
                                        <span class="material-symbols-outlined">
                                        expand_circle_down
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item"  style="color:#f8a01d" href="{{ route('stff.staff', ['staff_id' => $staff->staff_id]) }}"><span class="material-symbols-outlined">person</span>View profile</a></li>                                
                                    </ul>
                                </div>
                            </td>
                      
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


@endsection

@push('scripts')
    <script src="{{ asset('js/franken/staff.js') }}"></script>


@endpush
