@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{asset('css/staffs/list.css')}}">
    
@endpush

@section('content')
        <div class="content-bg">
            <div class="content-header">
                <div class="contents-display">
                    <form action="{{ route('products.list') }}" id="text-search" class="search-text-con" method="GET">
                            <input type="text" name="search" class="search-bar"
                                placeholder="Search by CUST ID. , Customer, Representative and status"
                                value="{{ request('search') }}"
                                style="outline:none;"
                            >
                            <button type="submit" class="search-btn"><span class="material-symbols-outlined">search</span></button>
                    </form>
                    <form action="{{ route('products.list') }}" class="date-search" id="from-to-date" method="GET">
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
                    <p class="heading">Return reports</p>
                </div>
            </div>
            <div class="content-body" style="background: #fff">
                <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                    <thead style="background-color: #fff;">
                        <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                            <th>#</th>
                            <th>Delivered at</th>
                            <th>Customer</th>
                            <th>Delivery ID</th>
                            <th>Variance</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>          
                        @if($varianceByDelivery->isNotEmpty())
                            @foreach($varianceByDelivery as $d)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $d->delivery->delivered_date->format('F j, y') }}</td>
                                    <td>{{ $d->delivery->customer->company_name }}</td>
                                    <td>#{{ $d->delivery_id }}</td> 
                                    <td>
                                        {{ $d->heads_variance }} - {{ number_format($d->kilos_variance, 2) }}kg
                                    </td>
                                    <td>Unresolved</td>
                                    <td>
                                        <div class="dropdown" style="display:flex; align-items: center; justify-content: center;">
                                            <button class="" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 13px; ">
                                                <span class="material-symbols-outlined">
                                                expand_circle_down
                                                </span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" style="color: #f8912a"
                                                        href="{{ route('pr.request', ['po_id' => $d->delivery->po_id ?? NULL]) }}">
                                                        <span class="material-symbols-outlined">package_2</span>
                                                        Purchase order
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" style="color: #333"
                                                        href="{{ route('dlv.delivery', ['delivery_id' => $d->delivery_id ?? NULL]) }}">
                                                        <span class="material-symbols-outlined">arrow_outward</span>
                                                        Go to delivery
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
          
        </div>
@endsection

@push('scripts')

@endpush
