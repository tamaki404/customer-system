@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{asset('css/staffs/list.css')}}">
@endpush

@section('content')


    <div class="modal fade" id="add-staff-modal" tabindex="-1" aria-labelledby="requestActionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content"  method="POST" action="{{ route('registration.staff.register') }}" >
            
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
                        <input type="file">

                    </div>

                    <div class="modal-option-groups">
                        <p>Fullname</p>
                        <div class="form-group">
                            <input type="text" name="lastname" placeholder="Lastname" required>
                            <input type="text" name="firstname" placeholder="Firstname" required>
                            <input type="text" name="middlename" placeholder="Middlename">
                        </div>

                    </div>
                    <div class="modal-option-groups">
                        <p>Staff role</p>
                        <select name="" id="">
                            <option value="">Sales representative</option>
                            <option value="">Procurement officer</option>
                            <option value="">Warehouse staff</option>
                            <option value="">Accounting staff</option>
                            <option value="">System administrator</option>

                        </select>
                    </div>

                    <div class="modal-option-groups">
                        <p>Account security</p>
                        <div class="form-group">
                            <input type="text" name="email_address" placeholder="Email address" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="passsword" placeholder="Password" minlength="8" required>
                            <input type="password" name="confirm_password" minlength="8" placeholder="Confirm password" required>
                        </div>
                    </div>


                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Add staff</button>
                </div>
            
            </form>
        </div>
    </div>


   <div class="content-bg">
        <div class="content-header">
            <div class="contents-display">
                <form action="{{ route('staffs.list') }}" id="text-search" class="search-text-con" method="GET">
                    <input type="text" name="search" class="search-bar"
                        placeholder="Search by SUP ID. , Supplier, Representative and status"
                        value="{{ request('search') }}"
                        style="outline:none;"
                    >
                    <button type="submit" class="search-btn"><span class="material-symbols-outlined">search</span></button>
                </form>


                <form action="{{ route('staffs.list') }}" class="date-search" id="from-to-date" method="GET">
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

            <div class="heading" style="display: flex; flex-direction: row; justify-content: space-between; margin-top: 10px;">
                <p class="heading">Staffs list</p>
                <button class="add-staff-btn btn-transition" data-bs-toggle="modal" data-bs-target="#add-staff-modal">
                    <span style="font-size: 15px; margin: 0" class="material-symbols-outlined">add</span>
                    Add staffs
                </button>
            </div>

        </div>

        <div class="content-body">
            <table style="width:100%; border-collapse:collapse; border: 1px solid #f7f7fa;">
                <thead style="background-color: #f9f9f9;">
                    <tr style="background:#f7f7fa; text-align: center; height: 30px">
                        <th>#</th>
                        <th>Staff ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>No. of contacts</th>
                        <th>Customers' balance</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($staffs as $staff)
                        <tr onclick="window.location.href='{{ route('staffs.staff', ['staff_id' => $staff->staff_id]) }}'">
                            <th>{{ $loop->iteration }}</th>
                            <td>{{ $staff->supplier_id }}</td>
                            <td>{{ $staff->company_name }}</td>
                            <td>{{ $staff->representative->rep_last_name }}sd</td>
                            <td>{{ $staff->user->email_address }}</td>
                            <td>{{ $staff->user->role }}</td>

                            <td>{{ $staff->representative->sdas ?? NULL}}</td>

                            <td>{{ $staff->user->status }}</td>
                            <td>0.00</td>
                        </tr>

                    @endforeach
                </tbody>
            </table>
       
        </div>

        <div class="pagination-div">
            <p>50 out of 100 <span>2/3</span></p>
            <div>
                <button>Previous</button>
                <button>Next</button>
            </div>
        </div>

   </div>
@endsection
