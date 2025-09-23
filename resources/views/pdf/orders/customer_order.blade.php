<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer order</title>
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
                <h1>Customer order</h1>
                <p>Sunny & Scramble</p>
                <p>Created at {{ $order->created_at}}</p>

                <style>
                    .details-box p{
                        margin: 0;
                        font-size: 14px;
                    }
                </style>
                <div class="details-box" style="display: flex; flex-direction: column; ">
                    <p>Order ID: {{ $order->order_id }}</p>
                    <p>PO ID: {{ $order->po_id }}</p>
                    <p>Supplier: {{ $order->supplier->company_name }}</p>
                    <p>
                        <span>Mobile# {{ $order->supplier->mobile_no }}</span>
                        <span>Telephone# {{ $order->supplier->telephone_no }}</span>
                        <span>Email {{ $order->supplier->user->email_address }}</span>

                    </p>
                    {{-- <p>
                        <span>Home address:
                              {{ implode(', ', array_filter([
                                    $order->supplier->home_street,
                                    $order->supplier->home_subdivision,
                                    $order->supplier->home_barangay,
                                    $order->supplier->home_city,
                                ])) }}
                        </span>
                    </p>
                    <p>
                        <span>Office address:
                              {{ implode(', ', array_filter([
                                    $order->supplier->office_street,
                                    $order->supplier->office_subdivision,
                                    $order->supplier->office_barangay,
                                    $order->supplier->office_city,
                                ])) }}
                        </span>
                    </p> --}}



                </div>
               

            </div>

        </div>
        <div class="body">

        <div class="content-body" style="padding: 10px; border: none; height: auto;">
          
            <div class="table-body" style="margin-top: 50px">
                <p style="margin: 5px; font-weight: bold;">Order items</p>
                <div class="table-content" style="background: #fff; border-radius: 10px; overflow: hidden;">
                    <table style="width:100%; border-collapse:collapse; border: 1px solid #fff; text-align: center;">
                        <thead style="background-color: #fff;">
                            <tr style="background:#fff; height: 30px; border-bottom: 1px solid #ccc;">
                                <th style="vertical-align: middle;">#</th>
                                <th style="vertical-align: middle;">Name</th>
                                <th style="vertical-align: middle;">Unit price</th>
                                <th style="vertical-align: middle;">Quantity</th>
                                <th style="vertical-align: middle;">Total amount</th>                                                
                            </tr>
                        </thead>
                        <tbody>                                
                            @foreach ($items as $item)
                                <tr>
                                    <td style="vertical-align: middle;">{{$loop->iteration}}</td>
                                    <td style="vertical-align: middle;">{{ $item->product->name }}</td>
                                    <td style="vertical-align: middle;">{{ $item->productSetting?->price ?? 'N/A' }}</td>
                                    <td style="vertical-align: middle;">{{ $item->quantity }}</td>
                                    <td style="vertical-align: middle;">&#8369;{{ number_format($item->total_price, 2) }}</td>
                                </tr>
                            @endforeach

                            <tr style="margin-top: 20px">
                                <td colspan="4" style="text-align: right; font-weight: bold; vertical-align: middle;">Grand Total:</td>
                                <td style="font-weight: bold; vertical-align: middle;">
                                    &#8369;{{ number_format($items->sum('total_price'), 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>


       
        </div>
        </div>
        <div class="footer">

        </div>
    </div>

    
</body>
</html>