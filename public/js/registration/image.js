const fileInput = document.getElementById('companyImage');
const errorMsg = document.getElementById('file-error');

fileInput.addEventListener('change', function() {
    const file = this.files[0];

    if (file && file.size > 2 * 1024 * 1024) { 
        errorMsg.textContent = 'File must not exceed 2MB';
        errorMsg.style.display = 'block';
        this.value = ''; 
        setTimeout(() => {
        errorMsg.style.display = 'none';
        }, 2000);
    } else {
        errorMsg.textContent = '';
        errorMsg.style.display = 'none';
        }
    });
         