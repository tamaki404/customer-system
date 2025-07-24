

@extends('layout')

@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/receipts.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/open-modal.css') }}">
</head>
<body>



<div id="myModal" class="modal">
  <div class="modal-content">
    <span class="close-btn">&times;</span>
     @if($user->user_type === 'Customer')
        <div class="form-section">
            <h3 class="form-title">Submit New Receipt</h3>
            <p>Please upload your receipt below. Ensure all information is accurate before submission.</p>
            <form action="/submit-receipt" class="receipt-form" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-grid">
                    <input type="hidden" name="customer_id" value="{{ auth()->user()->id }}">
                    <input type="hidden" name="status" value="Pending">
                    <input type="hidden" name="verified_by">
                    <input type="hidden" name="verified_at">

                    <div style="display: none">
                        <label>Store Name</label>
                        <input type="text" name="store_name" value="{{ auth()->user()->store_name }}" placeholder="Store name" hidden>
                    </div>

                    <div style="display: none">
                        <label>Username</label>
                        <input type="text" name="username" value="{{ auth()->user()->username }}" hidden>
                    </div>

                    <div>
                        <label>Receipt Number</label>
                        <input type="number" name="receipt_number" required>
                    </div>

                    <div>
                        <label>Upload Receipt</label>
                        <input type="file" name="receipt_image" accept="image/*" required>
                    </div>

                    <div>
                        <label>Purchase Date</label>
                        <input type="date" name="purchase_date" placeholder="e.g. 2025-07-22" required>
                    </div>

                    <div>
                        <label>Total Amount</label>
                        <input type="number" name="total_amount" placeholder="₱" required>
                    </div>

                    <div>
                        <label>Payment Method</label>
                        <input type="text" name="payment_method" placeholder="Cash / GCash / Card" required>
                    </div>

                    <div>
                        <label>Invoice Number</label>
                        <input type="number" name="invoice_number" required>
                    </div>

                    <div class="full">
                        <label>Additional Notes</label>
                        <textarea name="notes" rows="3" placeholder="Optional notes"></textarea>
                    </div>

                    {{-- <div>
                        <label>Date of Entry</label>
                        <input type="date" name="date" required>
                    </div> --}}
                </div>

                <button type="submit" class="submit-btn" id="submitBtn">Submit Receipt</button>
            </form>
        </div>
    @endif

    
  </div>
</div>



    <div class="receipt-wrapper">
        <div class="wrapper-title">
            <h2 class="title">Receipts</h2>
            <form action="/date-search" class="date-search" method="GET">
                <p>Date picker</p>
                <input type="month" class="search-date" name="search_date" placeholder="Search by date" value="{{ isset($month) ? $month : now()->format('Y-m') }}" onchange="this.form.submit()">
            </form>
        </div>
        <div class="receipt-container">


            @isset($receipts)
                @if(auth()->user()->user_type === 'Staff')
                    @php
                        // group receipts by year, month, and day
                        $grouped = $receipts->sortByDesc('created_at')->groupBy(function($item) {
                            return $item->created_at->format('Y-F');
                        });
                    @endphp
                    @if($receipts->isEmpty())
                        <div style="text-align:center; margin:2rem 0; color:#888; font-size:1.1rem;">No receipts for this month.</div>
                    @else
                        @foreach($grouped as $monthYear => $monthReceipts)
                            @php
                                $monthName = \Carbon\Carbon::parse($monthReceipts->first()->created_at)->format('F Y');
                                $days = $monthReceipts->groupBy(function($item) {
                                    return $item->created_at->format('F j, Y');
                                });
                            @endphp
                            <h1 class="month-title" style="font-size: 1.3rem; font-weight: bold; text-align: center; color:#f59c00;">{{ $monthName }}</h1>
                            @foreach($days as $day => $dayReceipts)
                                <h2 class="day-title" style="margin:1.5rem 0 0.5rem 0; color:#333; font-size:1rem;  background-color: #f9f9f9; padding: 8px; align-items: center;">{{ $day }}</h2>
                                <div class="table-wrapper">
                                    <table class="receipt-table">
                                        <thead>
                                            <tr>
                                                <th>Receipt #</th>
                                                <th>Customer</th>
                                                <th>Amount</th>
                                                <th>Purchase date</th>
                                                <th>Status</th>
                                                <th>Image</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dayReceipts as $receipt)
                                                <tr onclick="window.location='{{ url('/receipts_view/' . $receipt->receipt_id) }}'">
                                                    <td>{{ $receipt->receipt_number }}</td>
                                                    <td>{{ $receipt->store_name }}</td>
                                                    <td>₱{{ number_format($receipt->total_amount, 2) }}</td>
                                                    <td>{{ $receipt->purchase_date}}</td>
                                                    <td><span class="status {{ strtolower($receipt->status) }}">{{ $receipt->status }}</span></td>
                                                    <td>
                                                        @if($receipt->receipt_image)
                                                            <img src="{{ asset('images/' . $receipt->receipt_image) }}" class="receipt-thumb" alt="Receipt Image">
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                        @endforeach
                    @endif
                @elseif($user->user_type === 'Customer')
                    <div class="title-wrapper">
                        <h2 class="title">Your Receipts</h2>  
                        <button id="openModalBtn">Submit a Receipt</button>
                    </div>
                    @php
                        $userReceipts = $receipts->where('customer_id', $user->id);
                    @endphp
                    @if($userReceipts->isEmpty())
                        <div style="text-align:center; margin:2rem 0; color:#888; font-size:1.1rem;">No receipts for this month.</div>
                    @else
                        <div class="table-wrapper">
                            <table class="receipt-table">
                                <thead>
                                    <tr>
                                        <th>Receipt #</th>
                                        <th>Store</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Image</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($userReceipts as $receipt)
                                        <tr>
                                            <td>{{ $receipt->receipt_number }}</td>
                                            <td>{{ $receipt->store_name }}</td>
                                            <td>₱{{ number_format($receipt->total_amount, 2) }}</td>
                                            <td>{{ $receipt->purchase_date }}</td>
                                            <td><span class="status {{ strtolower($receipt->status) }}">{{ $receipt->status }}</span></td>
                                            <td>
                                                @if($receipt->receipt_image)
                                                    <img src="{{ asset('images/' . $receipt->receipt_image) }}" class="receipt-thumb" alt="Receipt Image">
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                @endif
            @else
                <div style="text-align:center; margin:2rem 0; color:#888; font-size:1.1rem;">No receipts for this month.</div>
            @endisset

            @if(session('success'))
                <div class="alert success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

        
        </div>
    </div>

<script src="{{ asset('scripts/open-modal.js') }}"></script>
<script></script>

</body>
</html>




@endsection
