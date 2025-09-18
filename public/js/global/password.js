
document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('add-staff-modal');
    if (!modal) return;
    var form = modal.querySelector('form');
    var password = form.querySelector('input[name="password"]');
    var confirm = form.querySelector('input[name="password_confirmation"]');
    var submit = document.getElementById('add-staff-submit');

    function strong(val){
        return val.length > 0 && val.length <= 8 && /[A-Z]/.test(val) && /[@$!%*?&]/.test(val);
    }

    function valid(){
        return strong(password.value) && password.value === confirm.value;
    }

    function update(){
        if (!strong(password.value)) {
            password.setCustomValidity('Password must be ≤ 8 chars and include at least 1 uppercase and 1 special character.');
        } else {
            password.setCustomValidity('');
        }
        if (confirm.value && password.value !== confirm.value) {
            confirm.setCustomValidity('Passwords do not match.');
        } else {
            confirm.setCustomValidity('');
        }
        if (submit) submit.disabled = !valid();

        var len = document.getElementById('staff-length-check');
        var match = document.getElementById('staff-match-check');
        if (len) len.style.color = (password.value.length >= 6) ? '#27ae60' : '#ccc';
        if (match) match.style.color = (password.value && confirm.value && password.value === confirm.value) ? '#27ae60' : '#ccc';
    }

    if (password) password.addEventListener('input', update);
    if (confirm) confirm.addEventListener('input', update);
    if (form) form.addEventListener('submit', function(e){ if (!valid()) { e.preventDefault(); update(); } });
    update();
});
