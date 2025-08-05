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
                <div class="form-grid" s>
                    <div >
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
                        <input type="number" name="price" placeholder="Price in Rs." id="price" min="0" required>
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
                    <tr style="background:#f7f7fa;">
                        <th style="padding:10px 8px; text-align:left;">Name</th>
                        <th style="padding:10px 8px; text-align:left;">Quantity</th>
                        <th style="padding:10px 8px; text-align:left;">Price</th>
                        <th style="padding:10px 8px; text-align:left;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                    <tr style="border-bottom:1px solid #eee; align-items: center;" onclick="window.location='{{ url('/product/' . $product->id) }}'">
                        <td style="padding:10px 8px; width:20%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; cursor:pointer;" onclick="window.location='{{ url('/product/' . $product->id) }}'">
                            {{ $product->name }}
                        </td>
                        <td style="padding:10px 8px; width:20%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $product->quantity }}x
                        </td>
                        <td style="padding:10px 8px; width:20%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            ₱{{ number_format($product->price, 2) }}
                        </td>
                        <td style="padding:10px 8px; width:20%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $product->status ?? 'Available' }}
                        </td>
                        <td style="padding:10px 8px; width:10%; text-align:center;">
                            @if(auth()->user()->user_type === 'Customer')
                                <button class="add-to-cart-btn" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-product-price="{{ $product->price }}" data-product-qty="{{ $product->quantity }}" style="background: #4caf50; color: #fff; border: none; border-radius: 50%; width: 32px; height: 32px; font-size: 18px; cursor: pointer;">
                                    <i class="fa fa-plus"></i>
                                </button>
                            @endif
                        </td>
                    </tr>


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
                    <p style="color:#888;">No products in cart.</p>
                    </div>
                    <hr>
                    <span class="total-container"><p style="color: #333">Total</p><p id="cartTotal"></p></span>
                    <button id="checkoutBtn">Checkout</button>
                    <div id="checkoutMessage" style="margin-top:1rem; color:green; display:none;"></div>
                </div>
            </div>

            <script>


            // Cart keys (guarded)
            if (typeof window.CART_KEY === 'undefined') {
                window.CART_KEY = 'customer_cart';
                window.CART_EXPIRY_KEY = 'customer_cart_expiry';
            }

            // Cart logic functions (always defined)
            async function checkoutCart() {
                const cart = getCart();
                if (!cart.length) return;
                const total = calculateTotal();
                try {
                    const response = await fetch('/checkout', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({ items: cart, total })
                    });
                    const data = await response.json();
                    if (data.success) {
                        // Clear cart
                        localStorage.removeItem(window.CART_KEY);
                        localStorage.removeItem(window.CART_EXPIRY_KEY);
                        renderCart();
                        document.getElementById('checkoutMessage').innerText = 'Order placed successfully!';
                        document.getElementById('checkoutMessage').style.display = 'block';
                    } else {
                        document.getElementById('checkoutMessage').innerText = 'Order failed. Please try again.';
                        document.getElementById('checkoutMessage').style.display = 'block';
                    }
                } catch (e) {
                    document.getElementById('checkoutMessage').innerText = 'Order failed. Please try again.';
                    document.getElementById('checkoutMessage').style.display = 'block';
                }
            }

            function getCart() {
                const expiry = localStorage.getItem(window.CART_EXPIRY_KEY);
                if (expiry && Date.now() > parseInt(expiry)) {
                    localStorage.removeItem(window.CART_KEY);
                    localStorage.removeItem(window.CART_EXPIRY_KEY);
                    return [];
                }
                const cart = localStorage.getItem(window.CART_KEY);
                return cart ? JSON.parse(cart) : [];
            }

            function setCart(cart) {
                localStorage.setItem(window.CART_KEY, JSON.stringify(cart));
                // Set expiry to 24 hours from now
                localStorage.setItem(window.CART_EXPIRY_KEY, (Date.now() + 24*60*60*1000).toString());
            }

            function addToCart(product) {
                let cart = getCart();
                // If already in cart, increase quantity
                const idx = cart.findIndex(item => item.id === product.id);
                if (idx > -1) {
                    cart[idx].qty = (cart[idx].qty || 1) + 1;
                } else {
                    cart.push({...product, qty: 1});
                }
                setCart(cart);
            }

            function updateQuantity(productId, newQuantity) {
                let cart = getCart();
                const idx = cart.findIndex(item => item.id === productId);
                if (idx > -1) {
                    if (newQuantity <= 0) {
                        cart.splice(idx, 1);
                    } else {
                        cart[idx].qty = newQuantity;
                    }
                    setCart(cart);
                    renderCart();
                }
            }

            function removeFromCart(productId) {
                let cart = getCart();
                cart = cart.filter(item => item.id !== productId);
                setCart(cart);
                renderCart();
            }

            function calculateTotal() {
                const cart = getCart();
                return cart.reduce((total, item) => {
                    return total + (parseFloat(item.price) * item.qty);
                }, 0);
            }

            function renderCart() {
                const cart = getCart();
                const container = document.getElementById('cartItemsContainer');
                const checkoutBtn = document.getElementById('checkoutBtn');
                const totalContainer = document.getElementById('cartTotal');
                
                if (!cart.length) {
                    container.innerHTML = '<p style="color:#888; font-size:15px;">No products in cart.</p>';
                    checkoutBtn.style.display = 'none';
                    totalContainer.innerHTML = '';
                    return;
                }
                
                let html = '<div>';
                cart.forEach(item => {
                    const itemTotal = parseFloat(item.price) * item.qty;
                    html += `
                        <div class="cart-item">
                            <div class="cart-item-info">
                                <div class="cart-item-name">${item.name}</div>
                                <div class="cart-item-price">₱${parseFloat(item.price).toLocaleString(undefined, {minimumFractionDigits:2})} each</div>
                            </div>
                            <div class="quantity-controls">
                                <button class="quantity-btn" onclick="updateQuantity('${item.id}', ${item.qty - 1})" ${item.qty <= 1 ? 'disabled' : ''}>−</button>
                                   <span class="quantity-display">${item.qty}</span>
                                <button class="quantity-btn" style="background-color: #ffde59: 5px;" onclick="updateQuantity('${item.id}', ${item.qty + 1})">+</button>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-weight: bold; margin-bottom: 4px;">₱${itemTotal.toLocaleString(undefined, {minimumFractionDigits:2})}</div>
                                <button class="remove-btn" onclick="removeFromCart('${item.id}')"> — </button>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                
                container.innerHTML = html;
                
                const total = calculateTotal();
                totalContainer.innerHTML = ` ₱${total.toLocaleString(undefined, {minimumFractionDigits:2})}`;
                
                checkoutBtn.style.display = 'inline-block';
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Add to cart button
                document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const product = {
                            id: this.getAttribute('data-product-id'),
                            name: this.getAttribute('data-product-name'),
                            price: this.getAttribute('data-product-price'),
                        };
                        addToCart(product);
                        renderCart();
                        document.getElementById('cartModal').style.display = 'block';
                    });
                });
                // Open cart modal from "Order cart" button
                const orderCartBtn = document.querySelector('button.addStaffBtn');
                if(orderCartBtn && orderCartBtn.textContent.includes('Order cart')) {
                    orderCartBtn.addEventListener('click', function() {
                        renderCart();
                        document.getElementById('cartModal').style.display = 'block';
                    });
                }
                // Close cart modal
                const closeCartBtn = document.querySelector('.close-cart-btn');
                if (closeCartBtn) {
                    closeCartBtn.onclick = function() {
                        document.getElementById('cartModal').style.display = 'none';
                    };
                }
                // Hide modal on outside click
                window.onclick = function(event) {
                    const modal = document.getElementById('cartModal');
                    if (event.target === modal) {
                        modal.style.display = 'none';
                    }
                };
                renderCart();
                // Checkout button event
                const checkoutBtn = document.getElementById('checkoutBtn');
                if (checkoutBtn) {
                    checkoutBtn.onclick = checkoutCart;
                }
            });

            function addToCart(product) {
                let cart = getCart();
                // If already in cart, increase quantity
                const idx = cart.findIndex(item => item.id === product.id);
                if (idx > -1) {
                    cart[idx].qty = (cart[idx].qty || 1) + 1;
                } else {
                    cart.push({...product, qty: 1});
                }
                setCart(cart);
            }

            function updateQuantity(productId, newQuantity) {
                let cart = getCart();
                const idx = cart.findIndex(item => item.id === productId);
                if (idx > -1) {
                    if (newQuantity <= 0) {
                        cart.splice(idx, 1);
                    } else {
                        cart[idx].qty = newQuantity;
                    }
                    setCart(cart);
                    renderCart();
                }
            }

            function removeFromCart(productId) {
                let cart = getCart();
                cart = cart.filter(item => item.id !== productId);
                setCart(cart);
                renderCart();
            }

            function calculateTotal() {
                const cart = getCart();
                return cart.reduce((total, item) => {
                    return total + (parseFloat(item.price) * item.qty);
                }, 0);
            }

            function renderCart() {
                const cart = getCart();
                const container = document.getElementById('cartItemsContainer');
                const checkoutBtn = document.getElementById('checkoutBtn');
                const totalContainer = document.getElementById('cartTotal');
                
                if (!cart.length) {
                    container.innerHTML = '<p style="color:#888;">No products in cart.</p>';
                    checkoutBtn.style.display = 'none';
                    totalContainer.innerHTML = '';
                    return;
                }
                
                let html = '<div>';
                cart.forEach(item => {
                    const itemTotal = parseFloat(item.price) * item.qty;
                    html += `
                        <div class="cart-item">
                            <div class="cart-item-info">
                                <div class="cart-item-name">${item.name}</div>
                                <div class="cart-item-price">₱${parseFloat(item.price).toLocaleString(undefined, {minimumFractionDigits:2})} each</div>
                            </div>
                            <div class="quantity-controls">
                                <button class="quantity-btn" onclick="updateQuantity('${item.id}', ${item.qty - 1})" ${item.qty <= 1 ? 'disabled' : ''}>−</button>
                                <span class="quantity-display">${item.qty}</span>
                                <button class="quantity-btn" onclick="updateQuantity('${item.id}', ${item.qty + 1})">+</button>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-weight: bold; margin-bottom: 4px;">₱${itemTotal.toLocaleString(undefined, {minimumFractionDigits:2})}</div>
                                <button class="remove-btn" onclick="removeFromCart('${item.id}')"> — </button>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                
                container.innerHTML = html;
                
                const total = calculateTotal();
                totalContainer.innerHTML = `₱${total.toLocaleString(undefined, {minimumFractionDigits:2})}`;
                
                checkoutBtn.style.display = 'inline-block';
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Add to cart button
                document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const product = {
                            id: this.getAttribute('data-product-id'),
                            name: this.getAttribute('data-product-name'),
                            price: this.getAttribute('data-product-price'),
                        };
                        addToCart(product);
                        renderCart();
                        document.getElementById('cartModal').style.display = 'block';
                    });
                });
                
                // Open cart modal from "Order cart" button
                const orderCartBtn = document.querySelector('button.addStaffBtn');
                if(orderCartBtn && orderCartBtn.textContent.includes('Order cart')) {
                    orderCartBtn.addEventListener('click', function() {
                        renderCart();
                        document.getElementById('cartModal').style.display = 'block';
                    });
                }
                
                // Close cart modal
                const closeCartBtn = document.querySelector('.close-cart-btn');
                if (closeCartBtn) {
                    closeCartBtn.onclick = function() {
                        document.getElementById('cartModal').style.display = 'none';
                    };
                }
                
                // Hide modal on outside click
                window.onclick = function(event) {
                    const modal = document.getElementById('cartModal');
                    if (event.target === modal) {
                        modal.style.display = 'none';
                    }
                };
                
                renderCart();
                // Checkout button event
                document.getElementById('checkoutBtn').onclick = checkoutCart;
            });

            </script>
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

    {{-- @elseif(auth()->user()->user_type === 'Staff')
        <div class="productList" style="padding: 15px;">
            @if(isset($products) && count($products) > 0)
            <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
                <thead>
                    <tr style="background:#f7f7fa;">
                        <th style="padding:10px 8px; text-align:left;">Name</th>
                        <th style="padding:10px 8px; text-align:left;">Quantity</th>
                        <th style="padding:10px 8px; text-align:left;">Price</th>
                        <th style="padding:10px 8px; text-align:left;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                    <tr style="border-bottom:1px solid #eee; align-items: center; cursor:pointer;" onclick="window.location='{{ url('/product/' . $product->id) }}'">
                    <td style="padding:10px 8px; width:20%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $product->name }}
                        </td>
                        <td style="padding:10px 8px; width:20%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $product->quantity }}x
                        </td>
                        <td style="padding:10px 8px; width:20%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            ₱{{ number_format($product->price, 2) }}
                        </td>
                        <td style="padding:10px 8px; width:20%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $product->acc_status ?? 'Active' }}
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

     @elseif(auth()->user()->user_type === 'Customer')
        <div class="productList" style="padding: 15px;">
            @if(isset($products) && count($products) > 0)
            <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
                <thead>
                    <tr style="background:#f7f7fa;">
                        <th style="padding:10px 8px; text-align:left;">Name</th>
                        <th style="padding:10px 8px; text-align:left;">Quantity</th>
                        <th style="padding:10px 8px; text-align:left;">Price</th>
                        <th style="padding:10px 8px; text-align:left;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                    <tr style="border-bottom:1px solid #eee; align-items: center; cursor:pointer;" onclick="window.location='{{ url('/product/' . $product->id) }}'">
                    <td style="padding:10px 8px; width:20%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $product->name }}
                        </td>
                        <td style="padding:10px 8px; width:20%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $product->quantity }}x
                        </td>
                        <td style="padding:10px 8px; width:20%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            ₱{{ number_format($product->price, 2) }}
                        </td>
                        <td style="padding:10px 8px; width:20%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $product->acc_status ?? 'Active' }}
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
     @else --}}



</div>


<script src="{{ asset('scripts/open-modal.js') }}"></script>
</body>
</html>


@endsection
