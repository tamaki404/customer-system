
@extends('layouts.main')

@section('content')


   <div class="content-bg" >
        <div class="content-header">
            <div class="contents-display">
               
            </div>

            <div class="title-actions">
                <p class="heading">Dashboard</p>

              

            </div>


        </div>

    <div class="content-body" style="padding: 10px; border: none; height: auto; display: flex; flex-direction: row; gap: 5px">

        @if(auth()->user()->role === 'Supplier')

            <div class="card" style="flex: 1; min-width: 220px; background: #f7f7fa; padding: 20px; border-radius: 10px;">
                <p style="font-size: 2em;">{{ $totalOrders }}</p>
                <p>Total Orders</p>
            </div>
            <div class="card" style="flex: 1; min-width: 220px; background: #f7f7fa; padding: 20px; border-radius: 10px;">
                <p style="font-size: 2em;">{{ $pendingOrders }}</p>
                <p>Pending Orders</p>
            </div>
            <div class="card" style="flex: 1; min-width: 220px; background: #f7f7fa; padding: 20px; border-radius: 10px;">
                <p style="font-size: 2em;">{{ $totalReceipts }}</p>
                <p>Total Receipts</p>
            </div>
            <div class="card" style="flex: 1; min-width: 220px; background: #f7f7fa; padding: 20px; border-radius: 10px;">
                @if($usedCredit)
            <p style="font-size: 2em;">₱{{ number_format($usedCredit, 2) }}</p>
        @endif

                <p>Outstanding Balance</p>
            </div>

        {{-- <div class="dashboard-activity" style="margin-top: 40px;">
            <h3>Recent Activity</h3>
            <table style="width:100%; border-collapse:collapse; background: #fff; border-radius: 10px;">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentActivities as $activity)
                    <tr>
                        <td>{{ $activity->created_at->format('F j, Y') }}</td>
                        <td>{{ $activity->type }}</td>
                        <td>{{ $activity->description }}</td>
                        <td>{{ $activity->status }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div> --}}

            @elseif(auth()->user()->role === 'Staff' || auth()->user()->role === 'Admin')
                <div class="card" style="flex: 1; min-width: 220px; background: #f7f7fa; padding: 20px; border-radius: 10px;">
                    <p style="font-size: 2em;">{{ $totalOrders }}</p>
                    <p>Total Orders</p>
                </div>
                <div class="card" style="flex: 1; min-width: 220px; background: #f7f7fa; padding: 20px; border-radius: 10px;">
                    <p style="font-size: 2em;">{{ $pendingOrders }}</p>
                    <p>Pending Orders</p>
                </div>
                <div class="card" style="flex: 1; min-width: 220px; background: #f7f7fa; padding: 20px; border-radius: 10px;">
                    <p style="font-size: 2em;">{{ $totalReceipts }}</p>
                    <p>Total Receipts</p>
                </div>
         
            @endif
    </div>

         
       


   </div>


@endsection
