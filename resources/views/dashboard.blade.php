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
    <title>Dashboard</title>
</head>
<body>
<script src="{{ asset('js/fadein.js') }}"></script>
<div class="dashBody">

    @if(auth()->user()->user_type === 'Admin')

     <div class="dashGreet">
       <h1>{{ $greeting }}, {{ auth()->user()->name }} 👋</h1>
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
        </a>
    </div>

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
            @php
                $status = strtolower($activity->status ?? 'verified');
                switch ($status) {
                    case 'cancelled':
                        $action = 'cancelled receipt';
                        break;
                    case 'rejected':
                        $action = 'rejected receipt';
                        break;
                    default:
                        $action = 'verified receipt';
                        break;
                }
            @endphp

            <a class="activityCard" style="height: 50px;" href="{{ route('receipt_view', ['receipt_id' => $activity->receipt_id]) }}">
                <span style="font-weight: bold">{{ $activity->verified_by ?? 'System' }} </span> 
                <span>{{ $action }}</span> <span> #{{ $activity->receipt_number }}</span>
                <span style="margin-left: auto; color: #333">
                    {{ \Carbon\Carbon::parse($activity->verified_at ?? $activity->created_at)->format('F j, Y, g:i A') }}
                </span>
            </a>
        @endforeach
        @else
            <div style="color:#888; align-items: center; justify-content: center; display: flex; height:100%;">
                No recent receipt activity today.
            </div>
        @endif
    </div>
    @elseif(auth()->user()->user_type === 'Staff')

    <div class="dashGreet">
       <h1>{{ $greeting }}, {{ auth()->user()->name }} 👋</h1>
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
        <a class="actCard" href="{{ route('receipts') }}" style="border:1px solid rgb(216, 215, 215); border-top-right-radius: 10px; border-bottom-right-radius: 10px;">
            <p class="cardTit">Weekly Receipt Count</p>
            <h1 id="totalReceipts">{{$totalReceipts}}</h1>
            <p class="dayP"> Past 7 Days</p>
        </a>

    </div>

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
            @php
                $status = strtolower($activity->status ?? 'verified');
                switch ($status) {
                    case 'cancelled':
                        $action = 'cancelled receipt';
                        break;
                    case 'rejected':
                        $action = 'rejected receipt';
                        break;
                    default:
                        $action = 'verified receipt';
                        break;
                }
            @endphp

            <a class="activityCard" style="height: 50px;" href="{{ route('receipt_view', ['receipt_id' => $activity->receipt_id]) }}">
                <span style="font-weight: bold">{{ $activity->verified_by ?? 'System' }} </span> 
                <span>{{ $action }}</span> <span> #{{ $activity->receipt_number }}</span>
                <span style="margin-left: auto; color: #333">
                    {{ \Carbon\Carbon::parse($activity->verified_at ?? $activity->created_at)->format('F j, Y, g:i A') }}
                </span>
            </a>
        @endforeach
        @endif
    </div>
    @elseif(auth()->user()->user_type === 'Customer')

    <div class="dashGreet">
       <h1>{{ $greeting }}, {{ auth()->user()->name }} 👋</h1>
        <h4>Here's your dashboard overview.</h4>
    </div>
    <div class="dashFrame" >
        <a class="actCard" href="{{ route('receipts') }}" style="border-top-left-radius: 10px; border-bottom-left-radius: 10px; border: 1px solid rgb(216, 215, 215);">
            <p class="cardTit">Pending Receipts</p>
           <h1 id="pendingDayCount">{{ $userPendingReceipts->count() }}</h1>
            <p class="dayP">On this week</p>
        </a>
        <a class="actCard" href="{{ route('receipts') }}" style="border:1px solid rgb(216, 215, 215);">
            <p class="cardTit">Approved Receipts</p>
         <h1 id="pendingWeekCount">{{ $userApprovedReceipts->count() }}</h1>
              <p class="dayP"> On this week</p>
        </a>        
    </div>


        <h2 style="margin-bottom: 0px;">Week's History</h2>
        <p style="margin: 0; font-size: 14px;">These are your receipt verification updates from staff for the current week.</p>
        <div class="activities" style="height: 400px; overflow-y: scroll;">

        @if(isset($userVerifiedReceiptsWeek) && count($userVerifiedReceiptsWeek))
        @foreach($userVerifiedReceiptsWeek as $activity)
            @php
                $status = strtolower($activity->status ?? 'verified');
                switch ($status) {
                    case 'cancelled':
                        $action = 'cancelled your receipt';
                        break;
                    case 'rejected':
                        $action = 'rejected your receipt';
                        break;
                    default:
                        $action = 'verified your receipt';
                        break;
                }
            @endphp

            <a class="activityCard" style="height: 50px;" href="{{ route('receipt_view', ['receipt_id' => $activity->receipt_id]) }}">
                <span style="font-weight: bold">{{ $activity->verified_by ?? 'System' }} </span> 
                <span>{{ $action }}</span> <span> #{{ $activity->receipt_number }}</span>
                <span style="margin-left: auto; color: #333">
                    {{ \Carbon\Carbon::parse($activity->verified_at ?? $activity->created_at)->format('F j, Y, g:i A') }}
                </span>
            </a>
        @endforeach
    @else
        <div style="color:#888; align-items: center; justify-content: center; display: flex; height:100%;">
            No recent receipt activity this week.
        </div>
    @endif

    </div>

    @endif



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
