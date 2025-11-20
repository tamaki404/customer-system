
@extends('layouts.main')

    <link rel="stylesheet" href="{{ asset('css/view/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fraken/dashboard.css') }}">

@section('content')


   <div class="content-bg" >
        <div class="content-header upper-header">
            <div class="title-row">
                <p class="heading">
                    <span class="greet">Goodmorning, </span>
                    @if (Auth()->user()->role !== "Customer")
                            <span class="company-name"> {{ auth()->user()->staff->lastname }} 👋 !</span>
                
                    @else
                        <span class="company-name">{{ auth()->user()->customer->company_name }} 👋 !</span>

                    @endif
                </p>
                <p class="sub-heading">Here's your dashboard overview</p>
            </div>

            <form action="{{ route('purchaseorders.list') }}" class="date-search" id="from-to-date" method="GET">
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

            <div class="content-body user-dash">

                {{-- <p style="font-size: 17px; border-left: 4px solid red; width: 50%; background-color: #fff; padding: 10px; border-radius: 5px; box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;">
                    <span class="material-symbols-outlined" style="font-size: 15px;">
                        handyman
                    </span>
                   The system is still under development, and you may experience some bugs. Kindly report any bugs you encounter, as well as any recommendations you may have. Thank you, and happy testing!
                    <span>----- If you have any question, you ma</span>
                </p> --}}


                @if(auth()->user()->role === 'Customer')
                    {{-- <div class="card">
                        <p class="card-head">
                            <span>Oct 1 - 30</span>
                        </p>
                        <p style="font-size: 2em;">
                            <span class="material-symbols-outlined icon">local_mall</span>
                            <span class="data">{{ $purchasesCount }}</span>
                        </p>
                        <p>Purchases</p>
                    </div>
                    <div class="card">
                        <p class="card-head">
                            <span>Oct 1 - 30</span>
                        </p>
                        <p style="font-size: 2em;">
                            <span class="material-symbols-outlined icon">receipt_long</span>
                            <span class="data">{{ $totalReceipts }}</span>
                        </p>
                        <p>Receipts</p>
                    </div>
                    <div class="card" style="width: 400px">
                        <p class="card-head">
                            <span>Oct 1 - 30</span>
                        </p>
                        <p style="font-size: 2em;">
                            <span class="material-symbols-outlined icon">account_balance_wallet</span>
                            <span class="data">₱{{ number_format($remainingBalance, 2) }}</span>
                        </p>
                        <p>Credits</p>
                    </div> --}}

                @elseif(auth()->user()->role !== 'Customer')
                    @php
                        //Purchases
                        $allPurchases = App\Models\PurchaseRequest::where('status', 'Pending')
                                            ->whereDate('created_at', '!=', today())
                                            ->count();
                        $purchaseToday = App\Models\PurchaseRequest::where('status', 'Pending')
                                            ->whereDate('created_at', today())
                                            ->count();
                        //Receipts
                        $allReceipts = App\Models\Payments::where('status', 'Pending')
                                            ->whereDate('created_at', '!=', today())
                                            ->count();
                        $receiptsToday = App\Models\Payments::where('status', 'Pending')
                                            ->whereDate('created_at', today())
                                            ->count();
                        //deliveries
                        $allDeliveries = App\Models\DeliveryRequest::where('status', 'Scheduled')
                                            ->whereDate('delivery_date', '!=', today())
                                            ->count();
                        $deliveryToday = App\Models\DeliveryRequest::where('status', 'Scheduled')
                                            ->whereDate('delivery_date', today())
                                            ->count();

                    @endphp
                    <section class="card-container">
                        <a class="card" href="{{ route('pr.list') }}">
                            <p class="card-head">
                                <span>Oct 1 - 30</span>
                            </p>
                            <p class="card-content">
                                <span class="material-symbols-outlined icon">local_mall</span>
                                <span class="data">{{$allPurchases}}</span>
                                @if ($purchaseToday)
                                    <span class="addition">+{{$purchaseToday}}</span>
                                @endif
                            </p>
                            <p>Purchase requests</p>
                        </a>
                        <a class="card" href="{{ route('pym.list') }}">
                            <p class="card-head">
                                <span>Oct 1 - 30</span>
                            </p>
                            <p class="card-content">
                                <span class="material-symbols-outlined icon">receipt_long</span>
                                <span class="data">{{$allReceipts}}</span>
                                @if ($receiptsToday)
                                    <span class="addition">+{{$receiptsToday}}</span>
                                @endif
                            </p>
                            <p>Payments</p>
                        </a>
                        <a class="card" href="{{ route('dlv.list') }}">
                            <p class="card-head">
                                <span>Oct 1 - 30</span>
                            </p>
                            <p class="card-content">
                                <span class="material-symbols-outlined icon">delivery_truck_speed</span>
                                <span class="data">{{$allDeliveries}}</span>
                                @if ($deliveryToday)
                                    <span class="addition">+{{$deliveryToday}}</span>
                                @endif
                            </p>
                            <p>Deliveries</p>
                        </a>
                    </section>  
                    <section class="logs">
                        <div class="header">
                            <p>Recent transactions</p>
                            <a href="">See all</a>
                        </div>
                        <table class="table-data">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody >
                                
                                @foreach ($histories as $history)
                                    <tr>
                                        <td>
                                            <div style="display: flex; flex-direction: row; gap: 5px;">
                                                @php
                                                    $imgSrc = $history->customer->user->image 
                                                        ? ('data:' . $history->customer->user->image_mime_type . ';base64,' . base64_encode($history->customer->user->image))
                                                        : asset('assets/default-company-logo.png');
                                                @endphp
                                                <img src="{{ $imgSrc }}" alt="Profile Image" style="height: 30px; border-radius: 9999%;">
                                                <p style="margin: 0; display: flex; flex-direction: column; align-items: start;">
                                                    <span style="font-size: 13px; color: #333;">{{$history->customer->company_name}}</span>
                                                    <span style="font-size: 12px; color: #888;">{{ $history->updated_at->format('F j g:i a') }}</span>
                                                </p>
                                            </div>
                                        </td>
                                        <td>
                                            <p style="margin: 0;font-size: 12px; color: #888;">
                                                <span style="color: #666">{{ $history->label }} {{ $history->status }}</span>
                                            </p>
                                            
                                        </td>
                                        <td>
                                            <p style="margin: 0; font-size: 13px;">
                                                @if ($history->label === "Delivery")
                                                    <span style="color: red">- ₱{{ number_format($history->amount, 2) }}</span>
                                                @elseif ($history->label === "Payment")
                                                    <span style="color: green">+ ₱{{ number_format($history->amount, 2) }}</span>
                                                    <span style="color: green">+ ₱{{ number_format($history->id, 2) }}</span>
                                                @else
                                                    <span style="color: #888">--</span>
                                                @endif
                                            </p>

                                            
                                        </td>
                                    </tr>
                                @endforeach


                            </tbody>
                            {{-- <div class="row">
                                <div class="user-date">
                                    <img src="" alt="">
                                    <p>
                                        <span class="name"></span>
                                        <span class="date"></span>
                                    </p>
                                </div>
                                <div class="status">

                                </div>
                                <div class="amount">
                                    <p></p>
                                </div>

                            </div> --}}
                        </table>
                    </section>

                @endif

            </div>

        {{-- @if(auth()->user()->role !== 'Customer')
                <div class="content-body user-dash" style="padding: 10px; border: none; height: auto; display: flex; flex-direction: row; gap: 5px">
                    <div class="card">
                            <p class="card-head">
                                <span>Oct 1 - 30</span>
                            </p>
                            <p style="font-size: 2em;">
                                <span class="material-symbols-outlined icon">local_mall</span>
                                <span class="data">100</span>
                            </p>
                            <p>Active users</p>
                    </div>
                    <div class="card">
                            <p class="card-head">
                                <span>Oct 1 - 30</span>
                            </p>
                            <p style="font-size: 2em;">
                                <span class="material-symbols-outlined icon">local_mall</span>
                                <span class="data">{{$orderCount}}</span>
                            </p>
                            <p>Orders</p>
                    </div>
                    <div class="card">
                            <p class="card-head">
                                <span>Oct 1 - 30</span>
                            </p>
                            <p style="font-size: 2em;">
                                <span class="material-symbols-outlined icon">local_mall</span>
                                @if ($pendingReceipts)
                                    <span class="data" style="color: orange">{{$pendingReceipts}}</span>
                                    <span style="font-size: 15px">Pending</span>
                                @elseif($pendingReceipts === 0)
                                    <span class="data">{{$verifiedreceipts}}</span>
                                @endif
                            </p>
                            <p>Receipts</p>
                    </div>
                    <div class="card">
                            <p class="card-head">
                                <span>(Today)</span>
                            </p>
                            <p style="font-size: 2em;">
                                <span class="material-symbols-outlined icon">local_mall</span>
                                <span class="data">{{$deliveryToday}}</span>
                            </p>
                            <p>Deliveries </p>
                    </div>                
                </div>

                <div class="content-body" style="height: 400px; padding: 10px; width: 400px; display: flex; flex-direction: column; background-color: #fff">
                    <div style="width: 100%; display: flex; flex-direction: row; justify-content: space-between;">
                        <p style="margin: 0; width: 100%;">
                            <span></span>
                            <span style="color: #333; font-weight: bold; font-size: 15px;">Deliveries scheduled today</span>
                            
                        </p>
                        <p style="border-radius: 9999%; background-color: #88888834; height: 25px; width: 25px; align-items: center; display: flex; justify-content: center; margin: 0;">
                            <span style="font-weight: bold">{{ $deliveryCount }}</span>
                        </p>
                        
                    </div>
                    <div class="card-list" style=" overflow-y: auto;">

                        @foreach ( $deliveries as $del )
                            <style>
                                .del-card:hover{
                                    background-color: #88888834
                                }
                                
                            </style>
                            <div class="del-card" style=" padding: 5px; border-radius:5px; height: 50px; overflow: hidden; margin-bottom: 2px;">
                                <p style="margin: 0; display: flex; justify-content: space-between;">
                                    <span style="font-size: 13px; color: #666;">{{ $del->delivery_id }}</span>
                                    <span style="font-size: 12px; color: #999;">{{ \Carbon\Carbon::parse($del->created_at)->format('M d, Y')}}</span>
                                </p>
                                <p style="margin: 0">
                                    <span>100 heads, 30 kg</span>
                                </p>
                            </div>
                        
                        @endforeach

                    </div>
                </div>

            @endif --}}

    </div>


         
       


   </div>


@endsection
