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


<style>
.table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
    color: #333;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    overflow: hidden; /* keeps rounded corners */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

/* Table headers */
.table thead {
    background-color: #f9f9f9;
    color: #444;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
}

.table th, 
.table td {
    padding: 10px 12px;
    border-bottom: 1px solid #eee;
    text-align: left;
}

/* Zebra striping */
.table tbody tr:nth-child(even) {
    background-color: #fafafa;
}

/* Hover effect */
.table tbody tr:hover {
    background-color: #f1f7ff;
    transition: background-color 0.2s ease;
}

/* Optional: highlight first column */
.table td:first-child {
    font-weight: 500;
    color: #222;
}

</style>


