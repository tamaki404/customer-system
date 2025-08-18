@extends('layout')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/store_create_order.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fadein.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />

    <title>Create an Order</title>
</head>
<body>
    <div class="create-layout">
        <div class="title">
            <a class="go-back-a" href="/purchase_order"><- Purchase order</a>
            <div class="form-steps">
                <span class="step active">Create Order</span>
                <span class="step">Edit Order</span>
                <span class="step">Address</span>
                <span class="step">Additional Info</span>
                <span class="step">Summary</span>
                <span class="step">Finalize</span>
            </div>
            <h2>Create Order</h2>
        </div>

        <div class="form-container">
            <!-- STEP 1: Add to cart -->
            <section class="products">
                <div class="number">
                    <span class="material-symbols-outlined">looks_one</span>
                </div>
                <div class="products">
                    <div class="box-description">
                        <h2>Add to cart</h2>
                        <p>Choose products here to add to cart</p>
                    </div>
                    <form method="GET" action="" class="date-search">
                        <input type="text" name="search" style="outline:none;" value="{{ request('search') }}" placeholder="Search by Name, Product ID & Status">
                        <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
                    </form>

                    <div class="products-box">
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
                        @else
                            <div style="text-align:center; margin:2rem 0; color:#888; font-size:1.1rem;">No products found.</div>
                        @endif
                    </div>
                </div>
            </section>

            <!-- STEP 2: Edit Order -->
            <section class="edit-order">
                <div class="number">
                    <span class="material-symbols-outlined">looks_two</span>
                </div>
                <h2>Edit Order</h2>
                <p>Review cart items, adjust quantities, or remove products.</p>
                <div id="cart-items">
                    <!-- dynamically filled cart items -->
                </div>
            </section>

            <!-- STEP 3: Address -->
            <section class="address-order">
                <div class="number">
                    <span class="material-symbols-outlined">looks_3</span>
                </div>
                <h2>Address</h2>
                <label>Shipping Address</label>
                <textarea name="shipping_address" rows="2"></textarea>

                <label>Billing Address</label>
                <textarea name="billing_address" rows="2"></textarea>

                <label>Contact Phone</label>
                <input type="text" name="contact_phone">
                <label>Email</label>
                <input type="email" name="contact_email">
            </section>

            <!-- STEP 4: Additional Info -->
            <section class="info-order">
                <div class="number">
                    <span class="material-symbols-outlined">looks_4</span>
                </div>
                <h2>Additional Info</h2>
                <label>Notes / Special Instructions</label>
                <textarea name="order_notes" rows="2"></textarea>

                <label>Receiver Name</label>
                <input type="text" name="receiver_name">

                <label>PO Number</label>
                <input type="text" name="po_number" required>

                <label>Reference Code / Department</label>
                <input type="text" name="reference_code">

                <label>Upload Attachment (optional)</label>
                <input type="file" name="po_attachment">
            </section>

            <!-- STEP 5: Summary -->
            <section class="summary-order">
                <div class="number">
                    <span class="material-symbols-outlined">looks_5</span>
                </div>
                <h2>Summary</h2>
                <p>Review your order before finalizing.</p>
                <div id="order-summary">
                    <!-- Order summary details -->
                </div>
            </section>

            <!-- STEP 6: Finalize -->
            <section class="finalize-order">
                <div class="number">
                    <span class="material-symbols-outlined">looks_6</span>
                </div>
                <h2>Finalize Order</h2>
                <button type="submit">Place Order</button>
            </section>
        </div>
    </div>


    <script src="{{ asset('js/purchase_order.js') }}"></script>
</body>
</html>


@endsection
