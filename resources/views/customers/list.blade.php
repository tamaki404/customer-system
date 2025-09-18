@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{asset('css/customers/list.css')}}">
@endpush

@section('content')
   <div class="content-bg">
        <div class="content-header">
            <div class="contents-display">
                <form action="{{ route('customers.list') }}" id="text-search" class="search-text-con" method="GET">
                    <input type="text" name="search" class="search-bar"
                        placeholder="Search by SUP ID. , Supplier, Representative and status"
                        value="{{ request('search') }}"
                        style="outline:none;"
                    >
                    <button type="submit" class="search-btn"><span class="material-symbols-outlined">search</span></button>
                </form>


                <form action="{{ route('customers.list') }}" class="date-search" id="from-to-date" method="GET">
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

            <p class="heading">Customers list</p>

        </div>


        <div class="content-body" style="background: #fff">
            <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                <thead style="background-color: #fff;">
                    <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                        <th>#</th>
                        <th>SUP ID.</th>
                        <th>Supplier</th>
                        <th>Representative</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Sales Agent</th>
                        <th>Status</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($suppliers as $supplier)
                        <tr onclick="window.location.href='{{ route('customers.customer', ['supplier_id' => $supplier->supplier_id]) }}'">
                            <th>{{ $loop->iteration }}</th>
                            <td>{{ $supplier->supplier_id }}</td>
                            <td>{{ $supplier->company_name }}</td>
                            <td>{{ $supplier->representative->rep_last_name }}sd</td>
                            <td>{{ $supplier->user->email_address }}</td>
                            <td>{{ $supplier->user->role }}</td>

                            <td>{{ $supplier->representative->sdas ?? NULL}}</td>

                            <td>{{ $supplier->user->status }}</td>
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
