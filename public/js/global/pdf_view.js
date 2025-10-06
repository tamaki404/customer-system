document.addEventListener('DOMContentLoaded', function () {
    const pdfModal = document.getElementById('pdfModal');
    pdfModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const url = button.getAttribute('data-url');
        const iframe = pdfModal.querySelector('#pdfFrame');
        iframe.src = url;
    });

    pdfModal.addEventListener('hidden.bs.modal', function () {
        pdfModal.querySelector('#pdfFrame').src = ""; 
    });
});
