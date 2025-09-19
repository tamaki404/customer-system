@if($products->count())
    <table class="table table-sm">
        <tbody>
        @foreach($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category }}</td>
                <td>{{ $product->unit }}</td>
                <td>{{ $product->weight }}</td>
                <td>
                    <button type="button" class="btn btn-success btn-sm"
                        onclick="addProduct('{{ $product->product_id }}', '{{ $product->name }}', '{{ $product->category }}', '{{ $product->unit }}', '{{ $product->weight }}')">
                        Add
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <p>No products found.</p>
@endif
