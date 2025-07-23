

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
</head>
<body>



    <div class="receipt-wrapper">

        
    <div class="receipt-container">
        @isset($receipts)
            @if($user->user_type === 'Staff')
                <h2 class="title">All Receipts</h2>
                <div class="table-wrapper">
                    <table class="receipt-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Receipt #</th>
                                <th>Customer</th>
                                <th>Store</th>
                                <th>Amount</th>
                                <th>Purchase date</th>
                                <th>Status</th>
                                <th>Image</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($receipts as $receipt)
                                <tr onclick="window.location='{{ url('/receipts_view/' . $receipt->receipt_id) }}'">
                                    <td>{{ $receipt->created_at->format('F j, Y') }}</td>
                                    <td>{{ $receipt->receipt_number }}</td>
                                    <td>{{ $receipt->customer->username ?? 'N/A' }}</td>
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
                            @empty
                                <tr><td colspan="7">No receipts found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @elseif($user->user_type === 'Customer')
                <h2 class="title">Your Receipts</h2>
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
                            @php
                                $userReceipts = $receipts->where('customer_id', $user->id);
                            @endphp
                            @forelse($userReceipts as $receipt)
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
                            @empty
                                <tr><td colspan="6">No receipts found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
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

        @if($user->user_type !== 'Staff')
        <div class="form-section">
            <h3 class="form-title">Submit New Receipt</h3>
            <form action="/submit-receipt" class="receipt-form" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-grid">
                    <input type="hidden" name="customer_id" value="{{ auth()->user()->id }}">
                    <input type="hidden" name="status" value="Pending">
                    <input type="hidden" name="verified_by">
                    <input type="hidden" name="verified_at">

                    <div>
                        <label>Store Name</label>
                        <input type="text" name="store_name" value="{{ auth()->user()->store_name }}" placeholder="Store name">
                    </div>

                    <div>
                        <label>Username</label>
                        <input type="text" name="username" value="{{ auth()->user()->username }}">
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
                        <input type="text" name="purchase_date" placeholder="e.g. 2025-07-22" required>
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

                    <div>
                        <label>Date of Entry</label>
                        <input type="date" name="date" required>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Submit Receipt</button>
            </form>
        </div>
        @endif
    </div>
</div>
</body>
</html>

@endsection
