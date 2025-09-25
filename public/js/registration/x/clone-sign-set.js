let signatureCount = 1;
const maxSignatures = 3;

function addSignatureRow() {
    const container = document.getElementById('signature-container');
    const addBtn = document.querySelector('.add-signatory-btn');  // consistent class
    const maxDisplay = document.querySelector('.max-error-display');

    // Check max before adding
    if (signatureCount >= maxSignatures) {
        // Show max message
        maxDisplay.style.display = "flex";
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
            <label for="sign-name-${signatureCount}"><span class="req-asterisk">*</span> Name</label>
            <div>
                <input id="sign-name-${signatureCount}" type="text" name="lastname" placeholder="Last name" required maxlength="50">
                <input type="text" name="firstname" placeholder="First name" required maxlength="50">
                <input type="text" name="middlename" placeholder="Middle name" maxlength="50">
            </div>
            <p class="error-text" style="display: none"></p>
        </div>
        <div class="input-forms">
            <label for="position-${signatureCount}"><span class="req-asterisk">*</span> Position</label>
            <div>
                <input id="position-${signatureCount}" type="text" name="position" required maxlength="50">
            </div>
            <p class="error-text" style="display: none"></p>
        </div>
        <div class="input-forms">
            <label for="id-signature-${signatureCount}"><span class="req-asterisk">*</span> E-signature</label>
            <div>
                <input type="file" id="id-signature-${signatureCount}" name="e_signature" accept="image/*" required>
            </div>
            <p class="error-text" style="display: none"></p>
        </div>

        <button type="button" class="remove-btn" onclick="removeSignatureRow(this)"><span class="material-symbols-outlined">remove</span></button>
    `;

    container.appendChild(newRow);

    // Update UI after adding
    updateSignatureUI();
}

function removeSignatureRow(button) {
    const container = document.getElementById('signature-container');
    const addBtn = document.querySelector('.add-signatory-btn'); // consistent class
    const maxDisplay = document.querySelector('.max-error-display');

    // Prevent removing last row
    if (container.children.length <= 1) {
        alert('At least one signature row is required');
        return;
    }

    button.parentElement.remove();
    signatureCount--;

    // Update UI after removing
    updateSignatureUI();
}

function updateSignatureUI() {
    const addBtn = document.querySelector('.add-signatory-btn');
    const maxDisplay = document.querySelector('.max-error-display');

    if (signatureCount >= maxSignatures) {
        addBtn.disabled = true;
        addBtn.style.display = "none";
        addBtn.classList.add('btn-disabled');
        maxDisplay.style.display = "flex";
    } else {
        addBtn.disabled = false;
        addBtn.classList.remove('btn-disabled');
        maxDisplay.style.display = "none";
        addBtn.style.display = "flex";

    }
}

// Call update UI on load to set initial state
document.addEventListener('DOMContentLoaded', () => {
    updateSignatureUI();
});
