
@extends('layouts.main')

<link rel="stylesheet" href="{{ asset('css/view/dashboard.css') }}">

@section('content')


   <div class="content-bg" >
        <div class="content-header">
            <div class="contents-display">
            </div>

            <div class="title-row">
                <p class="heading">
                    <span class="greet">Goodmorning,</span>
                    <span class="company-name">{{ auth()->user()->supplier->company_name }} 👋 !</span>
                </p>
                <p class="sub-heading">Here's your dashboard overview</p>
            </div>


        </div>

        <div class="content-body user-dash" style="padding: 10px; border: none; height: auto; display: flex; flex-direction: row; gap: 5px">

            @if(auth()->user()->role === 'Supplier')


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
                        <span class="data">{{ $remainingBalance }}</span>
                    </p>
                    <p>Credits</p>
                </div>

                <div>
                    
                </div>

                
            @endif


        </div>



                @if(auth()->user()->role === 'Staff' || auth()->user()->role === 'Admin')
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
