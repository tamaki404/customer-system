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
                <h3>Order Cart</h3>
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

        @if(auth()->user()->user_type === 'Admin' || auth()->user()->user_type === 'Staff')
             <button id="openModalBtn" class="addStaffBtn">Add Product</button>
        @elseif(auth()->user()->user_type === 'Customer')
            <button class="addStaffBtn">Order cart</button>
        @endif
    </div>

    <div class="titleCount">
        <h2>Products List</h2>
    </div>


    <div class="productList" style="padding: 15px;">

            @if(isset($products) && count($products) > 0)

                    <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
                        <thead>
                            <tr style="background:#f7f7fa; ">
                                <th style="padding:10px 8px; text-align:center;">Name</th>
                                <th style="padding:10px 8px; text-align:center;">Stock</th>
                                <th style="padding:10px 8px; text-align:center;">Price</th>
                                <th style="padding:10px 8px; text-align:center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $available = $products->filter(fn($p) => $p->status === 'Available' && $p->quantity > 10);
                                $lowStock = $products->filter(fn($p) => $p->status === 'Available' && $p->quantity > 0 && $p->quantity <= 10);
                                $noStock = $products->filter(fn($p) => $p->status === 'Available' && $p->quantity == 0);
                            @endphp

                            {{-- Available --}}
                            @foreach ($available as $product)
                                <tr style="padding: 10px; height: 60px;" onclick="window.location='{{ url('/product/' . $product->id) }}'">
                                    <td>{{ $product->name }}</td>
                                    <td style="color:#4caf50;">{{ $product->quantity }}x</td>
                                    <td>₱{{ number_format($product->price, 2) }}</td>
                                    <td><span style="color:green">{{ $product->status }}</span></td>
                                    <td style="padding:10px 8px; width:10%; text-align:center;">
                                        @if(auth()->user()->user_type === 'Customer')
                                                                
                                            @if($product->quantity > 0)
                                                <button class="add-to-cart-btn" 
                                                    data-product-id="{{ $product->id }}" 
                                                    data-product-name="{{ $product->name }}" 
                                                    data-product-price="{{ $product->price }}"
                                                    data-product-stock="{{ $product->quantity }}"
                                                    style="background: #4caf50; color: #fff; border: none; border-radius: 50%; width: 32px; height: 32px; font-size: 18px; cursor: pointer;">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            @else
                                                <button disabled style="background: #ccc; color: #fff; border: none; border-radius: 50%; width: 32px; height: 32px; font-size: 18px; cursor: not-allowed;">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            {{-- Low stock --}}
                            @foreach ($lowStock as $product)
                                <tr onclick="window.location='{{ url('/product/' . $product->id) }}'">
                                    <td>{{ $product->name }}</td>
                                    <td style="color:#ffa500;">{{ $product->quantity }}x</td>
                                    <td>₱{{ number_format($product->price, 2) }}</td>
                                    <td><span style="color:orange">Almost Out</span></td>
                                    <td style="padding:10px 8px; width:10%; text-align:center;">
                                        @if(auth()->user()->user_type === 'Customer')
                                                                
                                            @if($product->quantity > 0)
                                                <button class="add-to-cart-btn" 
                                                    data-product-id="{{ $product->id }}" 
                                                    data-product-name="{{ $product->name }}" 
                                                    data-product-price="{{ $product->price }}"
                                                    data-product-stock="{{ $product->quantity }}"
                                                    style="background: #4caf50; color: #fff; border: none; border-radius: 50%; width: 32px; height: 32px; font-size: 18px; cursor: pointer;">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            @else
                                                <button disabled style="background: #ccc; color: #fff; border: none; border-radius: 50%; width: 32px; height: 32px; font-size: 18px; cursor: not-allowed;">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            {{-- No stock --}}
                            @foreach ($noStock as $product)
                                <tr onclick="window.location='{{ url('/product/' . $product->id) }}'">
                                    <td>{{ $product->name }}</td>
                                    <td style="color:#ff6b6b;">0x</td>
                                    <td>₱{{ number_format($product->price, 2) }}</td>
                                    <td>
                                        @if(auth()->user()->user_type !== 'Customer')
                                            <span style="color:red">Add stocks!</span>
                                        @else
                                            <span style="color:red">No Stocks</span>
                                        @endif
                                    </td>
                                    <td style="padding:10px 8px; width:10%; text-align:center;">
                                        @if(auth()->user()->user_type === 'Customer')
                                                                
                                            @if($product->quantity > 0)
                                                <button class="add-to-cart-btn" 
                                                    data-product-id="{{ $product->id }}" 
                                                    data-product-name="{{ $product->name }}" 
                                                    data-product-price="{{ $product->price }}"
                                                    data-product-stock="{{ $product->quantity }}"
                                                    style="background: #4caf50; color: #fff; border: none; border-radius: 50%; width: 32px; height: 32px; font-size: 18px; cursor: pointer;">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            @else
                                                <button disabled style="background: #ccc; color: #fff; border: none; border-radius: 50%; width: 32px; height: 32px; font-size: 18px; cursor: not-allowed;">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                             
                        </tbody>
                    </table>
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
    </div>

</div>

<script src="{{ asset('scripts/cart.js') }}"></script>
<script src="{{ asset('scripts/open-modal.js') }}"></script>

</body>
</html>

@endsection