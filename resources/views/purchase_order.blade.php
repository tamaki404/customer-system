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
    <title>Purchase Order</title>
</head>
<body>

    <div class="purchaseFrame">
        <div class="search-container">
            <form action="{{ route('purchase_order') }}" id="text-search" class="date-search" method="GET">
                <input type="text" name="search" class="search-bar"
                    placeholder="Search receipt #, customer, amount, or date"
                    value="{{ request('search') }}">
                <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
            </form>

            <form action="{{ route('purchase_order') }}" class="date-search" id="from-to-date" method="GET">
                <span>From</span>
                <input type="date" name="from_date" class="input-date"
                    value="{{ request('from_date', now()->startOfMonth()->format('Y-m-d')) }}"
                    onchange="this.form.submit()">
                <span>To</span>
                <input type="date" name="to_date" class="input-date"
                    value="{{ request('to_date', now()->endOfMonth()->format('Y-m-d')) }}"
                    onchange="this.form.submit()">
            </form>
        </div>


        <div class="title-purchase">
            <h2>Purchase Order</h2>
            <button class="create-purchase-order" onclick="location.href='/purchase-order/store/order'"><span class="material-symbols-outlined" style="font-size: 14px; font-weight: bold;">add</span> Purchase order</button>
            {{-- <button class="create-purchase-order" onclick="location.href='/purchase-order/create/purchase-order-form'"><span class="material-symbols-outlined" style="font-size: 14px; font-weight: bold;">add</span> Purchase order</button> --}}
        </div>
            @php
                $tabStatuses = [
                    'All' => null,
                    'Draft' => 'Draft',
                    'Pending' => 'Pending',
                    'Processing' => 'Processing',
                    'Partial' => 'Partial',
                    'Completed' => 'Completed',
                    'Cancelled' => 'Cancelled',
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
                        <th style="width: 180px;">Receiver</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 100px;">Subtotal</th>
                        <th style="width: 100px;">Grand Total</th>
                        <th style="width: 100px;">Attachment</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseOrders as $index => $order)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $order->order_date->format('Y-m-d') }}</td>
                            <td>{{ $order->po_number }}</td>
                            <td>{{ $order->receiver_name }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower($order->status) }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>₱{{ number_format($order->subtotal, 2) }}</td>
                            <td>₱{{ number_format($order->grand_total, 2) }}</td>
                            <td>
                                @if($order->po_attachment)
                                    <a href="{{ asset('storage/'.$order->po_attachment) }}" target="_blank">📎</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                {{-- <a href="{{ route('purchase_order.show', $order->id) }}" class="btn-action">View</a>
                                <a href="{{ route('purchase_order.edit', $order->id) }}" class="btn-action">Edit</a> --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">No purchase orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper" style="margin-top: 2rem; text-align: center;">
            {{-- @if($users->hasPages()) --}}
                {{-- <div class="pagination-controls" style="display: flex; justify-content: center; align-items: center; gap: 1rem;">
                    @if($users->onFirstPage())
                        <span style="color: #ccc; cursor: not-allowed;">Previous</span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}" 
                           style="color: #1976d2; text-decoration: none; padding: 0.5rem 1rem; border: 1px solid #1976d2; border-radius: 4px;">
                            Previous
                        </a>
                    @endif
                    
                    @if($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" 
                           style="color: #1976d2; text-decoration: none; padding: 0.5rem 1rem; border: 1px solid #1976d2; border-radius: 4px;">
                            Next
                        </a>
                    @else
                        <span style="color: #ccc; cursor: not-allowed;">Next</span>
                    @endif
                </div> --}}
            {{-- @endif --}}
        </div>
   


    </div>

</body>
</html>


 
@endsection
