<table>
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Date</th>
            <th>Status</th>
            <th>Total Price</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
        <tr>
            <td>{{ $order->id }}</td>
            <td>{{ $order->created_at->format('Y-m-d') }}</td>
            <td>{{ $order->status }}</td>
            <td>{{ number_format($order->total_price, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
