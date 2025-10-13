

document.addEventListener('DOMContentLoaded', () => {
    const modifyButtons = document.querySelectorAll('.modify-btn');
    const repNameInput = document.getElementById('rep-name-input');
    const repRoleInput = document.getElementById('rep-role-input');
    const repIdInput = document.getElementById('rep-repid-input');

    modifyButtons.forEach(button => {
        button.addEventListener('click', () => {
            const name = button.getAttribute('data-name');
            repNameInput.value = name;
            const role = button.getAttribute('data-role');
            repRoleInput.value = role;
            const rep_id = button.getAttribute('data-repid');
            repIdInput.value = rep_id;
        });
    });
});
