@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/customer_orders.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Your Orders</title>
</head>
<body>
    
    <div class="orderFrame">

        {{-- <div class="wrapper-title" style="width: 100%; display: flex; justify-content: space-between; align-items: center; gap: 10px;">
            <form method="GET" action="" class="date-search" id="searchCon" style="margin-left: 10px">
                <input type="text" name="search" style="width: 390px; border: none; outline:none;" value="{{ request('search') }}" placeholder="Search by Order ID, Date, or Status">
                <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
            </form>
            <form method="GET" action="" class="date-search" id="from-to-date">
                <span>From</span>
                <input type="date" class="input-date" name="from_date" value="{{ request('from_date', now()->startOfMonth()->format('Y-m-d')) }}" onchange="this.form.submit()">
                <span>To</span>
                <input type="date" class="input-date" name="to_date" value="{{ request('to_date', now()->endOfMonth()->format('Y-m-d')) }}" onchange="this.form.submit()">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
            </form>
        </div> --}}

         <div class="wrapper-title">
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
            <h2 style="margin: 0">Your orders</h2> 
            <p style="margin: 0">These are the orders you've made</p>
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
                <a href="{{ route('customer_orders', $params) }}" class="status-tab{{ $isActive ? ' active' : '' }}">{{ $label }}</a>
            @endforeach
        </div>

        <div class="order-list">
            @if(isset($orders) && $orders->count() > 0)

            @forelse($orders as $order)
                <div class="order-summary">
                    <a class="order-meta" style="text-decoration: none; color: inherit;" href="{{ route('orders.view', $order->order_id) }}">

                        <span>
                            @php $dateToShow = $order->action_at ?? $order->created_at; @endphp
                            <p style="font-weight: bold; font-size: 15px;">{{ \Carbon\Carbon::parse($dateToShow)->format('M d, Y - h:i A') }}</p>
                            <p>x{{ $order->total_quantity }}</p>

                        </span>
                        <span>
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

                            <p class="{{ $statusClasses[ucfirst($order->status)] ?? 'status-default' }}">
                                {{ ucfirst($order->status) }}
                            </p>

                            <p style="font-weight: bold; color: green; font-size: 19px;">₱{{ number_format($order->total_amount, 2) }}</p>

                        </span>

                    </a>
                

                <div id="details-{{ $order->order_id }}" style="display: none; margin-top: 15px;">
                    <p><em>Click to load individual item details...</em></p>
                </div>
                </div>
            @empty
                <div class="text-center" style="padding: 50px;">
                    <h4>No orders found</h4>
                    <p>You haven't placed any orders yet.</p>
                </div>
            @endforelse
        @else
        <div class="noInput">No orders found.</div>
       @endif
    </div>

</body>
</html>




@endsection