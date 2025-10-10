    document.addEventListener('DOMContentLoaded', function () {
        const pdfModal = document.getElementById('pdfModal');
        const pdfFrame = document.getElementById('pdfFrame');

        pdfModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const url = button.getAttribute('data-url');
            pdfFrame.src = url;
        });

        pdfModal.addEventListener('hidden.bs.modal', function () {
            pdfFrame.src = ''; 
        });
    });