let authCount = 1;
const maxAuths = 3;

function addAuthRow() {
    const container = document.getElementById('auth-container');
    const addBtn = document.getElementById('add-auth-btn');
    const errorText = container.querySelector('.error-text-auth');

    if (authCount >= maxAuths) {
        errorText.textContent = 'Maximum of 3 authorized representatives allowed.';
        errorText.style.display = 'block';
        errorText.style.color = '#ec1e27';
        errorText.style.fontWeight = 'bold';
        addBtn.disabled = true;
        addBtn.classList.add('btn-disabled');
        addBtn.style.display = 'none';
        return;
    }

    authCount++;

    const newRow = document.createElement('div');
    newRow.style.cssText = 'display: flex; flex-direction: row; flex-wrap: wrap; gap:10px; margin-top:10px;';
    newRow.className = 'auth-set';

    newRow.innerHTML = `
        <div class="input-forms">
            <label><span class="req-asterisk">*</span> Name</label>
            <div>
                <input type="text" name="rep_lastname[]" placeholder="Last name" required maxlength="50">
                <input type="text" name="rep_firstname[]" placeholder="First name" required maxlength="50">
                <input type="text" name="rep_middlename[]" placeholder="Middle name" maxlength="50">
            </div>
            <p class="error-text" style="display: none"></p>
        </div>
        <div class="input-forms">
            <label><span class="req-asterisk">*</span> Position</label>
            <div>
                <input type="text" name="auth_position[]" required maxlength="50">
            </div>
            <p class="error-text" style="display: none"></p>
        </div>
        <div class="input-forms">
            <label><span class="req-asterisk">*</span> Contact no.</label>
            <div>
                <input type="text" name="rep_contact[]" placeholder="ex: 09XX-XXX-XXXX" required maxlength="11">
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
    if (container.querySelectorAll('.auth-set').length <= 1) {
        alert('At least one authorized representative is required');
        return;
    }
    button.parentElement.remove();
    authCount--;
    updateAuthUI();
}

function updateAuthUI() {
    const addBtn = document.getElementById('add-auth-btn');
    const container = document.getElementById('auth-container');
    const errorText = container.querySelector('.error-text-auth');

    if (authCount >= maxAuths) {
        addBtn.disabled = true;
        addBtn.style.display = "none";
        addBtn.classList.add('btn-disabled');
        errorText.textContent = 'Maximum of 3 authorized representatives allowed.';
        errorText.style.display = 'block';
        errorText.style.color = '#ec1e27';
        errorText.style.fontWeight = 'bold';
    } else {
        addBtn.disabled = false;
        addBtn.style.display = "flex";
        addBtn.classList.remove('btn-disabled');
        if (errorText) {
            errorText.style.display = 'none';
            errorText.textContent = '';
        }
    }
}