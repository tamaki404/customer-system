@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/reporting.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fadein.css') }}">
    <title>Reports</title>
</head>
<body>
<script src="{{ asset('js/fadein.js') }}"></script>
<div class="startBody">
    <div class="reportsHeader">
        <h2>📊 Reports Dashboard</h2>
        <p>Generate and download comprehensive reports for your customer system</p>
    </div>
    <div class="report-container">
        <nav class="tab-navigation">
            <button class="tab-button active" onclick="switchTab('sales')">Sales & Revenue</button>
            <button class="tab-button" onclick="switchTab('customers')">Customer Analytics</button>
            <button class="tab-button" onclick="switchTab('orders')">Order Management</button>
            <button class="tab-button" onclick="switchTab('support')">Support Tickets</button>
            <button class="tab-button" onclick="switchTab('products')">Product Performance</button>
            <button class="tab-button" onclick="switchTab('audit')">Audit & Security</button>
        </nav>

        {{-- Sales & Revenue Tab --}}
        <div id="sales" class="tab-content active">
            <h2>Sales & Revenue Reports</h2>
            
            <form method="GET" action="{{ route('reports') }}" id="filterForm">
                <div class="filters-row">
                    <div class="filter-group">
                        <label>Date Range</label>
                        <select name="date_range" id="dateRange" onchange="toggleCustomFields()">
                            <option value="last_7_days" {{ request('date_range') == 'last_7_days' ? 'selected' : '' }}>Last 7 days</option>
                            <option value="last_30_days" {{ request('date_range') == 'last_30_days' || !request('date_range') ? 'selected' : '' }}>Last 30 days</option>
                            <option value="last_3_months" {{ request('date_range') == 'last_3_months' ? 'selected' : '' }}>Last 3 months</option>
                            <option value="custom" {{ request('date_range') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                    </div>
                    <div class="filter-group" id="fromDateGroup" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                        <label>From Date</label>
                        <input type="date" name="from_date" value="{{ request('from_date', $startDate->format('Y-m-d')) }}">
                    </div>
                    <div class="filter-group" id="toDateGroup" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                        <label>To Date</label>
                        <input type="date" name="to_date" value="{{ request('to_date', $endDate->format('Y-m-d')) }}">
                    </div>
                    
                    <button type="submit" class="apply-filter">Apply Filters</button>
                </div>
            </form>

            <div class="report-actions">
                <a href="{{ route('reports.export', ['type' => 'excel'] + request()->all()) }}" class="btn">📊 Export Excel</a>
                <a href="{{ route('reports.export', ['type' => 'pdf'] + request()->all()) }}" class="btn">📄 Export PDF</a>
            </div>

            <!-- Display current date range -->
            <div style="margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 5px; font-size: 14px;">
                <strong>Current Period:</strong> 
                {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">₱{{ number_format($totalSales, 2) }}</div>
                    <div class="stat-label">Total Revenue</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $completedOrdersCount }}</div>
                    <div class="stat-label">Orders Completed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">₱{{ number_format($averageOrderValue, 2) }}</div>
                    <div class="stat-label">Average Order Value</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $paymentSuccessRate }}%</div>
                    <div class="stat-label">Payment Success Rate</div>
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
                                @foreach($monthlySales as $month => $total)
                                    "{{ DateTime::createFromFormat('!m', $month)->format('F') }}",
                                @endforeach
                            ],
                            datasets: [{
                                label: 'Monthly Revenue',
                                data: [
                                    @foreach($monthlySales as $total)
                                        {{ $total }},
                                    @endforeach
                                ],
                                backgroundColor: '#ffde59',
                                borderColor: '#e5c43f',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
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
            </div>
        </div>
        {{-- Customer Analytics tab --}}
        <div id="customers" class="tab-content active">
            <h2>Customer Analytics</h2>
            
            <form method="GET" action="{{ route('reports') }}" id="filterForm">
                <div class="filters-row">
                    <div class="filter-group">
                        <label>Date Range</label>
                        <select name="date_range" id="dateRange" onchange="toggleCustomFields()">
                            <option value="last_7_days" {{ request('date_range') == 'last_7_days' ? 'selected' : '' }}>Last 7 days</option>
                            <option value="last_30_days" {{ request('date_range') == 'last_30_days' || !request('date_range') ? 'selected' : '' }}>Last 30 days</option>
                            <option value="last_3_months" {{ request('date_range') == 'last_3_months' ? 'selected' : '' }}>Last 3 months</option>
                            <option value="custom" {{ request('date_range') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                    </div>
                    <div class="filter-group" id="fromDateGroup" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                        <label>From Date</label>
                        <input type="date" name="from_date" value="{{ request('from_date', $startDate->format('Y-m-d')) }}">
                    </div>
                    <div class="filter-group" id="toDateGroup" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                        <label>To Date</label>
                        <input type="date" name="to_date" value="{{ request('to_date', $endDate->format('Y-m-d')) }}">
                    </div>
                    
                    <button type="submit" class="apply-filter">Apply Filters</button>
                </div>
            </form>

            <div class="report-actions">
                <a href="{{ route('reports.export', ['type' => 'excel'] + request()->all()) }}" class="btn">📊 Export Excel</a>
                <a href="{{ route('reports.customers', ['type' => 'pdf'] + request()->all()) }}" class="btn">📄 Export PDF</a>
            </div>

            <!-- Display current date range -->
            <div style="margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 5px; font-size: 14px;">
                <strong>Current Period:</strong> 
                {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ $totalUsers }}</div>
                    <div class="stat-label">Total Customers</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $newThisMonth }}</div>
                    <div class="stat-label">New this month</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{$pendingUsers}}</div>
                    <div class="stat-label">Accepted users</div>
                </div>
              
            </div>

            <p style="margin: 10px; font-size: 17px; font-weight: bold; color: #333; margin-top: 20px;">Revenue Trend Chart</p>

            <div class="chart-container" style="overflow-x: auto;">
              <div class="data-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Telephone</th>
                            <th>Location</th>
                            <th>Representative</th>
                            <th>Registration Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                            @foreach ($customers as $customer)
                                <tr>
                                    <td>{{ $customer->store_name }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->mobile}}</td>
                                    <td>{{$customer->telephone}}</td>
                                    <td>{{$customer->address}}</td>
                                    <td>{{$customer->name}}</td>
                                    <td>{{ $customer->created_at->format('F Y') }}</td>
                                    <td>{{ $customer->acc_status }}</td>
                                </tr>
                            @endforeach


                    </tbody>
                </table>
            </div>

            </div>
        </div>
        {{-- order management tab --}}
        <div id="orders" class="tab-content active">
            <h2>Order Management Reports</h2>
            
            <form method="GET" action="{{ route('reports') }}" id="filterForm">
                <div class="filters-row">
                    <div class="filter-group">
                        <label>Date Range</label>
                        <select name="date_range" id="dateRange" onchange="toggleCustomFields()">
                            <option value="last_7_days" {{ request('date_range') == 'last_7_days' ? 'selected' : '' }}>Last 7 days</option>
                            <option value="last_30_days" {{ request('date_range') == 'last_30_days' || !request('date_range') ? 'selected' : '' }}>Last 30 days</option>
                            <option value="last_3_months" {{ request('date_range') == 'last_3_months' ? 'selected' : '' }}>Last 3 months</option>
                            <option value="custom" {{ request('date_range') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                    </div>
                    <div class="filter-group" id="fromDateGroup" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                        <label>From Date</label>
                        <input type="date" name="from_date" value="{{ request('from_date', $startDate->format('Y-m-d')) }}">
                    </div>
                    <div class="filter-group" id="toDateGroup" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                        <label>To Date</label>
                        <input type="date" name="to_date" value="{{ request('to_date', $endDate->format('Y-m-d')) }}">
                    </div>
                    
                    <button type="submit" class="apply-filter">Apply Filters</button>
                </div>
            </form>

            <div class="report-actions">
                <a href="{{ route('reports.export', ['type' => 'excel'] + request()->all()) }}" class="btn">📊 Export Excel</a>
                <a href="{{ route('reports.customers', ['type' => 'pdf'] + request()->all()) }}" class="btn">📄 Export PDF</a>
            </div>

            <!-- Display current date range -->
            <div style="margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 5px; font-size: 14px;">
                <strong>Current Period:</strong> 
                {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ $ordersCount }}</div>
                    <div class="stat-label">Total Orders</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $completedOrders }}</div>
                    <div class="stat-label">Completed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{$processingOrders}}</div>
                    <div class="stat-label">Processing</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{$pendingOrders}}</div>
                    <div class="stat-label">Pending</div>
                </div>
                 <div class="stat-card">
                    <div class="stat-value">{{$cancelledOrders}}</div>
                    <div class="stat-label">Cancelled</div>
                </div>     
                <div class="stat-card">
                    <div class="stat-value">{{$rejectedOrders}}</div>
                    <div class="stat-label">Rejected</div>
                </div>          
            </div>

            <p style="margin: 10px; font-size: 17px; font-weight: bold; color: #333; margin-top: 20px;">Best Selling Products</p>

            <div class="chart-container" style="overflow-x: auto;">


              <div class="data-table">
                <table class="table">
                    <thead>
                        <tr>
                        <th>Product Name</th>
                        <th>Total Quantity Sold</th>
                        <th>Total Revenue (₱)</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($bestSellingProducts as $product)
                        <tr>
                            <td>{{ $product->product_name }}</td>
                            <td>x{{ $product->total_quantity }}</td>
                            <td>₱{{ number_format($product->total_revenue, 2) }}</td>
                        </tr>
                    @endforeach


                    </tbody>
                </table>
            </div>


            </div>
        </div>














    </div>
</div>

<script>
    function switchTab(tabId) {
        const tabContents = document.querySelectorAll('.tab-content');
        tabContents.forEach(content => {
            content.classList.remove('active');
        });

        const tabButtons = document.querySelectorAll('.tab-button');
        tabButtons.forEach(button => {
            button.classList.remove('active');
        });

        document.getElementById(tabId).classList.add('active');
        event.target.classList.add('active');
    }

    function toggleCustomFields() {
        const dateRange = document.getElementById('dateRange').value;
        const fromDateGroup = document.getElementById('fromDateGroup');
        const toDateGroup = document.getElementById('toDateGroup');
        
        if (dateRange === 'custom') {
            fromDateGroup.style.display = 'block';
            toDateGroup.style.display = 'block';
        } else {
            fromDateGroup.style.display = 'none';
            toDateGroup.style.display = 'none';
        }
    }

    // Auto-submit form when predefined date range is selected
    document.getElementById('dateRange').addEventListener('change', function() {
        if (this.value !== 'custom') {
            document.getElementById('filterForm').submit();
        }
    });
</script>

</body> 
</html>
@endsection