document.addEventListener('DOMContentLoaded', () => {
    const modifyButtons = document.querySelectorAll('.modify-btn');
    const repNameInput = document.getElementById('rep-name-input');
    const repRoleInput = document.getElementById('rep-role-input');
    const repIdInput = document.getElementById('rep-repid-input');

    modifyButtons.forEach(button => {
        button.addEventListener('click', () => {
            // basic data
            repNameInput.value = button.getAttribute('data-name');
            repRoleInput.value = button.getAttribute('data-role');
            repIdInput.value = button.getAttribute('data-repid');

            // parse permissions object
            const perm = JSON.parse(button.getAttribute('data-permissions'));

            // loop through each permission and set checkbox checked state
            Object.keys(perm).forEach(key => {
                const checkbox = document.querySelector(`input[name="${key}"]`);
                if (checkbox) {
                    checkbox.checked = perm[key] === true;
                }
            });
        });
    });
});
