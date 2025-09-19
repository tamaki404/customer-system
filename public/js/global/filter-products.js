    function filterProducts() {
        const category = document.getElementById('filter-category').value;
        const unit = document.getElementById('filter-unit').value;
        const weight = document.getElementById('filter-weight').value;

        fetch("{{ route('products.filter') }}?category=" + category + "&unit=" + unit + "&weight=" + weight)
            .then(response => response.text())
            .then(html => {
                document.getElementById('product-results').innerHTML = html;
            });
    }