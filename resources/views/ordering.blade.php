@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/ordering.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fadein.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/open-modal.css') }}">

    <title>Ordering</title>
</head>
<body>
<script src="{{ asset('js/fadein.js') }}"></script>


{{-- add product modal --}}
<div id="myModal" class="modal">
  <div class="modal-content">
    <span class="close-btn">&times;</span>
     @if(auth()->user()->user_type === 'Admin')
        <div class="form-section">
            <h3 class="form-title"  style="margin: 1px">Add New Product</h3>
            <p style="font-size: 16px;">Please ensure all information entered is accurate and complete.</p>

            <form action="/add-product"  class="receipt-form"  method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-grid">
                    <div>
                        <label>Name</label>
                        <input type="text" name="name" placeholder="Name" required>
                    </div>
                    <div>
                        <label>Description</label>
                        <input type="text" name="description" placeholder="Product Description" id="description" required>
                        <span id="description-error" class="error-message"></span>
                    </div>
                    <div>
                        <label>Image</label>
                        <input type="file" name="image" id="image" ...>
                        <span id="image-error" class="error-message"></span>
                    </div>
                    <div>
                        <label>Quantity</label>
                        <input type="number" name="quantity" id="quantity" required>
                        <span id="quantity-error" class="error-message"></span>
                    </div>
                    <div>
                        <label>Price</label>
                        <input type="number" name="price" placeholder="Price in Rs." id="price" required>
                        <span id="price-error" class="error-message"></span>
                    </div>


                    <input type="text" name="action_by" value="{{ auth()->user()->username}}" hidden>
                    <input type="text" name="store_name" placeholder="Store Name" value="Sunny & Scramble" hidden>
                    <input type="text" name="user_type"  value="Staff" hidden>
                    <input type="text" name="acc_status"  value="Active" hidden>
            
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

<div class="startBody">


    <div class="titleFrame">

        <form method="GET" action="" class="date-search">
            <input type="text" name="search" style="outline:none;" value="{{ request('search') }}" placeholder="Search by Name or Product ID">
            <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
        </form>

        @if(auth()->user()->user_type === 'Admin')
        <button id="openModalBtn" class="addStaffBtn">Add Product</button>
        @endif
        
    </div>

    <div class="titleCount"> 
        <h2>Products List</h2> 
    </div>

    <div class="productList" style="padding: 15px;">
        @if(isset($products) && count($products) > 0)
        <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
            <thead>
                <tr style="background:#f7f7fa;">
                    <th style="padding:10px 8px; text-align:left;">Name</th>
                    <th style="padding:10px 8px; text-align:left;">Description</th>
                    <th style="padding:10px 8px; text-align:left;">Quantity</th>
                    <th style="padding:10px 8px; text-align:left;">Price</th>
                    <th style="padding:10px 8px; text-align:left;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                <tr style="border-bottom:1px solid #eee; align-items: center;">
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->description }}</td>
                    <td>{{ $product->quantity }}</td>
                    <td>₱{{ number_format($product->price, 2) }}</td>
                    <td>{{ $product->acc_status ?? 'Active' }}</td>
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


<script src="{{ asset('scripts/open-modal.js') }}"></script>
</body>
</html>


@endsection
