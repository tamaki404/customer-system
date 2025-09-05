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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>

        <!-- confirmation modal -->
    <div class="modal fade" id="confirmModal" style="display: none;"  tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"  style="justify-self: center; align-self: center; ">
            <div class="modal-content" style="border-top: 4px solid #ffde59;">
                <div class="modal-header">
                    <h5 class="modal-title" style="padding: 0; margin: 0;">Confirm action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" style="border: none; font-size: 14px;">
                    Are you sure you want to commit changes?
                </div>

                <div class="modal-footer" style="padding: 5px">
                    <button type="button" id="cancelBtn" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmSaveBtn" class="btn" style="background: #ffde59; font-weight: bold; font-size: 14px;">Confirm</button>
                </div>
            </div>
        </div>
    </div>

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

        <a class="go-back-a" href="/customers"><- Customers</a>
        <style>
            .go-back-a{
                font-size: 15px;
                color: #f8912a;
                text-decoration: none;
                width: auto;
            }
            .go-back-a:hover{
                color: #cd741c;
            }
        </style>
        
        
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
                {{-- <p class="customer-id">cID: {{ $customer->id }}</p> --}}
                <div class="store-name" >
                    <p class="storename">
                        <span class="name" title="{{ $customer->store_name}} ">{{ $customer->store_name}} </span>
                        <span class="status"
                            style="
                                text-transform: uppercase;
                                margin: 0;
                                
                                align-items: center;
                                @if($customer->acc_status === 'Active')
                                    color: #155724;
                                @elseif($customer->acc_status === 'accepted')
                                    color: #0c5460;
                                @elseif($customer->acc_status === 'Suspended')
                                    color: #721c24;
                                @else
                                    color: #856404;
                                @endif 
                            ">
                            {{$customer->acc_status}}
                        </span>
                    </p>

                    <span class="customer-id" style="color:#f8912a">{{$customer->id}}</span>
                        
                 
                </div>
                <span class="username" style="margin: 0"><p class="handle" style="margin: 0">Handled by</p><p style="margin: 0">{{ $customer->name }}</p></span>
                <div class="customer-contacts">
                    <p>
                        <span class="material-symbols-outlined">mail</span>
                        {{ $customer->email }}
                    </p>
                    <p>
                        <span class="material-symbols-outlined">smartphone</span>
                        {{ $customer->mobile }}
                    </p>
                    @if ($customer->telephone)
                        <p>
                            <span class="material-symbols-outlined">call</span>
                            {{ $customer->telephone }}
                        </p>                    
                    @endif

                </div>


                <div class="statusButton">
                    <!-- Action Buttons -->
                    @if(auth()->user()->user_type !== 'Customer')
                        <div class="action-buttons" style="margin-left: auto">

                            @if($customer->acc_status === 'Active')
                                <form action="{{ url('/customer/suspend/' . $customer->id) }}" method="POST" style="display: inline-block; margin-right: 10px;">
                                    @csrf
                                    <button type="button" class="btn-confirm suspend-btn" 
                                    data-action="Suspend" >Suspend account</button>
                             
                                </form>
                            @elseif($customer->acc_status === 'Suspended')
                                <form action="{{ url('/customer/activate/' . $customer->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    <button type="submit" style="background-color: #28a745" class="reactivate-btn">
                                        Restore account
                                    </button>
                                </form>
                            @elseif($customer->acc_status === 'Pending')
                                <form action="{{ url('/customer/activate/' . $customer->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    <button type="submit" style="background-color: #28a745" class="reactivate-btn">
                                        Activate account
                                    </button>
                                </form>
                            @endif


                        </div>
                    @endif
                </div>

                <p class="joined-date"><i class="far fa-clock"></i> Joined on {{ $customer->created_at -> format('F Y')}}</p>
            </div>

        </div>

        <div class="receiptsCorner" style=" overflow-y: auto;">
            <nav class="tab-navigation" style="height: auto">
                <button class="tab-button active" onclick="switchTab('statisticsTab')">Statistics</button>
                <button class="tab-button" onclick="switchTab('ordersTab')">Orders History</button>
                <button class="tab-button" onclick="switchTab('receiptsTab')">Receipts History</button>
                <button class="tab-button" onclick="switchTab('purchaseOrderTab')">Purchase Order History</button>

            </nav>
            <div class="tab-content active" id="statisticsTab" style="overflow-x:auto;  auto; height: auto; padding: 0">
                <div class="statistics-customer">
                    <div class="head">
                        {{-- date-picker --}}
                        {{-- <form action="" method="POST" class="date-picker">
                            @csrf
                            <div class="date-search">
                                <span>From</span>
                                <input type="date" name="from_date">
                            </div>
                            <div class="date-search">
                                <span>To</span>
                                <input type="date" name="to_date">
                            </div>
                        </form> --}}

                        {{-- <div class="date-searched">
                            <p>Showing results from</p>
                            <div class="dates">
                                <span>{{ \Carbon\Carbon::parse($from)->format('d M, Y') }}</span>
                                <span>-</span>
                                <span>{{ \Carbon\Carbon::parse($to)->format('d M, Y') }}</span>
                            </div>
                        </div> --}}

                        <form action="{{ route('customer.view',  ['customer_id' => $customer->id]) }}"  class="date-picker" id="from-to-date" method="GET">
                                <div class="date-search">
                                    <span>From</span>
                                    <input type="date" name="from_date" 
                                        value="{{ request('from_date', now()->startOfMonth()->format('Y-m-d')) }}"
                                        onchange="this.form.submit()">
                                </div>
                                <div class="date-search">
                                    <span>To</span>
                                    <input type="date" name="to_date"
                                        value="{{ request('to_date', now()->endOfMonth()->format('Y-m-d')) }}"
                                        onchange="this.form.submit()">
                                </div>
                        </form>
                    </div>

                    {{-- summary total cards --}}
                    <p class="stats-title">Summary</p>
                    <div class="summary-cards">
                        <div class="card">
                            <h2>{{$sum_PO}}</h2>
                            <p>Total purchase orders</p>
                        </div>
                        <div class="card">
                            <h2>{{$sum_receipts}}</h2>
                            <p>Total receipts</p>
                        </div>
                        <div class="card">
                            <h2>{{$sum_orders}}</h2>
                            <p>Total orders</p>
                        </div>
                    </div>

                    {{-- spending value --}}
                    <br>
                    <p class="stats-title">Spending</p>
                    <div class="summary-cards">
                        <div class="card">
                            <h2></h2>
                            <p>Total Amount Spent</p>
                        </div>
                        <div class="card">
                            <h2></h2>
                            <p>Highest Single Purchase Value</p>
                        </div>
                        <div class="card">
                            <h2></h2>
                            <p>Average Spend per Order</p>
                        </div>
                        <div class="card">
                            <h2></h2>
                            <p>Lifetime Value (LTV)</p>
                        </div>
                    </div>

                    {{-- chart --}}
                    <br>
                    <p class="stats-title">Charts</p>
                    <div class="stats-charts">
                        {{-- Orders status --}}
                        <div class="orders-month">

                        </div>
                        <div class="order-status">

                        </div>
                    </div>
                    {{-- top product --}}
                    <br>
                    <p class="stats-title">Top product</p>
                    <div class="top-product">
                        <div class="product">

                        </div>

                    </div>

                </div>
            </div>
            <div class="tab-content" id="ordersTab" style="overflow-x:auto; padding: 0">
                @if(isset($orders) && count($orders) > 0)
                    <table  style="overflow-x:auto; overflow-y: auto; ">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Last Update</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($orders as $order)
                                @php 
                                    $dateToShow = $order->action_at ?? $order->created_at;
                                    $statusClasses = [
                                        'Pending' => 'status-pending',
                                        'Processing' => 'status-processing',
                                        'Cancelled' => 'status-cancelled',
                                        'Rejected' => 'status-rejected',
                                        'Done' => 'status-done',
                                        'Completed' => 'status-completed',
                                    ];
                                 @endphp

                                <tr onclick="window.location='{{ url('/view-order/' . $order->order_id) }}'">
                                    <td class="td-tem">{{ $order->order_id }}</td>
                                    <td class="td-tem" >{{ \Carbon\Carbon::parse($order->action_at)->format('M d, Y H:i') }}</td>
                                    <td class="td-tem">x{{ $order->total_quantity }}</td>
                                    <td class="td-tem" >₱{{ number_format($order->total_price, 2) }}</td>
                                    <td class="td-tem">
                                        <div  
                                            class="{{ $statusClasses[$order->status] ?? 'status-default' }}">
                                            {{ $order->status }}
                                        </div>
                                    </td>


                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="pagination-wrapper" >
                        @if ($orders->total() > 0)
                            <div style="font-size: 14px">
                                Page {{ $orders->currentPage() }} of {{ $orders->lastPage() }}
                            </div>
                        @endif

                        @if ($orders->hasPages())
                            <div class="pagination-controls">
                                @if ($orders->onFirstPage())
                                    <span class="previous-btn" >Previous</span>
                                @else
                                    <a href="{{ $orders->previousPageUrl() }}" class="href-previous-btn">Previous</a>
                                @endif

                                @if ($orders->hasMorePages())
                                    <a class="href-next-btn" href="{{ $orders->nextPageUrl() }}" >Next</a>
                                @else
                                    <span class="span-next">Next</span>
                                @endif
                            </div>
                        @endif
                    </div>
                @else
                    <div class="no-receipts">No orders found for this customer.</div>
                @endif
            </div>

            <div class="tab-content" id="receiptsTab" style="overflow-x:auto;  auto; height: auto; padding: 0">
                @if(isset($receipts) && count($receipts) > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>Receipt #</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receipts as $receipt)
                                <tr onclick="window.location='{{ url('/receipts_view/' . $receipt->receipt_id) }}'">
                                    <td>{{ $receipt->receipt_number }}</td>
                                    <td>₱{{ number_format($receipt->total_amount, 2) }}</td>
                                    <td>{{ $receipt->created_at->format('F j, Y') }}</td>
                               <td>
                                    @php 
                                        $statusClasses = [
                                            'Pending' => 'status-pending',
                                            'Verified' => 'status-verified',
                                            'Cancelled' => 'status-cancelled',
                                            'Rejected' => 'status-rejected',
                                         ];
                                    @endphp

                                    <div class="{{ $statusClasses[$receipt->status] ?? 'status-default' }}">
                                        {{ $receipt->status }}
                                    </div>
                               </td>


                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="pagination-wrapper" >
                        @if ($orders->total() > 0)
                            <div style="font-size: 14px">
                                Page {{ $orders->currentPage() }} of {{ $orders->lastPage() }}
                            </div>
                        @endif

                        @if ($orders->hasPages())
                            <div class="pagination-controls">
                                @if ($orders->onFirstPage())
                                    <span class="previous-btn" >Previous</span>
                                @else
                                    <a href="{{ $orders->previousPageUrl() }}" class="href-previous-btn">Previous</a>
                                @endif

                                @if ($orders->hasMorePages())
                                    <a class="href-next-btn" style="" href="{{ $orders->nextPageUrl() }}" >Next</a>
                                @else
                                    <span class="span-next">Next</span>
                                @endif
                            </div>
                        @endif
                    </div>
                @else
                    <div class="no-receipts">No receipts found for this customer.</div>
                @endif

            </div>
            <div class="tab-content" id="purchaseOrderTab" style="overflow-x:auto;  auto; height: auto; padding: 0">
                @if(isset($receipts) && count($receipts) > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>PO #</th>
                                <th>Date</th>
                                <th>Quantity</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseOrders as $purchase)
                                <tr onclick="window.location='{{ route('purchase_order.view', $purchase->po_number) }}'">

                                    <td >{{ $purchase->po_number }}</td>
                                    <td>{{ $purchase->created_at->format('F j, Y') }}</td>
                                    <td>x{{ $purchase->items->sum('quantity') }}</td>                            

                                    <td>₱{{ number_format($purchase->grand_total, 2) }}</td>
                                <td class="order-actions">
                                    @php 
                                        $dateToShow = $order->action_at ?? $order->created_at;
                                        $statusClasses = [
                                            'Pending' => 'status-pending',
                                            'Processing' => 'status-processing',
                                            'Accepted' => 'status-approved',
                                            'Rejected' => 'status-rejected',
                                            'Delivered' => 'status-delivered',
                                            'Cancelled' => 'status-cancelled',
                                            'Draft' => 'status-draft',

                                        ];
                                    @endphp
                                    <span class="{{ $statusClasses[$order->status] ?? 'status-default' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>


                                </td>


                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="pagination-wrapper" >
                        @if ($orders->total() > 0)
                            <div style="font-size: 14px">
                                Page {{ $orders->currentPage() }} of {{ $orders->lastPage() }}
                            </div>
                        @endif

                        @if ($orders->hasPages())
                            <div class="pagination-controls">
                                @if ($orders->onFirstPage())
                                    <span class="previous-btn" >Previous</span>
                                @else
                                    <a href="{{ $orders->previousPageUrl() }}" class="href-previous-btn">Previous</a>
                                @endif

                                @if ($orders->hasMorePages())
                                    <a class="href-next-btn" style="" href="{{ $orders->nextPageUrl() }}" >Next</a>
                                @else
                                    <span class="span-next">Next</span>
                                @endif
                            </div>
                        @endif
                    </div>
                @else
                    <div class="no-receipts">No purchase order found for this customer.</div>
                @endif

            </div>
        </div>

    </div>

    <script>
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
    <script src="{{ asset('js/confirmation-modal/customer_view.js') }}"></script>

</body>
</html>

@endsection