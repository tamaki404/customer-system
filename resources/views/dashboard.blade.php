@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Dashboard</title>
</head>
<body>

<div class="dashBody">
    <div class="dashGreet">
        <h1>Goodmorning, {{ auth()->user()->username }} 👋</h1>
        <h4>Here's your dashboard overview.</h4>
    </div>
    <div class="dashFrame" style="width: 60%;">
        <div class="actCard">
            <p class="cardTit">Pending Receipts</p>
            <h1>{{$pendingDayCount}}</h1>
            <p>On this day</p>
        </div>
        <div class="actCard">
            <p class="cardTit">Accomplished Receipts</p>
            <h1>{{$pendingWeekCount}}</h1>
            <p>In last 7 days</p>
        </div>        

    </div>

    

    {{-- <h2>Projects you're working on</h2>
    <div class="dashFrame">
        <div class="card">
            <p>Sales from accepted receipts</p>
            <h1 id="monthlyTotal" style="color:green">{{ $monthlyTotal }}</h1>
            <p class="pSmall">On this day</p>
        </div>     
        <div class="card">
            <p>Total Receipts</p>
            <h1>{{$totalReceipts}}</h1>
            <p class="pSmall">In last 7 days</p>
        </div>
   
        <div class="card">
            <p>Pending Joins</p>
            <h1>{{$pendingJoins}}</h1>
            <a href="">View Customers</a>
        </div>
        <div class="card">
            <p>Active users</p>
            <h1>25</h1>
            <a href="">View Customers</a>
        </div>

    </div> --}}

    <h2>Recent Activities</h2>

    <h2>Top Stores</h2>
    <div class="dashFrame">
        <canvas id="topStoresChart" height="100"></canvas>
    </div>
    <p>This week</p>
    

    {{-- <h2>Recent Activities</h2>
    <div class="actFrame">

</div> --}}

<script>
document.addEventListener('DOMContentLoaded', function() {
    const topStores = @json($topStores ?? []);
    // Shorten label with ellipsis if over 15 chars, but keep full name for tooltip
    const storeLabels = topStores.map(s => s.name.length > 15 ? s.name.slice(0, 15) + '…' : s.name);
    const storeSales = topStores.map(s => s.sales);
    const fullStoreNames = topStores.map(s => s.name);
    const ctx = document.getElementById('topStoresChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: storeLabels,
                datasets: [{
                    label: 'Sales',
                    data: storeSales,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: { display: true, text: 'Top Stores by Sales' },
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                // Show full store name in tooltip
                                const idx = context[0].dataIndex;
                                return fullStoreNames[idx];
                            }
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
});
function formatPesoShort(amount) {
    const absAmount = Math.abs(amount);
    let formatted;

    if (absAmount >= 1_000_000_000) {
        formatted = '₱' + (amount / 1_000_000_000).toFixed(2) + 'B';
    } else if (absAmount >= 1_000_000) {
        formatted = '₱' + (amount / 1_000_000).toFixed(2) + 'M';
    } else if (absAmount >= 1_000) {
        formatted = '₱' + (amount / 1_000).toFixed(2) + 'K';
    } else {
        formatted = '₱' + Number(amount).toFixed(2);
    }

    return formatted;
}

document.addEventListener('DOMContentLoaded', function() {
    const el = document.getElementById('monthlyTotal');
    if (el) {
        el.textContent = formatPesoShort(Number(el.textContent));
    }
});
</script>



</body> 
</html>


 
@endsection
