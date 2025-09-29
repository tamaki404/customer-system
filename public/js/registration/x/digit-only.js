document.getElementById('contact').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
    if (value.length > 11) {
        value = value.substring(0, 11);
    }
    e.target.value = value;
});