<table class="table" style="width:100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th>PO Number</th>
            <th>Company</th>
            <th>Contact Person</th>
            <th>Total Amount (₱)</th>
            <th>Paid (₱)</th>
            <th>Available Balance (₱)</th>
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
                <td>₱{{ number_format($po->grand_total, 2) }}</td>
                <td>₱{{ number_format($po->paid_amount ?? 0, 2) }}</td>
                <td>₱{{ number_format($po->available_balance ?? 0, 2) }}</td>
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


