function showLoginForm(selected) {
    document.querySelectorAll('.loginForm .rep-inputs').forEach(inputs => {
        inputs.style.display = 'none';
    });
    const form = selected.closest('.loginForm');
    const inputs = form.querySelector('.rep-inputs');
    inputs.style.display = 'block';
}