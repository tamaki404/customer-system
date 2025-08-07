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
            <button class="tab-button active" onclick="switchTab('sales')">Sales & Revenue from </button>
            <button class="tab-button" onclick="switchTab('customers')"> Customer Analytics</button>
            <button class="tab-button" onclick="switchTab('orders')"> Order Management</button>
            <button class="tab-button" onclick="switchTab('support')"> Support Tickets</button>
            <button class="tab-button" onclick="switchTab('products')"> Product Performance</button>
            <button class="tab-button" onclick="switchTab('audit')"> Audit & Security</button>
        </nav>

          <!-- Sales & Revenue Tab -->
        <div id="sales" class="tab-content active">
            <h2>Sales & Revenue Reports</h2>
            
                <div class="filters-row">
                    <div class="filter-group">
                        <label>Date Range</label>
                        <select>
                            <option>Last 7 days</option>
                            <option>Last 30 days</option>
                            <option>Last 3 months</option>
                            <option>Custom Range</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>From Date</label>
                        <input type="date" value="2025-01-01">
                    </div>
                    <div class="filter-group">
                        <label>To Date</label>
                        <input type="date" value="2025-08-07">
                    </div>
                    
                    <button class="apply-filter">Apply Filters</button>
                </div>

            <div class="report-actions">
                <button class="btn">📊 Export Excel</button>
                <button class="btn">📄 Export PDF</button>

            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">₱{{ number_format($totalSales, 2) }}</div>
                    <div class="stat-label">Total Revenue</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{$completedOrdersCount}}</div>
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

            <div class="chart-container">
                
            </div>
        </div>

    </div>




</div>


    <script>
        function switchTab(tabId) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => {
                content.classList.remove('active');
            });

            // Remove active class from all tab buttons
            const tabButtons = document.querySelectorAll('.tab-button');
            tabButtons.forEach(button => {
                button.classList.remove('active');
            });

            // Show selected tab content
            document.getElementById(tabId).classList.add('active');

            // Add active class to clicked button
            event.target.classList.add('active');
        }

        // Simulate some interactive functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Add click handlers for export buttons
            const exportButtons = document.querySelectorAll('.btn-success');
            exportButtons.forEach(button => {
                button.addEventListener('click', function() {
                    alert('Export functionality would be implemented here!');
                });
            });

            // Add click handlers for filter buttons
            const filterButtons = document.querySelectorAll('.btn-primary');
            filterButtons.forEach(button => {
                if (button.textContent.includes('Apply Filters')) {
                    button.addEventListener('click', function() {
                        alert('Filters applied! Data would be refreshed here.');
                    });
                }
            });
        });
    </script>

</body> 
</html>


 
@endsection
