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
        @foreach($orders as $order)
            <tr>
                <td>{{ $order->order_id }}</td>
                <td>{{ $order->store_name ?? optional($order->user)->store_name }}</td>
                <td>{{ $order->status }}</td>
                <td>{{ $order->total_quantity ?? $order->quantity }}</td>
                <td>{{ number_format($order->total_price, 2) }}</td>
                <td>{{ \Carbon\Carbon::parse($order->action_at ?? $order->updated_at)->format('Y-m-d H:i') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>


