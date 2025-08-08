<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Product Performance Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
        h2 { text-align: center; }
        .status-title { background: #ddd; padding: 5px; margin-top: 15px; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Product Performance Report</h2>
    <p>Period: {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Status</th>
                    <th>Unit Price</th>
                    <th>Quantity Sold</th>
                    <th>Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>{{ $product->product_name }}</td>
                    @if($product->current_stock === 0)
                        <td style="color: red">No stocks</td>
                    @elseif($product->current_stock < 5)
                        <td style="color: orange">Low on stocks</td>
                    @else
                        @if ($product->product_status === 'Available')
                            <td style="color: green">{{ $product->product_status }}</td>
                        @elseif ($product->product_status === 'Unlisted')
                            <td style="color: grey">{{ $product->product_status }}</td>
                        @endif
                    @endif
                
                    <td>&#8369;{{ number_format($product->unit_price, 2) }}</td>
                    <td>x{{ $product->total_quantity }}</td>
                    <td>&#8369;{{ number_format($product->total_revenue, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

</body>
</html>
