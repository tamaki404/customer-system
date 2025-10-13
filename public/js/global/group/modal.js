

document.addEventListener('DOMContentLoaded', () => {
    const modifyButtons = document.querySelectorAll('.modify-btn');
    const repEmailInput = document.getElementById('rep-email-input');
    const repRoleInput = document.getElementById('rep-role-input');

    modifyButtons.forEach(button => {
        button.addEventListener('click', () => {
            const email = button.getAttribute('data-email');
            repEmailInput.value = email;
            const role = button.getAttribute('data-role');
            repRoleInput.value = role;
        });
    });
});
