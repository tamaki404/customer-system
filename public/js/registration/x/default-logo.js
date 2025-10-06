document.getElementById('use-default').addEventListener('change', function () {
    const fileInput = document.getElementById('company-image');
    const defaultImageFlag = document.getElementById('default-image-flag');

    if (this.checked) {
        fileInput.disabled = true;
        fileInput.value = ''; // clear any selected file
        defaultImageFlag.value = 'true'; 
    } else {
        fileInput.disabled = false;
        defaultImageFlag.value = 'false';
    }
});