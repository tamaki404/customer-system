@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{asset('css/staffs/list.css')}}">
@endpush

@section('content')


    @if (session('success') || session('error'))
        <div id="flash-message"
            class="flash-message alert {{ session('success') ? 'alert-success' : 'alert-danger' }}">
            {{ session('success') ?? session('error') }}
        </div>
    @endif


        <div class="content-bg" style="overflow: hidden">
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
                    <p class="heading">Products list</p>
  
                </div>
            </div>

           
                @if (auth()->user()->role === 'Customer')

                    <div class="content-body" style="background: #fff">
                        <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th>#</th>
                                    <th>Product ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Measurement</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($setProducts as $prod)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>#{{ $prod->product->product_id }}</td>
                                        <td>{{ $prod->product->name }}</td>
                                        <td>{{ $prod->product->category }}</td>
                                        <td>{{ $prod->product->measurement_type }}</td>
                                        <td style="display: flex; flex-direction: column;">

                                            @if ($prod->promo && $prod->promo->value_type === "Fixed")
                                                <span style="color: #f8912a">₱{{$prod->promo->value}} </span>
                                            @elseif ($prod->promo && $prod->promo->value_type === "Percentage")
                                                @php
                                                    $decimal = $prod->promo->value/100;
                                                    $percentValue = $decimal * $prod->nego_price;
                                                @endphp
                                                <span style="color: #f8912a">₱{{$percentValue}} </span>
                                                <span style="text-decoration: line-through">₱{{ $prod->nego_price }}</span>
                                            @else
                                                ₱{{ $prod->nego_price }}
                                            @endif
                                            
                                        </td>
                                    </tr>
                                @endforeach 
                            </tbody>
                        </table>
                    </div>

                @endif

        </div>


@endsection

@push('scripts')




@endpush
