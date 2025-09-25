document.querySelector('.image').addEventListener('change', function(event) {
  const file = event.target.files[0];
  const errorText = this.parentElement.querySelector('.error-text');
  if (!file) {
    errorText.textContent = '';
    return;
  }

  const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'];
  const maxSize = 2 * 1024 * 1024; // 2MB

  if (!allowedTypes.includes(file.type)) {
    errorText.textContent = 'Only PNG, JPEG, JPG, and WEBP images are allowed.';
    event.target.value = '';
    return;
  }

  if (file.size > maxSize) {
    errorText.textContent = 'Image size must be less than 2MB.';
    event.target.value = '';
    return;
  }

  // Clear error if valid
  errorText.textContent = '';
});
