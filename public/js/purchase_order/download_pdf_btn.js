
    function downloadPDF() {
        // If we're on Reports → Purchase Orders tab, print the dedicated purchase_order table via hidden iframe
        const isReportsPO = document.querySelector('#purchase_orders.tab-content') !== null;
        if (isReportsPO) {
            // Try to read date filters from the Purchase Orders filter form on Reports
            const form = document.getElementById('purchaseOrdersFilterForm') || document.querySelector('#purchase_orders form');
            const fromInput = form ? form.querySelector('input[name="from_date"]') : null;
            const toInput = form ? form.querySelector('input[name="to_date"]') : null;

            const from = fromInput ? fromInput.value : '';
            const to = toInput ? toInput.value : '';

            const params = new URLSearchParams();
            if (from) params.append('from_date', from);
            if (to) params.append('to_date', to);

            const url = '/reports/purchase-orders/preview' + (params.toString() ? ('?' + params.toString()) : '');

            const iframe = document.createElement('iframe');
            iframe.style.position = 'fixed';
            iframe.style.right = '0';
            iframe.style.bottom = '0';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = '0';
            iframe.src = url;
            document.body.appendChild(iframe);

            iframe.onload = function() {
                try {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                } catch (e) {
                    // fallback: open in new tab for manual print
                    window.open(url, '_blank');
                } finally {
                    setTimeout(() => iframe.remove(), 1000);
                }
            };
            return;
        }

        // Default behavior on pages with their own table (e.g., purchase_order page)
        const goBack = document.querySelector('.go-back-a');
        const downloadBtn = document.querySelector('.download-purchase-order');
        if (goBack) goBack.style.display = 'none';
        if (downloadBtn) downloadBtn.style.display = 'none';
        window.print();
    }

        document.addEventListener('DOMContentLoaded', function() {
            const currentDate = new Date().toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long', 
                day: 'numeric'
            });
            
        });
