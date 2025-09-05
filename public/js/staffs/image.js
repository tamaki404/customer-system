const fileInput = document.getElementById('image');
const errorMsg = document.getElementById('file-error');
const submitBtn = document.getElementById('submitBtn');

fileInput.addEventListener('change', function () {
    const file = this.files[0];

    if (file && file.size > 2 * 1024 * 1024) { 
        errorMsg.textContent = 'File must not exceed 2MB';
        errorMsg.style.display = 'block';
        submitBtn.style.display = 'none';
        this.value = ''; 

        setTimeout(() => {
            errorMsg.style.display = 'none';
        }, 2000);
    } else if (file) {
        errorMsg.textContent = '';
        errorMsg.style.display = 'none';
        submitBtn.style.display = 'inline-block';
    } else {
        errorMsg.style.display = 'none';
        submitBtn.style.display = 'none';
    }
});
