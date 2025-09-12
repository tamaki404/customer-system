<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Purchase Orders</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #f3f4f6; }
        .text-right { text-align: right; }
        .meta { margin-bottom: 10px; font-size: 12px; }
    </style>
    </head>
<body>
    <div class="meta">Period: {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</div>
    <table>
        <thead>
            <tr>
                <th>PO Number</th>
                <th>Company</th>
                <th>Contact Person</th>
                <th class="text-right">Total Amount (₱)</th>
                <th class="text-right">Paid (₱)</th>
                <th class="text-right">Available Balance (₱)</th>
                <th>Payment Status</th>
                <th>Status</th>
                <th>Order Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchaseOrders as $po)
                <tr>
                    <td>{{ $po->po_id }}</td>
                    <td>{{ $po->company_name }}</td>
                    <td>{{ $po->receiver_name }}</td>
                    <td class="text-right">{{ number_format($po->grand_total, 2) }}</td>
                    <td class="text-right">{{ number_format($po->paid_amount ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($po->available_balance ?? 0, 2) }}</td>
                    <td>{{ $po->payment_status ?? 'Unpaid' }}</td>
                    <td>{{ $po->status }}</td>
                    <td>{{ $po->order_date->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center;">No purchase orders for this period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

