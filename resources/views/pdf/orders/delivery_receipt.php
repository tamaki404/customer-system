<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery receipt</title>
    <style>
    body {
        font-family: 'DejaVu Sans', sans-serif;
    }
</style>

</head>
<body>

    <div>
        <div class="header">

            <div>
                <h1>Delivery receipt</h1>
                <p>Sunny & Scramble</p>
                <p>Created at {{ $order->created_at}}</p>

                <style>
                    .details-box p{
                        margin: 0;
                        font-size: 14px;
                    }
                </style>
                <div class="details-box" style="display: flex; flex-direction: column; ">
                    <p><strong>Delivery Information:</strong></p>
                    <p>Delivery Receipt #: {{ $order->order_id }}</p>
                    <p>Order ID: {{ $order->order_id }}</p>
                    <p>PO ID: {{ $order->po_id }}</p>
                    <p>Delivery Date: {{ $order->created_at->format('F j, Y') }}</p>
                    
                    <p style="margin-top: 15px;"><strong>Deliver To:</strong></p>
                    <p>{{ $order->supplier->company_name }}</p>
                    <p>
                        <span>Mobile# {{ $order->supplier->mobile_no }}</span>
                        <span>Telephone# {{ $order->supplier->telephone_no }}</span>
                        <span>Email {{ $order->supplier->user->email_address }}</span>
                    </p>
                    <p>
                        <span><strong>Delivery Address:</strong>
                              {{ implode(', ', array_filter([
                                    $order->supplier->home_street,
                                    $order->supplier->home_subdivision,
                                    $order->supplier->home_barangay,
                                    $order->supplier->home_city,
                                ])) }}
                        </span>
                    </p>
                    <p>
                        <span><strong>Billing Address:</strong>
                              {{ implode(', ', array_filter([
                                    $order->supplier->office_street,
                                    $order->supplier->office_subdivision,
                                    $order->supplier->office_barangay,
                                    $order->supplier->office_city,
                                ])) }}
                        </span>
                    </p>
                </div>
               

            </div>

        </div>
        <div class="body">

        <div class="content-body" style="padding: 10px; border: none; height: auto;">
          
            <div class="table-body" style="margin-top: 50px">
                <p style="margin: 5px; font-weight: bold;">Delivered Items</p>
                <div class="table-content" style="background: #fff; border-radius: 10px; overflow: hidden;">
                    <table style="width:100%; border-collapse:collapse; border: 1px solid #fff; text-align: center;">
                        <thead style="background-color: #fff;">
                            <tr style="background:#fff; height: 30px; border-bottom: 1px solid #ccc;">
                                <th style="vertical-align: middle;">#</th>
                                <th style="vertical-align: middle;">Product ID</th>
                                <th style="vertical-align: middle;">Description</th>
                                <th style="vertical-align: middle;">Unit Price</th>
                                <th style="vertical-align: middle;">Quantity Delivered</th>
                                <th style="vertical-align: middle;">Total Amount</th>                                                
                            </tr>
                        </thead>
                        <tbody>                                
                            @foreach ($items as $item)
                                <tr>
                                    <td style="vertical-align: middle;">{{$loop->iteration}}</td>
                                    <td style="vertical-align: middle;">{{ $item->set_id }}</td>
                                    <td style="vertical-align: middle;">{{ $item->product->name }}</td>
                                    <td style="vertical-align: middle;">₱{{ number_format($item->unit_price, 2) }}</td>
                                    <td style="vertical-align: middle;">{{ $item->quantity }}</td>
                                    <td style="vertical-align: middle;">₱{{ number_format($item->total_price, 2) }}</td>
                                </tr>
                            @endforeach

                            <tr style="margin-top: 20px">
                                <td colspan="5" style="text-align: right; font-weight: bold; vertical-align: middle;">Grand Total:</td>
                                <td style="font-weight: bold; vertical-align: middle;">
                                    ₱{{ number_format($items->sum('total_price'), 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>


       
        </div>
        </div>
        <div class="footer" style="margin-top: 50px;">
            <div style="display: flex; justify-content: space-between; margin-top: 30px;">
                <div style="width: 45%;">
                    <p><strong>Delivery Confirmation:</strong></p>
                    <p>I acknowledge receipt of the above items in good condition.</p>
                    <p>Date: _________________</p>
                    <p>Signature: _________________</p>
                </div>
                <div style="width: 45%; text-align: right;">
                    <p><strong>Delivery Personnel:</strong></p>
                    <p>Name: _________________</p>
                    <p>Signature: _________________</p>
                    <p>Date: _________________</p>
                </div>
            </div>
            <div style="margin-top: 20px; text-align: center; font-size: 12px;">
                <p>Authorized signatories to accept deliveries & sign invoices</p>
            </div>
        </div>
    </div>

    
</body>
</html>