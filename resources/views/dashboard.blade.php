@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/new_dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fadein.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Dashboard</title>
</head>
<body>

<div class="dashBody">
    <section class="head-section">
        <div class="greet-div">
            <h1>{{ $greeting }}, {{ auth()->user()->name }} 👋</h1>
            <h4>Here's your dashboard overview.</h4>
        </div>
    </section>
    <section class="status-cards-list">
        <a class="status-card" href="{{ route('receipts') }}">
            <p class="card-title">Purchase orders</p>
            <div class="card-content">
                <h1 class="card-count" id="pendingDayCount">{{$purchaseOrdersCount}}</h1>
                @if($newPurchaseOrdersCount > 0)
                    <span class="card-add-count">+{{$newPurchaseOrdersCount}}</span>
                @endif
            </div>
        </a>

        <a class="status-card" href="{{ route('receipts') }}">
            <p class="card-title">Orders</p>
            <div class="card-content">
                <h1 class="card-count" id="pendingDayCount">{{$ordersCount}}</h1>
                @if($newOrdersCount > 0)
                    <span class="card-add-count">+{{$newOrdersCount}}</span>
                @endif
            </div>
        </a>
        <a class="status-card" href="{{ route('receipts') }}">
            <p class="card-title">Receipts</p>
            <div class="card-content">
                <h1 class="card-count" id="pendingDayCount">{{$receiptsCount}}</h1>
                @if($newReceiptsCount > 0)
                    <span class="card-add-count">+{{$newReceiptsCount}}</span>
                @endif                
            </div>
        </a>
        <a class="status-card" href="{{ route('receipts') }}">
            <p class="card-title">Users</p>
            <div class="card-content">
                <h1 class="card-count" id="pendingDayCount">{{$usersCount}}</h1>
                @if($newUsersCount > 0)
                    <span class="card-add-count">+{{$newUsersCount}}</span>
                @endif                
                
            </div>
        </a>
    </section>
    <section class="purchase-order-chart">
        <p>Purchase orders this month</p>
        <canvas id="purchaseOrdersChart" height="300px"></canvas>
    </section>
    <section class="end-contents">
        <div class="recent-orders-list">
            <div class="recent-orders-title">
                <p>Recent orders</p>
                <a href="/purchase_order">Show all ></a>
            </div>
            <div class="recent-orders">
                @foreach($recentPurchaseOrders as $order)
                    <a class="order-row">
                        <p>
                            <span class="company">{{$order->user->store_name}}</span>
                            <span class="po-id">{{$order->po_number}}</span>
                        </p>
                        <p>
                            <span class="name">{{$order->receiver_name}}</span>
                            {{-- <span class="status">Pending</span> --}}
                            @php 
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
                            <span style="font-size: 13px" class="{{ $statusClasses[$order->status] ?? 'status-default' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </p>

                    </a>
                @endforeach

            </div>

        </div>
        <div class="delivery-status">

        </div>

    </section>


</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('purchaseOrdersChart').getContext('2d');

    const purchaseOrdersChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [{
                label: 'Purchase Orders',
                data: @json($data),
                fill: true,
                borderColor: '#ffde59',
                backgroundColor: '#ffde5972',
                borderWidth: 2,
                tension: 0.3,
                pointRadius: 4,
                pointBackgroundColor: '#ffde59',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    },
                    ticks: {
                        callback: function(value, index, ticks) {
                            let date = this.getLabelForValue(value); 
                            let d = new Date(date);
                            return d.toLocaleDateString('en-US', { day: 'numeric', month: 'short' });
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Orders'
                    }
                }
            }
        }
    });
</script>


</body> 
</html>


 
@endsection
