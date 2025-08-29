    document.addEventListener('DOMContentLoaded', function() {
    const cartModal = document.getElementById('cartModal');
    const cartBtn = document.getElementById('openCartBtn'); 
    const closeCartBtn = document.querySelector('.close-cart-btn');

    const productModal = document.getElementById('myModal');
    const productBtn = document.getElementById('openAddProductModalBtn'); 
    const closeProductBtn = document.querySelector('.close-product-btn');

    if (cartBtn) {
        cartBtn.addEventListener('click', function(e) {
            e.preventDefault();
            cartModal.style.display = 'block';
            document.body.style.overflow = 'hidden'; 
        });
    }

    if (productBtn) {
        productBtn.addEventListener('click', function(e) {
            e.preventDefault();
            productModal.style.display = 'block';
            document.body.style.overflow = 'hidden'; 
        });
    }
    
    if (closeCartBtn) {
        closeCartBtn.addEventListener('click', function() {
            cartModal.style.display = 'none';
            document.body.style.overflow = 'auto'; 
        });
    }

    if (closeProductBtn) {
        closeProductBtn.addEventListener('click', function() {
            productModal.style.display = 'none';
            document.body.style.overflow = 'auto'; 
        });
    }

    window.addEventListener('click', function(e) {
        if (e.target === cartModal) {
            cartModal.style.display = 'none';
            document.body.style.overflow = 'auto'; 
        }
        if (e.target === productModal) {
            productModal.style.display = 'none';
            document.body.style.overflow = 'auto'; 
        }
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (cartModal.style.display === 'block') {
                cartModal.style.display = 'none';
            }
            if (productModal.style.display === 'block') {
                productModal.style.display = 'none';
            }
            document.body.style.overflow = 'auto'; 
        }
    });
});

