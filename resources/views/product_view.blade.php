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
    <a class="go-back-a" href="/store"><- Store</a>
        <style>
            .go-back-a{
                font-size: 15px;
                color: #f8912a;
                text-decoration: none;
                width: 80px;
            }
            .go-back-a:hover{
                color: #cd741c;
            }
        </style>

    <span><h2 style="font-size: 25px; font-weight: bold; color: #333;">Product View</h2> </span>

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

        <div class="product-info">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h1 class="product-name">{{ $product->name }} ({{ $product->unit }})</h1>
                <h2 class="product-value">₱{{ number_format($product->price, 2) }}</h2>
            </div>

            <span class="product-quantity">
                <strong>Stocks:</strong> x{{ $product->quantity }}
            </span>

            <p class="product-description">{{ $product->description }}</p>

            @if($soldQuantity > 0)
                <p style="color:#666; font-size:14px;">{{ $soldQuantity }} sold</p>
            @endif
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
                {{-- @if (auth()->user()->user_type != 'Admin')
                    <button class="delete-btn" id="openDeleteModalBtn" style="width: auto; background-color: rgba(255, 0, 0, 0.281);" disabled><i class="fa-regular fa-circle-xmark"></i> Can't delete</button>
                @else
                    <button class="delete-btn" id="openDeleteModalBtn" style="width: 120px; background-color: rgba(255, 0, 0, 0.664);"><i class="fa-solid fa-trash-can"></i> Delete</button>
                @endif --}}
                <!-- Delete Modal -->
                <div id="deleteModal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.4); justify-content:center; align-items:center;">
                    <div class="formBlock" style="background:#fff; padding:2rem; border-top: #e53935 4px solid; border-radius:8px; min-width:320px; max-width:90vw; position:relative; text-align:center;">
                        <span id="closeDeleteModalBtn" style="position:absolute; top:10px; right:20px; font-size:2rem; cursor:pointer;">&times;</span>

                        <h2 style="font-size: 25px; font-weight: bold; margin: 5px 0;">Confirm Delete</h2>
                        <p>Are you sure you want to delete this product? This action cannot be undone.</p>
                        <form id="deleteProductForm" action="{{ route('products.deleteProduct', $product->id) }}" method="POST" style="margin-top:1.5rem;">
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
                        <form id="addStockForm" id="submitForm" class="addStockForm" action="{{ route('products.addStock', $product->id) }}" method="POST" style="margin-top:1.5rem;">
                            @csrf
                            <input type="int" name="addedStock" maxlength="3" required>
                            <button type="submit" id="submitBtn" class="add-stock-btn" style="">Add stock</button>
                        </form>
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function() {

                    const openStock = document.getElementById('openStockModal');
                    const stockModal = document.getElementById('stockModal');
                    const closeStock = document.getElementById('closeStockModal');

                    const openBtn = document.getElementById('openDeleteModalBtn');
                    const modal = document.getElementById('deleteModal');
                    const closeBtn = document.getElementById('closeDeleteModalBtn');
                    const cancelBtn = document.getElementById('cancelDeleteBtn');

                    if (openBtn && modal && closeBtn && openStock) {

                        openStock.onclick = () => { stockModal.style.display = 'flex'; };
                        closeStock.onclick = () => { stockModal.style.display = 'none'; };

                        openBtn.onclick = () => { modal.style.display = 'flex'; };
                        closeBtn.onclick = () => { modal.style.display = 'none'; };

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
<script src="{{ asset('js/disbaleBtn.js') }}"></script>

</body>
</html>
@endsection
