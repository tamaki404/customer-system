document.addEventListener('DOMContentLoaded', function(){
    var passwordInput = document.getElementById('password');
    var errorMessage = passwordInput.nextElementSibling;

    passwordInput.addEventListener('input', function(){
        if (passwordInput.value.length < 6) {
            errorMessage.textContent = 'Password must be at least 6 characters long.';
        } else {
            errorMessage.textContent = '';
        }
    });
});
