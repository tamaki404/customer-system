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
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif !important; }</style>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <title>Store</title>
</head>
<body>
    <script src="{{ asset('js/fadein.js') }}"></script>

    {{-- add product modal --}}
    <div id="myModal" class="modal"  style="display:none;">
    <div class="modal-content">
        <span class="close-product-btn" style="cursor: pointer;">&times;</span>
        @if(auth()->user()->user_type === 'Admin')
            <div class="form-section">
                <h3 class="form-title" style="margin: 1px">Add New Product</h3>
                <p style="font-size: 16px;">Please ensure all information entered is accurate and complete.</p>

                <form action="/add-product" class="receipt-form" id="submitForm" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-grid">
                        <!-- Product Name -->
                        <div>
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" autocomplete="name" placeholder="Product Name" maxlength="100" required>
                            @error('name')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description">Description</label>
                            <input type="text" name="description" id="description" placeholder="Product Description" maxlength="255" required>
                            @error('description')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Product ID -->
                        <div>
                            <label for="product_id">Product ID</label>
                            <input type="text" name="product_id" id="product_id" placeholder="Unique Product ID" maxlength="255" required>
                            @error('product_id')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Quantity -->
                        <div>
                            <label for="quantity">Quantity</label>
                            <input type="number" name="quantity" id="quantity" min="0" required>
                            @error('quantity')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Unit -->
                        <div>
                            <label for="unit-selector">Unit</label> 
                            <select class="unit" name="unit" id="unit-selector" required> 
                                <option value="">-- Select Unit --</option>
                                <option value="Piece">Piece</option> 
                                <option value="Dozen">Dozen</option>
                                <option value="Pack">Pack</option>
                                <option value="Tray">Tray</option>
                                <option value="Case">Case</option> 
                            </select> 
                        </div>

                        <!-- Price -->
                        <div>
                            <label for="price">Price</label>
                            <input type="number" name="price" id="price" placeholder="0.00" min="0" step="0.01" required>
                            @error('price')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category">Category</label>
                            <select name="category" id="category" required>
                                <option value="">-- Select Category --</option>
                                <option value="Eggs">Eggs</option>
                                <option value=">Whole Chickens">Whole Chickens</option>
                                <option value="Meat & Poultry Cuts">Meat & Poultry Cuts</option>
                                <option value="Processed">Processed</option>
                                <option value="Ready Ulam">Ready Ulam</option>
                            </select>
                            @error('category')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status">Status</label>
                            <select name="status" id="status" required>
                                <option value="Available">Listed</option>
                                <option value="Unlisted">Unlisted</option>
                            </select>
                            @error('status')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Image -->
                        <div class="full">
                            <label for="image">Product Image</label>
                            <input type="file" name="image" id="image" accept="image/*">
                            @error('image')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Show all validation errors (fallback) -->
                    @if ($errors->any())
                        <div class="alert alert-danger" style="margin-top:10px;">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <button type="submit" class="submit-btn" id="submitBtn" 
                        style="color: #333; font-size: 15px; box-shadow: rgba(0, 0, 0, 0.15) 1.95px 1.95px 2.6px;">
                        Add Product
                    </button>
                </form>
            </div>
        @endif
    </div>
    </div>

    <!-- cart modal -->
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

        @if (auth()->user()->user_type === 'Admin' || auth()->user()->user_type === 'Staff')
            <div class="titleFrame">
                <form method="GET" action="" class="date-search">
                    <input type="text" name="search" style="outline:none;" value="{{ request('search') }}" placeholder="Search by Name, Product ID, Category or Status">
                    <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
                </form>

                @if(auth()->user()->user_type === 'Admin')
                    <button id="openAddProductModalBtn" class="addStaffBtn">Add Product</button>
                @endif 
            </div>

            <div class="titleCount">
                @if (auth()->user()->user_type === 'Admin' || auth()->user()->user_type === 'Staff')
                    <h2>Inventory</h2>
                    @endif
            </div>
        @endif

        {{-- admin and staff inventory status tabs --}}
        @if (auth()->user()->user_type !== 'Customer')
            @php
                $tabStatuses = [
                    'All' => null,
                    'Available' => 'Available',
                    'Unlisted' => 'Unlisted',
                    'Low stock' => 'Low stock',
                    'No stock' => 'No stock',
                ];

                $baseParams = [
                    'search' => request('search', ''),
                    'filter' => request('filter', '')
                ];
                $baseParams = array_filter($baseParams);

                $currentStatus = request('status'); 
            @endphp

            <div class="status-tabs" style="display: flex; flex-direction: row; justify-content: space-between; margin: 0;">
                {{-- admin  and staff sttaus tabs display --}}
                <div class="staff-status-tabs">
                    @foreach($tabStatuses as $label => $value)
                        @php
                            $params = $value ? array_merge($baseParams, ['status' => $value]) : $baseParams;
                            $isActive = ($value === null && empty($currentStatus)) || ($value !== null && $currentStatus === $value);
                            $count = $statusCounts[$label] ?? 0;
                        @endphp
                        <a href="{{ route('store', $params) }}" class="status-tab{{ $isActive ? ' active' : '' }}">
                            {{ $label }} ({{ $count }})
                        </a>
                    @endforeach
                </div>

                <div class="filter-container">
                    <span class="material-symbols-outlined">filter_alt</span>
                    <select id="filter"  style="color: #333">
                        <option style="color: #333" value="">-- Filter --</option>
                        <option style="color: #333" value="asc" {{ request('filter') === 'asc' ? 'selected' : '' }}>Ascending</option>
                        <option style="color: #333" value="desc" {{ request('filter') === 'desc' ? 'selected' : '' }}>Descending</option>
                        <option style="color: #333" value="new" {{ request('filter') === 'new' ? 'selected' : '' }}>Newest</option>
                        <option style="color: #333" style="color: #333"n value="old" {{ request('filter') === 'old' ? 'selected' : '' }}>Oldest</option>
                        <option style="color: #333" value="sold-most" {{ request('filter') === 'sold-most' ? 'selected' : '' }}>Sold most</option>
                        <option style="color: #333" value="sold-least" {{ request('filter') === 'sold-least' ? 'selected' : '' }}>Sold least</option>
                    </select>
                </div>
            </div>

        @elseif (auth()->user()->user_type === 'Customer')

            <div class="category-container" style="background-image: linear-gradient(to bottom, rgba(255, 222, 89, 0.8), rgba(255, 222, 89, 0.5)), url('{{ asset('assets/store_bg.jpg') }}');">

                <div class="search-cart">
                    <form method="GET" action="" class="date-search" style="background-color: #fffbee; border-radius: 15px; height: 45px;">
                        <input type="text" class="search-input"  name="search" style="outline:none; background-color: #fffbee; font-size: 14px;" value="{{ request('search') }}" placeholder="Search by Name, Product ID, Category or Status">
                        <button type="submit" class="search-btn" style="background-color: #fffbee; height: 100%;"><i class="fas fa-search"></i></button>
                    </form>
                    <button id="openCartBtn" class="addStaffBtn" style="background: #ffde59; width: 80px;">
                        <span class="material-symbols-outlined" style="color: #333; font-weight: normal;">shopping_cart</span>
                    </button>
                </div>

                <div class="category-list">
                    <style>
                        .category-list a{
                            padding: 8px 14px;
                            border-radius: 999px;
                            background: #fffbee;
                            color: #333;
                            border: 1px solid #ffeaa7;
                            text-decoration: none;
                            font-size: 14px;
                            font-weight: 600;
                            align-content: center;
                            box-shadow: rgba(0, 0, 0, 0.05) 0 2px 6px;
                            transition: all 0.2s ease;
                        }
                    </style>
                    <a href="#">
                        <img src="{{ asset('assets/categories/category_eggs.jpeg') }}" alt="Eggs" class="category_img">
                        <span>Eggs</span>
                    </a>

                    <a href="#">
                        <img src="{{ asset('assets/categories/category_whole_chickens.jpeg') }}" alt="Whole Chickens" class="category_img">
                        <span>Whole Chickens</span>
                    </a>

                    <a href="#">
                        <img src="{{ asset('assets/categories/category_cuts.jpg') }}" alt="Meat & Poultry Cuts" class="category_img">
                        <span>Meat & Poultry Cuts</span>
                    </a>

                    <a href="#">
                        <img src="{{ asset('assets/categories/category_processed.jpg') }}" alt="Processed & Value-Added" class="category_img">
                        <span>Processed & Value-Added</span>
                    </a>

                    <a href="#">
                        <img src="{{ asset('assets/categories/category_ready_ulam.jpg') }}" alt="Ready Ulam" class="category_img">
                        <span>Ready Ulam</span>
                    </a>
                </div>

            </div>

        @endif

        
        <div class="productList" style="padding: 10px">
            @if (auth()->user()->user_type === 'Admin' || auth()->user()->user_type === 'Staff')

                @if(isset($products) && count($products) > 0)

                    <table style="width:100%; border-collapse:collapse;" class="orders-table">
                        <thead style="background-color: #f9f9f9;">
                            <tr style="height: 50px; text-align: center; cursor:pointer;" style="background:#dfdfdf; text-align: center; ">
                                <th style="width: 30px; padding: 10px;  font-size: 13px">#</th> 
                                <th style="width: 120px; overflow: hidden; font-size: 13px;">Product</th>
                                <th style="width: 120px; overflow: hidden; font-size: 13px;">Price</th>
                                <th style="width: 70px; font-size: 13px;">Product ID</th>
                                <th style="width: 120px; overflow: hidden; font-size: 13px;">Category</th>
                                <th style="width: 80px; font-size: 13px;">Sold</th>
                                <th style="width: 80px; font-size: 13px;">Unit</th>
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

                                        <p style="text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{ $product->name }} </p>
                                    </td>
                                    <td style="padding:10px 8px; font-size: 13px;">₱{{ number_format($product->price, 2) }}</td>
                                    <td style="padding:10px 8px; font-size: 13px;">{{ $product->product_id }}</td>
                                    <td style="padding:10px 8px; font-size: 13px;">{{ $product->category }}</td>
                                    <td style="padding:10px 8px; font-size: 13px;">{{ $product->sold_quantity }}</td>
                                    <td style="padding:10px 8px; font-size: 13px;">{{ $product->unit }}</td>
                                    <td style="padding:10px 8px; font-size: 13px;">x{{ $product->quantity }}</td>
                                    <td style="padding:10px 8px; font-size: 13px;">

                                        @if($product->status === 'Unlisted')
                                            <span class="status-unlisted"> Unlisted</span>
                                        @elseif($product->status === 'No stock')
                                            <span class="status-noStock"> No Stock</span>
                                        @elseif($product->status === 'Low stock')
                                            <span class="status-lowStock"> Low Stock</span>
                                        @elseif($product->status === 'Available')
                                            <span class="status-available"> Available</span>
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
                            @if($product->status != "Unlisted" && $product->quantity !== 0)

                            @php
                                $dataUri = (!empty($product->image) && !empty($product->image_mime)) ? ('data:' . $product->image_mime . ';base64,' . $product->image) : null;
                                $isOut = $product->quantity == 0;
                                $isLow = !$isOut && $product->quantity <= 10;
                            @endphp
                            <div class="product-card" style="background-color: #fffbee;" onclick="window.location='{{ url('/product/' . $product->id) }}'">
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
                                    <div class="product-name" title="{{ $product->name }}">{{ $product->name }} <span style="font-size: 11px; color: #9a9a9a; text-transform: uppercase;"> {{$product->unit}}</span></div>
                                    <div class="product-price" style="flex-direction: row; display: flex; justify-content: space-between;">
                                        ₱{{ number_format($product->price, 2) }}
                                        @if($product->sold_quantity !== '0')
                                            <span class="sold-quantity" style="color: #888; font-weight: normal; font-size: 12px; align-items: center; justify-content: center; display: flex;">{{$product->sold_quantity}} sold</span>
                                        @endif
                                    </div>
                                    {{-- <div class="product-meta">
                                        <span class="stock">{{ $product->quantity }}x</span>
                                        
                                    </div> --}}
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
                            @endif
                        @endforeach

                    </div>

                    {{-- <div class="pagination-wrapper" style="margin-top: 2rem; text-align: center;">
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
                    </div> --}}
                @else
                    <div style="text-align:center; margin:2rem 0; color:#888; font-size:1.1rem;">No products found.</div>
                @endif


            @endif

        </div>

    </div>

    <script src="{{ asset('scripts/cart.js') }}"></script>
    <script src="{{ asset('js/store-modals.js') }}"></script>
    <script src="{{ asset('js/filter-products.js') }}"></script>
    <script src="{{ asset('js/store.js') }}"></script>

</body>
</html>

@endsection