@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/purchase-order.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fadein.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

    <title>Purchase Order</title>
</head>
<body>

    <div class="purchaseFrame">
        <div class="search-container">
            <form action="{{ route('purchase_order') }}" id="text-search" class="date-search" method="GET">
                <input type="text" name="search" class="search-bar"
                    placeholder="Search receipt #, customer, amount, or date"
                    value="{{ request('search') }}"
                    style="outline:none;"
                >
                <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
            </form>

            <form action="{{ route('purchase_order') }}" class="date-search" id="from-to-date" method="GET">
                <div>
                    <span>From</span>
                    <input type="date" name="from_date" class="input-date"
                        value="{{ request('from_date', now()->startOfMonth()->format('Y-m-d')) }}"
                        onchange="this.form.submit()">
                </div>
                <div>
                    <span>To</span>
                    <input type="date" name="to_date" class="input-date"
                        value="{{ request('to_date', now()->endOfMonth()->format('Y-m-d')) }}"
                        onchange="this.form.submit()">
                </div>


            </form>
        </div>


        <div class="title-purchase">
            <h2>Purchase Order</h2>
            @if(auth()->user()->user_type === 'Customer')
                 <button class="create-purchase-order" onclick="location.href='/purchase-order/store/order'"><span class="material-symbols-outlined" style="font-size: 14px; font-weight: bold;">add</span> Purchase order</button>
            @endif
        </div>
            @php
                $tabStatuses = [
                    'All' => null,
                    'Draft' => 'Draft',
                    'Pending' => 'Pending',
                    'Processing' => 'Processing',
                    'Delivered' => 'Delivered',
                    'Cancelled' => 'Cancelled',
                    'Rejected' => 'Rejected',
                ];

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
                <a href="{{ route('purchase_order', $params) }}" class="status-tab{{ $isActive ? ' active' : '' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>


        <div class="purchase-list">
            <table style="width:100%; border-collapse:collapse;" class="orders-table">
                <thead style="background-color: #f9f9f9;">
                    <tr style="background:#f7f7fa; text-align: center;">
                        <th style="width: 50px; padding: 10px;">#</th>
                        <th style="width: 100px;">Order Date</th>
                        <th style="width: 140px;">PO Number</th>
                        @if(auth()->user()->user_type === 'Admin' || auth()->user()->user_type === 'Staff' )
                            <th style="width: 150px;">Company</th>
                        @endif
                        <th style="width: 50px;">Quantity</th>
                        <th style="width: 100px;">Subtotal</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseOrders as $index => $order)
                        <tr style="height: 50px; text-align: center; cursor:pointer;" 
                            onclick="window.location='{{ route('purchase_order.view', $order->po_number) }}'">

                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $order->order_date->format('Y-m-d') }}</td>
                            <td>{{ $order->po_number }}</td>
                            @if(auth()->user()->user_type === 'Admin' || auth()->user()->user_type === 'Staff' )
                              <td style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $order->user->store_name }}</td>
                            @endif

                            <td>x{{ $order->items->sum('quantity') }}</td>                            
                            <td>₱{{ number_format($order->subtotal, 2) }}</td>
                            <td class="order-actions">
                                @php 
                                    $dateToShow = $order->action_at ?? $order->created_at;
                                    $statusClasses = [
                                        'Pending' => 'status-pending',
                                        'Processing' => 'status-processing',
                                        'Accepted' => 'status-approved',
                                        'Rejected' => 'status-rejected',
                                        'Delivered' => 'status-delivered',
                                        'Cancelled' => 'status-cancelled',
                                        'Draft' => 'status-draft',

                                    ];
                                @endphp
                                <span class="{{ $statusClasses[$order->status] ?? 'status-default' }}">
                                       {{ ucfirst($order->status) }}
                                </span>


                            </td>
                            <td>
                                <a href="" style="text-decoration: none; text-align: align; display: flex; gap: 5px; justify-content: center;">
                                    <span style="font-size: 17px;" class="material-symbols-outlined">download</span>
                                    <span>Purchase Order</span>
                                </a>
                                {{-- <a href="{{ route('purchase_order.show', $order->id) }}" class="btn-action">View</a>
                                <a href="{{ route('purchase_order.edit', $order->id) }}" class="btn-action">Edit</a> --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="color: #888">No purchase orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
     


           
        </div>

            <div class="pagination-wrapper" style="margin-top: 10px; text-align: center; display: flex; flex-direction: row; justify-content: space-between;">
                    {{-- page count --}}
                @if ($purchaseOrders->total() > 0)
                    <div style="text-align: center; font-size:14px; color: #555;">
                        Page {{ $purchaseOrders->currentPage() }} of {{ $purchaseOrders->lastPage() }}
                    </div>
                @endif


                @if ($purchaseOrders->hasPages())
                    <div class="pagination-controls" style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; ">

                        {{-- previous --}}
                        @if ($purchaseOrders->onFirstPage())
                            <span style="color: #fb8e24; font-size: 14px; padding: 0.5rem 1rem; border: 1px solid #fb8e24; border-radius: 10px;">Previous</span>
                        @else
                            <a href="{{ $purchaseOrders->previousPageUrl() }}" 
                            style="color: #fb8e24; text-decoration: none; font-size: 14px; padding: 0.5rem 1rem; border: 1px solid #fb8e24; border-radius: 10px;">
                                Previous
                            </a>
                        @endif

                        {{-- next --}}
                        @if ($purchaseOrders->hasMorePages())
                            <a href="{{ $purchaseOrders->nextPageUrl() }}" 
                            style="color: #fb8e24; text-decoration: none; font-size: 14px; padding: 0.5rem 1rem; border: 1px solid #fb8e24; border-radius: 10px;">
                                Next
                            </a>
                        @else
                            <span style="color: #ccc; padding: 0.5rem 1rem; font-size: 14px; border: 1px solid #ddd; border-radius: 10px;">Next</span>
                        @endif

                    </div>
                @endif

            </div>
    


    </div>

</body>
</html>


 
@endsection
