@extends('layouts.main')

@section('content')
    <h2>Select Products for Purchase Order #{{ $po->po_id }}</h2>
    <form action="{{ route('purchaseorders.storeOrderItems', $po->po_id) }}" method="POST">
        @csrf
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td>
                        <input type="checkbox" name="products[{{ $product->id }}][selected]" value="1">
                        {{ $product->name }}
                    </td>
                    <td>
                        <input type="number" name="products[{{ $product->id }}][quantity]" min="0" value="0">
                    </td>
                    <td>
                        <input type="text" name="products[{{ $product->id }}][price]" value="{{ $product->price ?? '' }}">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit">Submit Order Items</button>
    </form>
@endsection