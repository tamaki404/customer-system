   function switchTab(tabId) {
        const tabContents = document.querySelectorAll('.tab-content');
        tabContents.forEach(content => {
            content.classList.remove('active');
        });

        const tabButtons = document.querySelectorAll('.tab-button');
        tabButtons.forEach(button => {
            button.classList.remove('active');
        });

        document.getElementById(tabId).classList.add('active');
        event.target.classList.add('active');
    }

    function toggleCustomFields(tabName) {
        const dateRange = document.getElementById(tabName + 'DateRange').value;
        const fromDateGroup = document.getElementById(tabName + 'FromDateGroup');
        const toDateGroup = document.getElementById(tabName + 'ToDateGroup');
        
        if (dateRange === 'custom') {
            fromDateGroup.style.display = 'block';
            toDateGroup.style.display = 'block';
        } else {
            fromDateGroup.style.display = 'none';
            toDateGroup.style.display = 'none';
        }
    }

    // Auto-submit form when predefined date range is selected for each tab
    document.getElementById('salesDateRange').addEventListener('change', function() {
        if (this.value !== 'custom') {
            document.getElementById('salesFilterForm').submit();
        }
    });

    document.getElementById('customersDateRange').addEventListener('change', function() {
        if (this.value !== 'custom') {
            document.getElementById('customersFilterForm').submit();
        }
    });

    document.getElementById('ordersDateRange').addEventListener('change', function() {
        if (this.value !== 'custom') {
            document.getElementById('ordersFilterForm').submit();
        }
    });

    document.getElementById('purchaseOrdersDateRange').addEventListener('change', function() {
        if (this.value !== 'custom') {
            document.getElementById('purchaseOrdersFilterForm').submit();
        }
    });

    document.getElementById('productsDateRange').addEventListener('change', function() {
        if (this.value !== 'custom') {
            document.getElementById('productsFilterForm').submit();
        }
    });

    document.getElementById('receiptsDateRange').addEventListener('change', function() {
        if (this.value !== 'custom') {
            document.getElementById('receiptsFilterForm').submit();
        }
    });