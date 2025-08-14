@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/orders.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fadein.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <title>Orders</title>
</head>
<body>

    <div class="ordersFrame fadein-animate">
         <div class="wrapper-title" >
            <form action="/date-search" id="searchCon" style="margin-left: 10px" class="date-search" method="GET">
                <input type="text" style=" width: 390px; border: none;" name="search" class="search-bar" placeholder="Search receipt #, customer, amount, or date" value="{{ request('search') }}">
                <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
            </form>
            <form action="/date-search" class="date-search" id="from-to-date" method="GET">
                <span>From</span>
                <input type="date" name="from_date" class="input-date" value="{{ request('from_date', now()->startOfMonth()->format('Y-m-d')) }}" onchange="this.form.submit()">
                <span >To</span>
                <input type="date" name="to_date" class="input-date" value="{{ request('to_date', now()->endOfMonth()->format('Y-m-d')) }}" onchange="this.form.submit()">
            </form>
        </div> 



        <div class="titleCount">
            <h2 style="margin: 5px; width: 100%">Orders</h2>
        </div>

        @php
            $tabStatuses = ['All' => null, 'Pending' => 'Pending', 'Processing' => 'Processing', 'Completed' => 'Completed', 'Cancelled' => 'Cancelled', 'Rejected' => 'Rejected'];
            $baseParams = [
                'search' => request('search', ''),
                'from_date' => request('from_date', now()->startOfMonth()->format('Y-m-d')),
                'to_date' => request('to_date', now()->endOfMonth()->format('Y-m-d')),
            ];
            $currentStatus = request('status');
        @endphp

        <div class="status-tabs">
            @foreach($tabStatuses as $label => $value)
                @php
                    $params = $value ? array_merge($baseParams, ['status' => $value]) : $baseParams;
                    $isActive = ($value === null && empty($currentStatus)) || ($value !== null && $currentStatus === $value);
                @endphp
                <a href="{{ route('orders', $params) }}" class="status-tab{{ $isActive ? ' active' : '' }}">{{ $label }}</a>
            @endforeach
        </div>


        <div class="productList" style="padding: 5px;">
           @if ($orders->count() > 0)

                {{-- <table style="width:100%; border-collapse:collapse;" class="orders-table">
                    <thead>
                        <tr style="background:#f7f7fa; text-align: center;">
                            <th style="width: 50px;">#</th> 
                            <th style="width: 140px;">Date</th>
                            <th style="width: 250px;">Store Name</th>
                            <th style="width: 80px;">Status</th>
                            <th style="width: 80px;">Qty</th>
                            <th style="width: 120px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            @php 
                                $dateToShow = $order->action_at ?? $order->created_at;
                                $statusClasses = [
                                    'Pending' => 'status-pending',
                                    'Processing' => 'status-processing',
                                    'Cancelled' => 'status-cancelled',
                                    'Rejected' => 'status-rejected',
                                    'Done' => 'status-done',
                                    'Completed' => 'status-completed',
                                ];
                            @endphp
                            <tr style="height: 50px; text-align: center; cursor:pointer;" 
                                onclick="window.location='{{ route('order.view', $order->order_id) }}'">
                                
                                <td style="padding:10px 8px; font-size: 13px;">
                                    {{ $loop->iteration }}
                                </td>
                                <td style="padding:10px 8px; font-size: 13px;">
                                    {{ \Carbon\Carbon::parse($dateToShow)->format('F j, Y') }}
                                </td>
                                <td style="padding:10px 8px; font-size: 13px; font-weight: bold;">
                                    {{ $order->user->store_name }}
                                </td>
                                <td style="padding:10px; font-size: 13px;" 
                                    class="{{ $statusClasses[$order->status] ?? 'status-default' }}">
                                    {{ $order->status }}
                                </td>
                                <td style="padding:10px 8px; font-size: 13px;">
                                    x{{ $order->total_quantity }}
                                </td>
                                <td style="color: green; font-weight: bold; padding:10px 8px; font-size: 13px;">
                                    ₱{{ number_format($order->total_amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td ></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table> --}}

                <table style="width:100%; border-collapse:collapse;" class="orders-table">
                    <thead>
                        <tr style="background:#f7f7fa; text-align: center; ">
                            <th style="width: 50px; padding: 10px;">#</th> 
                            <th style="width: 140px;">Date</th>
                            <th style="width: 250px;">Customer</th>
                            <th style="width: 80px;">Total</th>
                            <th style="width: 80px;">Items</th>
                            <th style="width: 120px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            @php 
                                $dateToShow = $order->action_at ?? $order->created_at;
                                $statusClasses = [
                                    'Pending' => 'status-pending',
                                    'Processing' => 'status-processing',
                                    'Cancelled' => 'status-cancelled',
                                    'Rejected' => 'status-rejected',
                                    'Done' => 'status-done',
                                    'Completed' => 'status-completed',
                                ];
                            @endphp
                            <tr style="height: 50px; text-align: center; cursor:pointer;" 
                                onclick="window.location='{{ route('order.view', $order->order_id) }}'">
                                
                                <td style="padding:10px 8px; font-size: 13px;">
                                    {{ $loop->iteration }}
                                </td>
                                <td style="padding:10px 8px; font-size: 13px;">
                                    {{ \Carbon\Carbon::parse($dateToShow)->format(' j F, Y') }}
                                </td>
                                <td style="padding:10px 8px; font-size: 13px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                    {{ $order->user->store_name }}
                                </td>
                                <td style="padding:10px 8px; font-size: 13px;">
                                    ₱{{ number_format($order->total_amount, 2) }}
                                </td>
                                <td style="padding:10px 8px; font-size: 13px;">
                                    x{{ $order->total_quantity }}
                                </td>
                                <td >
                              <div style="display: flex; align-items: center; justify-content: center;" 
                                    class="{{ $statusClasses[$order->status] ?? 'status-default' }}">
                                    ● {{ $order->status }}
                                </div>

                                </td>


                            </tr>
                        @empty
                            <tr>
                                <td ></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            @else

                <p style="text-align:center; margin:0; width:100%; line-height:500px; font-size: 15px; color: #888">No orders found</p>

            @endif
        </div>



    </div>


</body>
</html>
@endsection
