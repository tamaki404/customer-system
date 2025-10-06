document.querySelectorAll('.docu-file').forEach(input => {
  input.addEventListener('change', function(event) {
    const file = event.target.files[0];
    const errorText = this.parentElement.querySelector('.error-text');
    
    if (!file) {
      errorText.textContent = '';
      errorText.style.display = 'none';
      return;
    }

    const accept = this.getAttribute('accept'); // e.g. "application/pdf" or "image/*"
    let allowedTypes = [];

    if (accept.includes('image')) {
      allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'];
    } else if (accept.includes('pdf')) {
      allowedTypes = ['application/pdf'];
    }

    const maxSize = 2 * 1024 * 1024; // 2MB

    if (!allowedTypes.includes(file.type)) {
      errorText.textContent = `Only ${allowedTypes.map(t => t.split('/')[1].toUpperCase()).join(', ')} files are allowed.`;
      errorText.style.display = 'block';
      event.target.value = '';
      return;
    }

    if (file.size > maxSize) {
      errorText.textContent = 'File size must be less than 2MB.';
      errorText.style.display = 'block';
      event.target.value = '';
      return;
    }

    // Clear error if valid
    errorText.textContent = '';
    errorText.style.display = 'none';
  });
});
