
    function downloadPDF() {
        // Hide the Go Back link and Download button
        const goBack = document.querySelector('.go-back-a');
        const downloadBtn = document.querySelector('.download-purchase-order');

        if (goBack) goBack.style.display = 'none';
        if (downloadBtn) downloadBtn.style.display = 'none';

        // Trigger print (or download PDF)
        window.print();
    }

        document.addEventListener('DOMContentLoaded', function() {
            const currentDate = new Date().toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long', 
                day: 'numeric'
            });
            
        });
