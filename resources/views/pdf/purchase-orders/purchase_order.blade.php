<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order</title>
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
                <h1>Purchase Order</h1>
                <p>Sunny & Scramble</p>
                <p>Created at {{ $purchaseOrder->created_at->format('F j, Y g:i A') }}</p>

                <style>
                    .details-box p{
                        margin: 0;
                        font-size: 14px;
                    }
                </style>
                <div class="details-box" style="display: flex; flex-direction: column;">
                    <p>PO ID: {{ $purchaseOrder->po_id }}</p>
                    <p>Supplier: {{ $purchaseOrder->supplier->company_name }}</p>
                    <p>
                        <span>Mobile# {{ $purchaseOrder->supplier->mobile_no }}</span>
                        <span>Telephone# {{ $purchaseOrder->supplier->telephone_no }}</span>
                        <span>Email {{ $purchaseOrder->supplier->user->email_address }}</span>
                    </p>
                    <p>
                        <span>Home address:
                              {{ implode(', ', array_filter([
                                    $purchaseOrder->supplier->home_street,
                                    $purchaseOrder->supplier->home_subdivision,
                                    $purchaseOrder->supplier->home_barangay,
                                    $purchaseOrder->supplier->home_city,
                                ])) }}
                        </span>
                    </p>
                    <p>
                        <span>Office address:
                              {{ implode(', ', array_filter([
                                    $purchaseOrder->supplier->office_street,
                                    $purchaseOrder->supplier->office_subdivision,
                                    $purchaseOrder->supplier->office_barangay,
                                    $purchaseOrder->supplier->office_city,
                                ])) }}
                        </span>
                    </p>
                    @if($purchaseOrder->staff)
                        <p>Confirmed by: {{ $purchaseOrder->staff->first_name }} {{ $purchaseOrder->staff->last_name }}</p>
                    @endif
                    @if($purchaseOrder->confirmed_at)
                        <p>Confirmed at: {{ $purchaseOrder->confirmed_at->format('F j, Y g:i A') }}</p>
                    @endif
                    @if($purchaseOrder->notes)
                        <p>Notes: {{ $purchaseOrder->notes }}</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="body">
            <div class="content-body" style="padding: 10px; border: none; height: auto;">
                <div class="table-body" style="margin-top: 50px">
                    <p style="margin: 5px; font-weight: bold;">Purchase Order Items</p>
                    <div class="table-content" style="background: #fff; border-radius: 10px; overflow: hidden;">
                        <table style="width:100%; border-collapse:collapse; border: 1px solid #fff; text-align: center;">
                            <thead style="background-color: #fff;">
                                <tr style="background:#fff; height: 30px; border-bottom: 1px solid #ccc;">
                                    <th style="vertical-align: middle;">#</th>
                                    <th style="vertical-align: middle;">Product ID</th>
                                    <th style="vertical-align: middle;">Name</th>
                                    <th style="vertical-align: middle;">Category</th>
                                    <th style="vertical-align: middle;">Unit</th>
                                    <th style="vertical-align: middle;">Unit Price</th>
                                    <th style="vertical-align: middle;">Supplier Qty</th>
                                    <th style="vertical-align: middle;">Staff Qty</th>
                                    <th style="vertical-align: middle;">Status</th>
                                    <th style="vertical-align: middle;">Total Amount</th>                                                
                                </tr>
                            </thead>
                            <tbody>                                
                                @foreach ($purchaseOrder->items as $item)
                                    <tr>
                                        <td style="vertical-align: middle;">{{$loop->iteration}}</td>
                                        <td style="vertical-align: middle;">{{ $item->set_id }}</td>
                                        <td style="vertical-align: middle;">{{ $item->product->name }}</td>
                                        <td style="vertical-align: middle;">{{ $item->product->category }}</td>
                                        <td style="vertical-align: middle;">{{ $item->product->unit }}</td>
                                        <td style="vertical-align: middle;">₱{{ number_format($item->unit_price, 2) }}</td>
                                        <td style="vertical-align: middle;">{{ $item->supplier_quantity }}</td>
                                        <td style="vertical-align: middle;">{{ $item->staff_quantity }}</td>
                                        <td style="vertical-align: middle;">{{ $item->status }}</td>
                                        <td style="vertical-align: middle;">₱{{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                @endforeach

                                <tr style="margin-top: 20px">
                                    <td colspan="9" style="text-align: right; font-weight: bold; vertical-align: middle;">Grand Total:</td>
                                    <td style="font-weight: bold; vertical-align: middle;">
                                        ₱{{ number_format($purchaseOrder->total_amount, 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p>
                <span></span>
                <span>Purchase Order Status: {{ $purchaseOrder->status }}</span>
            </p>
        </div>
    </div>
</body>
</html>
