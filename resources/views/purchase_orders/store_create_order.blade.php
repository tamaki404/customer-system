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
        <div id="message-area" style="color: red; margin-bottom: 10px;"></div>

        <div class="title">
            <a class="go-back-a" href="/purchase_order"><- Purchase order</a>
           <div class="checkout-steps">
                @php
                    $steps = [
                        1 => 'Add to Cart',
                        2 => 'Modify Order',
                        3 => 'Shipping Address',
                        4 => 'Additional Info',
                        5 => 'Order Summary',
                        6 => 'Finalize Order',
                    ];
                    $currentStep = 1;
                @endphp

                <ul class="steps-list">
                    @foreach($steps as $stepNumber => $stepName)
                        <li class="step-item {{ $currentStep === $stepNumber ? 'active' : '' }}">
                            <span class="step-number">{{ $stepNumber }}</span>
                            <span class="step-name">{{ $stepName }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- <h2>Create Order</h2> --}}
        </div>

        <!-- Display Validation Errors -->
        @if ($errors->any())
            <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 10px; margin: 10px 0; border: 1px solid #f5c6cb; border-radius: 5px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="order-form" method="POST" action="{{ route('purchase_orders.store') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="form-container" style="overflow: hidden">
                <!-- STEP 1: Add to cart -->
                <section class="products step-section" data-step="3" style="z-index: 1">
          
                    <div class="products" style="z-index: 1">
                        <div class="box-description" >
                            <div class="box-description-inner">
                                <h2>Add to cart</h2>
                                <p>Choose products here to add to cart</p>
                            </div>


                            <div class="date-search">
                                <form method="GET" action="">
                                    <input type="text" name="search" style="outline:none;" value="{{ request('search') }}" placeholder="Search by Name, Product ID & Status">
                                    <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
                                </form>
                             </div>
                        </div>
                        

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
                                                        <button type="button" class="add-to-cart-btn"
                                                            data-product-id="{{ $product->id }}"
                                                            data-product-name="{{ $product->name }}"
                                                            data-product-price="{{ $product->price }}"
                                                            data-product-stock="{{ $product->quantity }}">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="add-to-cart-btn" disabled>
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
                    <div class="step-nav">
                        <button type="button" class="next-btn">Next</button>
                    </div>
                </section>

                <!-- STEP 2: Edit Order -->
                <section class="edit-order step-section" data-step="2" style="overflow: hidden">

                  
                    <div class="edit-orders" >
                        <h2>Edit Order</h2>
                        <p>Review cart items, adjust quantities, or remove products.</p>
                        <div id="cart-items">
                        </div>
                    </div>
                    <div class="step-nav">
                        <button type="button" class="prev-btn">Previous</button>
                        <button type="button" class="next-btn">Next</button>
                    </div>
                </section>

                <!-- STEP 3: Address -->
                <section class="address-order step-section" data-step="1" style="display:none;">
                    <div class="address-form">
                        <h2>Where are you sending to?</h2>
                        <p>Enter the address where you want your order delivered.</p>

                        <div class="addresses">
                            <div class="add-form">
                                <label for="postal_code">Postal Code<span style="color: red;">*</span></label>
                                <input type="text" name="postal_code" id="postal_code" required value="{{ old('postal_code') }}">
                            </div>
                            <div class="add-form">
                                <label for="region">Region</label>
                                <select id="region" style="width: 200px" name="region">
                                    <option value="">-- Select Region --</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->region_id }}">{{ $region->region_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="add-form">
                                <label for="province">Province</label>
                                <select id="province" style="width: 200px" name="province"></select>
                            </div>

                            <div class="add-form">
                                <label for="municipality">Municipality</label>
                                <select style="width: 220px" id="municipality" name="municipality"></select>
                            </div>

                            <div class="add-form">
                                <label for="barangay">Barangay</label>
                                <select id="barangay" style="width: 230px" name="barangay"></select>
                            </div>

                            <div class="add-form">
                                <label for="street">Street Name, Building, House No. <span style="color: red;">*</span></label>
                                <input type="text" style="width: 350px" name="street" id="street" required value="{{ old('street') }}">
                            </div>

                            <div class="add-form">
                                <label for="company_name">Company Name <span style="color: red;">*</span></label>
                                <input type="text" name="company_name" style="width: 300px" id="company_name" required value="{{ auth()->user()->store_name ?? old('contact_phone') }}" readonly>
                            </div>

                          

                            <div class="add-form">
                                <label>Billing Address <span style="color: red;">*</span></label>
                                <input name="billing_address" style="width: 300px" required>{{ old('billing_address') }}
                            </div>

                        </div>

                        <div class="contact-form">
                            <h3>Contact Information</h3>
                            <div class="add-form">
                                <label>Mobile <span style="color: red;">*</span></label>
                                <input type="text" style="width: 170px" name="contact_phone" required value="{{ auth()->user()->mobile ?? old('contact_phone') }}" readonly>
                            </div>


                            <div class="add-form">
                                <label>Email <span style="color: red;">*</span></label>
                                <input type="email" style="width: 300px" name="contact_email" value="{{ auth()->user()->email }}" readonly>
                            </div>



                        </div>

                        


                    </div>
                
                   

                    <div class="step-nav">
                        <button type="button" class="prev-btn">Previous</button>
                        <button type="button" class="next-btn">Next</button>
                    </div>
                </section>

                <!-- STEP 4: Additional Info -->
                <section class="info-order step-section" data-step="4" style="display:none;">
                    <div class="number">
                        <span class="material-symbols-outlined">looks_4</span>
                    </div>
                    <h2>Additional Info</h2>
                    
                    <label>Notes / Special Instructions</label>
                    <textarea name="order_notes" rows="2">{{ old('order_notes') }}</textarea>

                    <label>Receiver Name <span style="color: red;">*</span></label>
                    <input type="text" name="receiver_name" required value="{{ old('receiver_name') }}">



                    <div class="step-nav">
                        <button type="button" class="prev-btn">Previous</button>
                        <button type="button" class="next-btn">Next</button>
                    </div>
                </section>

                <!-- STEP 5: Summary -->
                <section class="summary-order step-section" data-step="5" style="display:none;">
                    <div class="number">
                        <span class="material-symbols-outlined">looks_5</span>
                    </div>
                    <h2>Summary</h2>
                    <p>Review your order before finalizing.</p>

                    <h3>Cart Items</h3>
                    <div id="order-summary" style="background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px;"></div>

                    <h3>Shipping & Billing</h3>
                    <div style="background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                        <p><strong>Shipping Address:</strong> <span id="summary-shipping">Not provided</span></p>
                        <p><strong>Billing Address:</strong> <span id="summary-billing">Not provided</span></p>
                        <p><strong>Contact Phone:</strong> <span id="summary-phone">Not provided</span></p>
                        <p><strong>Email:</strong> <span id="summary-email">Not provided</span></p>
                    </div>

                    <h3>Additional Info</h3>
                    <div style="background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                        <p><strong>Receiver Name:</strong> <span id="summary-receiver">Not provided</span></p>
                        <p><strong>Notes:</strong> <span id="summary-notes">Not provided</span></p>
                    </div>

                    <div class="step-nav">
                        <button type="button" class="prev-btn">Previous</button>
                        <button type="button" class="next-btn">Next</button>
                    </div>
                </section>

                <!-- STEP 6: Finalize -->
                <section class="finalize-order step-section" data-step="6" style="display:none;">
                    <div class="number">
                        <span class="material-symbols-outlined">looks_6</span>
                    </div>
                    <h2>Finalize Order</h2>
                    <p>Click "Place Order" to finalize your purchase.</p>
                    <div class="step-nav">
                        <button type="button" class="prev-btn">Previous</button>
                        <button type="submit" class="submit-btn" style="background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;">Place Order</button>
                    </div>
                </section>
            </div>
        </form>
    </div>

    <script src="{{ asset('js/purchase_order.js') }}"></script>
    <script src="{{ asset('js/address.js') }}"></script>
</body>
</html>

@endsection