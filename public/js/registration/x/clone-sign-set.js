let signatureCount = 1;
const maxSignatures = 3;

function addSignatureRow() {
    const container = document.getElementById('signature-container');
    const addBtn = document.getElementById('add-signatory-btn');
    const errorText = container.querySelector('.error-text-sign');

    if (signatureCount >= maxSignatures) {
        errorText.textContent = 'Maximum of 3 signatories allowed.';
        errorText.style.display = 'block';
        errorText.style.color = '#ec1e27';
        errorText.style.fontWeight = 'bold';

        addBtn.disabled = true;
        addBtn.classList.add('btn-disabled');
        addBtn.style.display = 'none';

        setTimeout(() => {
            errorText.style.display = 'none';
            errorText.textContent = '';
        }, 3000);

        return;
    }

    signatureCount++;

    const newRow = document.createElement('div');
    newRow.className = 'sign-set';
    newRow.style.cssText = 'display: flex; flex-direction: row; flex-wrap: wrap; gap: 10px; margin-top: 10px;';

    newRow.innerHTML = `
        <div class="input-forms">
            <div>
                <input id="sign-name-${signatureCount}" type="text" name="lastname" placeholder="Last name" required maxlength="50">
                <input type="text" name="firstname" placeholder="First name" required maxlength="50">
                <input type="text" name="middlename" placeholder="Middle name" maxlength="50">
            </div>
            <p class="error-text" style="display: none"></p>
        </div>
        <div class="input-forms">
            <div>
                <input id="position-${signatureCount}" type="text" name="sign_position" required maxlength="50">
            </div>
            <p class="error-text" style="display: none"></p>
        </div>
        <div class="input-forms">
            <div>
                <input type="file" id="id-signature-${signatureCount}" name="e_image" accept="image/*" required>
            </div>
            <p class="error-text" style="display: none"></p>
        </div>

        <button type="button" class="remove-btn" onclick="removeSignatureRow(this)">
            <span class="material-symbols-outlined">remove</span>
        </button>
    `;

    container.appendChild(newRow);

    updateSignatureUI();
}

function removeSignatureRow(button) {
    const container = document.getElementById('signature-container');

    if (container.querySelectorAll('.sign-set').length <= 1) {
        alert('At least one signature row is required');
        return;
    }

    button.parentElement.remove();
    signatureCount--;

    updateSignatureUI();
}

function updateSignatureUI() {
    const addBtn = document.getElementById('add-signatory-btn');
    const container = document.getElementById('signature-container');
    const errorText = container.querySelector('p.error-text');

    if (signatureCount >= maxSignatures) {
        addBtn.disabled = true;
        addBtn.style.display = "none";
        addBtn.classList.add('btn-disabled');

        errorText.textContent = 'Maximum of 3 signatories allowed.';
        errorText.style.display = 'block';
        errorText.style.color = '#ec1e27';
        errorText.style.fontWeight = 'bold';

        // setTimeout(() => {
        //     errorText.style.display = 'none';
        //     errorText.textContent = '';
        // }, 3000);

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

document.addEventListener('DOMContentLoaded', () => {
    updateAuthUI();
    updateSignatureUI();
});
