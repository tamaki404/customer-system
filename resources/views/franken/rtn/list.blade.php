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
                <table>
                    <thead>
                        <tr>
                            <th>Delivered date</th>
                            <th>Delivery ID</th>
                            <th>Product</th>
                            <th>Variance</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if($varianceByDelivery->isNotEmpty())
                        <table>
                            <thead>
                                <tr>
                                    <th>Delivery ID</th>
                                    <th>Total Heads Variance</th>
                                    <th>Total Kilos Variance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($varianceByDelivery as $d)
                                    <tr>
                                        <td>{{ $d->delivery_id }}</td>
                                        <td>{{ $d->heads_variance }}</td>
                                        <td>{{ number_format($d->kilos_variance, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>No variances found.</p>
                    @endif




                    </tbody>
                </table>
          
        </div>
@endsection

@push('scripts')

@endpush
