@foreach($logs as $log)
    <p>{{ $log->description }} (by user {{ $log->user->user_id }})</p>
@endforeach

@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{asset('css/staffs/list.css')}}">
@endpush

@section('content')



   <div class="content-bg">
        <div class="content-header">
            <div class="contents-display">
                <form action="{{ route('logs.list') }}" id="text-search" class="search-text-con" method="GET">
                    <input type="text" name="search" class="search-bar"
                        placeholder="Search by SUP ID. , Supplier, Representative and status"
                        value="{{ request('search') }}"
                        style="outline:none;"
                    >
                    <button type="submit" class="search-btn"><span class="material-symbols-outlined">search</span></button>
                </form>


                <form action="{{ route('logs.list') }}" class="date-search" id="from-to-date" method="GET">
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

        <div class="content-body" style="background: #fff">
            <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                <thead style="background-color: #fff;">
                    <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
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
                    @foreach ($logs as $log)
                        <tr onclick="window.location.href='{{ route('logs.log', ['staff_id' => $staff->staff_id]) }}'">
                            <th >{{ $loop->iteration }}</th>
                            <td>{{ $staff->staff_id }}</td>
                            <td>
                                {{ implode(', ', array_filter([
                                    $staff->lastname,
                                    $staff->firstname,
                                    $staff->middlename
                                ])) }}                            
                            </td>
                            <td>{{ $staff->user->role_type }}</td>
                            <td>{{ $staff->user->status }}</td>
                            <td>0</td>

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

@push('scripts')


@endpush

