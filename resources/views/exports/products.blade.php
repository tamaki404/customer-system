<table>
    <thead>
        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Status</th>
            <th>Current Stock</th>
            <th>Unit Price</th>
            <th>Total Quantity Sold</th>
            <th>Total Revenue</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
            <tr>
                <td>{{ $product->product_id }}</td>
                <td>{{ $product->product_name }}</td>
                <td>{{ $product->product_status }}</td>
                <td>{{ $product->current_stock }}</td>
                <td>{{ number_format($product->unit_price, 2) }}</td>
                <td>{{ $product->total_quantity }}</td>
                <td>{{ number_format($product->total_revenue, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>


