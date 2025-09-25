let authCount = 1;
const maxAuths = 3;

function addAuthRow() {
    const container = document.getElementById('auth-container');
    const addBtn = document.getElementById('add-auth-btn');
    
    // Grab the existing error-text paragraph inside auth-container (first one)
    const errorText = container.querySelector('.error-text-auth');

    if (authCount >= maxAuths) {
        errorText.textContent = 'Maximum of 3 authorized representatives allowed.';
        errorText.style.display = 'block';
        errorText.style.color = '#ec1e27';
        errorText.style.fontWeight = 'bold';

        addBtn.disabled = true;
        addBtn.classList.add('btn-disabled');
        addBtn.style.display = 'none';

        // Hide error message after 3 seconds
        // setTimeout(() => {
        //     errorText.style.display = 'none';
        //     errorText.textContent = '';
        // }, 3000);

        return;
    }

    authCount++;

    // Create new authorized rep row (same as your existing code)
    const newRow = document.createElement('div');
    newRow.style.cssText = 'display: flex; flex-direction: row; flex-wrap: wrap; gap:10px; margin-top:10px;';
    newRow.className = 'auth-set';

    newRow.innerHTML = `
        <div class="input-forms">
            <label><span class="req-asterisk">*</span> Name</label>
            <div>
                <input type="text" name="lastname" placeholder="Last name" required maxlength="50">
                <input type="text" name="firstname" placeholder="First name" required maxlength="50">
                <input type="text" name="middlename" placeholder="Middle name" maxlength="50">
            </div>
            <p class="error-text" style="display: none"></p>
        </div>
        <div class="input-forms">
            <label><span class="req-asterisk">*</span> Position</label>
            <div>
                <input type="text" name="auth_position" required maxlength="50">
            </div>
            <p class="error-text" style="display: none"></p>
        </div>
        <div class="input-forms">
            <label><span class="req-asterisk">*</span> Contact no.</label>
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
    const errorText = container.querySelector('p.error-text');

    if (authCount >= maxAuths) {
        addBtn.disabled = true;
        addBtn.style.display = "none";
        addBtn.classList.add('btn-disabled');

        errorText.textContent = 'Maximum of 3 authorized representatives allowed.';
        errorText.style.display = 'block';
        errorText.style.color = '#ec1e27';
        errorText.style.fontWeight = 'bold';

        setTimeout(() => {
            errorText.style.display = 'none';
            errorText.textContent = '';
        }, 3000);

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
