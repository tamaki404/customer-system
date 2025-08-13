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

    <div class="ordersFrame">
         <div class="wrapper-title" style="height: auto">
            <form action="/date-search" id="searchCon" style="margin-left: 10px" class="date-search" method="GET">
                <input type="text" style="    width: 390px; border: none;" name="search" class="search-bar" placeholder="Search receipt #, customer, amount, or date" value="{{ request('search') }}">
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
            <h2 style="margin: 5px; width: 100%">Orders List</h2>
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


        <div class="productList" style="padding: 15px;">
            @forelse($orders as $order)
                <a class="order-card" onclick="window.location='{{ route('order.view', $order->order_id) }}'" style="text-decoration: none; color: inherit;">
                    @php $dateToShow = $order->action_at ?? $order->created_at; @endphp
                    <p style="margin: 0; font-size: 15px;">{{ \Carbon\Carbon::parse($dateToShow)->format('F j, Y') }}</p>
                    <span>
                        <p class="store-name">{{ $order->user->store_name }}</p>
                         @php
                            $status = $order->status;

                            $statusClasses = [
                                'Pending' => 'status-pending',
                                'Processing' => 'status-processing',
                                'Cancelled' => 'status-cancelled',
                                'Rejected' => 'status-rejected',
                                'Done' => 'status-done',
                                'Completed' => 'status-completed',
                            ];
                        @endphp

                        <p class="{{ $statusClasses[ $order->status] ?? 'status-default' }}">
                            {{  $order->status }}
                        </p>


                        <p>x{{ $order->total_quantity }}</p>
                        <p style="color: green; font-size: 16px; font-weight: bold;">₱{{ number_format($order->total_amount, 2) }}</p>
                    </span>

                    

                </a>
            @empty
                <div class="noInput">No orders found.</div>
            @endforelse

        </div>



    </div>


</body>
</html>
@endsection
