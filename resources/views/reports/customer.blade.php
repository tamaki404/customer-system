@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/reporting.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer-reports.css')}}">
    <link rel="stylesheet" href="{{ asset('css/fadein.css') }}">
    <title>My Reports</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .my-report-grid{ display:grid; grid-template-columns: repeat(auto-fit, minmax(220px,1fr)); gap:12px; }
        .my-card{ background:#fff; border-radius:10px; padding:14px; box-shadow: rgba(0, 0, 0, 0.05) 0 6px 24px 0, rgba(0, 0, 0, 0.08) 0 0 0 1px; }
        .chart-box{ position:relative; height:320px; min-height:220px; }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-processing {
            background: #cce5ff;
            color: #004085;
            border: 1px solid #a3d5ff;
        }

        .status-completed {
            background: #d1edff;
            color: #155724;
            border: 1px solid #a8d8a8;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .status-draft {
            background: #e2e3e5;
            color: #383d41;
            border: 1px solid #d6d8db;
        }

        .po-actions {
            display: flex;
            gap: 8px;
            margin-top: 10px;
        }

        .btn-small {
            padding: 4px 8px;
            font-size: 12px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-view {
            background: #007bff;
            color: white;
        }

        .btn-view:hover {
            background: #0056b3;
            color: white;
        }

        .btn-download {
            background: #28a745;
            color: white;
        }

        .btn-download:hover {
            background: #1e7e34;
            color: white;
        }

        .btn-cancel {
            background: #dc3545;
            color: white;
        }

        .btn-cancel:hover {
            background: #c82333;
            color: white;
        }
    </style>
</head>
<body>
<div class="startBody">
    <div class="reportsHeader">
        <h2>📊 My Reports</h2>
        <p>Your personal orders, spending, and receipts</p>
    </div>
        @php
            $activeTab = request('active_tab', 'my-overview');
            $baseParams = [
                'date_range' => request('date_range', 'last_30_days'),
                'from_date' => request('from_date'),
                'to_date' => request('to_date'),
            ];
        @endphp
        <nav class="tab-navigation" style="margin-top:10px; background:#fff;">
            <a style="text-decoration: none" class="tab-button {{ $activeTab === 'my-overview' ? 'active' : '' }}" href="{{ route('customer_reports', array_merge($baseParams, ['active_tab' => 'my-overview'])) }}">Overview</a>
            <a style="text-decoration: none" class="tab-button {{ $activeTab === 'my-orders' ? 'active' : '' }}" href="{{ route('customer_reports', array_merge($baseParams, ['active_tab' => 'my-orders'])) }}">My Orders</a>
            <a style="text-decoration: none" class="tab-button {{ $activeTab === 'my-purchase-orders' ? 'active' : '' }}" href="{{ route('customer_reports', array_merge($baseParams, ['active_tab' => 'my-purchase-orders'])) }}">My Purchase Orders</a>
            <a style="text-decoration: none" class="tab-button {{ $activeTab === 'my-products' ? 'active' : '' }}" href="{{ route('customer_reports', array_merge($baseParams, ['active_tab' => 'my-products'])) }}">Top Products</a>
            <a style="text-decoration: none" class="tab-button {{ $activeTab === 'my-receipts' ? 'active' : '' }}" href="{{ route('customer_reports', array_merge($baseParams, ['active_tab' => 'my-receipts'])) }}">My Receipts</a>
        </nav>

    <div class="report-container"  style="overflow-y: auto;">
    <form method="GET" action="{{ route('customer_reports') }}" class="filters-row" style="margin-bottom: 10px; background:#fff;">
        <input type="hidden" name="active_tab" value="{{ $activeTab }}">
        <div class="filter-group">
            <label>Date Range</label>
            <select name="date_range" id="myDateRange" onchange="this.form.submit()">
                <option value="last_7_days" {{ request('date_range') == 'last_7_days' ? 'selected' : '' }}>Last 7 days</option>
                <option value="last_30_days" {{ request('date_range') == 'last_30_days' || !request('date_range') ? 'selected' : '' }}>Last 30 days</option>
                <option value="last_3_months" {{ request('date_range') == 'last_3_months' ? 'selected' : '' }}>Last 3 months</option>
                <option value="custom" {{ request('date_range') == 'custom' ? 'selected' : '' }}>Custom Range</option>
            </select>
        </div>
        <div class="filter-group" id="fromDateGroup" style="{{ request('date_range') == 'custom' ? '' : 'display:none;' }}">
            <label>From</label>
            <input type="date" name="from_date" value="{{ request('from_date', $startDate->format('Y-m-d')) }}">
        </div>
        <div class="filter-group" id="toDateGroup" style="{{ request('date_range') == 'custom' ? '' : 'display:none;' }}">
            <label>To</label>
            <input type="date" name="to_date" value="{{ request('to_date', $endDate->format('Y-m-d')) }}">
        </div>
        <button type="submit" class="apply-filter">Apply</button>
    </form>

        <div style="margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 5px; font-size: 14px; ">
            <strong>Current Period:</strong> {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
        </div>

        <!-- Overview Tab -->
        <div id="my-overview" class="tab-content {{ $activeTab === 'my-overview' ? 'active' : '' }}">
            <div class="my-report-grid">
                <div class="my-card">
                    <div class="stat-value">₱{{ number_format($myTotalSpent, 2) }}</div>
                    <div class="stat-label">Total Spent</div>
                </div>
                <div class="my-card">
                    <div class="stat-value">{{ $myCompletedOrders }}</div>
                    <div class="stat-label">Completed Orders</div>
                </div>
                <div class="my-card">
                    <div class="stat-value">₱{{ number_format($myAverageOrderValue, 2) }}</div>
                    <div class="stat-label">Average Order Value</div>
                </div>
                <div class="my-card">
                    <div class="stat-value">{{ $myOrdersCount }}</div>
                    <div class="stat-label">Total Orders</div>
                </div>
                <div class="my-card">
                    <div class="stat-value">{{ $myPurchaseOrdersCount ?? 0 }}</div>
                    <div class="stat-label">Purchase Orders</div>
                </div>
                <div class="my-card">
                    <div class="stat-value">₱{{ number_format($myPurchaseOrdersTotal ?? 0, 2) }}</div>
                    <div class="stat-label">PO Total Value</div>
                </div>
            </div>

            <p style="margin: 12px 0 6px 0; font-weight: 700;">Monthly Spend</p>
            <div class="my-card chart-box">
                <canvas id="mySpendChart"></canvas>
            </div>
        </div>

        <!-- My Orders Tab -->
        <div id="my-orders" class="tab-content {{ $activeTab === 'my-orders' ? 'active' : '' }}">
            <p style="margin: 12px 0 6px 0; font-weight: 700;">Orders In Range</p>
            <div class="my-card" style="overflow:auto;">
                <table class="table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Status</th>
                            <th>Total Quantity</th>
                            <th>Total Price</th>
                            <th>Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($myOrders as $o)
                            <tr>
                                <td>{{ $o->order_id }}</td>
                                <td>{{ $o->status }}</td>
                                <td>{{ $o->total_quantity }}</td>
                                <td>₱{{ number_format($o->total_price, 2) }}</td>
                                <td>{{ \Carbon\Carbon::parse($o->action_at ?? $o->created_at)->format('M d, Y g:i A') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- My Purchase Orders Tab -->
        <div id="my-purchase-orders" style="overflow-y: auto;" class="tab-content {{ $activeTab === 'my-purchase-orders' ? 'active' : '' }}">
            <p style="margin: 12px 0 6px 0; font-weight: 700;">My Purchase Orders</p>
            
            <!-- PO Statistics -->
            <div class="my-report-grid" style="margin-bottom: 20px;">
                <div class="my-card">
                    <div class="stat-value">{{ $myPurchaseOrdersCount ?? 0 }}</div>
                    <div class="stat-label">Total Purchase Orders</div>
                </div>
                <div class="my-card">
                    <div class="stat-value">{{ $myPOPending ?? 0 }}</div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="my-card">
                    <div class="stat-value">{{ $myPOProcessing ?? 0 }}</div>
                    <div class="stat-label">Processing</div>
                </div>
                <div class="my-card">
                    <div class="stat-value">{{ $myPOCompleted ?? 0 }}</div>
                    <div class="stat-label">Completed</div>
                </div>
                <div class="my-card">
                    <div class="stat-value">{{ $myPOCancelled ?? 0 }}</div>
                    <div class="stat-label">Cancelled</div>
                </div>
                <div class="my-card">
                    <div class="stat-value">₱{{ number_format($myPurchaseOrdersTotal ?? 0, 2) }}</div>
                    <div class="stat-label">Total Value</div>
                </div>
            </div>

            <!-- Purchase Orders Table -->
            <div class="my-card" style="overflow-y:auto;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    {{-- <h4 style="margin: 0;">Purchase Orders List</h4> --}}
                    {{-- <a href="{{ route('customer.purchase_order.create') }}" class="btn btn-primary" style="background: #ffde59; color: #333; padding: 8px 16px; border-radius: 5px; text-decoration: none; font-weight: 600;">
                        + Create New PO
                    </a> --}}
                </div>
                <table class="table" style="width:100%;">
                    <thead>
                        <tr>
                            <th>PO Number</th>
                            <th>Company Name</th>
                            <th>Status</th>
                            <th>Total Amount</th>
                            <th>Order Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($myPurchaseOrders) && $myPurchaseOrders->count() > 0)
                            @foreach($myPurchaseOrders as $po)
                                <tr>
                                    <td>
                                        <strong>{{ $po->po_number }}</strong>
                                        <br>
                                        <small style="color: #666;">{{ $po->receiver_name }}</small>
                                    </td>
                                    <td>{{ $po->company_name }}</td>
                                    <td>
                                        <span class="status-badge status-{{ strtolower($po->status) }}">
                                            {{ $po->status }}
                                        </span>
                                    </td>
                                    <td>₱{{ number_format($po->grand_total, 2) }}</td>
                                    <td>{{ $po->order_date->format('M d, Y') }}</td>
                                    <td>
                                        <div class="po-actions">
                                            <a href="{{ route('purchase_order.view', $po->po_number) }}" class="btn-small btn-view">
                                                👁️ View
                                            </a>
                                            {{-- <a href="{{ route('purchase_order.download', $po->po_number) }}" class="btn-small btn-download">
                                                📄 PDF
                                            </a> --}}
                                            {{-- @if(in_array($po->status, ['Pending', 'Processing']))
                                                <form method="POST" action="{{ route('purchase_order.cancel', $po->id) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to cancel this purchase order?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn-small btn-cancel">
                                                        ❌ Cancel
                                                    </button>
                                                </form>
                                            @endif --}}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            {{-- <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: #666;">
                                    <div>
                                        <h4 f>No Purchase Orders Found</h4>
                                        <p>You haven't created any purchase orders yet.</p>
                                        <a href="{{ route('customer.purchase_order.create') }}" class="btn" style="background: #ffde59; color: #333; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: 600; margin-top: 10px; display: inline-block;">
                                            Create Your First Purchase Order
                                        </a>
                                    </div>
                                </td>
                            </tr> --}}
                        @endif
                    </tbody>
                </table>
            </div>

            @if(isset($myPOMonthlyData) && $myPOMonthlyData->count() > 0)
                <p style="margin: 20px 0 6px 0; font-weight: 700;">Purchase Orders Trend</p>
                <div class="my-card chart-box">
                    <canvas id="myPOChart"></canvas>
                </div>
            @endif
        </div>

        <!-- My Products Tab -->
        <div id="my-products" class="tab-content {{ $activeTab === 'my-products' ? 'active' : '' }}">
            <p style="margin: 12px 0 6px 0; font-weight: 700;">Top Products</p>
            <div class="my-card chart-box">
                <canvas id="myTopProductsChart"></canvas>
            </div>
        </div>

        <!-- My Receipts Tab -->
        <div id="my-receipts" class="tab-content {{ $activeTab === 'my-receipts' ? 'active' : '' }}">
            <p style="margin: 12px 0 6px 0; font-weight: 700;">My Receipts</p>
            <div class="my-report-grid">
                <div class="my-card"><div class="stat-value">{{ $myReceiptsCount }}</div><div class="stat-label">Total Receipts</div></div>
                <div class="my-card"><div class="stat-value">{{ $myReceiptsPending }}</div><div class="stat-label">Pending</div></div>
                <div class="my-card"><div class="stat-value">{{ $myReceiptsVerified }}</div><div class="stat-label">Verified</div></div>
                <div class="my-card"><div class="stat-value">{{ $myReceiptsCancelled }}</div><div class="stat-label">Cancelled</div></div>
                <div class="my-card"><div class="stat-value">{{ $myReceiptsRejected }}</div><div class="stat-label">Rejected</div></div>
                <div class="my-card"><div class="stat-value">₱{{ number_format($myReceiptsVerifiedAmount, 2) }}</div><div class="stat-label">Verified Amount</div></div>
            </div>
        </div>
    </div>
</div>

<script>
    function switchTab(tabId) {
        const sections = document.querySelectorAll('.tab-content');
        sections.forEach(s => s.classList.remove('active'));
        document.getElementById(tabId).classList.add('active');
        const buttons = document.querySelectorAll('.tab-navigation .tab-button');
        buttons.forEach(b => b.classList.remove('active'));
        event.target.classList.add('active');
    }

    // Monthly Spend Chart
    const spendCtx = document.getElementById('mySpendChart');
    if (spendCtx) {
        new Chart(spendCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: [
                    @foreach($myMonthlySpend as $month => $total)
                        "{{ DateTime::createFromFormat('!m', $month)->format('F') }}",
                    @endforeach
                ],
                datasets: [{
                    label: 'Spend (₱)',
                    data: [
                        @foreach($myMonthlySpend as $total)
                            {{ $total }},
                        @endforeach
                    ],
                    backgroundColor: 'rgba(255, 222, 89, 0.6)',
                    borderColor: 'rgba(212, 183, 65, 1)',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
        });
    }

    // Top Products Chart
    const tpCtx = document.getElementById('myTopProductsChart');
    if (tpCtx) {
        new Chart(tpCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: @json($myTopProducts->pluck('product_name')),
                datasets: [{
                    label: 'Quantity',
                    data: @json($myTopProducts->pluck('total_quantity')),
                    backgroundColor: 'rgba(255, 222, 89, 0.6)',
                    borderColor: 'rgba(212, 183, 65, 1)',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
    }

    // Purchase Orders Chart
    const poCtx = document.getElementById('myPOChart');
    if (poCtx) {
        new Chart(poCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: @json(isset($myPOMonthlyData) ? $myPOMonthlyData->keys()->map(fn($key) => \DateTime::createFromFormat('Y-m', $key)->format('M Y'))->toArray() : []),
                datasets: [{
                    label: 'Purchase Orders Count',
                    data: @json(isset($myPOMonthlyData) ? $myPOMonthlyData->pluck('count')->toArray() : []),
                    backgroundColor: 'rgba(255, 222, 89, 0.2)',
                    borderColor: '#ffde59',
                    borderWidth: 2,
                    fill: true
                }, {
                    label: 'Total Value (₱)',
                    data: @json(isset($myPOMonthlyData) ? $myPOMonthlyData->pluck('value')->toArray() : []),
                    backgroundColor: 'rgba(248, 145, 42, 0.2)',
                    borderColor: '#f8912a',
                    borderWidth: 2,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Number of Orders'
                        },
                        beginAtZero: true
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Total Value (₱)'
                        },
                        beginAtZero: true,
                        grid: {
                            drawOnChartArea: false,
                        },
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Handle custom date range toggle
    document.getElementById('myDateRange').addEventListener('change', function() {
        const isCustom = this.value === 'custom';
        document.getElementById('fromDateGroup').style.display = isCustom ? 'block' : 'none';
        document.getElementById('toDateGroup').style.display = isCustom ? 'block' : 'none';
    });
</script>

</body>
</html>
@endsection