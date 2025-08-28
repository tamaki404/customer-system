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

{{-- edit product modal --}}
<div id="modalmodifyProduct" style="display:none; position:fixed; z-index:9999; left:0; overflow: hidden; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.4); justify-content:center; align-items:center;">
    <div class="formBlock" style="">
        <span id="closeModifyProduct" style="position:absolute; top:10px; right:20px; font-size:2rem; cursor:pointer;">&times;</span>

        <div class="product-header" style="text-align: center; margin-bottom: 1.5rem;">

            <h2 style="font-size: 20px; font-weight: bold; margin: 5px 0; color: #333;">
                Edit product
            </h2>
        </div>

        <form id="modifyProductForm" class="modifyProductForm" action="{{ route('products.editProduct', $product->id) }}" method="POST" style="margin-top:1.5rem;">
            @csrf
            <div class="form-grid">
                <!-- Name -->
                <div>
                    <label for="name">Name</label>
                    <input  type="text" name="name" id="name" value="{{$product->name}}" placeholder="Product Name" maxlength="100" required>
                    {{-- @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror --}}
                </div>
                <!-- Description -->
                <div style="width: 100%;">
                    <label for="description">Description</label>
                    <textarea 
                        name="description" 
                        id="description" 
                        placeholder="Product Description" 
                        maxlength="500" 
                        required
                        style="width: 100%; height: 100px; resize: vertical; padding: 8px; box-sizing: border-box;"
                    >{{ $product->description }}</textarea>
                    @error('description')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <!-- Price -->
                <div>
                    <label for="price">Price</label>
                    <input type="number" name="price" id="price" value="{{$product->price}}" placeholder="0.00" min="0" step="0.01" required>
                    @error('price')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Product ID -->
                <div>
                    <label for="product_id">Product ID</label>
                    <input type="string" name="product_id" id="product_id" value="{{$product->product_id}}" placeholder="Product ID" min="0" maxlength="255" required>
                    @error('product_id')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Unit -->
                <div>
                    <label for="unit">Unit</label>
                    <select name="unit" id="unit-selector" required> 
                        <option value="">-- Select Unit --</option>
                        <option value="Piece" {{ $product->unit == 'Piece' ? 'selected' : '' }}>Piece</option> 
                        <option value="Dozen" {{ $product->unit == 'Dozen' ? 'selected' : '' }}>Dozen</option>
                        <option value="Pack" {{ $product->unit == 'Pack' ? 'selected' : '' }}>Pack</option>
                        <option value="Tray" {{ $product->unit == 'Tray' ? 'selected' : '' }}>Tray</option>
                        <option value="Case" {{ $product->unit == 'Case' ? 'selected' : '' }}>Case</option> 
                    </select>

                    @error('product_id')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <!-- Category -->
                <div>
                    <label for="category">Category </label>
                    <select name="category" id="category" required>
                        <option value="">-- Select Category --</option>
                        <option value="Eggs" {{ $product->category == 'Eggs' ? 'selected' : '' }}>Eggs</option>
                        <option value="Whole Chickens" {{ $product->category == 'Whole Chickens' ? 'selected' : '' }}>Whole Chickens</option>
                        <option value="Meat & Poultry Cuts" {{ $product->category == 'Meat & Poultry Cuts' ? 'selected' : '' }}>Meat & Poultry Cuts</option>
                        <option value="Processed" {{ $product->category == 'Processed' ? 'selected' : '' }}>Processed</option>
                        <option value="Ready Ulam" {{ $product->category == 'Ready Ulam' ? 'selected' : '' }}>Ready Ulam</option>

                    </select>

                    @error('category')
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
        <p style="margin: 5px; font-size: 13px; color: #666;;">Kindly double check the changed details before submitting</p>
        <button type="submit" class="submit-btn" id="submitBtn" 
            style="color: #333; font-size: 15px; box-shadow: rgba(0, 0, 0, 0.15) 1.95px 1.95px 2.6px;">
            Save
        </button>        
        </form>
    </div>
</div>

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

            <div class="category-div">
                <p class="category-label">Category: </p>
                <p class="category">{{$product->category}}</p>
            </div>      
                                                                                                                                                                                           


        </div>


        @if (auth()->user()->user_type != 'Customer')
            <div class="modify-block">
                {{-- <form action="{{ url('/product/add-stocks/' . $product->id) }}" method="POST">
                    @csrf
                </form> --}}
                <button class="edit-btn openStockBtn" data-product-id="{{ $product->id }}" style="width: 190px; background: linear-gradient(135deg, #4caf50, #45a049);">
                    <i class="fa-regular fa-square-plus"></i> Add stocks
                </button>

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

                <button class="edit-btn openModifyProduct" data-product-id="{{ $product->id }}" 
                    style="width: 190px; background: linear-gradient(135deg, #ffde59, #ffde59); color: #333;">
                    <i class="fa-regular fa-square-plus"></i> Edit Product
                </button>


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
                        <form id="addStockForm" class="addStockForm" action="{{ route('products.addStock', $product->id) }}" method="POST" style="margin-top:1.5rem;">
                            @csrf
                            <input type="number" name="addedStock" min="3" placeholder="000">
                            <button type="submit" id="submitBtn" class="add-stock-btn" style="">Add stock</button>
                        </form>
                    </div>
                </div>

                
            </div>          
        @endif
    </div>
</div>

<script src="{{ asset('scripts/product_view.js') }}"></script>

</body>
</html>
@endsection
