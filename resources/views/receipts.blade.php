@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/receipts.css') }}">
    <title>Receipts</title>
</head>
<body>

    <div class="receiptFrame">
        @isset($receipts)
            <h2>Your Receipts</h2>
            <table style="width:100%; border-collapse:collapse; margin-bottom:20px;">
                <thead>
                    <tr>
                        <th>Receipt #</th>
                        <th>Store Name</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($receipts as $receipt)
                    <tr>
                        <td>{{ $receipt->receipt_number }}</td>
                        <td>{{ $receipt->store_name }}</td>
                        <td>{{ $receipt->total_amount }}</td>
                        <td>{{ $receipt->purchase_date }}</td>
                        <td>{{ $receipt->status }}</td>
                        <td>
                            @if($receipt->receipt_image)
                                <img src="{{ asset('images/' . $receipt->receipt_image) }}" alt="Receipt Image" style="max-width:60px; max-height:60px;">
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
        @endisset
        @if(session('success'))
            <div style="color: green; margin-bottom: 10px;">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div style="color: red; margin-bottom: 10px;">
                <ul style="margin:0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <h1>Receipts</h1>
        <p>Here you can view and manage your receipts.</p>

        <form action="/submit-receipt" class="receiptForm" method="POST" enctype="multipart/form-data">
            @csrf
            <p>Manage your receipts below</p>
            <input type="text" name="customer_id"  value="{{ auth()->user()->id }}"  required>
            <input type="text" name="store_name" placeholder="Store Name" value="{{ auth()->user()->store_name }}">
            <input type="text" name="username"  value="{{ auth()->user()->username }}"  required>
            <input type="number" name="receipt_number" placeholder="Receipt Number" required>
            <input type="file" name="receipt_image" accept="image/*" required>
            <input type="text" name="status" value="Pending" hidden>
            <input type="text" name="purchase_date" placeholder="Purchase Date" required>
            <input type="number" name="total_amount" placeholder="Total Amount" required>
            <input type="text" name="payment_method" placeholder="Payment Method" required>
            <input type="number" name="invoice_number" placeholder="Invoice Number" required>
            <textarea name="notes" placeholder="Additional Notes" rows="4"></textarea>
            <input type="date" name="date" required>
            <input type="text" name="verified_by" hidden>
            <input type="date" name="verified_at" hidden>


            <button type="submit">Submit Receipt</button>
        </form>
    </div>

</body>
</html>
 
@endsection