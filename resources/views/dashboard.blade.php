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
       <h1>{{ $greeting }}, {{ auth()->user()->username }} 👋</h1>
        <h4>Here's your dashboard overview.</h4>
    </div>
    <div class="dashFrame" >
        <a class="actCard" href="{{ route('receipts') }}" style="border-top-left-radius: 10px; border-bottom-left-radius: 10px; border: 1px solid rgb(216, 215, 215);">
            <p class="cardTit">Pending Receipts</p>
            <h1 id="pendingDayCount">{{$pendingDayCount}}</h1>
            <p class="dayP">On this day</p>
        </a>
        <a class="actCard" href="{{ route('receipts') }}" style="border:1px solid rgb(216, 215, 215);">
            <p class="cardTit">Accomplished Receipts</p>
            <h1 id="pendingWeekCount">{{$pendingWeekCount}}</h1>
            <p class="dayP"> Past 7 Days</p>
        </a>        
        <a class="actCard" href="{{ route('receipts') }}" style="border:1px solid rgb(216, 215, 215);">
            <p class="cardTit">Weekly Receipt Count</p>
            <h1 id="totalReceipts">{{$totalReceipts}}</h1>
            <p class="dayP"> Past 7 Days</p>
        </a>
        {{-- <a class="actCard" href="{{ route('receipts') }}" style="border-top-right-radius: 10px; border-bottom-right-radius: 10px; border: 1px solid rgb(216, 215, 215);">
            <p class="cardTit">Received amount</p>
            <h1 id="monthlyTotal" style="color:green">{{ $monthlyTotal }}</h1>
            <p class="dayP">On this day </p>
        </a>     --}}
        <a class="actCard" href="{{ route('customers') }}" style="border-top-right-radius: 10px; border-bottom-right-radius: 10px; border: 1px solid rgb(216, 215, 215);">
            <p class="cardTit">Pending Joins</p>
            <h1 id="pendingJoins" style="color:green">{{ $pendingJoins }}</h1>
            {{-- <p class="dayP">On this day </p> --}}
        </a>
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

     <div class="graphFrame">

        <h2>Top Stores on this week</h2>
        <div class="dashFrame" style="position:relative; height:350px; min-height:200px;">
            <canvas id="topStoresChart" style="width:100%;height:100%;"></canvas>
        </div>
     </div>

    <h2>Today's Verified Receipts</h2>
    <div class="activities">

        @if(isset($verifiedReceiptsToday) && count($verifiedReceiptsToday))
                @foreach($verifiedReceiptsToday as $activity)
                    <a class="activityCard" href="{{ route('receipt_view', ['receipt_id' => $activity->receipt_id]) }}">
                        <span style="font-weight: bold">{{ $activity->verified_by }} </span> 
                        <span> verified receipt </span> <span>{{ $activity->receipt_number }}</span>
                        <span style="margin-left: auto; font-weight: bold;">{{ \Carbon\Carbon::parse($activity->verified_at)->format('h:i A') }}</span>
                    </a>
                @endforeach
        @else
            <div style="color:#888;">No receipts verified today.</div>
        @endif
    </div>

    

    {{-- <h2>Recent Activities</h2>
    <div class="actFrame">

</div> --}}

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Format numbers for dashboard cards
    function formatPesoShort(amount) {
        const absAmount = Math.abs(amount);
        let formatted;
        if (absAmount >= 1_000_000_000) {
            formatted = (amount / 1_000_000_000).toFixed(2) + 'B';
        } else if (absAmount >= 1_000_000) {
            formatted = (amount / 1_000_000).toFixed(2) + 'M';
        } else if (absAmount >= 1_000) {
            formatted = (amount / 1_000).toFixed(2) + 'K';
        } else {
            formatted = Number(amount).toFixed(2);
        }
        return formatted;
    }

    // Use count formatting for counts (not money)
    function formatCountShort(amount) {
        const absAmount = Math.abs(amount);
        let formatted;
        if (absAmount >= 1_000_000_000) {
            formatted = (amount / 1_000_000_000).toFixed(2).replace(/\.00$/, '') + 'B';
        } else if (absAmount >= 1_000_000) {
            formatted = (amount / 1_000_000).toFixed(2).replace(/\.00$/, '') + 'M';
        } else if (absAmount >= 1_000) {
            formatted = (amount / 1_000).toFixed(1).replace(/\.0$/, '') + 'K';
        } else {
            formatted = Math.floor(amount).toString();
        }
        return formatted;
    }

    const pendingDayCountEl = document.getElementById('pendingDayCount');
    if (pendingDayCountEl) {
        pendingDayCountEl.textContent = formatCountShort(Number(pendingDayCountEl.textContent));
    }
    const pendingWeekCountEl = document.getElementById('pendingWeekCount');
    if (pendingWeekCountEl) {
        pendingWeekCountEl.textContent = formatCountShort(Number(pendingWeekCountEl.textContent));
    }
    const totalReceiptsEl = document.getElementById('totalReceipts');
    if (totalReceiptsEl) {
        totalReceiptsEl.textContent = formatCountShort(Number(totalReceiptsEl.textContent));
    }
    const pendingJoinsEl = document.getElementById('pendingJoins');
    if (pendingJoinsEl) {
        pendingJoinsEl.textContent = formatCountShort(Number(pendingJoinsEl.textContent));
    }

    const topStores = @json($topStores ?? []);
    // Shorten label with ellipsis if over 15 chars, but keep full name for tooltip
    const storeLabels = topStores.map(s => s.name.length > 15 ? s.name.slice(0, 15) + '…' : s.name);
    const storeSales = topStores.map(s => s.sales);
    const fullStoreNames = topStores.map(s => s.name);
    const ctx = document.getElementById('topStoresChart').getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: storeLabels,
                datasets: [{
                    label: 'Sales',
                    data: storeSales,
                    backgroundColor: 'RGB(255, 222, 89)',
                    borderColor: 'RGB(212, 183, 65)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
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
