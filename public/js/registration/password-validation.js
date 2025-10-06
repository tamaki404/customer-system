document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('registerForm');
    if (!form) return;

    var passwordInput = document.getElementById('password');
    var confirmInput = document.getElementById('password_confirmation');
    var submitBtn = form.querySelector('button[type="submit"]');

    var lengthCheck = document.getElementById('length-check');
    var numberCheck = document.getElementById('number-check');
    var specialCheck = document.getElementById('special-check');
    var matchCheck = document.getElementById('match-check');

    function evaluatePassword(value) {
        var checks = {
            length: value.length >= 6,
            number: /\d/.test(value),
            special: /[!@#$%^&*(),.?":{}|<>]/.test(value)
        };

        lengthCheck.style.color = checks.length ? '#27ae60' : '#ccc';
        numberCheck.style.color = checks.number ? '#27ae60' : '#ccc';
        specialCheck.style.color = checks.special ? '#27ae60' : '#ccc';

        return checks.length && checks.number && checks.special;
    }

    function evaluateMatch() {
        var ok = passwordInput.value === confirmInput.value;
        matchCheck.style.color = ok ? '#27ae60' : '#ccc';

        var matchError = document.getElementById('password-match-error');
        if (matchError) {
            matchError.style.display = ok ? 'none' : 'block';
            matchError.textContent = ok ? '' : 'Passwords do not match.';
        }

        return ok;
    }

    function updateState() {
        var strong = evaluatePassword(passwordInput.value);
        var matches = evaluateMatch();
        if (submitBtn) submitBtn.disabled = !(strong && matches);
    }

    passwordInput.addEventListener('input', updateState);
    confirmInput.addEventListener('input', updateState);

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
