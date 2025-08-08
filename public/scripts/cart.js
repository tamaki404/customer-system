

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
