<table>
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Store Name</th>
            <th>Status</th>
            <th>Total Quantity</th>
            <th>Total Price</th>
            <th>Updated at</th>
        </tr>
    </thead>
    <tbody>
        @php
            // Group orders by order_id for summary rows
            $grouped = $orders->groupBy('order_id');
        @endphp
        @foreach($grouped as $orderId => $items)
            @php
                $first = $items->first();
                $totalQuantity = $items->sum('quantity');
                $totalPrice = $items->sum('total_price');
            @endphp
            <tr>
                <td>{{ $orderId }}</td>
                <td>{{ optional($first->user)->store_name }}</td>
                <td>{{ $first->status }}</td>
                <td>{{ $totalQuantity }}</td>
                <td>{{ number_format($totalPrice, 2) }}</td>
                <td>{{ \Carbon\Carbon::parse($first->action_at ?? $first->updated_at)->format('Y-m-d H:i') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
