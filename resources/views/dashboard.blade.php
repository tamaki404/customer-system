
@extends('layouts.main')

<link rel="stylesheet" href="{{ asset('css/view/dashboard.css') }}">

@section('content')


   <div class="content-bg" >
        <div class="content-header">
            <div class="contents-display">
            </div>

            <div class="title-row">
                <p class="heading">
                    <span class="greet">Goodmorning, </span>
                    @if (Auth()->user()->role !== "Customer")
                            <span class="company-name">{{ auth()->user()->staff->lastname }} 👋 !</span>
                
                    @else
                        <span class="company-name">{{ auth()->user()->customer->company_name }} 👋 !</span>

                    @endif
                </p>
                <p class="sub-heading">Here's your dashboard overview</p>
            </div>


        </div>

            <div class="content-body user-dash" style="padding: 10px; border: none; height: auto; display: flex; flex-direction: row; gap: 5px">

                {{-- <p style="font-size: 17px; border-left: 4px solid red; width: 50%; background-color: #fff; padding: 10px; border-radius: 5px; box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;">
                    <span class="material-symbols-outlined" style="font-size: 15px;">
                        handyman
                    </span>
                   The system is still under development, and you may experience some bugs. Kindly report any bugs you encounter, as well as any recommendations you may have. Thank you, and happy testing!
                    <span>----- If you have any question, you ma</span>
                </p> --}}


                @if(auth()->user()->role === 'Customer')
                    <div class="card">
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
                    </div>
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
