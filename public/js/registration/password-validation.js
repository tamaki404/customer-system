document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('registerForm');
    if (!form) return;

    var passwordInput = document.getElementById('password');
    var confirmInput = document.getElementById('password_confirmation');
    var submitBtn = form.querySelector('button[type="submit"]');
    var matchError = document.getElementById('password-match-error');

    var lengthCheck = document.getElementById('length-check');
    var upperCheck = document.getElementById('uppercase-check');
    var specialCheck = document.getElementById('special-check');

    function evaluatePassword(value) {
        var checks = {
            length: value.length >= 6,
        };

        if (lengthCheck) lengthCheck.style.color = checks.length ? '#27ae60' : '#ccc';
        if (upperCheck) upperCheck.style.display = 'none';
        if (specialCheck) specialCheck.style.display = 'none';

        return checks.length;
    }

    function evaluateMatch() {
        var ok = passwordInput.value === confirmInput.value;
        if (matchError) {
            if (!ok) {
                matchError.textContent = 'Passwords do not match.';
                matchError.style.display = 'block';
            } else {
                matchError.textContent = '';
                matchError.style.display = 'none';
            }
        }
        return ok;
    }

    function updateState() {
        var strong = evaluatePassword(passwordInput.value);
        var matches = evaluateMatch();
        if (submitBtn) submitBtn.disabled = !(strong && matches);
    }

    if (passwordInput) passwordInput.addEventListener('input', updateState);
    if (confirmInput) confirmInput.addEventListener('input', updateState);

    if (form) {
        form.addEventListener('submit', function (e) {
            var strong = evaluatePassword(passwordInput.value);
            var matches = evaluateMatch();
            if (!(strong && matches)) {
                e.preventDefault();
                updateState();
            }
        });
    }

    // initialize state
    updateState();
});


