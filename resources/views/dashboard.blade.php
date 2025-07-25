@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

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

    <h2>Projects you're working on</h2>
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

    </div>

    <h2>Recent Activities</h2>
    <div class="actFrame">

    </div>

</div>

<script>
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
