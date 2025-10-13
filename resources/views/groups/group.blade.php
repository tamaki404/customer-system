@extends('layouts.main')


@push('styles')
    <link rel="stylesheet" href="{{ asset('css/views/groups/group.css') }}">
@endpush



@section('content')


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

    {{-- process action --}}
    <div class="modal fade" id="modify-modal" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('group.modify') }}" style=" overflow-x: auto; display: flex; flex-direction: column;">
                @csrf

                {{-- Validation & Flash Messages --}}
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

                @if (session('success'))
                    <div class="alert alert-success" style="margin: 10px;">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger" style="margin: 10px;">
                        <h6 style="margin-bottom: 10px; font-weight: bold;">Error:</h6>
                        <p style="margin: 0; font-size: 14px;">{{ session('error') }}</p>
                    </div>
                @endif

                <div class="modal-header">
                    <p class="modal-title" id="requestActionLabel">Modify user account</p>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <input type="hidden" name="user_id" value="{{ auth()->user()->user_id }}" required>
                <input type="hidden" name="rep_id" value="{{ $user->rep->rep_id }}" required>

                <div class="modal-body" >

                    <div class="modal-option-groups">
                        <p>
                            <span class="req-asterisk">*</span>
                           Name
                        </p>
                        <input type="text" id="rep-name-input" readonly>
                    </div>
                    <div class="modal-option-groups">
                        <p>
                            <span class="req-asterisk">*</span>
                           Role type
                        </p>
                        <input type="text" id="rep-role-input" readonly>
                    </div>
                    <div class="modal-option-groups">
                        <p>
                            <span class="req-asterisk">*</span>
                           Permissions
                        </p>
                        <table class="permission-table">
                            <thead>
                                <tr>
                                    <th>Permission</th>
                                    <th>Allow</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <span class="title">Dashboard</span><br>
                                        <span class="desc"><i>Can see overall statistics</i></span>
                                    </td>
                                    <td><input type="checkbox" value="Dashboard" name="Dashboard"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="title">Profile</span><br>
                                        <span class="desc"><i>Can edit available profile data</i></span>
                                    </td>
                                    <td><input type="checkbox" value="Profile" name="Profile"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="title">Credits</span><br>
                                        <span class="desc"><i>Can submit proof of payments and see credit data</i></span>
                                    </td>
                                    <td><input type="checkbox" value="Credits" name="Credits"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="title">Proof of payment</span><br>
                                        <span class="desc"><i>Can view submitted proof of payments</i></span>
                                    </td>
                                    <td><input type="checkbox" value="POP" name="POP"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="title">Purchase order</span><br>
                                        <span class="desc"><i>Can submit and modify a purchase order</i></span>
                                    </td>
                                    <td><input type="checkbox" value="PO" name="PO"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="title">Orders</span><br>
                                        <span class="desc"><i>Can modify orders</i></span>
                                    </td>
                                    <td><input type="checkbox" value="Orders" name="Orders"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="title">Products</span><br>
                                        <span class="desc"><i>Can view listed products</i></span>
                                    </td>
                                    <td><input type="checkbox" value="Products" name="Products"></td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Apply changes</button>
                </div>
            </form>

        </div>
    </div>

        <div class="content-bg" >
            <div class="content-header">
                <div class="title-actions" style="display: flex; flex-direction: column;">
                    <p class="heading">Groups</p>
                    <p class="head-info" style="width: 500px">
                        <span class="material-symbols-outlined">info</span>
                        <span >Here is a list of the authorized representatives you added during signup. You can create accounts for them with selected privileges.</span>
                    </p>
                </div>
            </div>
            <div class="table-con">
                <p>
                    <span class="material-symbols-outlined"> person_apron </span>
                    Authorized Representatives
                </p>      
                <div class="content-body" style="background: #fff; height: auto;">

                        <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th>#</th>
                                    <th>Rep. ID</th>
                                    <th>Name</th>
                                    <th>Role type</th>
                                    <th>Permissions</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reps as $rep)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $rep->rep_id }}</td>
                                        <td>
                                            {{$rep->rep_lastname}}
                                            {{$rep->rep_firstname}}
                                            {{$rep->rep_middlename}}
                                        </td>
                                        <td>{{ $rep->auth_position }}</td>
                                        <td>
                                            @if ($rep->auth_position !== "Admin")
                                                --
                                            @elseif ($rep->auth_position === "Admin")
                                                All
                                            @endif

                                        </td>
                                        @if ($rep->auth_position !== "Admin")
                                            <td style="display: flex; align-items: center; justify-content: center;">
                                                <button 
                                                    class="btn-transition modify-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modify-modal"
                                                    data-name="{{ $rep->rep_lastname }}, {{ $rep->rep_firstname }}{{ $rep->rep_middlename ? ' '.$rep->rep_middlename : '' }}"
                                                    data-role="{{ $rep->auth_position }}"

                                                >
                                                <span style="font-size: 15px; margin: 0" class="material-symbols-outlined">manage_accounts</span>
                                                    Modify account
                                                </button>
                                            </td>
                                        @elseif ($rep->auth_position === "Admin")
                                            <td>
                                               <em style="color: #666">You're the admin</em>
                                            </td>
                                        @endif


                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                </div>
            </div>

            <div class="table-con">
                    <p>
                        <span class="material-symbols-outlined"> stylus_note </span>
                        Signatories to accept deliveries ad sign invoices
                    </p>
                    <div class="content-body" style="background: #fff; height: auto;">

                        <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th>#</th>
                                    <th>Rep. ID</th>
                                    <th>Name</th>
                                    <th>Role type</th>
                                    <th>Permissions</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reps as $rep)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>--</td>
                                        <td>
                                            {{$rep->rep_lastname}}
                                            {{$rep->rep_firstname}}
                                            {{$rep->rep_middlename}}
                                        </td>
                                        <td>{{ $rep->auth_position }}</td>
                                        <td></td>
                                        <td></td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                
            </div>
        </div>
@endsection



@push('scripts')
     <script src="{{ asset('js/global/group/modal.js') }}"></script>

@endpush