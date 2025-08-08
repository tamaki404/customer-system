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
        <span class="total-container"><p style="color: #333">Total</p><p id="cartTotal"></p></span>
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
    {{-- 
    <button onclick="window.location='{{ route('all-orders') }}'">Orders</button>
    <button onclick="window.location='{{ route('spec-orders', ['id' => auth()->user()->id]) }}'">My Orders</button> --}}

    <div class="productList" style="padding: 15px;">
            @if(isset($products) && count($products) > 0)
            <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
                <thead>
                    <tr style="background:#f7f7fa;">
                        <th style="padding:10px 8px; text-align:left;">Name</th>
                        <th style="padding:10px 8px; text-align:left;">Stock</th>
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
                            <span style="color: {{ $product->quantity <= 5 ? '#ff6b6b' : ($product->quantity <= 10 ? '#ffa500' : '#4caf50') }}">
                                {{ $product->quantity }}x
                            </span>
                       
                        </td>
                        <td style="padding:10px 8px; width:20%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            ₱{{ number_format($product->price, 2) }}
                        </td>
                        <td style="padding:10px 8px; width:20%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            <span class="status-span">
                                @if ($product->quantity == 0)
                                    <span style="color: red">Add stocks!</span>
                                @elseif ($product->quantity < 5)
                                    <span style="color: orange">Low on stocks!</span>
                                
                                @else
                                    @if($product->status == "Unlisted")
                                        <span style="color: grey">{{ $product->status}}</span>
                                    @elseif($product->status == "Available")
                                        <span style="color: green">{{ $product->status}}</span>
                                    @endif

                                @endif
                            </span>
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



<!-- JavaScript with Stock Validation -->
<script>
// Prevent multiple script executions
if (window.cartInitialized) {
    console.log('Cart already initialized, exiting');
} else {
    window.cartInitialized = true;
    console.log('Initializing cart system with stock validation');

    // Cart keys
    window.CART_KEY = 'customer_cart';
    window.CART_EXPIRY_KEY = 'customer_cart_expiry';

    // Store product stock data for validation
    window.productStock = {};
    
    // Initialize product stock data from the page
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
            const productId = btn.getAttribute('data-product-id');
            const stock = parseInt(btn.getAttribute('data-product-stock'));
            if (productId && stock !== null) {
                window.productStock[productId] = stock;
            }
        });
    });

    // Cart logic functions with stock validation
    async function checkoutCart() {
        const cart = getCart();
        if (!cart.length) return;
        
        // Validate cart against current stock before checkout
        const stockIssues = validateCartStock(cart);
        if (stockIssues.length > 0) {
            document.getElementById('stockWarning').innerHTML = 
                'Stock issues found:<br>' + stockIssues.join('<br>');
            document.getElementById('stockWarning').style.display = 'block';
            return;
        }
        
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
                localStorage.removeItem(window.CART_KEY);
                localStorage.removeItem(window.CART_EXPIRY_KEY);
                renderCart();
                document.getElementById('checkoutMessage').innerText = 'Order placed successfully!';
                document.getElementById('checkoutMessage').style.display = 'block';
                document.getElementById('stockWarning').style.display = 'none';
            } else {
                document.getElementById('checkoutMessage').innerText = data.message || 'Order failed. Please try again.';
                document.getElementById('checkoutMessage').style.display = 'block';
            }
        } catch (e) {
            document.getElementById('checkoutMessage').innerText = 'Order failed. Please try again.';
            document.getElementById('checkoutMessage').style.display = 'block';
        }
    }

    function validateCartStock(cart) {
        const issues = [];
        cart.forEach(item => {
            const availableStock = window.productStock[item.id];
            if (availableStock === undefined) {
                issues.push(`${item.name}: Stock information unavailable`);
            } else if (item.qty > availableStock) {
                issues.push(`${item.name}: Only ${availableStock} available (you have ${item.qty} in cart)`);
            } else if (availableStock === 0) {
                issues.push(`${item.name}: Out of stock`);
            }
        });
        return issues;
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
        localStorage.setItem(window.CART_EXPIRY_KEY, (Date.now() + 24*60*60*1000).toString());
    }

    function addToCart(product) {
        console.log('addToCart called for:', product.name);
        
        const availableStock = parseInt(product.stock);
        let cart = getCart();
        const idx = cart.findIndex(item => item.id === product.id);
        
        let currentQtyInCart = 0;
        if (idx > -1) {
            currentQtyInCart = cart[idx].qty || 0;
        }
        
        // Check if adding one more would exceed stock
        if (currentQtyInCart + 1 > availableStock) {
            showStockMessage(`Cannot add more ${product.name}. Only ${availableStock} available.`);
            return false;
        }
        
        if (idx > -1) {
            cart[idx].qty = currentQtyInCart + 1;
            console.log('Updated quantity to:', cart[idx].qty);
        } else {
            cart.push({...product, qty: 1});
            console.log('Added new item to cart');
        }
        setCart(cart);
        return true;
    }

    function updateQuantity(productId, newQuantity) {
        let cart = getCart();
        const idx = cart.findIndex(item => item.id === productId);
        if (idx > -1) {
            const availableStock = window.productStock[productId];
            
            if (newQuantity <= 0) {
                cart.splice(idx, 1);
            } else if (newQuantity > availableStock) {
                showStockMessage(`Cannot set quantity to ${newQuantity}. Only ${availableStock} available.`);
                return;
            } else {
                cart[idx].qty = newQuantity;
            }
            setCart(cart);
            renderCart();
            hideStockMessage();
        }
    }

    function removeFromCart(productId) {
        let cart = getCart();
        cart = cart.filter(item => item.id !== productId);
        setCart(cart);
        renderCart();
        hideStockMessage();
    }

    function calculateTotal() {
        const cart = getCart();
        return cart.reduce((total, item) => {
            return total + (parseFloat(item.price) * item.qty);
        }, 0);
    }

    function showStockMessage(message) {
        const warningEl = document.getElementById('stockWarning');
        if (warningEl) {
            warningEl.innerText = message;
            warningEl.style.display = 'block';
            // Auto-hide after 3 seconds
            setTimeout(() => {
                hideStockMessage();
            }, 3000);
        }
    }

    function hideStockMessage() {
        const warningEl = document.getElementById('stockWarning');
        if (warningEl) {
            warningEl.style.display = 'none';
        }
    }

    function renderCart() {
        const cart = getCart();
        const container = document.getElementById('cartItemsContainer');
        const checkoutBtn = document.getElementById('checkoutBtn');
        const totalContainer = document.getElementById('cartTotal');
        
        if (!cart.length) {
            container.innerHTML = '<p style="color:#888; font-size:15px; margin-top:200px;">No products in cart.</p>';
            if (checkoutBtn) checkoutBtn.style.display = 'none';
            if (totalContainer) totalContainer.innerHTML = '';
            return;
        }
        
        let html = '<div>';
        cart.forEach(item => {
            const itemTotal = parseFloat(item.price) * item.qty;
            const availableStock = window.productStock[item.id] || 0;
            const isOverStock = item.qty > availableStock;
            
            html += `
                <div class="cart-item" style="${isOverStock ? 'background-color: #ffe6e6; border: 1px solid #ff9999;' : ''}">
                    <div class="cart-item-info">
                        <div class="cart-item-name">
                            ${item.name}
                            ${isOverStock ? '<span style="color: #ff6b6b; font-size: 12px;">(Exceeds stock!)</span>' : ''}
                        </div>
                        <div class="cart-item-price">₱${parseFloat(item.price).toLocaleString(undefined, {minimumFractionDigits:2})} each</div>
                        <div style="font-size: 12px; color: #666;">${availableStock} pcs. only</div>
                    </div>
                    <div class="quantity-controls">
                        <button class="quantity-btn" onclick="updateQuantity('${item.id}', ${item.qty - 1})" ${item.qty <= 1 ? 'disabled' : ''}>−</button>
                        <span class="quantity-display" style="${isOverStock ? 'color: #ff6b6b; font-weight: bold;' : ''}">${item.qty}</span>
                        <button class="quantity-btn" onclick="updateQuantity('${item.id}', ${item.qty + 1})" ${item.qty >= availableStock ? 'disabled style="background-color: #ccc; cursor: not-allowed;"' : ''}>+</button>
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
        if (totalContainer) {
            totalContainer.innerHTML = `₱${total.toLocaleString(undefined, {minimumFractionDigits:2})}`;
        }
        
        if (checkoutBtn) {
            checkoutBtn.style.display = 'inline-block';
        }
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, setting up event listeners');
        
        // Add to cart buttons
        document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                console.log('Add to cart button clicked');
                
                const product = {
                    id: this.getAttribute('data-product-id'),
                    name: this.getAttribute('data-product-name'),
                    price: this.getAttribute('data-product-price'),
                    stock: this.getAttribute('data-product-stock'),
                };
                
                const success = addToCart(product);
                if (success) {
                    renderCart();
                    document.getElementById('cartModal').style.display = 'block';
                }
            });
        });
        
        // Order cart button
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
                hideStockMessage();
            };
        }
        
        // Hide modal on outside click
        window.onclick = function(event) {
            const modal = document.getElementById('cartModal');
            if (event.target === modal) {
                modal.style.display = 'none';
                hideStockMessage();
            }
        };
        
        // Checkout button
        const checkoutBtn = document.getElementById('checkoutBtn');
        if (checkoutBtn) {
            checkoutBtn.onclick = checkoutCart;
        }
        
        renderCart();
    });
}
</script>

<script src="{{ asset('scripts/open-modal.js') }}"></script>
</body>
</html>

@endsection