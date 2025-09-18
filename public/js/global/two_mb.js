document.addEventListener('DOMContentLoaded', function(){
    var MAX = 2 * 1024 * 1024; // 2MB
    var allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'];

    // Match either new_image OR image
    var profilePicInput = document.querySelector(
        '.modal-option-groups input[type="file"][name="new_image"], .modal-option-groups input[type="file"][name="image"]'
    );

    var errorMessage = document.createElement('div');
    errorMessage.style.color = '#dc3545';
    errorMessage.style.fontSize = '13px';
    errorMessage.style.marginTop = '5px';

    if (profilePicInput) {
        profilePicInput.parentNode.appendChild(errorMessage);

        profilePicInput.addEventListener('change', function(){
            errorMessage.textContent = ''; // clear previous error
            var file = profilePicInput.files[0];
            if (!file) return; // nothing selected

            if (file.size > MAX) {
                errorMessage.textContent = 'File must be 2MB or less.';
                profilePicInput.value = '';
                return;
            }

            if (!allowedTypes.includes(file.type)) {
                errorMessage.textContent = 'Only PNG, JPEG, JPG, or WEBP images are allowed.';
                profilePicInput.value = '';
                return;
            }
        });
    }
});
