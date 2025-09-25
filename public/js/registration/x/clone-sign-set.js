let signatureCount = 1;
const maxSignatures = 3;

function addSignatureRow() {
    const container = document.getElementById('signature-container');
    const addBtn = document.querySelector('.add-signatory-btn');
    const displayMessage = document.querySelector('.display-message');
    const errorText = displayMessage.querySelector('.error-text');

    if (signatureCount >= maxSignatures) {
        // Show error message
        errorText.textContent = 'Maximum of 3 signatories allowed.';
        displayMessage.style.display = "flex";
        addBtn.disabled = true;
        addBtn.classList.add('btn-disabled');
        return;
    }

    signatureCount++;

    // Create new row
    const newRow = document.createElement('div');
    newRow.className = 'sign-set';
    newRow.style.cssText = 'display: flex; flex-direction: row; flex-wrap: wrap; gap:10px';

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
                <input id="position-${signatureCount}" type="text" name="position" required maxlength="50">
            </div>
            <p class="error-text" style="display: none"></p>
        </div>
        <div class="input-forms">
            <div>
                <input type="file" id="id-signature-${signatureCount}" name="e_signature" accept="image/*" required>
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

    if (container.children.length <= 1) {
        alert('At least one signature row is required');
        return;
    }

    button.parentElement.remove();
    signatureCount--;

    updateSignatureUI();
}

function updateSignatureUI() {
    const addBtn = document.querySelector('.add-signatory-btn');
    const displayMessage = document.querySelector('.display-message');
    const errorText = displayMessage.querySelector('.error-text');

    if (signatureCount >= maxSignatures) {
        addBtn.disabled = true;
        addBtn.style.display = "none";
        addBtn.classList.add('btn-disabled');

        errorText.textContent = 'Maximum of 3 signatories allowed.';
        displayMessage.style.display = "flex";
        displayMessage.style.backgroundColor = "#ec1e27"; 
        displayMessage.style.color = "#fff"; 

        setTimeout(() => {
            displayMessage.style.display = 'none';
            errorText.textContent = '';
        }, 3000);
    } else {
        addBtn.disabled = false;
        addBtn.classList.remove('btn-disabled');
        addBtn.style.display = "flex";
        displayMessage.style.display = "none";
        errorText.textContent = '';
    }

}

document.addEventListener('DOMContentLoaded', () => {
    updateSignatureUI();
});
