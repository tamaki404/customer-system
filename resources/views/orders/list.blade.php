@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{asset('css/staffs/list.css')}}">
@endpush

@section('content')



        <div class="content-bg">
                <div class="content-header">
                    <div class="contents-display">
                        <form action="{{ route('products.list') }}" id="text-search" class="search-text-con" method="GET">
                            <input type="text" name="search" class="search-bar"
                                placeholder="Search by SUP ID. , Supplier, Representative and status"
                                value="{{ request('search') }}"
                                style="outline:none;"
                            >
                            <button type="submit" class="search-btn"><span class="material-symbols-outlined">search</span></button>
                        </form>


                        <form action="{{ route('products.list') }}" class="date-search" id="from-to-date" method="GET">
                            <p>Date range</p>
                            <div class="from-to-picker">
                                <div class="month-div">
                                    <span>From</span>
                                    <input type="date" name="from_date" class="input-date"
                                        value="{{ request('from_date', now()->startOfMonth()->format('Y-m-d')) }}"
                                        onchange="this.form.submit()">
                                </div>
                                <div class="month-div">
                                    <span>To</span>
                                    <input type="date" name="to_date" class="input-date"
                                        value="{{ request('to_date', now()->endOfMonth()->format('Y-m-d')) }}"
                                        onchange="this.form.submit()">
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="title-actions">
                        <p class="heading">Deliveries sumamry</p>
                
                    </div>

                </div>


                @if (auth()->user()->role !== 'Supplier')
                    <div class="content-body order-list" style="border: none">
{{-- 
                        <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Supplier</th>
                                    <th>Order ID</th>
                                    <th>Heads/Kilos</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                    <th>Running balance</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>                                
                                @foreach ($orders as $order)
                                    @php
                                        $totalDeliveries = count($order->deliveries);
                                        $deliveredCount = 0;
                                        $scheduledCount = 0;
                                        $cancelledCount = 0;

                                        foreach ($order->deliveries as $delivery) {
                                            if ($delivery->status === 'Delivered') {
                                                $deliveredCount++;
                                            } elseif ($delivery->status === 'Scheduled') {
                                                $scheduledCount++;
                                            } elseif ($delivery->status === 'Cancelled') {
                                                $cancelledCount++;
                                            }
                                        }

                                        $deliveryRatio = $totalDeliveries > 0 ? "{$deliveredCount}/{$totalDeliveries}" : "0/0";

                                        if ($totalDeliveries === 0) {
                                            $deliveryStatus = 'No Delivery';
                                        } elseif ($deliveredCount === $totalDeliveries) {
                                            $deliveryStatus = 'Completed';
                                        } elseif ($deliveredCount > 0 && $scheduledCount > 0) {
                                            $deliveryStatus = 'Partially Completed';
                                        } elseif ($deliveredCount === 0 && $scheduledCount > 0) {
                                            $deliveryStatus = 'Scheduled';
                                        } else {
                                            $deliveryStatus = 'Mixed';
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{ $order->created_at->format('F j, Y') }}</td>
                                        <td>{{$order->supplier->company_name}}</td>
                                        <td>{{$order->order_id}}</td>
                                        <td>
                                            @if ($order->all_scheduled || $order->deliveries->isEmpty())
                                                —
                                            @else
                                                <div class="dropdown">
                                                    <button class="btn btn-sm dropdown-toggle" type="button" style="border: 1px solid #333" data-bs-toggle="dropdown">
                                                        {{ $order->running_balance[0]['heads'] }} heads / {{ $order->running_balance[0]['kilos'] }} kg
                                                    </button>
                                                    <ul class="dropdown-menu p-2" style="min-width: auto;">
                                                        @foreach ($order->running_balance as $balance)
                                                            <li>
                                                                {{ $balance['heads'] }} heads / {{ $balance['kilos'] }} kg
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                <style>
                                                    .dropdown-menu li{
                                                        font-size: 14px;
                                                        padding: 2px;
                                                    }
                                                </style>
                                            @endif
                                        </td>



                                        <td>₱{{ number_format($order->total_amount, 2) }}</td>
                                        <td>{{$order->payment_status}}</td>
                                        <td>₱{{ number_format($order->total_amount, 2) }}</td>

                                        <td>
                                            @if ($deliveryRatio !== '0/0')
                                                {{ $deliveryStatus }} ({{ $deliveryRatio }})
                                            @else

                                                No deliveries set yet
                                            @endif
                                        </td>    
                                        
                                        <td><button onclick="window.location.href='{{ route('orders.order', ['order_id' => $order->order_id]) }}'">View</button></td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table> --}}

                        @foreach ($orders as $order)
                            <a class="order-box" href="{{ route('orders.order', ['order_id' => $order->order_id]) }}">
                                @php
                                    // Supplier image setup
                                    $imgSrc =  $order->supplier->user->image 
                                        ? ('data:' . $order->supplier->user->image_mime_type . ';base64,' . base64_encode($order->supplier->user->image))
                                        : asset('images/default-avatar.png');

                                    // Initialize counters
                                    $totalDeliveries = count($order->deliveries);
                                    $deliveredCount = 0;
                                    $scheduledCount = 0;
                                    $cancelledCount = 0;

                                    // Variables to handle "today" and color logic
                                    $isToday = false;
                                    $statusColor = 'black';
                                    $displayStatus = '';

                                    foreach ($order->deliveries as $delivery) {
                                        // Count delivery statuses
                                        if ($delivery->status === 'Delivered') {
                                            $deliveredCount++;
                                        } elseif ($delivery->status === 'Scheduled') {
                                            $scheduledCount++;
                                        } elseif ($delivery->status === 'Cancelled') {
                                            $cancelledCount++;
                                        }

                                        // Apply the color logic from your first block
                                        $color = match($delivery->status) {
                                            'Completed', 'Delivered' => 'green',
                                            'Scheduled' => 'orange',
                                            'In Transit' => 'blue',
                                            'Cancelled' => 'gray',
                                            default => 'black',
                                        };

                                        // Check if this delivery is today
                                        $isToday = \Carbon\Carbon::parse($delivery->delivery_date)->isToday();

                                        // Determine display text
                                        if ($isToday && $delivery->status === 'Scheduled') {
                                            $displayStatus = '<strong style="color: green;">Delivery Today</strong>';
                                            $statusColor = 'green';
                                        } else {
                                            $displayStatus = $delivery->status;
                                            $statusColor = $color;
                                        }
                                    }

                                    // Delivery ratio and summary status
                                    $deliveryRatio = $totalDeliveries > 0 ? "{$deliveredCount}/{$totalDeliveries}" : "0/0";

                                    if ($totalDeliveries === 0) {
                                        $deliveryStatus = 'No Delivery';
                                    } elseif ($deliveredCount === $totalDeliveries) {
                                        $deliveryStatus = 'Done';
                                    } elseif ($deliveredCount > 0 && $scheduledCount > 0) {
                                        $deliveryStatus = 'Partially';
                                    } elseif ($deliveredCount === 0 && $scheduledCount > 0) {
                                        $deliveryStatus = 'Scheduled';
                                    } else {
                                        $deliveryStatus = 'Mixed';
                                    }
                                @endphp
                                <div class="header">
                                    <p>
                                        <span>{{ $order->order_id }}</span>
                                        <span class="material-symbols-outlined" style="font-size: 15px">
                                        content_copy
                                        </span>                                
                                    </p>
                                    @if ($order->all_scheduled || $order->deliveries->isEmpty())
                                        
                                    @else
                                        <div class="dropdown">
                                            <button class="btn btn-sm dropdown-toggle scale" type="button" style="" data-bs-toggle="dropdown">
                                                {{-- {{ $order->running_balance[0]['heads'] }} heads / {{ $order->running_balance[0]['kilos'] }} kg --}} 
                                                <span class="material-symbols-outlined">
                                                    scale
                                                </span>
                                            </button>
                                            <ul class="dropdown-menu p-2" style="min-width: 200px;">
                                                @foreach ($order->running_balance as $balance)
                                                    <li style="font-size: 13px">
                                                        {{ $balance['heads'] }} heads / {{ $balance['kilos'] }} kg
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                  
                                    @endif
                                </div>


                                <div class="supplier">
                                    <div class="profile">
                                        <img class="supplier-image" src="{{ $imgSrc }}" alt="Profile Image">
                                        <p>{{ $order->supplier->company_name ?? 'N/A' }}</p>
                                    </div>

                                    <p class="status">
                                        @if ($deliveryRatio !== '0/0')
                                            {{ $deliveryStatus }} ({{ $deliveryRatio }})
                                        @else
                                           
                                        @endif
                                    </p>


                                </div>
                                @if ($deliveryRatio !== '0/0')

                                    <div class="deliveries">
                                        
                                        @foreach ($order->deliveries as $del)


                                            <div class="delivery-box" style="gap: 5px">
                                                @php
                                                    $isToday = \Carbon\Carbon::parse($del->delivery_date)->isToday();
                                                    $color = match($del->status) {
                                                        'Completed', 'Delivered' => 'green',
                                                        'Scheduled' => 'orange',
                                                        'In Transit' => 'blue',
                                                        'Cancelled' => 'gray',
                                                        default => 'black',
                                                    };
                                                @endphp
                                                <p>
                                                    <span>{{ $del->delivery_date->format('F j') }}</span>

                                                    <span style="color: {{ $color }}; font-size: 12px" >
                                                        @if($isToday && $del->status === 'Scheduled')
                                                            <strong style="color: green; font-size: 12px">Today</strong>
                                                        @else
                                                                <strong>{{ $del->status }}</strong>
                                                    
                                                        @endif
                                                    </span>
                                                </p>

                                                <div class="headKilos">
                                                    @php
                                                        $plannedHeads = $del->deliveryItems->sum('planned_heads');
                                                        $plannedKilos = $del->deliveryItems->sum('planned_kilos');
                                                    @endphp
                                                    <div style="display: flex; gap: 5px;" class="heads-kilos">
                                                        @if ($plannedHeads > 0 && $plannedKilos > 0)
                                                            <p>
                                                                <span class="material-symbols-outlined">
                                                                egg
                                                                </span>      
                                                                <span style="font-weight: bold">{{$plannedHeads}}</span>                                          
                                                            </p>
                                                            -
                                                            <p>
                                                                <span class="material-symbols-outlined">
                                                                weight
                                                                </span>      
                                                                <span style="font-weight: bold">{{$plannedKilos}} kg</span>                                          
                                                            </p>

                                                        @elseif ($plannedHeads > 0)
                                                            <p>
                                                                <span class="material-symbols-outlined">
                                                                egg
                                                                </span>      
                                                                <span style="font-weight: bold">{{$plannedHeads}}</span>                                          
                                                            </p>
                                                        @elseif ($plannedKilos > 0)
                                                            <p>
                                                                <span class="material-symbols-outlined">
                                                                weight
                                                                </span>      
                                                                <span style="font-weight: bold">{{$plannedKilos}} kg</span>                                          
                                                            </p>
                                                        @else
                                                            —
                                                        @endif
                                                        {{-- RECEIVED --}}
                                                        @php
                                                            $receivedHeads = $del->deliveryItems->sum('received_heads');
                                                            $receivedKilos = $del->deliveryItems->sum('received_kilos');
                                                            $hasReceived = $receivedHeads > 0 || $receivedKilos > 0;
                                                        @endphp
                                                    </div>

                                                    {{-- <td>
                                                            @if ($hasReceived)
                                                                @if ($receivedHeads > 0 && $receivedKilos > 0)
                                                                    {{ $receivedHeads }} heads<br>{{ $receivedKilos }} kg
                                                                @elseif ($receivedHeads > 0)
                                                                    {{ $receivedHeads }} heads
                                                                @elseif ($receivedKilos > 0)
                                                                    {{ $receivedKilos }} kg
                                                                @endif
                                                            @else
                                                                —
                                                            @endif
                                                        </td> --}}
                                                            @if ($del->status === "Delivered")
                                                                @php
                                                                    $varianceHeads = $del->deliveryItems->sum('variance_heads');
                                                                    $varianceKilos = $del->deliveryItems->sum('variance_kilos');
                                                                    $hasVariance = $varianceHeads != 0 || $varianceKilos != 0;
                                                                @endphp
                                                                    @if ($hasReceived && $hasVariance)
                                                                    <p class="variance-display" style="gap: 5px">
                                                                        @if ($varianceHeads != 0 && $varianceKilos != 0)
                                                                            <span  style="color: {{ $varianceHeads == 0 ? 'green' : 'red' }};">
                                                                                {{ $varianceHeads > 0 ? '+' : '' }}{{ $varianceHeads }} heads
                                                                            </span>
                                                                            <br>
                                                                            <span style="color: {{ $varianceKilos == 0 ? 'green' : 'red' }};">
                                                                                {{ $varianceKilos > 0 ? '+' : '' }}{{ number_format($varianceKilos, 2) }} kg
                                                                            </span>
                                                                        @elseif ($varianceHeads != 0)
                                                                            <span style="color: {{ $varianceHeads == 0 ? 'green' : 'red' }};">
                                                                                {{ $varianceHeads > 0 ? '+' : '' }}{{ $varianceHeads }} heads
                                                                            </span>
                                                                        @elseif ($varianceKilos != 0)
                                                                            <span style="color: {{ $varianceKilos == 0 ? 'green' : 'red' }};">
                                                                                {{ $varianceKilos > 0 ? '+' : '' }}{{ number_format($varianceKilos, 2) }} kg
                                                                            </span>
                                                                        @else
                                                                            <span style="color: green;">Exact</span>
                                                                        @endif
                                                                    </p>
                                                                    @elseif($hasReceived && !$hasVariance)
                                                                    <p style="margin: 0; display: flex; flex-direction: row; gap: 5px; align-items: center; margin-left: auto;">
                                                                        <span title="Exact" style="color: green; font-size: 16px;" class="material-symbols-outlined">check_circle</span>
                                                                        <span style="font-size: 12px; color: green;">Exact</span>                                                                
                                                                    </p>

                                                                    @else
                                                                        —
                                                                    @endif
                                                                
                                                            @endif

                                                </div>



                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p style="margin: auto; color: #666;">No delivery schedule has been set yet. </p>
                                @endif


                            </a>
                        @endforeach

                
                    </div>

                @elseif (auth()->user()->role === 'Supplier')

                    <div class="content-body" style="background: #fff">
                        <table style="width:100%; border-collapse:collapse; border: 1px solid #fff;">
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; text-align: center; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Supplier</th>
                                    <th>Order ID</th>
                                    <th>Heads/Kilos</th>
                        
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>                                
                                @foreach ($orders as $order)
                                    @php
                                        $totalDeliveries = count($order->deliveries);
                                        $deliveredCount = 0;
                                        $scheduledCount = 0;
                                        $cancelledCount = 0;

                                        foreach ($order->deliveries as $delivery) {
                                            if ($delivery->status === 'Delivered') {
                                                $deliveredCount++;
                                            } elseif ($delivery->status === 'Scheduled') {
                                                $scheduledCount++;
                                            } elseif ($delivery->status === 'Cancelled') {
                                                $cancelledCount++;
                                            }
                                        }

                                        $deliveryRatio = $totalDeliveries > 0 ? "{$deliveredCount}/{$totalDeliveries}" : "0/0";

                                        if ($totalDeliveries === 0) {
                                            $deliveryStatus = 'No Delivery';
                                        } elseif ($deliveredCount === $totalDeliveries) {
                                            $deliveryStatus = 'Completed';
                                        } elseif ($deliveredCount > 0 && $scheduledCount > 0) {
                                            $deliveryStatus = 'Partially Completed';
                                        } elseif ($deliveredCount === 0 && $scheduledCount > 0) {
                                            $deliveryStatus = 'Scheduled';
                                        } else {
                                            $deliveryStatus = 'Mixed';
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{ $order->created_at->format('F j, Y') }}</td>
                                        <td>{{$order->supplier->company_name}}</td>
                                        <td>{{$order->order_id}}</td>
                                        <td>
                                            @if ($order->all_scheduled || $order->deliveries->isEmpty())
                                                —
                                            @else
                                                <div class="dropdown">
                                                    <button class="btn btn-sm dropdown-toggle" type="button" style="border: 1px solid #333" data-bs-toggle="dropdown">
                                                        {{ $order->running_balance[0]['heads'] }} heads / {{ $order->running_balance[0]['kilos'] }} kg
                                                    </button>
                                                    <ul class="dropdown-menu p-2" style="min-width: auto;">
                                                        @foreach ($order->running_balance as $balance)
                                                            <li>
                                                                {{ $balance['heads'] }} heads / {{ $balance['kilos'] }} kg
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                <style>
                                                    .dropdown-menu li{
                                                        font-size: 14px;
                                                        padding: 2px;
                                                    }
                                                </style>
                                            @endif
                                        </td>

                                                                <td>
                                            @if ($deliveryRatio !== '0/0')
                                                {{ $deliveryStatus }} ({{ $deliveryRatio }})
                                            @else

                                                No deliveries set yet
                                            @endif
                                        </td>    
                                        
                                        <td><button onclick="window.location.href='{{ route('orders.order', ['order_id' => $order->order_id]) }}'">View</button></td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

           

                @endif

        </div>


@endsection

@push('scripts')
    <script src="{{ asset('js/global/two_mb.js') }}"></script>
    <script src="{{ asset('js/global/file-preview.js') }}"></script>

@endpush
