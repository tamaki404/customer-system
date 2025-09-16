document.querySelectorAll('.file-upload-section input[type="file"]').forEach(input => {
    const section = input.closest('.file-upload-section');
    const preview = section.querySelector('.image-preview-container');
    const errorMsg = section.querySelector('.error-message');
    const fileCount = section.querySelector('.file-count');
    let selectedFiles = [];

    input.addEventListener('change', function () {
        errorMsg.textContent = "";

        // Merge new files with old ones
        const newFiles = Array.from(this.files);
        newFiles.forEach(file => {
            if (selectedFiles.length >= 3) return; // limit max 3
            if (!['image/png', 'image/jpeg', 'image/webp'].includes(file.type)) {
                errorMsg.textContent = "Only PNG, JPG, JPEG, or WEBP files are allowed.";
                return;
            }
            if (file.size > 2 * 1024 * 1024) { // 2MB limit
                errorMsg.textContent = "Each file must be less than 2MB.";
                return;
            }
            selectedFiles.push(file);
        });

        // Reset input so user can add more later
        input.value = "";

        // Update preview
        preview.innerHTML = "";
        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const div = document.createElement("div");
                div.classList.add("preview-item");
                div.innerHTML = `
                    <img src="${e.target.result}" alt="preview">
                    <button type="button" class="remove-btn">&times;</button>
                `;
                div.querySelector(".remove-btn").addEventListener("click", () => {
                    selectedFiles.splice(index, 1);
                    updateInputFiles();
                    div.remove();
                    fileCount.textContent = `${selectedFiles.length} file(s) selected`;
                });
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });

        fileCount.textContent = `${selectedFiles.length} file(s) selected`;
        updateInputFiles();
    });

    function updateInputFiles() {
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        input.files = dataTransfer.files;
    }
});