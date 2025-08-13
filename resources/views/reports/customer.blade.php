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
        
    </style>
    </head>
<body>
<div class="startBody">
    <div class="reportsHeader">
        <h2>📊 My Reports</h2>
        <p>Your personal orders, spending, and receipts</p>
    </div>
        <nav class="tab-navigation" style="margin-top:10px; background:#fff;">
        <button class="tab-button active" onclick="switchTab('my-overview')">Overview</button>
        <button class="tab-button" onclick="switchTab('my-orders')">My Orders</button>
        <button class="tab-button" onclick="switchTab('my-products')">Top Products</button>
        <button class="tab-button" onclick="switchTab('my-receipts')">My Receipts</button>
    </nav>

    <div class="report-container">

        <form method="GET" action="{{ route('reports') }}" class="filters-row" style="margin-bottom: 10px; background:#fff;">
            <input type="hidden" name="active_tab" value="sales">
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



        <div id="my-overview" class="tab-content active">
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
            </div>

            <p style="margin: 12px 0 6px 0; font-weight: 700;">Monthly Spend</p>
            <div class="my-card chart-box">
                <canvas id="mySpendChart"></canvas>
            </div>
        </div>



        <div id="my-products" class="tab-content">
            <p style="margin: 12px 0 6px 0; font-weight: 700;">Top Products</p>
            <div class="my-card chart-box">
                <canvas id="myTopProductsChart"></canvas>
            </div>
        </div>

        <div id="my-orders" class="tab-content">
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

        <div id="my-receipts" class="tab-content">
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

    const tpCtx = document.getElementById('myTopProductsChart');
    if (tpCtx) {
        new Chart(tpCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: @json($myTopProducts->pluck('product_name')),
                datasets: [{
                    label: 'Quantity',
                    data: @json($myTopProducts->pluck('total_quantity')),
                    backgroundColor: 'rgba(25, 118, 210, 0.2)',
                    borderColor: 'rgba(25, 118, 210, 0.8)',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
    }
</script>

</body>
</html>
@endsection


