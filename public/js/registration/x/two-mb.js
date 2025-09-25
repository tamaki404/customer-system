document.addEventListener('DOMContentLoaded', function () {
    var MAX = 2 * 1024 * 1024; // 2MB
    var allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'];

    // Select all relevant image inputs
    var imageInputs = document.querySelectorAll(
        '.input-forms input[type="file"][name="image"], ' +
        '.modal-option-groups input[type="file"][name="image"], ' +
        '.form-group input[type="file"][name="image"], ' +
        '.input-forms input[type="file"][name="id_image"],'  +
        '.input-forms input[type="file"][name="e_signature"]'  

    );

    imageInputs.forEach(function (input) {
        var errorMessage = document.createElement('div');
        errorMessage.style.color = '#dc3545';
        errorMessage.style.fontSize = '13px';
        errorMessage.style.marginTop = '5px';

        input.parentNode.appendChild(errorMessage);

        input.addEventListener('change', function () {
            errorMessage.textContent = ''; // clear previous error
            var file = input.files[0];
            if (!file) return; // nothing selected

            if (file.size > MAX) {
                errorMessage.textContent = 'File must be 2MB or less.';
                input.value = '';
                return;
            }

            if (!allowedTypes.includes(file.type)) {
                errorMessage.textContent = 'Only PNG, JPEG, JPG, or WEBP images are allowed.';
                input.value = '';
                return;
            }
        });
    });
});
