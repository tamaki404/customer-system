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
    <span style="display: flex; flex-direction: row; width: 100%; justify-content: space-between;"><h2>Product </h2> <p style="font-size: 15px; font-weight: normal">Added on <strong>{{ $product->created_at -> format ('F j, Y') }}</strong></p></span>
    <div class="backLink">
        <a href="{{ url('/store') }}">&larr; Back to Store</a>
    </div>
    <div class="mainBlock">
        <div class="product-media">
            @php
                $hasImg = !empty($product->image) && !empty($product->image_mime);
                $dataUri = $hasImg ? ('data:' . $product->image_mime . ';base64,' . $product->image) : null;
            @endphp
            @if($hasImg)
                <img src="{{ $dataUri }}" alt="{{ $product->name }}" class="product-hero">
            @else
                <div class="product-hero placeholder">No Image</div>
            @endif
        </div>

        <div class="product-info" style="gap: 14px; display: flex; flex-direction: column; width: 100%;">
            <div style="display:flex; justify-content: space-between; align-items: baseline; gap: 12px;">
                <h1 class="product-name" style="margin:0;">{{ $product->name }}</h1>
                <h2 style="color: #2e7d32; font-size: 28px; margin:0;">₱{{ number_format($product->price, 2) }}</h2>
            </div>
            <p class="product-description">{{ $product->description }}</p>
            <div style="display:flex; gap: 12px; align-items:center;">
                <span class="quantity-span" style="background:#f8f9fa; border-radius: 8px;">Quantity: <p>{{ $product->quantity }}</p></span>
                <span class="status-span" style="background:#f8f9fa; border-radius: 8px;">Status:
                    @if ($product->quantity == 0)
                        <span style="color: #b71c1c">Out of stock</span>
                    @elseif ($product->quantity <= 10)
                        <span style="color: #ef6c00">Low on stocks</span>
                    @else
                        <span style="color: #2e7d32">{{ $product->status ?? 'Available' }}</span>
                    @endif
                </span>
            </div>
        </div>

        @if (auth()->user()->user_type != 'Customer')
            <div class="modify-block">
                {{-- <form action="{{ url('/product/add-stocks/' . $product->id) }}" method="POST">
                    @csrf
                </form> --}}
                <button class="edit-btn" id="openStockModal" style="width: 190px; background: linear-gradient(135deg, #4caf50, #45a049);"><i class="fa-regular fa-square-plus"></i> Add stocks</button>

               @if ($product->status === 'Listed')
                    <form action="{{ url('/product/unlist/' . $product->id) }}" method="POST">
                        @csrf
                        <button class="unlist-btn" type="submit" style="width: 120px; background-color: rgba(128, 128, 128, 0.665);"><i class="fa-solid fa-minus"></i> Unlist</button>
                    </form>
                @elseif($product->status === 'Unlisted')
                    <form action="{{ url('/product/list/' . $product->id) }}" method="POST">
                        @csrf
                        <button class="unlist-btn" type="submit" style="width: 120px; background-color: rgba(128, 128, 128, 0.665);"><i class="fa-solid fa-plus"></i> List</button>
                    </form>
                @else
                    <form action="{{ url('/product/unlist/' . $product->id) }}" method="POST">
                        @csrf
                        <button class="unlist-btn" type="submit" style="width: 120px; background-color: rgba(128, 128, 128, 0.665);"><i class="fa-solid fa-minus"></i> Unlist</button>
                    </form>                
                @endif

                <!-- Delete Button triggers modal -->
                @if (auth()->user()->user_type != 'Admin')
                    <button class="delete-btn" id="openDeleteModalBtn" style="width: auto; background-color: rgba(255, 0, 0, 0.281);" disabled><i class="fa-regular fa-circle-xmark"></i> Can't delete</button>
                @else
                    <button class="delete-btn" id="openDeleteModalBtn" style="width: 120px; background-color: rgba(255, 0, 0, 0.664);"><i class="fa-solid fa-trash-can"></i> Delete</button>
                @endif
                <!-- Delete Modal -->
                <div id="deleteModal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.4); justify-content:center; align-items:center;">
                    <div class="formBlock" style="background:#fff; padding:2rem; border-top: #e53935 4px solid; border-radius:8px; min-width:320px; max-width:90vw; position:relative; text-align:center;">
                        <span id="closeDeleteModalBtn" style="position:absolute; top:10px; right:20px; font-size:2rem; cursor:pointer;">&times;</span>

                        <h2 style="font-size: 25px; font-weight: bold; margin: 5px 0;">Confirm Delete</h2>
                        <p>Are you sure you want to delete this product? This action cannot be undone.</p>
                        <form id="deleteProductForm" action="{{ url('/product/delete/' . $product->id) }}" method="POST" style="margin-top:1.5rem;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" id="delete" style="">Yes, Delete</button>
                            <button type="button" id="cancelDeleteBtn" style="margin-left:1rem; background:#aaa; color:#fff; padding:0.5rem 1.5rem; border:none; border-radius:4px;">Cancel</button>
                        </form>
                    </div>
                </div>

                <!-- Stock Modal -->
                <div id="stockModal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.4); justify-content:center; align-items:center;">
                    <div class="formBlock" style="background:#fff; padding:2rem; border-top: green 4px solid; border-radius:8px; min-width:320px; max-width:90vw; position:relative; text-align:center;">
                        <span id="closeStockModal" style="position:absolute; top:10px; right:20px; font-size:2rem; cursor:pointer;">&times;</span>

                        <h2 style="font-size: 25px; font-weight: bold; margin: 5px 0;">Add Stocks</h2>
                        <p>You can add stocks here to keep the product available</p>
                        <form id="addStockForm" class="addStockForm" action="{{ route('products.addStock', $product->id) }}" method="POST" style="margin-top:1.5rem;">
                            @csrf
                            <input type="int" name="addedStock" maxlength="3" required>
                            <button type="submit" class="add-stock-btn" style="">Add stock</button>
                        </form>
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function() {

                    const openStock = document.getElementById('openStockModal');
                    const stockModal = document.getElementById('stockModal');
                    const closeStock = document.getElementById('closeStockModal');
                    const cancelStock = document.getElementById('cancelStockModal');

                    const openBtn = document.getElementById('openDeleteModalBtn');
                    const modal = document.getElementById('deleteModal');
                    const closeBtn = document.getElementById('closeDeleteModalBtn');
                    const cancelBtn = document.getElementById('cancelDeleteBtn');

                    if (openBtn && modal && closeBtn && cancelBtn && openStock) {

                        openStock.onclick = () => { stockModal.style.display = 'flex'; };
                        closeStock.onclick = () => { stockModal.style.display = 'none'; };
                        cancelStock.onclick = () => { stockModal.style.display = 'none'; };

                        openBtn.onclick = () => { modal.style.display = 'flex'; };
                        closeBtn.onclick = () => { modal.style.display = 'none'; };
                        cancelBtn.onclick = () => { modal.style.display = 'none'; };

                        window.onclick = function(event) {
                            if (event.target === modal) { modal.style.display = 'none'; }
                            if (event.target === stockModal) { stockModal.style.display = 'none'; }
                        };
                    }
                });
                </script>
            </div>          
        @endif
    </div>


</div>
</body>
</html>
@endsection
