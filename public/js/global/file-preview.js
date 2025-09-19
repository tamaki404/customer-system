document.addEventListener('DOMContentLoaded', function () {
    const MAX = 2 * 1024 * 1024; // 2MB
    const imageTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'];
    const docTypes   = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

    const fileInput = document.getElementById('order_file');
    const preview   = document.getElementById('file-preview');
    const errorMsg  = document.getElementById('file-error');

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            errorMsg.textContent = '';
            preview.innerHTML = '';

            const file = fileInput.files[0];
            if (!file) return;

            // size check
            if (file.size > MAX) {
                errorMsg.textContent = 'File must be 2MB or less.';
                fileInput.value = '';
                return;
            }

            // type check
            if (![...imageTypes, ...docTypes].includes(file.type)) {
                errorMsg.textContent = 'Invalid file type selected.';
                fileInput.value = '';
                return;
            }

            // preview
            if (imageTypes.includes(file.type)) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.style.maxWidth = '150px';
                img.style.maxHeight = '150px';
                img.style.display = 'block';
                img.style.marginTop = '5px';
                preview.appendChild(img);
            } else {
                const text = document.createElement('p');
                text.textContent = 'Selected file: ' + file.name;
                preview.appendChild(text);
            }
        });
    }
});
