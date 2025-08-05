@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/product_view.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fadein.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <title>Product Details</title>
</head>
<body>
<div class="productDetailFrame">
    <span style="display: flex; flex-direction: row; width: 100%; justify-content: space-between;"><h2>Product </h2> <p>{{ $product->created_at -> format ('F j, Y, g: i A') }}</p></span>
    <div class="backLink">
        <a href="{{ url('/ordering') }}">&larr; Back to Products List</a>
    </div>
    <div class="mainBlock">
        <div class="product-info" style="gap: 10px; display: flex; flex-direction: column; width: 100%;">
            <span><h1 class="product-name">{{ $product->name }}</h1> <h2 style="color: green; font-size: 25px;">₱{{ number_format($product->price, 2) }}</h2></span>
            <p class="product-description">{{ $product->description }}</p>
            <hr>
            <span class="quantity-span">Quantity:<p>{{ $product->quantity }}</p></span>
            <hr>
            <span class="status-span">Status:<p>{{ $product->status ?? 'Available' }}</p></span>
        </div>
        <div class="modify-block">

            <button class="edit-btn" style="width: 170px; background: linear-gradient(135deg, #4caf50, #45a049);"><i class="fa-regular fa-pen-to-square"></i> Edit</button>
           @if ($product->status === 'Listed')
                <form action="{{ url('/product/unlist/' . $product->id) }}" method="POST">
                    @csrf
                    <button class="unlist-btn" type="submit" style="width: 120px; background-color: rgba(128, 128, 128, 0.665);"><i class="fa-solid fa-minus"></i> Unlist</button>
                </form>
            @else
                <form action="{{ url('/product/list/' . $product->id) }}" method="POST">
                    @csrf
                    <button class="list-btn" type="submit" style="width: 120px; background-color: rgba(128, 128, 128, 0.665);"><i class="fa-solid fa-plus"></i> List</button>
                </form>
            @endif

            <button class="delete-btn" style="width: 120px; background-color: rgba(255, 0, 0, 0.664);"><i class="fa-solid fa-trash-can"></i> Delete</button>
        </div>
    </div>


</div>
</body>
</html>
@endsection
