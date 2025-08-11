@extends('layout')
@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
  
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/customer_view.css') }}">
    <link rel="stylesheet" href="{{ asset('css/reporting.css') }}">
    <title>Document</title>
</head>
<body>
    <div class="customerFrame">

        @if(session('success'))
            <div class="alert alert-success" style="background: #d4edda; color: #155724; ; position: absolute; z-index: 100; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                {{ session('error') }}
            </div>
        @endif
        
        <h2>Customer Details</h2>

        <div class="customerDetails">

            <div class="image">
                @php
                    $isBase64 = !empty($customer->image_mime);
                    $imgSrc = $isBase64 ? ('data:' . $customer->image_mime . ';base64,' . $customer->image) : asset('images/' . $customer->image);
                @endphp
                <img src="{{ $imgSrc }}" alt="User Image" style="">
            </div>
            <div class="details">
                <p class="customer-id">cID: {{ $customer->id }}</p>
                <p class="store-name">{{ $customer->store_name}}</p>
                <span class="username"><p class="handle">Handled by</p><p>{{ $customer->name }}</p></span>
                

                <div class="statusButton">
                    <!-- Account Status Display -->
                    <div class="status-section" style="margin: 15px 0;">
                        <span class="status-badge" style="
                            padding: 6px 12px;
                            border-radius: 20px;
                            font-size: 12px;
                            font-weight: 600;
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                            width: auto;
                            @if($customer->acc_status === 'Active')
                                background: #d4edda;
                                color: #155724;
                                border: 1px solid #c3e6cb;
                            @elseif($customer->acc_status === 'accepted')
                                background: #d1ecf1;
                                color: #0c5460;
                                border: 1px solid #bee5eb;
                            @elseif($customer->acc_status === 'suspended')
                                background: #f8d7da;
                                color: #721c24;
                                border: 1px solid #f5c6cb;
                            @else
                                background: #fff3cd;
                                color: #856404;
                                border: 1px solid #ffeaa7;
                            @endif
                        ">
                            {{ $customer->acc_status }}
                        </span>
                    </div>

                <!-- Action Buttons -->
                    @if(auth()->user()->user_type === 'Admin' || auth()->user()->user_type === 'Staff')
                        <div class="action-buttons" style="margin: 20px 0;">
                            @if($customer->acc_status !== 'Active')
                                <form action="{{ url('/customer/activate/' . $customer->id) }}" method="POST" style="display: inline-block; margin-right: 10px;">
                                    @csrf
                                    <button type="submit" class="activate-btn" onmouseover="this.style.background='#218838'" onmouseout="this.style.background='#28a745'">
                                        Activate Account
                                    </button>
                                </form>
                            @endif

                            @if($customer->acc_status === 'Active')
                                <form action="{{ url('/customer/suspend/' . $customer->id) }}" method="POST" style="display: inline-block; margin-right: 10px;">
                                    @csrf
                                    <button type="submit" class="suspend-btn">
                                         Suspend Account
                                    </button>
                                </form>
                            @endif

                            @if($customer->acc_status === 'suspended')
                                <form action="{{ url('/customer/activate/' . $customer->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    <button type="submit" class="reactivate-btn">
                                        Reactivate Account
                                    </button>
                                </form>
                            @endif
                        </div>
                @endif
                </div>


                <p style="font-size: 14px;">Joined on {{ $customer->created_at -> format('F Y')}}</p>
            </div>

        </div>

        <div class="receiptsCorner">
            <nav class="tab-navigation">
                <button class="tab-button active" onclick="switchTab('ordersTab')">Orders History</button>
                <button class="tab-button" onclick="switchTab('receiptsTab')">Receipts History</button>
            </nav>

            <div class="tab-content active" id="ordersTab" style="overflow-x:auto;">
                @if(isset($orders) && count($orders) > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Status</th>
                                <th>Total Qty</th>
                                <th>Total Price</th>
                                <th>Last Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr onclick="window.location='{{ url('/view-order/' . $order->order_id) }}'">
                                    <td>{{ $order->order_id }}</td>
                                    <td>{{ $order->status }}</td>
                                    <td>x{{ $order->total_quantity }}</td>
                                    <td>₱{{ number_format($order->total_price, 2) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($order->action_at)->format('M d, Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="no-receipts">No orders found for this customer.</div>
                @endif
            </div>

            <div class="tab-content" id="receiptsTab" style="overflow-x:auto;">
                @if(isset($receipts) && count($receipts) > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>Receipt #</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receipts as $receipt)
                                <tr onclick="window.location='{{ url('/receipts_view/' . $receipt->receipt_id) }}'">
                                    <td>{{ $receipt->receipt_number }}</td>
                                    <td>₱{{ number_format($receipt->total_amount, 2) }}</td>
                                    <td>{{ $receipt->created_at->format('F j, Y') }}</td>
                                    <td style="color:
                                        @if($receipt->status === 'Verified') green
                                        @elseif($receipt->status === 'Pending') #333
                                        @elseif($receipt->status === 'Cancelled') orange
                                        @elseif($receipt->status === 'Rejected') red
                                        @else #333
                                        @endif
                                    ;">{{ $receipt->status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="no-receipts">No receipts found for this customer.</div>
                @endif
            </div>
        </div>

    </div>

    <script>
        // Auto-hide success/error messages after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s ease';
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 500);
            });
        }, 5000);

        // Match reports tabs behavior/design
        function switchTab(tabId) {
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById(tabId).classList.add('active');

            const tabButtons = document.querySelectorAll('.tab-button');
            tabButtons.forEach(button => {
                button.classList.remove('active');
            });
            event.target.classList.add('active');
        }
    </script>
</body>
</html>

@endsection