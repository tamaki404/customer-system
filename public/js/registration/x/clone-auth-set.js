let authCount = 1;
const maxAuths = 3;
function addAuthRow() {
    const container = document.getElementById('auth-container');
    const addBtn = document.getElementById('add-auth-btn');

    let displayMessage = container.querySelector('.display-message');
    if (!displayMessage) {
        displayMessage = document.createElement('div');
        displayMessage.className = 'display-message';
        displayMessage.style.display = 'none';
        displayMessage.style.padding = '10px';
        displayMessage.style.marginTop = '10px';
        displayMessage.style.borderRadius = '5px';
        displayMessage.style.fontWeight = 'bold';

        const errorText = document.createElement('p');
        errorText.className = 'error-text';
        displayMessage.appendChild(errorText);

        container.appendChild(displayMessage);
    }
    const errorText = displayMessage.querySelector('.error-text');

    if (authCount >= maxAuths) {
        errorText.textContent = 'Maximum of 3 authorized representatives allowed.';
        displayMessage.style.display = "flex";
        displayMessage.style.backgroundColor = "#ec1e27"; 
        displayMessage.style.color = "#fff"; 
        addBtn.disabled = true;
        addBtn.classList.add('btn-disabled');
        return;
    }

    authCount++;

    // Create new row for authorized rep
    const newRow = document.createElement('div');
    newRow.style.cssText = 'display: flex; flex-direction: row; flex-wrap: wrap; gap:10px; margin-top:10px;';
    newRow.className = 'auth-set';

    newRow.innerHTML = `
        <div class="input-forms">
            <div>
                <input type="text" name="lastname" placeholder="Last name" required maxlength="50">
                <input type="text" name="firstname" placeholder="First name" required maxlength="50">
                <input type="text" name="middlename" placeholder="Middle name" maxlength="50">
            </div>
            <p class="error-text" style="display: none"></p>
        </div>
        <div class="input-forms">
            <div>
                <input type="text" name="auth_position" required maxlength="50">
            </div>
            <p class="error-text" style="display: none"></p>
        </div>
        <div class="input-forms">
            <div>
                <input type="text" name="contact" placeholder="ex: 09XX-XXX-XXXX" required maxlength="11">
            </div>
            <p class="error-text" style="display: none"></p>
        </div>

        <button type="button" class="remove-btn" onclick="removeAuthRow(this)">
            <span class="material-symbols-outlined">remove</span>
        </button>
    `;

    container.insertBefore(newRow, addBtn);

    updateAuthUI();
}

function removeAuthRow(button) {
    const container = document.getElementById('auth-container');


    button.parentElement.remove();
    authCount--;

    updateAuthUI();
}

function updateAuthUI() {
    const addBtn = document.getElementById('add-auth-btn');
    const container = document.getElementById('auth-container');
    const displayMessage = container.querySelector('.display-message');
    const errorText = displayMessage ? displayMessage.querySelector('.error-text') : null;

    if (authCount >= maxAuths) {
        addBtn.disabled = true;
        addBtn.style.display = "none";
        addBtn.classList.add('btn-disabled');
        if (displayMessage && errorText) {
            errorText.textContent = 'Maximum of 3 authorized representatives allowed.';
            displayMessage.style.display = "flex";
            displayMessage.style.backgroundColor = "#ec1e27"; 
            displayMessage.style.color = "#fff"; 
            setTimeout(() => {
                displayMessage.style.display = 'none';
                errorText.textContent = '';
            }, 3000);
        }
    } else {
        addBtn.disabled = false;
        addBtn.style.display = "flex";
        addBtn.classList.remove('btn-disabled');
        if (displayMessage) displayMessage.style.display = "none";
        if (errorText) errorText.textContent = '';
    }



}
    document.addEventListener('DOMContentLoaded', () => {
    updateAuthUI();
    updateSignatureUI();
});