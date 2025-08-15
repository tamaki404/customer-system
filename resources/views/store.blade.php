@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/store.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fadein.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/open-modal.css') }}">
    <title>Store</title>
</head>
<body>
<script src="{{ asset('js/fadein.js') }}"></script>

{{-- add product modal --}}
<div id="myModal" class="modal">
  <div class="modal-content">
    <span class="close-btn">&times;</span>
     @if(auth()->user()->user_type === 'Admin')
        <div class="form-section">
            <h3 class="form-title" style="margin: 1px">Add New Product</h3>
            <p style="font-size: 16px;">Please ensure all information entered is accurate and complete.</p>

            <form action="/add-product" class="receipt-form" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-grid">
                    <div>
                        <label>Name</label>
                        <input type="text" name="name" placeholder="Name" maxlength="100" required>
                    </div>
                    <div>
                        <label>Description</label>
                        <input type="text" name="description" placeholder="Product Description" id="description" maxlength="255" required>
                        <span id="description-error" class="error-message"></span>
                    </div>
                    <div>
                        <label>Quantity</label>
                        <input type="number" name="quantity" id="quantity" min="0" required>
                        <span id="quantity-error" class="error-message"></span>
                    </div>
                    <div>
                        <label>Price</label>
                        <input type="number" name="price" placeholder="Price." id="price" min="0" required>
                        <span id="price-error" class="error-message"></span>
                    </div>
                    <div class="full">
                        <label>Product Image</label>
                        <input type="file" name="image" accept="image/*">
                    </div>
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <button type="submit" class="submit-btn" id="submitBtn" style="color: #333; font-size: 15px; box-shadow: rgba(0, 0, 0, 0.15) 1.95px 1.95px 2.6px;">Add Product</button>
            </form>
        </div>
    @endif
  </div>
</div>

<!-- Cart Modal -->
<div id="cartModal" class="modal" style="display:none;">
    <div class="cart-content">
        <div class="header-cart">
            <span>
                <h3 class="cart-title">Order Cart</h3>
                <p>Items in your cart are reserved for 24 hours. Please complete your order before they expire.</p>
            </span>
            <p class="close-cart-btn" style="float:right; font-size:2rem; cursor:pointer; color: #ffde59;">&times;</p>
        </div>
        
        <div id="cartItemsContainer" class="cart-container">
            <p style="color:#888; margin-top: 200px;">No products in cart.</p>
        </div>
        <hr>
        <span class="total-container"><p id="cartTotal" style="margin-left: auto"></p></span>
        <button id="checkoutBtn">Checkout</button>
        <div id="checkoutMessage" style="margin-top:1rem; color:green; display:none;"></div>
        <div id="stockWarning" style="margin-top:1rem; color:orange; display:none;"></div>
    </div>
</div>

<div class="startBody">

    <div class="titleFrame">
        <form method="GET" action="" class="date-search">
            <input type="text" name="search" style="outline:none;" value="{{ request('search') }}" placeholder="Search by Name, Product ID & Status">
            <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
        </form>

        @if(auth()->user()->user_type === 'Admin')
             <button id="openModalBtn" class="addStaffBtn">Add Product</button>
        @elseif(auth()->user()->user_type === 'Customer')
            <button class="addStaffBtn">Order cart</button>
        @endif
    </div>


    <div class="titleCount">
        @if (auth()->user()->user_type === 'Admin' || auth()->user()->user_type === 'Staff')
            <h2>Inventory</h2>
        @elseif (auth()->user()->user_type === 'Customer')
            <h2>Products</h2>
        @endif
    </div>

    
    <div class="productList">
        @if (auth()->user()->user_type === 'Admin')

            @if(isset($products) && count($products) > 0)
                <table style="width:100%; border-collapse:collapse;" class="orders-table">
                    <thead style="background-color: #f9f9f9;">
                        <tr style="height: 50px; text-align: center; cursor:pointer;" style="background:#dfdfdf; text-align: center; ">
                            <th style="width: 30px; padding: 10px;  font-size: 13px">#</th> 
                            <th style="width: 120px; overflow: hidden; font-size: 13px;">Product</th>
                            <th style="width: 120px; overflow: hidden; font-size: 13px;">Price</th>
                            <th style="width: 70px; font-size: 13px;">Product ID</th>
                            <th style="width: 80px; font-size: 13px;">Sold</th>
                            <th style="width: 80px; font-size: 13px;">Available</th>
                            <th style="width: 100px; font-size: 13px;">Status</th>
                        </tr>
                    </thead>
                <tbody>
                    
                    @foreach ($products as $index => $product)
                    
                        <tr style="text-align: center;" onclick="window.location='{{ url('/product/' . $product->id) }}'">
                            <td style="padding:10px 8px; font-size: 13px;">{{ $index + 1 }}</td>
                            @php
                                $dataUri = (!empty($product->image) && !empty($product->image_mime)) ? ('data:' . $product->image_mime . ';base64,' . $product->image) : null;
                                
                            @endphp
                            <td style=" padding:10px 8px; gap: 10px; font-size: 13px; display: flex; flex-direction: row; align-items: center;"> 
                                @if($dataUri)
                                <img src="{{ $dataUri }}" alt="{{ $product->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                @else
                                    <div class="thumb-placeholder" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">No Image</div>
                                @endif

                                <p style="text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{ $product->name }}</p>
                            </td>
                            <td style="padding:10px 8px; font-size: 13px;">₱{{ number_format($product->price, 2) }}</td>
                            <td style="padding:10px 8px; font-size: 13px;">{{ $product->id }}</td>
                            <td style="padding:10px 8px; font-size: 13px;">{{ $product->sold_quantity }}</td>
                            <td style="padding:10px 8px; font-size: 13px;">{{ $product->quantity }}</td>
                            <td style="padding:10px 8px; font-size: 13px;">

                                @if($product->status === 'Unlisted')
                                    <span class="status-unlisted">● Unlisted</span>
                                @elseif($product->quantity === 0)
                                    <span class="status-noStock">● Out of Stock</span>
                                @elseif($product->quantity < 5)
                                    <span class="status-lowStock">● Low Stock</span>
                                @elseif($product->quantity > 0)
                                    <span class="status-available">● Available</span>
                                @endif

                            </td>
                        </tr>
                    @endforeach
                </tbody>

            @else
                <p style="text-align:center; margin:0; width:100%; line-height:500px; font-size: 15px; color: #888">No orders found</p>
            @endif

        @elseif(auth()->user()->user_type === 'Customer')

            @if(isset($products) && count($products) > 0)
                <div class="product-grid">
                    @foreach ($products as $product)
                        @php
                            $dataUri = (!empty($product->image) && !empty($product->image_mime)) ? ('data:' . $product->image_mime . ';base64,' . $product->image) : null;
                            $isOut = $product->quantity == 0;
                            $isLow = !$isOut && $product->quantity <= 10;
                        @endphp
                        <div class="product-card" onclick="window.location='{{ url('/product/' . $product->id) }}'">
                            <div class="product-thumb">
                                @if($dataUri)
                                    <img src="{{ $dataUri }}" alt="{{ $product->name }}">
                                @else
                                    <div class="thumb-placeholder">No Image</div>
                                @endif
                                @if($isOut)
                                    <span class="badge badge-out">Out of stock</span>
                                @elseif($isLow)
                                    <span class="badge badge-low">Low stock</span>
                                @else
                                    <span class="badge badge-available">Available</span>
                                @endif
                            </div>
                            <div class="product-body">
                                <div class="product-name" title="{{ $product->name }}">{{ $product->name }}</div>
                                <div class="product-price">₱{{ number_format($product->price, 2) }}</div>
                                <div class="product-meta">
                                    <span class="stock">{{ $product->quantity }}x</span>
                                </div>
                            </div>
                            @if(auth()->user()->user_type === 'Customer')
                                <div class="product-actions" onclick="event.stopPropagation();">
                                    @if($product->quantity > 0)
                                        <button class="add-to-cart-btn"
                                            data-product-id="{{ $product->id }}"
                                            data-product-name="{{ $product->name }}"
                                            data-product-price="{{ $product->price }}"
                                            data-product-stock="{{ $product->quantity }}">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    @else
                                        <button class="add-to-cart-btn" disabled>
                                            <i class="fa fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="pagination-wrapper" style="margin-top: 2rem; text-align: center;">
                    @if($products->hasPages())
                        <div class="pagination-controls" style="display: flex; justify-content: center; align-items: center; gap: 1rem;">
                            @if($products->onFirstPage())
                                <span style="color: #ccc; cursor: not-allowed;">Previous</span>
                            @else
                                <a href="{{ $products->previousPageUrl() }}" style="color: #1976d2; text-decoration: none; padding: 0.5rem 1rem; border: 1px solid #1976d2; border-radius: 4px;">Previous</a>
                            @endif
                            <span>Page {{ $products->currentPage() }} of {{ $products->lastPage() }}</span>
                            @if($products->hasMorePages())
                                <a href="{{ $products->nextPageUrl() }}" style="color: #1976d2; text-decoration: none; padding: 0.5rem 1rem; border: 1px solid #1976d2; border-radius: 4px;">Next</a>
                            @else
                                <span style="color: #ccc; cursor: not-allowed;">Next</span>
                            @endif
                        </div>
                    @endif
                </div>
            @else
                <div style="text-align:center; margin:2rem 0; color:#888; font-size:1.1rem;">No products found.</div>
            @endif


        @endif

    </div>

</div>

<script src="{{ asset('scripts/cart.js') }}"></script>
<script src="{{ asset('scripts/open-modal.js') }}"></script>

</body>
</html>

@endsection