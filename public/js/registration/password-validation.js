
document.addEventListener('DOMContentLoaded', function () {
    // Account password elements
    const accountForm = document.getElementById('registerForm');
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    const submitBtn = accountForm?.querySelector('button[type="submit"]');

    const lengthCheck = document.getElementById('length-check');
    const numberCheck = document.getElementById('number-check');
    const specialCheck = document.getElementById('special-check');
    const matchCheck = document.getElementById('match-check');

    // Gate password elements
    const gatePassword = document.getElementById('gate_password');
    const gateConfirm = document.getElementById('gate_password_confirmation');

    const gateLength = document.getElementById('gate-length-check');
    const gateNumber = document.getElementById('gate-number-check');
    const gateSpecial = document.getElementById('gate-special-check');
    const gateMatch = document.getElementById('gate-match-check');

    const gateError = document.getElementById('gate-password-match-error');

    function evaluatePassword(value, checksUI) {
        const checks = {
            length: value.length >= 6,
            number: /\d/.test(value),
            special: /[!@#$%^&*(),.?":{}|<>]/.test(value)
        };

        checksUI.length.style.color = checks.length ? '#27ae60' : '#ccc';
        checksUI.number.style.color = checks.number ? '#27ae60' : '#ccc';
        checksUI.special.style.color = checks.special ? '#27ae60' : '#ccc';

        return checks.length && checks.number && checks.special;
    }

    function evaluateMatch(passInput, confirmInput, matchUI, errorUI = null) {
        const ok = passInput.value === confirmInput.value && passInput.value.length > 0;
        matchUI.style.color = ok ? '#27ae60' : '#ccc';

        if (errorUI) {
            errorUI.style.display = ok ? 'none' : 'block';
            errorUI.textContent = ok ? '' : 'Passwords do not match.';
        }

        return ok;
    }

    function updateState() {
        const strong = evaluatePassword(passwordInput.value, {
            length: lengthCheck,
            number: numberCheck,
            special: specialCheck
        });
        const matches = evaluateMatch(passwordInput, confirmInput, matchCheck, document.getElementById('password-match-error'));

        const gateStrong = evaluatePassword(gatePassword.value, {
            length: gateLength,
            number: gateNumber,
            special: gateSpecial
        });
        const gateMatches = evaluateMatch(gatePassword, gateConfirm, gateMatch, gateError);

        if (submitBtn) submitBtn.disabled = !(strong && matches && gateStrong && gateMatches);
    }

    // Event listeners for both password fields
    [passwordInput, confirmInput, gatePassword, gateConfirm].forEach(input => {
        if (input) input.addEventListener('input', updateState);
    });

    if (accountForm) {
        accountForm.addEventListener('submit', function (e) {
            const strong = evaluatePassword(passwordInput.value, {
                length: lengthCheck,
                number: numberCheck,
                special: specialCheck
            });
            const matches = evaluateMatch(passwordInput, confirmInput, matchCheck, document.getElementById('password-match-error'));

            const gateStrong = evaluatePassword(gatePassword.value, {
                length: gateLength,
                number: gateNumber,
                special: gateSpecial
            });
            const gateMatches = evaluateMatch(gatePassword, gateConfirm, gateMatch, gateError);

            if (!(strong && matches && gateStrong && gateMatches)) {
                e.preventDefault();
                updateState();
            }
        });
    }

    updateState(); // Initialize state
});

