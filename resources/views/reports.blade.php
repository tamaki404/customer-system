@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/reporting.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fadein.css') }}">
    <link rel="stylesheet" href="{{ asset('css/print-purchase-orders.css') }}" media="print">
    <title>Reports</title>
</head>
<body>
<script src="{{ asset('js/fadein.js') }}"></script>
<div class="startBody">
    <div class="reportsHeader">
        <h2>📊 Reports Dashboard</h2>
        <p>Generate and download comprehensive reports</p>
    </div>
    <div class="report-container">
        <nav class="tab-navigation">
            <button class="tab-button {{! request('active_tab') || request('active_tab') == 'summary' ? 'active' : '' }}" onclick="switchTab('summary')">Summary</button>
            <button class="tab-button {{ request('active_tab') == 'sales' ? 'active' : '' }}" onclick="switchTab('sales')">Sales & Revenue</button>
            <button class="tab-button {{ request('active_tab') == 'customers' ? 'active' : '' }}" onclick="switchTab('customers')">Customer Analytics</button>
            <button class="tab-button {{ request('active_tab') == 'orders' ? 'active' : '' }}" onclick="switchTab('orders')">Order Management</button>
            <button class="tab-button {{ request('active_tab') == 'purchase_orders' ? 'active' : '' }}" onclick="switchTab('purchase_orders')">Purchase Orders</button>
            <button class="tab-button {{ request('active_tab') == 'products' ? 'active' : '' }}" onclick="switchTab('products')">Product Performance</button>
            <button class="tab-button {{ request('active_tab') == 'receipts' ? 'active' : '' }}" onclick="switchTab('receipts')">Receipts</button>
        </nav>

        {{-- Summary Tab (POs + Receipts only) --}}
        <div id="summary" class="tab-content {{! request('active_tab') || request('active_tab')  == 'summary' ? 'active' : '' }}">
            <h2>Summary</h2>

            <form method="GET" action="{{ route('reports') }}" id="summaryFilterForm">
                <input type="hidden" name="active_tab" value="summary">
                <div class="filters-row">
                    <div class="filter-group">
                        <label>Date range</label>
                        <select name="date_range" id="summaryDateRange" onchange="toggleCustomFields('summary')">
                            <option value="last_7_days" {{ request('date_range') == 'last_7_days' ? 'selected' : '' }}>Last 7 days</option>
                            <option value="last_30_days" {{ request('date_range') == 'last_30_days' || !request('date_range') ? 'selected' : '' }}>Last 30 days</option>
                            <option value="last_3_months" {{ request('date_range') == 'last_3_months' ? 'selected' : '' }}>Last 3 months</option>
                            <option value="custom" {{ request('date_range') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                    </div>
                    <div class="filter-group" id="summaryFromDateGroup" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                        <label>From Date</label>
                        <input type="date" name="from_date" value="{{ request('from_date', $startDate->format('Y-m-d')) }}">
                    </div>
                    <div class="filter-group" id="summaryToDateGroup" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                        <label>To Date</label>
                        <input type="date" name="to_date" value="{{ request('to_date', $endDate->format('Y-m-d')) }}">
                    </div>
                    <button type="submit" class="apply-filter">Apply Filters</button>
                </div>
            </form>

            <div class="current-period">
                <strong>Current Period:</strong>
                {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ $poCount }}</div>
                    <div class="stat-label">Total Purchase Orders</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $poDeliveredCount }}</div>
                    <div class="stat-label">Delivered Purchase Orders</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">₱{{ number_format($poGrandTotal, 2) }}</div>
                    <div class="stat-label">PO Grand Total (in range)</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">₱{{ number_format($verifiedReceiptsAmount, 2) }}</div>
                    <div class="stat-label">Verified Receipts (payments)</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">₱{{ number_format($outstandingBalance, 2) }}</div>
                    <div class="stat-label">Outstanding Balance</div>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ $fullyPaidCount }}</div>
                    <div class="stat-label">POs Fully Paid</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $partiallySettledCount }}</div>
                    <div class="stat-label">POs Partially Settled</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $processingOrUnpaidCount }}</div>
                    <div class="stat-label">POs Unpaid/Processing</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $overpaidCount }}</div>
                    <div class="stat-label">POs Overpaid</div>
                </div>
            </div>

            <p style="margin: 10px; font-size: 17px; font-weight: bold; color: #333; margin-top: 20px;">Monthly PO Value vs Collections</p>
            <div class="chart-container">
                <div style="width: 100%; margin: auto; height: 420px;">
                    <canvas id="summaryRevenueChart"></canvas>
                </div>
                <script>
                    const sctx = document.getElementById('summaryRevenueChart').getContext('2d');
                    const summaryRevenueChart = new Chart(sctx, {
                        type: 'bar',
                        data: {
                            labels: [
                                @if(isset($monthlyLabels))
                                    @foreach($monthlyLabels as $label)
                                        "{{ $label }}",
                                    @endforeach
                                @endif
                            ],
                            datasets: [
                                {
                                    label: 'PO Value',
                                    data: [
                                        @if(isset($monthlyPOValues))
                                            @foreach($monthlyPOValues as $v)
                                                {{ $v }},
                                            @endforeach
                                        @endif
                                    ],
                                    borderColor: '#ffde59',
                                    backgroundColor: '#ffde5972',
                                    borderWidth: 2,
                                },
                                {
                                    label: 'Collections',
                                    type: 'line',
                                    data: [
                                        @if(isset($monthlyCollectionValues))
                                            @foreach($monthlyCollectionValues as $v)
                                                {{ $v }},
                                            @endforeach
                                        @endif
                                    ],
                                    borderColor: '#f8912a',
                                    backgroundColor: '#f8912a33',
                                    borderWidth: 2,
                                    yAxisID: 'y1'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { mode: 'index', intersect: false },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) { return '₱' + value.toLocaleString(); }
                                    },
                                    position: 'left'
                                },
                                y1: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) { return '₱' + value.toLocaleString(); }
                                    },
                                    position: 'right',
                                    grid: { drawOnChartArea: false }
                                }
                            }
                        }
                    });
                </script>
            </div>

            <p style="margin: 10px; font-size: 17px; font-weight: bold; color: #333; margin-top: 20px;">Top Products (by PO items)</p>
            <div class="chart-container" style="overflow-x: auto;">
                <div class="data-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Total Quantity</th>
                                <th>Total Revenue (₱)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topPOProducts as $item)
                                <tr>
                                    <td>{{ $item->product_name }}</td>
                                    <td>x{{ (int)$item->total_quantity }}</td>
                                    <td>₱{{ number_format($item->total_revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center">No data for this period</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <p style="margin: 10px; font-size: 17px; font-weight: bold; color: #333; margin-top: 20px;">Top Customers (by PO value)</p>
            <div class="chart-container" style="overflow-x: auto;">
                <div class="data-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>POs</th>
                                <th>Total PO Value (₱)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topPOCustomers as $c)
                                <tr style="cursor: pointer" onclick="window.location='{{ url('/customer_view/' . $c->id) }}'">
                                    <td>{{ $c->store_name ?? 'Unknown' }}</td>
                                    <td>{{ $c->total_pos }}</td>
                                    <td>₱{{ number_format($c->total_po_value, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center">No data for this period</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- Sales & Revenue Tab --}}
        <div id="sales" class="tab-content {{ request('active_tab') == 'sales' ? 'active' : '' }}">
            <h2>Sales & Revenue Reports</h2>
            
            <form method="GET" action="{{ route('reports') }}" id="salesFilterForm">
                <input type="hidden" name="active_tab" value="sales">
                <div class="filters-row">
                    <div class="filter-group">
                        <label>Date range</label>
                        <select name="date_range" id="salesDateRange" onchange="toggleCustomFields('sales')">
                            <option value="last_7_days" {{ request('date_range') == 'last_7_days' ? 'selected' : '' }}>Last 7 days</option>
                            <option value="last_30_days" {{ request('date_range') == 'last_30_days' || !request('date_range') ? 'selected' : '' }}>Last 30 days</option>
                            <option value="last_3_months" {{ request('date_range') == 'last_3_months' ? 'selected' : '' }}>Last 3 months</option>
                            <option value="custom" {{ request('date_range') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                    </div>
                    <div class="filter-group" id="salesFromDateGroup" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                        <label>From Date</label>
                        <input type="date" name="from_date" value="{{ request('from_date', $startDate->format('Y-m-d')) }}">
                    </div>
                    <div class="filter-group" id="salesToDateGroup" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                        <label>To Date</label>
                        <input type="date" name="to_date" value="{{ request('to_date', $endDate->format('Y-m-d')) }}">
                    </div>
                    
                    <button type="submit" class="apply-filter">Apply Filters</button>
                </div>
            </form>

            <div class="report-actions">
                <a href="{{ route('reports.export', ['type' => 'pdf'] + request()->all()) }}" class="btn">📄 Export PDF</a>
            </div>

            <!-- Display current Date range -->
            <div style="margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 5px; font-size: 14px;">
                <strong>Current Period:</strong> 
                {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">₱{{ number_format($totalSales, 2) }}</div>
                    <div class="stat-label">Total PO Value</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">₱{{ number_format($verifiedCollections ?? 0, 2) }}</div>
                    <div class="stat-label">Verified Collections</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $paymentSuccessRate }}%</div>
                    <div class="stat-label">Collection Rate</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $completedOrdersCount }}</div>
                    <div class="stat-label">Delivered POs</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">₱{{ number_format($averageOrderValue, 2) }}</div>
                    <div class="stat-label">Average PO Value</div>
                </div>
            </div>

            <p style="margin: 10px; font-size: 17px; font-weight: bold; color: #333; margin-top: 20px;">Revenue Trend Chart</p>

            <div class="chart-container">
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <div style="width: 100%; margin: auto; height: 490px;">
                    <canvas id="revenueChart"></canvas>
                </div>

                <script>
                    const ctx = document.getElementById('revenueChart').getContext('2d');
                    const revenueChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: [
                                @if(isset($monthlyLabels))
                                    @foreach($monthlyLabels as $label)
                                        "{{ $label }}",
                                    @endforeach
                                @else
                                    @foreach($monthlySales as $month => $total)
                                        "{{ $month }}",
                                    @endforeach
                                @endif
                            ],
                            datasets: [
                                {
                                    label: 'PO Value',
                                    data: [
                                        @if(isset($monthlyPOValues))
                                            @foreach($monthlyPOValues as $v)
                                                {{ $v }},
                                            @endforeach
                                        @else
                                            @foreach($monthlySales as $total)
                                                {{ $total }},
                                            @endforeach
                                        @endif
                                    ],
                                    borderColor: '#ffde59',
                                    backgroundColor: '#ffde5972',
                                    borderWidth: 2,
                                },
                                {
                                    label: 'Collections',
                                    type: 'line',
                                    data: [
                                        @if(isset($monthlyCollectionValues))
                                            @foreach($monthlyCollectionValues as $v)
                                                {{ $v }},
                                            @endforeach
                                        @else
                                            @foreach($monthlySales as $total)
                                                {{ $total }},
                                            @endforeach
                                        @endif
                                    ],
                                    borderColor: '#f8912a',
                                    backgroundColor: '#f8912a33',
                                    borderWidth: 2,
                                    yAxisID: 'y1'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { mode: 'index', intersect: false },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) { return '₱' + value.toLocaleString(); }
                                    },
                                    position: 'left'
                                },
                                y1: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) { return '₱' + value.toLocaleString(); }
                                    },
                                    position: 'right',
                                    grid: { drawOnChartArea: false }
                                }
                            }
                        }
                    });
                </script>
            </div>

            <p style="margin: 10px; font-size: 17px; font-weight: bold; color: #333; margin-top: 20px;">Top Customers</p>
            <div class="chart-container" style="overflow-x: auto;">
                <div class="data-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>POs</th>
                                <th>Total PO Value (₱)</th>
                                <th>Collections (₱)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $collectionsIndex = collect($topCustomersByCollections ?? [])->keyBy('id');
                            @endphp
                            @foreach(($topCustomersByPO ?? []) as $c)
                                <tr style="cursor: pointer" onclick="window.location='{{ url('/customer_view/' . $c->id) }}'">
                                    <td>{{ $c->store_name ?? 'Unknown' }}</td>
                                    <td>{{ $c->pos ?? $c->total_pos ?? 0 }}</td>
                                    <td>₱{{ number_format($c->total_value ?? $c->total_po_value ?? 0, 2) }}</td>
                                    <td>₱{{ number_format(optional($collectionsIndex->get($c->id))->collected ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                            @if(empty($topCustomersByPO) || count($topCustomersByPO) === 0)
                                <tr><td colspan="4" class="text-center">No data for this period</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- Customer Analytics tab --}}
        <div id="customers" class="tab-content {{ request('active_tab') == 'customers' ? 'active' : '' }}">
            <h2>Customer Analytics</h2>
            
            <form method="GET" action="{{ route('reports') }}" id="customersFilterForm">
                <input type="hidden" name="active_tab" value="customers">
                <div class="filters-row">
                    <div class="filter-group">
                        <label>Date range</label>
                        <select name="date_range" id="customersDateRange" onchange="toggleCustomFields('customers')">
                            <option value="last_7_days" {{ request('date_range') == 'last_7_days' ? 'selected' : '' }}>Last 7 days</option>
                            <option value="last_30_days" {{ request('date_range') == 'last_30_days' || !request('date_range') ? 'selected' : '' }}>Last 30 days</option>
                            <option value="last_3_months" {{ request('date_range') == 'last_3_months' ? 'selected' : '' }}>Last 3 months</option>
                            <option value="custom" {{ request('date_range') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                    </div>
                    <div class="filter-group" id="customersFromDateGroup" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                        <label>From Date</label>
                        <input type="date" name="from_date" value="{{ request('from_date', $startDate->format('Y-m-d')) }}">
                    </div>
                    <div class="filter-group" id="customersToDateGroup" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                        <label>To Date</label>
                        <input type="date" name="to_date" value="{{ request('to_date', $endDate->format('Y-m-d')) }}">
                    </div>
                    
                    <button type="submit" class="apply-filter">Apply Filters</button>
                </div>
            </form>

            <div class="report-actions">
                <a href="{{ route('reports.customers', ['type' => 'pdf'] + request()->all()) }}" class="btn">📄 Export PDF</a>
            </div>

            <!-- Display current Date range -->
            <div style="margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 5px; font-size: 14px;">
                <strong>Current Period:</strong> 
                {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ $totalUsers }}</div>
                    <div class="stat-label">
                        @if(request('date_range') == 'last_7_days')
                            Customers (Last 7 Days)
                        @elseif(request('date_range') == 'last_30_days' || !request('date_range'))
                            Customers (Last 30 Days)
                        @elseif(request('date_range') == 'last_3_months')
                            Customers (Last 3 Months)
                        @elseif(request('date_range') == 'custom')
                            Customers (Custom Range)
                        @else
                            Total Customers
                        @endif
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $newThisMonth }}</div>
                    <div class="stat-label">
                        @if(request('date_range') == 'last_7_days')
                            New (Last 7 Days)
                        @elseif(request('date_range') == 'last_30_days' || !request('date_range'))
                            New (Last 30 Days)
                        @elseif(request('date_range') == 'last_3_months')
                            New (Last 3 Months)
                        @elseif(request('date_range') == 'custom')
                            New (Custom Range)
                        @else
                            New Customers
                        @endif
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{$pendingUsers}}</div>
                    <div class="stat-label">
                        @if(request('date_range') == 'last_7_days')
                            Pending (Last 7 Days)
                        @elseif(request('date_range') == 'last_30_days' || !request('date_range'))
                            Pending (Last 30 Days)
                        @elseif(request('date_range') == 'last_3_months')
                            Pending (Last 3 Months)
                        @elseif(request('date_range') == 'custom')
                            Pending (Custom Range)
                        @else
                            Pending Users
                        @endif
                    </div>
                </div>
              
            </div>

            <p style="margin: 10px; font-size: 17px; font-weight: bold; color: #333; margin-top: 20px;">Customer Activity</p>

            <div class="chart-container" style="overflow-x: auto;">
              <div class="data-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Telephone</th>
                            <th>Location</th>
                            <th>Representative</th>
                            <th>POs</th>
                            <th>PO Value (₱)</th>
                            <th>Collections (₱)</th>
                            <th>Outstanding (₱)</th>
                            <th>Last PO</th>
                            <th>Last Receipt</th>
                            <th>Reg. Date</th>
                            <th>Acc. Status</th>
                        </tr>
                    </thead>
                    <tbody>
                            @foreach ($customers as $customer)
                                <tr style="cursor: pointer" onclick="window.location='{{ url('/customer_view/' . $customer->id) }}'">
                                    <td>{{ $customer->store_name }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->mobile}}</td>
                                    <td>{{ $customer->telephone}}</td>
                                    <td>{{ $customer->address}}</td>
                                    <td>{{ $customer->name}}</td>
                                    <td>{{ (int)($customer->po_count ?? 0) }}</td>
                                    <td>₱{{ number_format($customer->po_value ?? 0, 2) }}</td>
                                    <td>₱{{ number_format($customer->collections ?? 0, 2) }}</td>
                                    <td>₱{{ number_format($customer->outstanding ?? 0, 2) }}</td>
                                    <td>{{ optional($customer->last_po_date)->format('M d, Y') ?? '-' }}</td>
                                    <td>{{ optional($customer->last_receipt_date)->format('M d, Y') ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($customer->created_at)->format('F Y') }}</td>
                                    <td>{{ $customer->acc_status }}</td>
                                </tr>
                            @endforeach
                    </tbody>
                </table>
            </div>
            </div>

            <p style="margin: 10px; font-size: 17px; font-weight: bold; color: #333; margin-top: 20px;">Top Customers & Their Most-Bought Product</p>
            <div class="chart-container" style="overflow-x: auto;">
              <div class="data-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Total PO Value (₱)</th>
                            <th>Most-Bought Product</th>
                            <th>Quantity</th>
                            <th>Product Revenue (₱)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $topMost = collect($topCustomersByPO_CA ?? []); @endphp
                        @foreach(($topMost ?? []) as $row)
                            @php $tp = optional(($customerTopProducts ?? collect())->get($row->customer_id)); @endphp
                            <tr style="cursor: pointer" onclick="window.location='{{ url('/customer_view/' . $row->customer_id) }}'">
                                <td>{{ $row->store_name }}</td>
                                <td>₱{{ number_format($row->total_po_value ?? 0, 2) }}</td>
                                <td>{{ $tp->product_name ?? '-' }}</td>
                                <td>{{ (int)($tp->total_quantity ?? 0) }}</td>
                                <td>₱{{ number_format($tp->total_revenue ?? 0, 2) }}</td>
                            </tr>
                        @endforeach
                        @if(empty($topCustomersByPO_CA) || count($topCustomersByPO_CA) === 0)
                            <tr><td colspan="5" class="text-center">No data for this period</td></tr>
                        @endif
                    </tbody>
                </table>
              </div>
            </div>
        </div>

        {{-- Order management tab --}}
        <div id="orders" class="tab-content {{ request('active_tab') == 'orders' ? 'active' : '' }}">
            <h2>Order Management Reports</h2>
            
            <form method="GET" action="{{ route('reports') }}" id="ordersFilterForm">
                <input type="hidden" name="active_tab" value="orders">
                <div class="filters-row">
                    <div class="filter-group">
                        <label>Date range</label>
                        <select name="date_range" id="ordersDateRange" onchange="toggleCustomFields('orders')">
                            <option value="last_7_days" {{ request('date_range') == 'last_7_days' ? 'selected' : '' }}>Last 7 days</option>
                            <option value="last_30_days" {{ request('date_range') == 'last_30_days' || !request('date_range') ? 'selected' : '' }}>Last 30 days</option>
                            <option value="last_3_months" {{ request('date_range') == 'last_3_months' ? 'selected' : '' }}>Last 3 months</option>
                            <option value="custom" {{ request('date_range') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                    </div>
                    <div class="filter-group" id="ordersFromDateGroup" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                        <label>From Date</label>
                        <input type="date" name="from_date" value="{{ request('from_date', $startDate->format('Y-m-d')) }}">
                    </div>
                    <div class="filter-group" id="ordersToDateGroup" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                        <label>To Date</label>
                        <input type="date" name="to_date" value="{{ request('to_date', $endDate->format('Y-m-d')) }}">
                    </div>
                    
                    <button type="submit" class="apply-filter">Apply Filters</button>
                </div>
            </form>

            <div class="report-actions">
                <a href="{{ route('reports.orders', ['type' => 'pdf'] + request()->all()) }}" class="btn">📄 Export PDF</a>
            </div>

            <!-- Display current Date range -->
            <div style="margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 5px; font-size: 14px;">
                <strong>Current Period:</strong> 
                {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ $OrdersordersCount }}</div>
                    <div class="stat-label">
                        @if(request('date_range') == 'last_7_days')
                            Orders (Last 7 Days)
                        @elseif(request('date_range') == 'last_30_days' || !request('date_range'))
                            Orders (Last 30 Days)
                        @elseif(request('date_range') == 'last_3_months')
                            Orders (Last 3 Months)
                        @elseif(request('date_range') == 'custom')
                            Orders (Custom Range)
                        @else
                            Total Orders
                        @endif
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $OrderscompletedOrders }}</div>
                    <div class="stat-label">
                        @if(request('date_range') == 'last_7_days')
                            Completed (Last 7 Days)
                        @elseif(request('date_range') == 'last_30_days' || !request('date_range'))
                            Completed (Last 30 Days)
                        @elseif(request('date_range') == 'last_3_months')
                            Completed (Last 3 Months)
                        @elseif(request('date_range') == 'custom')
                            Completed (Custom Range)
                        @else
                            Completed Orders
                        @endif
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{$OrdersprocessingOrders}}</div>
                    <div class="stat-label">
                        @if(request('date_range') == 'last_7_days')
                            Processing (Last 7 Days)
                        @elseif(request('date_range') == 'last_30_days' || !request('date_range'))
                            Processing (Last 30 Days)
                        @elseif(request('date_range') == 'last_3_months')
                            Processing (Last 3 Months)
                        @elseif(request('date_range') == 'custom')
                            Processing (Custom Range)
                        @else
                            Processing Orders
                        @endif
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{$OrderspendingOrders}}</div>
                    <div class="stat-label">
                        @if(request('date_range') == 'last_7_days')
                            Pending (Last 7 Days)
                        @elseif(request('date_range') == 'last_30_days' || !request('date_range'))
                            Pending (Last 30 Days)
                        @elseif(request('date_range') == 'last_3_months')
                            Pending (Last 3 Months)
                        @elseif(request('date_range') == 'custom')
                            Pending (Custom Range)
                        @else
                            Pending Orders
                        @endif
                    </div>
                </div>
                 <div class="stat-card">
                    <div class="stat-value">{{$OrderscancelledOrders}}</div>
                    <div class="stat-label">
                        @if(request('date_range') == 'last_7_days')
                            Cancelled (Last 7 Days)
                        @elseif(request('date_range') == 'last_30_days' || !request('date_range'))
                            Cancelled (Last 30 Days)
                        @elseif(request('date_range') == 'last_3_months')
                            Cancelled (Last 3 Months)
                        @elseif(request('date_range') == 'custom')
                            Cancelled (Custom Range)
                        @else
                            Cancelled Orders
                        @endif
                    </div>
                </div>     
                <div class="stat-card">
                    <div class="stat-value">{{$OrdersrejectedOrders}}</div>
                    <div class="stat-label">
                        @if(request('date_range') == 'last_7_days')
                            Rejected (Last 7 Days)
                        @elseif(request('date_range') == 'last_30_days' || !request('date_range'))
                            Rejected (Last 30 Days)
                        @elseif(request('date_range') == 'last_3_months')
                            Rejected (Last 3 Months)
                        @elseif(request('date_range') == 'custom')
                            Rejected (Custom Range)
                        @else
                            Rejected Orders
                        @endif
                    </div>
                </div>          
            </div>

            <p style="margin: 10px; font-size: 17px; font-weight: bold; color: #333; margin-top: 20px;">Most Active Stores</p>

            <div class="chart-container" style="overflow-x: auto;">
              <div class="data-table">
                <table class="table">
                    <thead>
                        <tr>
                        <th>Store Name</th>
                        <th>Total Orders</th>
                        <th>Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                   @foreach($topStores as $store)
                        <tr style="cursor: pointer"  onclick="window.location='{{ url('/customer_view' . $store->id) }}'">
                            <td>{{ $store->customer->store_name ?? 'Unknown' }}</td>
                            <td>{{ $store->total_orders }}</td>
                            <td>₱{{ $store->total_revenue }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            </div>
        </div>

        {{-- Purchase Orders tab --}}
        <div id="purchase_orders" class="tab-content {{ request('active_tab') == 'purchase_orders' ? 'active' : '' }}">
            <h2>Purchase Orders Reports</h2>
            
            <form method="GET" action="{{ route('reports') }}" id="purchaseOrdersFilterForm">
                <input type="hidden" name="active_tab" value="purchase_orders">
                <div class="filters-row">
                    <div class="filter-group">
                        <label>Date range</label>
                        <select name="date_range" id="purchaseOrdersDateRange" onchange="toggleCustomFields('purchaseOrders')">
                            <option value="last_7_days" {{ request('date_range') == 'last_7_days' ? 'selected' : '' }}>Last 7 days</option>
                            <option value="last_30_days" {{ request('date_range') == 'last_30_days' || !request('date_range') ? 'selected' : '' }}>Last 30 days</option>
                            <option value="last_3_months" {{ request('date_range') == 'last_3_months' ? 'selected' : '' }}>Last 3 months</option>
                            <option value="custom" {{ request('date_range') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                    </div>
                    <div class="filter-group" id="purchaseOrdersFromDateGroup" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                        <label>From Date</label>
                        <input type="date" name="from_date" value="{{ request('from_date', $startDate->format('Y-m-d')) }}">
                    </div>
                    <div class="filter-group" id="purchaseOrdersToDateGroup" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                        <label>To Date</label>
                        <input type="date" name="to_date" value="{{ request('to_date', $endDate->format('Y-m-d')) }}">
                    </div>
                    
                    <button type="submit" class="apply-filter">Apply Filters</button>
                </div>
            </form>

            <div class="report-actions">
                <a href="{{ route('purchase_order') }}" class="btn">📋 View All Purchase Orders</a>
                <!-- {{-- <a href="{{ route('reports.purchase_orders', ['type' => 'pdf'] + request()->all()) }}" class="btn">📄 Export PDF</a> --}} -->
                <button type="button" class="btn download-purchase-order" onclick="downloadPDF()">🖨️ Download PDF</button>

                <!-- Hidden inputs to store current Date range -->
                <input type="hidden" id="currentFromDate" value="{{ $startDate->format('Y-m-d') }}">
                <input type="hidden" id="currentToDate" value="{{ $endDate->format('Y-m-d') }}">
                <input type="hidden" id="currentDateRange" value="{{ request('date_range', 'today') }}">
            </div>

            <!-- Display current Date range -->
            <div style="margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 5px; font-size: 14px;">
                <strong>Current Period:</strong> 
                {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ $POpurchaseOrdersCount ?? 0 }}</div>
                    <div class="stat-label">
                        @if(request('date_range') == 'last_7_days')
                            Purchase Orders (Last 7 Days)
                        @elseif(request('date_range') == 'last_30_days' || !request('date_range'))
                            Purchase Orders (Last 30 Days)
                        @elseif(request('date_range') == 'last_3_months')
                            Purchase Orders (Last 3 Months)
                        @elseif(request('date_range') == 'custom')
                            Purchase Orders (Custom Range)
                        @else
                            Total Purchase Orders
                        @endif
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $POpendingPOs ?? 0 }}</div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $POprocessingPOs ?? 0 }}</div>
                    <div class="stat-label">Processing</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $POcompletedPOs ?? 0 }}</div>
                    <div class="stat-label">Completed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $POcancelledPOs ?? 0 }}</div>
                    <div class="stat-label">Cancelled</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $POrejectedPOs ?? 0 }}</div>
                    <div class="stat-label">Rejected</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">₱{{ number_format($POtotalRevenue ?? 0, 2) }}</div>
                    <div class="stat-label">Total Completed PO Value</div>
                </div>
            </div>

            <p style="margin: 10px; font-size: 17px; font-weight: bold; color: #333; margin-top: 20px;">Top Companies by Purchase Orders</p>

            <div class="chart-container" style="overflow-x: auto;">
              <div class="data-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>PO Number</th>
                            <th>Company</th>
                            <th>Contact Person</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Order Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($topPurchaseOrders))
                            @foreach($topPurchaseOrders as $po)
                                <tr style="cursor: pointer"  onclick="window.location='{{ url('/purchase_order/view/' . $po->po_id) }}'">
                                    <td>{{ $po->po_id }}</td>
                                    <td>{{ $po->company_name }}</td>
                                    <td>{{ $po->receiver_name }}</td>
                                    <td>₱{{ number_format($po->grand_total, 2) }}</td>
                                    <td>
                                        <span class="status-badge status-{{ strtolower($po->status) }}">
                                            {{ $po->status }}
                                        </span>
                                    </td>
                                    <td>{{ $po->order_date->format('M d, Y') }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center">No purchase orders found for this period</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            </div>

            @if(isset($POmonthlyData) && $POmonthlyData->count() > 0)
            <p style="margin: 10px; font-size: 17px; font-weight: bold; color: #333; margin-top: 30px;">Purchase Orders Trend</p>
            
            <div class="chart-container">
                <div style="width: 100%; margin: auto; height: 400px;">
                    <canvas id="purchaseOrdersChart"></canvas>
                </div>

                <script>
                    const poCtx = document.getElementById('purchaseOrdersChart').getContext('2d');
                    const poChart = new Chart(poCtx, {
                        type: 'line',
                        data: {
                            labels: [
                                @foreach($POmonthlyData as $month => $data)
                                    "{{ DateTime::createFromFormat('Y-m', $month)->format('M Y') }}",
                                @endforeach
                            ],
                            datasets: [{
                                label: 'Purchase Orders Count',
                                data: [
                                    @foreach($POmonthlyData as $data)
                                        {{ $data['count'] }},
                                    @endforeach
                                ],
                                backgroundColor: 'rgba(255, 222, 89, 0.2)',
                                borderColor: '#ffde59',
                                borderWidth: 2,
                                fill: true
                            }, {
                                label: 'Total Value (₱)',
                                data: [
                                    @foreach($POmonthlyData as $data)
                                        {{ $data['value'] }},
                                    @endforeach
                                ],
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
                </script>
            </div>
            @endif
        </div>

        {{-- Product performance --}}
        <div id="products" class="tab-content {{ request('active_tab') == 'products' ? 'active' : '' }}">
            <h2>Product Performance Reports</h2>
            
            <form method="GET" action="{{ route('reports') }}" id="productsFilterForm">
                <input type="hidden" name="active_tab" value="products">
                <div class="filters-row">
                    <div class="filter-group">
                        <label>Date range</label>
                        <select name="date_range" id="productsDateRange" onchange="toggleCustomFields('products')">
                            <option value="last_7_days" {{ request('date_range') == 'last_7_days' ? 'selected' : '' }}>Last 7 days</option>
                            <option value="last_30_days" {{ request('date_range') == 'last_30_days' || !request('date_range') ? 'selected' : '' }}>Last 30 days</option>
                            <option value="last_3_months" {{ request('date_range') == 'last_3_months' ? 'selected' : '' }}>Last 3 months</option>
                            <option value="custom" {{ request('date_range') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                    </div>
                    <div class="filter-group" id="productsFromDateGroup" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                        <label>From Date</label>
                        <input type="date" name="from_date" value="{{ request('from_date', $startDate->format('Y-m-d')) }}">
                    </div>
                    <div class="filter-group" id="productsToDateGroup" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                        <label>To Date</label>
                        <input type="date" name="to_date" value="{{ request('to_date', $endDate->format('Y-m-d')) }}">
                    </div>
                    
                    <button type="submit" class="apply-filter">Apply Filters</button>
                </div>
            </form>

            <div class="report-actions">
                <a href="{{ route('reports.products', ['type' => 'pdf'] + request()->all()) }}" class="btn">📄 Export PDF</a>
            </div>

            <!-- Display current Date range -->
            <div style="margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 5px; font-size: 14px;">
                <strong>Current Period:</strong> 
                {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ $productsCount }}</div>
                    <div class="stat-label">Total Products</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">₱{{ number_format($inventoryValuation ?? 0, 2) }}</div>
                    <div class="stat-label">Inventory Valuation</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{$lowStock}}</div>
                    <div class="stat-label">Low Stock</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{$outOfStock}}</div>
                    <div class="stat-label">Out Of Stock</div>
                </div>
            </div>

            <p style="margin: 10px; font-size: 17px; font-weight: bold; color: #333; margin-top: 20px;">Monthly Product Revenue</p>
            <div class="chart-container">
                <div style="width: 100%; margin: auto; height: 380px;">
                    <canvas id="productRevenueChart"></canvas>
                </div>
                <script>
                    const prCtx = document.getElementById('productRevenueChart').getContext('2d');
                    const productRevenueChart = new Chart(prCtx, {
                        type: 'line',
                        data: {
                            labels: [
                                @if(isset($productMonthlyLabels))
                                    @foreach($productMonthlyLabels as $label)
                                        "{{ $label }}",
                                    @endforeach
                                @endif
                            ],
                            datasets: [{
                                label: 'Revenue',
                                data: [
                                    @if(isset($productMonthlyValues))
                                        @foreach($productMonthlyValues as $v)
                                            {{ $v }},
                                        @endforeach
                                    @endif
                                ],
                                borderColor: '#86c942',
                                backgroundColor: '#86c94222',
                                borderWidth: 2,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: { beginAtZero: true, ticks: { callback: (v)=>'₱'+v.toLocaleString() } }
                            }
                        }
                    });
                </script>
            </div>

            <p style="margin: 10px; font-size: 17px; font-weight: bold; color: #333; margin-top: 20px;">Top Products by Revenue</p>

            <div class="chart-container" style="overflow-x: auto;">
              <div class="data-table">
                <table class="table">
                    <thead>
                        <tr>
                        <th>Product</th>
                        <th>Total Revenue (₱)</th>
                        <th>Total Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach(($topByRevenue ?? []) as $product)
                        <tr style="cursor: pointer" onclick="window.location='{{ url('/product_view/' . $product->product_id) }}'">
                            <td>{{ $product->product_name }}</td>
                            <td>₱{{ number_format($product->total_revenue, 2) }}</td>
                            <td>x{{ $product->total_quantity }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            </div>

            <p style="margin: 10px; font-size: 17px; font-weight: bold; color: #333; margin-top: 20px;">Top Products by Quantity</p>
            <div class="chart-container" style="overflow-x: auto;">
              <div class="data-table">
                <table class="table">
                    <thead>
                        <tr>
                        <th>Product</th>
                        <th>Total Quantity</th>
                        <th>Total Revenue (₱)</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach(($topByQuantity ?? []) as $product)
                        <tr style="cursor: pointer" onclick="window.location='{{ url('/product_view/' . $product->product_id) }}'">
                            <td>{{ $product->product_name }}</td>
                            <td>x{{ $product->total_quantity }}</td>
                            <td>₱{{ number_format($product->total_revenue, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            </div>

            <p style="margin: 10px; font-size: 17px; font-weight: bold; color: #333; margin-top: 20px;">Slow Movers</p>
            <div class="chart-container" style="overflow-x: auto;">
              <div class="data-table">
                <table class="table">
                    <thead>
                        <tr>
                        <th>Product</th>
                        <th>Total Quantity (range)</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach(($slowMovers ?? []) as $product)
                        <tr style="cursor: pointer" onclick="window.location='{{ url('/product_view/' . $product->product_id) }}'">
                            <td>{{ $product->product_name }}</td>
                            <td>x{{ $product->total_quantity }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            </div>

            <p style="margin: 10px; font-size: 17px; font-weight: bold; color: #333; margin-top: 20px;">Zero Sales Products (in range)</p>
            <div class="chart-container" style="overflow-x: auto;">
              <div class="data-table">
                <table class="table">
                    <thead>
                        <tr>
                        <th>Product</th>
                        <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach(($zeroSalesProducts ?? []) as $product)
                        <tr style="cursor: pointer" onclick="window.location='{{ url('/product_view/' . $product->id) }}'">
                            <td>{{ $product->name }}</td>
                            <td>x{{ $product->quantity }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            </div>
        </div>

        {{-- Receipts tab --}}
        <div id="receipts" class="tab-content {{ request('active_tab') == 'receipts' ? 'active' : '' }}">
            <h2>Receipts</h2>

            <form method="GET" action="{{ route('reports') }}" id="receiptsFilterForm">
                <input type="hidden" name="active_tab" value="receipts">
                <div class="filters-row">
                    <div class="filter-group">
                        <label>Date range</label>
                        <select name="date_range" id="receiptsDateRange" onchange="toggleCustomFields('receipts')">
                            <option value="last_7_days" {{ request('date_range') == 'last_7_days' ? 'selected' : '' }}>Last 7 days</option>
                            <option value="last_30_days" {{ request('date_range') == 'last_30_days' || !request('date_range') ? 'selected' : '' }}>Last 30 days</option>
                            <option value="last_3_months" {{ request('date_range') == 'last_3_months' ? 'selected' : '' }}>Last 3 months</option>
                            <option value="custom" {{ request('date_range') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                    </div>
                    <div class="filter-group" id="receiptsFromDateGroup" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                        <label>From Date</label>
                        <input type="date" name="from_date" value="{{ request('from_date', $startDate->format('Y-m-d')) }}">
                    </div>
                    <div class="filter-group" id="receiptsToDateGroup" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                        <label>To Date</label>
                        <input type="date" name="to_date" value="{{ request('to_date', $endDate->format('Y-m-d')) }}">
                    </div>

                    <button type="submit" class="apply-filter">Apply Filters</button>
                </div>
            </form>

            <div class="report-actions">
                <a href="{{ route('date.search', request()->only(['from_date','to_date'])) }}" class="btn">🔎 View Receipts List</a>
                <a href="{{ route('reports.receipts', ['type' => 'pdf'] + request()->all()) }}" class="btn">📄 Export PDF</a>
            </div>

            <div style="margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 5px; font-size: 14px;">
                <strong>Current Period:</strong>
                {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ $Receiptscount }}</div>
                    <div class="stat-label">
                        @if(request('date_range') == 'last_7_days')
                            Receipts (Last 7 Days)
                        @elseif(request('date_range') == 'last_30_days' || !request('date_range'))
                            Receipts (Last 30 Days)
                        @elseif(request('date_range') == 'last_3_months')
                            Receipts (Last 3 Months)
                        @elseif(request('date_range') == 'custom')
                            Receipts (Custom Range)
                        @else
                            Total Receipts
                        @endif
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $ReceiptspendingCount }}</div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $ReceiptsverifiedCount }}</div>
                    <div class="stat-label">Verified</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $ReceiptscancelledCount }}</div>
                    <div class="stat-label">Cancelled</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $ReceiptsrejectedCount }}</div>
                    <div class="stat-label">Rejected</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">₱{{ number_format($ReceiptsverifiedAmount, 2) }}</div>
                    <div class="stat-label">Verified Amount</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">₱{{ number_format($averageVerifiedReceipt ?? 0, 2) }}</div>
                    <div class="stat-label">Avg. Verified Receipt</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $rejectionRate ?? 0 }}%</div>
                    <div class="stat-label">Rejection Rate</div>
                </div>
            </div>

            <p style="margin: 10px; font-size: 17px; font-weight: bold; color: #333; margin-top: 20px;">Monthly Verified Collections</p>
            <div class="chart-container">
                <div style="width: 100%; margin: auto; height: 360px;">
                    <canvas id="receiptsChart"></canvas>
                </div>
                <script>
                    const rctx = document.getElementById('receiptsChart').getContext('2d');
                    const receiptsChart = new Chart(rctx, {
                        type: 'line',
                        data: {
                            labels: [
                                @if(isset($receiptMonthlyLabels))
                                    @foreach($receiptMonthlyLabels as $label)
                                        "{{ $label }}",
                                    @endforeach
                                @endif
                            ],
                            datasets: [{
                                label: 'Verified Collections',
                                data: [
                                    @if(isset($receiptMonthlyValues))
                                        @foreach($receiptMonthlyValues as $v)
                                            {{ $v }},
                                        @endforeach
                                    @endif
                                ],
                                borderColor: '#4e73df',
                                backgroundColor: '#4e73df22',
                                borderWidth: 2,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: { beginAtZero: true, ticks: { callback: (v)=>'₱'+v.toLocaleString() } }
                            }
                        }
                    });
                </script>
            </div>

            <p style="margin: 10px; font-size: 17px; font-weight: bold; color: #333; margin-top: 20px;">Top Customers by Verified Collections</p>
            <div class="chart-container" style="overflow-x: auto;">
              <div class="data-table">
                <table class="table">
                    <thead>
                        <tr>
                        <th>Customer</th>
                        <th>Collections (₱)</th>
                        <th>Receipts</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach(($topReceiptCustomers ?? []) as $row)
                        <tr style="cursor: pointer" onclick="window.location='{{ url('/customer_view/' . $row->id) }}'">
                            <td>{{ $row->store_name }}</td>
                            <td>₱{{ number_format($row->collected, 2) }}</td>
                            <td>{{ $row->rec_count }}</td>
                        </tr>
                    @endforeach
                    @if(empty($topReceiptCustomers) || count($topReceiptCustomers) === 0)
                        <tr><td colspan="3" class="text-center">No data for this period</td></tr>
                    @endif
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/reports.js') }}"></script>
<script src="{{ asset('js/purchase_order/download_pdf_btn.js') }}"></script>



</body> 
</html>
@endsection