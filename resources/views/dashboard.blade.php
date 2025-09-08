@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fadein.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Dashboard</title>
</head>
<body>

<script src="{{ asset('js/fadein.js') }}"></script>

<div class="dashBody">
 

    @if(auth()->user()->user_type === 'Admin' || auth()->user()->user_type === 'Staff')
        <section class="head-section">

            <div class="greet-div">
                <h1>{{ $greeting }}, {{ auth()->user()->name }} 👋</h1>
                <h4>Here's your dashboard overview.</h4>
            </div>

            <form action="{{ route('dashboard') }}" class="date-search" id="from-to-date" method="GET">
                <p>Date picker</p>
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

        </section>

        @if(request('from_date') && request('to_date'))
            <p class="date-results-label" >
                <span style="">Showing results from</span>
                <strong style="">{{ \Carbon\Carbon::parse(request('from_date'))->format('M d, Y') }}</strong> 
                <span style="">to</span> 
                <strong style="">{{ \Carbon\Carbon::parse(request('to_date'))->format('M d, Y') }}</strong>
            </p>
        @endif

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
                <p class="card-title">Users (Pending)</p>
                <div class="card-content">
                    <h1 class="card-count" id="pendingDayCount">{{$usersCount}}</h1>
                    @if($newUsersCount > 0)
                        <span class="card-add-count">+{{$newUsersCount}}</span>
                    @endif                
                    
                </div>
            </a>
        </section>
        <section class="purchase-order-chart">
            <p>
                Purchase orders record 

            </p>
            <canvas id="purchaseOrdersChart" height="300px"></canvas>
        </section>
        <section class="end-contents">
            <div class="recent-orders-list">
                <div class="recent-orders-title">
                    <p>Recent orders</p>
                    <a href="/purchase_order">Show all ></a>
                </div>
                <div class="recent-orders">
                    @if ($recentPurchaseOrders->count() > 0)
                        @foreach($recentPurchaseOrders as $order)

                            <a class="order-row" href="{{ route('purchase_order.view', $order->po_number) }}">
                                <p>
                                    <span class="company">{{$order->user->store_name}}</span>
                                    <span class="po-id">{{$order->po_number}}</span>
                                </p>
                                <p>
                                    <span class="name">{{$order->receiver_name}}</span>
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
                    @else
                    <p style="font-size: 14px; color:#666; align-self: center; justify-self: center; margin-top: 100px">No order data found.</p>
                    @endif
                </div>
            </div>
            <div class="delivery-status">
                <div class="recent-orders-title">
                    <p>Order status</p>
                </div>
                <p class="order-count">
                    <span class="delivered-count">{{$completedOrders}}</span>
                    <span class="delivered-label">Fulfilled orders</span>
                </p>
                <div class="recent-orders">
                    <canvas id="statusChart" height="120"></canvas>
                </div>
            </div>
        </section>

    @elseif(auth()->user()->user_type === 'Customer')
        <section class="head-section">

            <div class="greet-div">
                <h1>{{ $greeting }}, {{ auth()->user()->name }} 👋</h1>
                <h4>Here's your dashboard overview.</h4>
            </div>

            <form action="{{ route('dashboard') }}" class="date-search" id="from-to-date" method="GET">
                <p>Date picker</p>
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

        </section>

        @if(request('from_date') && request('to_date'))
            <p class="date-results-label" >
                <span style="">Showing results from</span>
                <strong style="">{{ \Carbon\Carbon::parse(request('from_date'))->format('M d, Y') }}</strong> 
                <span style="">to</span> 
                <strong style="">{{ \Carbon\Carbon::parse(request('to_date'))->format('M d, Y') }}</strong>
            </p>
        @endif

        <section class="status-cards-list">
            <a class="status-card" href="{{ route('receipts') }}">
                <p class="card-title">Purchase orders</p>
                <div class="card-content">
                    <h1 class="card-count" id="pendingDayCount">{{$customerPendings}}</h1>
                    @if($newCustomerPendings > 0)
                        <span class="card-add-count">+{{$newCustomerPendings}}</span>
                    @endif
                </div>
            </a>

            <a class="status-card" href="{{ route('receipts') }}">
                <p class="card-title">Orders</p>
                <div class="card-content">
                    <h1 class="card-count" id="pendingDayCount">{{$customerOrders}}</h1>
                    @if($newCustomerOrders > 0)
                        <span class="card-add-count">+{{$newCustomerOrders}}</span>
                    @endif
                </div>
            </a>
            <a class="status-card" href="{{ route('receipts') }}">
                <p class="card-title">Receipts</p>
                <div class="card-content">
                    <h1 class="card-count" id="pendingDayCount">{{$customerReceipts}}</h1>
                    @if($newCustomerReceipts > 0)
                        <span class="card-add-count">+{{$newCustomerReceipts}}</span>
                    @endif                
                </div>
            </a>
            <a class="status-card">
                <p class="card-title">Outstanding balance</p>
                <div class="card-content">
                    @if($outstandingBalance > 0)
                        <h1 class="card-count" id="pendingDayCount">₱{{$outstandingBalance}}</h1>
                    @else
                        <p style="font-size: 14px">Your payments are updated</p>
                    @endif   
                </div>
            </a>
        </section>
        <section class="recent-order">
            <p class="recent-order-label">
                <span>Recent order</span>
                @if($recentOrder)
                    <a style="" href="{{ route('purchase_order.view', $recentOrder->po_number) }}">View order ></a>
                    </p>
                    <p class="order-details" style="margin-bottom: 10px">
                        <span class="po_number">{{ $recentOrder->po_number }}</span>
                        <span class="quan-total">
                            <span>x{{ $recentOrder->items->sum('quantity') }}</span>
                            <span style="font-weight: bold; color: green">₱{{ number_format($recentOrder->subtotal, 2) }}</span>
                        </span>
                        
                    </p>

                    <div class="order-timeline">
                        <div class="timeline-step {{ $recentOrder->created_at ? 'active' : '' }}">
                            @if ($recentOrder->created_at)
                            <div id="line" class=""></div>
                            @endif                        
                            <p>Order placed<br><small>{{ $recentOrder->created_at->format('M d, H:i a') }}</small></p>
                        </div>

                        @if ($recentOrder->cancelled_at)
                            <div class="timeline-step {{ $recentOrder->cancelled_at ? 'active' : '' }}">
                            @if ($recentOrder->cancelled_at)
                            <div id="line" class="" style="border-bottom: 18px solid red;"></div>
                            @else
                            <div id="line" style="border-bottom: 18px solid #d1d5db;"></div>
                            @endif

                            <p>
                                Cancelled
                                <br>
                                <small>{{ $recentOrder->cancelled_at ? $recentOrder->cancelled_at->format('M d, H:i a') : '' }}</small>
                            </p>
                        @elseif($recentOrder->rejected_at) 
                            <div class="timeline-step {{ $recentOrder->rejected_at ? 'active' : '' }}">
                            @if ($recentOrder->rejected_at)
                            <div id="line" class="" style="border-bottom: 18px solid red;"></div>
                            @else
                            <div id="line" style="border-bottom: 18px solid #d1d5db;"></div>
                            @endif

                            <p>
                                Rejected
                                <br>
                                <small>{{ $recentOrder->rejected_at ? $recentOrder->rejected_at->format('M d, H:i a') : '' }}</small>
                            </p>

                        @else
                        
                            <div class="timeline-step {{ $recentOrder->approved_at ? 'active' : '' }}">
                                @if ($recentOrder->approved_at)
                                <div id="line" class=""></div>
                                @else
                                    <div id="line" style="border-bottom: 18px solid #d1d5db;"></div>
                                @endif

                                <p>Approved<br>
                                    <small>{{ $recentOrder->approved_at ? $recentOrder->approved_at->format('M d, H:i a') : '' }}</small>
                                </p>
                            </div>

                            <div class="timeline-step {{ $recentOrder->delivered_at ? 'active' : '' }}">
                                @if ($recentOrder->delivered_at)
                                    <div id="line" class=""></div>
                                @else
                                    <div id="line" style="border-bottom: 18px solid #d1d5db;"></div>

                                @endif

                                <p>Delivered<br>
                                    <small>{{ $recentOrder->delivered_at ? $recentOrder->delivered_at->format('M d, H:i a') : '' }}</small>
                                </p>
                            </div>

                        @endif



                        
                    </div>
                @else

                <p style="font-size: 14px; color:#666; text-align:center; margin-top: 50px">
                    No order data found.
                </p>
            @endif
        </section>

        <section class="spending-summary-chart">
            <p style="color: #333; font-weight: bold; font-size: 16px;">
                Spending summary  
            </p>
            <canvas id="spendingChart" height="140px"></canvas>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const spendingCtx = document.getElementById('spendingChart').getContext('2d');

                const chart = new Chart(spendingCtx, {
                    type: 'line',
                    data: {
                        labels: @json($spendingLabels), 
                        datasets: [{
                            label: 'Weekly spending',   
                            data: @json($spendingData), 
                            borderColor: '#ffde59',
                            backgroundColor: '#ffde5972', 
                            fill: true,                 
                            borderWidth: 2,
                            tension: 0.3,
                            pointRadius: 4,
                            pointBackgroundColor: '#ffde59',
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '₱' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            </script>
        </section>






       
    @endif
    


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
        },
        
    });

    //order single line graph
    const statusData = @json($statusPercents);

    const statusCtx = document.getElementById('statusChart').getContext('2d');

    const statusChart = new Chart(statusCtx, {
        type: 'bar',
        data: {
            labels: [''], 
            datasets: Object.keys(statusData).map((status, index) => ({
                label: status,
                data: [statusData[status]],
                backgroundColor: [
                '#37415172', 
                '#3730a372',
                '#33415572', 
                '#f59e0b72', 
                '#991b1b72' 
                ][index % 5],
                borderWidth: 0
            }))
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.x + '%';
                        }
                    }
                }
            },
            scales: {
                x: {
                    stacked: true,
                    min: 0,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                },
                y: {
                    stacked: true,
                    display: false
                }
            }
        },
        plugins: [{
            afterDatasetsDraw: function(chart) {
                const ctx = chart.ctx;
                chart.data.datasets.forEach((dataset, i) => {
                    const meta = chart.getDatasetMeta(i);
                    if (!meta.hidden) {
                        meta.data.forEach((element, index) => {
                            const data = dataset.data[index];
                            if (data > 8) { 
                                ctx.fillStyle = '#fff';
                                ctx.font = 'bold 14px Arial';
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'middle';
                                const pos = element.tooltipPosition();
                                ctx.fillText(data + '%', pos.x, pos.y);
                            }
                        });
                    }
                });
            }
        }]
    });
</script>


</body> 
</html>


 
@endsection
