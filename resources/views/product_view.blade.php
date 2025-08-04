@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/ordering.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fadein.css') }}">
    <title>Product Details</title>
</head>
<body>
<div class="productDetailFrame" style="max-width:600px;margin:2rem auto;padding:2rem;background:#fff;border-radius:12px;box-shadow:0 2px 8px #eee;">
    <h2 style="margin-bottom:1rem;">Product Details</h2>
    <table style="width:100%;font-size:1.1rem;">
        <tr><th style="text-align:left;width:150px;">Name:</th><td>{{ $product->name }}</td></tr>
        <tr><th>Description:</th><td>{{ $product->description }}</td></tr>
        <tr><th>Quantity:</th><td>{{ $product->quantity }}</td></tr>
        <tr><th>Price:</th><td>₱{{ number_format($product->price, 2) }}</td></tr>
        <tr><th>Status:</th><td>{{ $product->acc_status ?? 'Active' }}</td></tr>
    </table>
    <div style="margin-top:2rem;">
        <a href="{{ url('/ordering') }}" style="color:#1976d2;text-decoration:underline;">&larr; Back to Products List</a>
    </div>
</div>
</body>
</html>
@endsection
