<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receipts Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { margin-bottom: 8px; }
        .range { margin-bottom: 12px; color: #555; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #f3f3f3; }
        .right { text-align: right; }
    </style>
    </head>
<body>
    <h2>Receipts Report</h2>
    <div class="range">Period: {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</div>

    <table>
        <thead>
            <tr>
                <th>Receipt #</th>
                <th>Store</th>
                <th class="right">Amount</th>
                <th>Purchase Date</th>
                <th>Status</th>
                <th>Verified By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($receipts as $r)
            <tr>
                <td>{{ $r->receipt_number }}</td>
                <td>{{ $r->store_name }}</td>
                <td class="right">{{ number_format($r->total_amount, 2) }}</td>
                <td>{{ \Carbon\Carbon::parse($r->purchase_date)->format('Y-m-d') }}</td>
                <td>{{ $r->status }}</td>
                <td>{{ $r->verified_by }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>


