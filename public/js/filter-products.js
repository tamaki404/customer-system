document.addEventListener('DOMContentLoaded', function() {
    const filterSelect = document.getElementById('filter');
    
    // Set the current filter value if it exists
    const urlParams = new URLSearchParams(window.location.search);
    const currentFilter = urlParams.get('filter');
    if (currentFilter && filterSelect) {
        filterSelect.value = currentFilter;
    }
    
    // Handle filter change
    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            const filterValue = this.value;
            const url = new URL(window.location);
            
            if (filterValue) {
                url.searchParams.set('filter', filterValue);
            } else {
                url.searchParams.delete('filter');
            }
            
            window.location.href = url.toString();
        });
    }
});