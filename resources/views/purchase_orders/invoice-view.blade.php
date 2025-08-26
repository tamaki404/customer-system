<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/invoice-view.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <title>Invoice</title>
</head>
<body style="display: flex; flex-direction: column; flex-wrap: wrap;">

    <a class="go-back-a" href="{{ route('purchase_order.view', $invoice->purchaseOrder->po_number) }}">
         <- Back to Order
    </a>

    <div class="form">
        <div class="header-1">
            <h2>Invoice</h2> 
            <div class="header">
                <div class="sunny-info">
                    <img src="{{ asset('assets/sunnyLogo1.png') }}" alt="">
                    <div class="address">
                        <p><strong>Sunny & Scramble</strong></p>
                        <p>123 Sunny Street</p>
                        <p>Sunnyville, 1860</p>
                    </div>
                </div>
                <div class="order-info">
                    <p><strong>Invoice No:</strong> {{$invoice->invoice_number}}</p>
                    <p><strong>Date:</strong> {{$invoice->created_at->format('d F Y h:i A')}}</p>
                    <p><strong>Status:</strong> {{$invoice->status}}</p>
                </div>
            </div>
        </div>

        <div class="form-body" style="padding: 10px">
            <div class="shipping-info">
                <div class="vendor">
                    <h3>Vendor Information</h3>
                    <p>Sunny & Scramble</p>
                    <p>123 Sunny Street Sunnyville, 1860</p>
                    <p><strong>Sales person:</strong> Lisa Hanabishi</p>
                    <p><strong>Phone:</strong> (555) 987-6543</p>
                    <p><strong>Email:</strong> lisa.hanabishi@abcsupplies.com</p>
                </div>
                <div class="customer">
                    <h3>Customer Information</h3>
                    <p>{{$invoice->purchaseOrder->company_name}}</p>
                    <p style="white-space:wrap;">
                        {{ $invoice->purchaseOrder->street }},
                        {{ $invoice->purchaseOrder->barangay['barangay_name'] }},
                        {{ $invoice->purchaseOrder->municipality['municipality_name'] }},
                        {{ $invoice->purchaseOrder->province['province_name'] }},
                        {{ $invoice->purchaseOrder->region['region_name'] }}
                    </p>
                    <p><strong>Contact Person:</strong> {{$invoice->purchaseOrder->receiver_name}}</p>
                    <p><strong>Phone:</strong> {{$invoice->purchaseOrder->receiver_mobile}}</p>
                </div>
            </div>

            <div class="items">
                <h3>Invoice Items</h3>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product no.</th>
                            <th>Product Name</th>
                            <th>Unit</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Discount</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoiceItems as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{$item->product_id}}</td>
                            <td>{{$item->product->name}}</td>
                            <td>{{$item->product->unit}}</td>
                            <td>{{$item->quantity}}</td>
                            <td>&#8369;{{ number_format($item->unit_price, 2) }}</td>
                            <td>{{ $item->discount ?? '—' }}</td>
                            <td>&#8369;{{ number_format($item->total_price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="totals">
                <table style="width: 300px; margin-left: auto; border-collapse: collapse; font-size: 14px;">
                    <tr>
                        <td style="padding: 6px; border: 1px solid #ccc;">Subtotal</td>
                        <td style="padding: 6px; border: 1px solid #ccc; text-align: right;">
                            &#8369;{{ number_format($invoice->subtotal, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 6px; border: 1px solid #ccc;">Tax</td>
                        <td style="padding: 6px; border: 1px solid #ccc; text-align: right;">
                            &#8369;{{ number_format($invoice->tax_amount, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 6px; border: 1px solid #ccc;">Discount</td>
                        <td style="padding: 6px; border: 1px solid #ccc; text-align: right;">
                            {{ $invoice->discount ?? '—' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 6px; border: 1px solid #ccc; font-weight: bold;">Total</td>
                        <td style="padding: 6px; border: 1px solid #ccc; text-align: right; font-weight: bold;">
                            &#8369;{{ number_format($invoice->grand_total, 2) }}
                        </td>
                    </tr>
                </table>
            </div>

            <div class="notes-signatory">
                <div class="notes">
                    <h3>Additional Notes</h3>
                    <p type="text" style="white-space: wrap; overflow: hidden; text-overflow: ellipsis; font-size: 14px;">
                        {{$invoice->notes}}
                    </p>
                </div>
                <div class="signatory">
                    <h3>Authorized by</h3>
                    <p type="text" style="font-size: 13px;">{{$invoice->purchaseOrder->approved_by ?? 'Pending'}}</p>
                    <p style="font-size: 12px; color: #666;">Name with signature of Authorized person</p>
                </div>
            </div>
        </div>
    </div>

    <a class="download-purchase-order" onclick="window.print()">
        <span class="material-symbols-outlined">download</span>
         Download PDF
    </a>
</body>
</html>
