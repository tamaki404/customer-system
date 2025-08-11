<table>
    <thead>
        <tr>
            <th colspan="7" style="text-align:left;">Receipts Report: {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</th>
        </tr>
        <tr>
            <th>Receipt #</th>
            <th>Store</th>
            <th>Amount</th>
            <th>Purchase Date</th>
            <th>Status</th>
            <th>Verified By</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($receipts as $r)
            <tr>
                <td>{{ $r->receipt_number }}</td>
                <td>{{ $r->store_name }}</td>
                <td>{{ number_format($r->total_amount, 2) }}</td>
                <td>{{ \Carbon\Carbon::parse($r->purchase_date)->format('Y-m-d') }}</td>
                <td>{{ $r->status }}</td>
                <td>{{ $r->verified_by }}</td>
                <td>{{ \Carbon\Carbon::parse($r->created_at)->format('Y-m-d H:i') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>


