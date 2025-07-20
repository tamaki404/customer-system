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


    <div class="header">
        <h1>Dashboard</h1>
    </div>

    <div class="content">
        <div class="nameCard">
            <h2>Welcome, {{ auth()->user()->username }}!</h2>
            <p>Here is your dashboard overview.</p>
        </div>
        <div class="cardFrame">
            <div class="card">
                <h3>Active users</h3>
                <h1>6535</h1>
            </div>
            <div class="card">
                <h3>Open Tickets</h3>
                <h1>3585</h1>
            </div>
            <div class="card">
                <h3>Reports Today</h3>
                <h1>25</h1>

            </div>
        </div>
        <div class="activityCard">
            <h2>Recent Activities</h2>
            <div class="activityList">
                <div class="activityItem">
                    <p>User <strong>John Doe</strong> created a new ticket.</p>
                </div>
                <div class="activityItem">
                    <p>User <strong>Jane Smith</strong> updated their profile.</p>
                </div>
                <div class="activityItem">
                    <p>User <strong>Mark Johnson</strong> closed a ticket.</p>
                </div>
            </div>

        </div>




</div>
</body>
</html>


 
@endsection
