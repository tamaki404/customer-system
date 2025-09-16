document.addEventListener('DOMContentLoaded', function() {
    // Handle multiple file uploads for document sections
    document.querySelectorAll('.file-upload-section input[type="file"]').forEach(input => {
        const section = input.closest('.file-upload-section');
        const preview = section.querySelector('.image-preview-container');
        const errorMsg = section.querySelector('.error-message');
        const fileCount = section.querySelector('.file-count');
        let selectedFiles = [];

        input.addEventListener('change', function(e) {
            const errorMsg = section.querySelector('.error-message');
            // Clear and hide previous errors
            errorMsg.textContent = "";
            errorMsg.style.display = "none";
            
            // Get new files from input
            const newFiles = Array.from(e.target.files);
            
            // Validate and add new files
            newFiles.forEach(file => {
                // Check if we already have 3 files
                if (selectedFiles.length >= 3) {
                    errorMsg.textContent = "Maximum 3 files allowed.";
                    errorMsg.style.display = "block";
                    return;
                }
                
                // Check file type
                if (!['image/png', 'image/jpeg', 'image/webp', 'image/jpg'].includes(file.type)) {
                    errorMsg.textContent = "Only PNG, JPG, JPEG, or WEBP files are allowed.";
                    errorMsg.style.display = "block";
                    return;
                }
                
                // Check file size (2MB limit)
                if (file.size > 2 * 1024 * 1024) {
                    errorMsg.textContent = "Each file must be less than 2MB.";
                    errorMsg.style.display = "block";
                    return;
                }
                
                selectedFiles.push(file);
            });

            // Update the file input with selected files
            updateInputFiles();
            
            // Update preview
            updatePreview();
            
            // Update file count
            fileCount.textContent = `${selectedFiles.length} file(s) selected`;
        });

        function updateInputFiles() {
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            input.files = dataTransfer.files;
        }

        function updatePreview() {
            preview.innerHTML = "";
            
            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement("div");
                    div.classList.add("preview-item");
                    div.style.cssText = `
                        position: relative; 
                        display: inline-block; 
                        margin: 5px; 
                        border: 1px solid #ddd; 
                        border-radius: 4px; 
                        overflow: hidden;
                    `;
                    
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="preview" style="
                            width: 100px; 
                            height: 100px; 
                            object-fit: cover; 
                            display: block;
                        ">
                        <button type="button" class="remove-btn" style="
                            position: absolute; 
                            top: 2px; 
                            right: 2px; 
                            background: rgba(255,0,0,0.8); 
                            color: white; 
                            border: none; 
                            border-radius: 50%; 
                            width: 20px; 
                            height: 20px; 
                            cursor: pointer; 
                            font-size: 12px; 
                            line-height: 1;
                        ">&times;</button>
                    `;
                    
                    // Add remove functionality
                    div.querySelector(".remove-btn").addEventListener("click", function() {
                        selectedFiles.splice(index, 1);
                        updateInputFiles();
                        updatePreview();
                        fileCount.textContent = `${selectedFiles.length} file(s) selected`;
                        
                        // Clear error message if files are now valid
                        const errorMsg = section.querySelector('.error-message');
                        if (selectedFiles.length <= 3) {
                            errorMsg.textContent = "";
                            errorMsg.style.display = "none";
                        }
                    });
                    
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }
    });

    // Handle company logo upload with automatic error display
    const companyImageInput = document.getElementById('company-image');
    if (companyImageInput) {
        const logoErrorMsg = companyImageInput.parentElement.querySelector('.error-message');
        
        companyImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Clear previous error and show element
                if (logoErrorMsg) {
                    logoErrorMsg.textContent = "";
                    logoErrorMsg.style.display = "none";
                }
                
                // Validate file type
                if (!['image/png', 'image/jpeg', 'image/webp', 'image/jpg'].includes(file.type)) {
                    if (logoErrorMsg) {
                        logoErrorMsg.textContent = "Only PNG, JPG, JPEG, or WEBP files are allowed for company logo.";
                        logoErrorMsg.style.display = "block";
                    }
                    e.target.value = "";
                    return;
                }
                
                // Validate file size (2MB limit)
                if (file.size > 2 * 1024 * 1024) {
                    if (logoErrorMsg) {
                        logoErrorMsg.textContent = "Company logo must be less than 2MB.";
                        logoErrorMsg.style.display = "block";
                    }
                    e.target.value = "";
                    return;
                }
                
                // Remove existing preview
                const existingPreview = companyImageInput.parentElement.querySelector('.logo-preview');
                if (existingPreview) {
                    existingPreview.remove();
                }
                
                // Show preview for company logo
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('div');
                    preview.className = 'logo-preview';
                    preview.style.cssText = 'margin-top: 10px;';
                    preview.innerHTML = `
                        <img src="${e.target.result}" alt="Company Logo Preview" style="
                            max-width: 150px; 
                            max-height: 150px; 
                            border: 1px solid #ddd; 
                            border-radius: 4px;
                        ">
                        <p style="font-size: 11px; color: #666; margin-top: 5px;">
                            ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)
                        </p>
                    `;
                    companyImageInput.parentElement.appendChild(preview);
                };
                reader.readAsDataURL(file);
            } else {
                // Clear preview if no file selected
                const existingPreview = companyImageInput.parentElement.querySelector('.logo-preview');
                if (existingPreview) {
                    existingPreview.remove();
                }
            }
        });
    }

    // Form submission validation
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            let hasErrors = false;
            
            // Check if all required file uploads have files
            const requiredFileInputs = document.querySelectorAll('.file-upload-section input[type="file"][required]');
            requiredFileInputs.forEach(input => {
                if (input.files.length === 0) {
                    const section = input.closest('.file-upload-section');
                    const errorMsg = section.querySelector('.error-message');
                    const label = section.querySelector('label').textContent.replace('*', '').replace(/\(.*?\)/g, '').trim();
                    
                    errorMsg.textContent = `${label} is required.`;
                    errorMsg.style.display = "block";
                    hasErrors = true;
                }
            });
            
            // Check company logo
            const companyImage = document.getElementById('company-image');
            if (companyImage && companyImage.files.length === 0) {
                const logoErrorMsg = companyImage.parentElement.querySelector('.error-message');
                if (logoErrorMsg) {
                    logoErrorMsg.textContent = "Company image/logo is required.";
                    logoErrorMsg.style.display = "block";
                }
                hasErrors = true;
            }
            
            // Check other required fields
            const requiredInputs = document.querySelectorAll('input[required], select[required]');
            requiredInputs.forEach(input => {
                if (input.type !== 'file' && !input.value.trim()) {
                    // Find or create error message element
                    let errorMsg = input.parentElement.querySelector('.error-message');
                    if (!errorMsg) {
                        errorMsg = document.createElement('p');
                        errorMsg.className = 'error-message';
                        errorMsg.style.cssText = 'color: #e74c3c; font-size: 12px; margin-top: 5px;';
                        input.parentElement.appendChild(errorMsg);
                    }
                    
                    const label = input.parentElement.querySelector('label');
                    const fieldName = label ? label.textContent.replace('*', '').trim() : input.name;
                    errorMsg.textContent = `${fieldName} is required.`;
                    errorMsg.style.display = "block";
                    hasErrors = true;
                }
            });
            
            if (hasErrors) {
                e.preventDefault();
                // Scroll to first error
                const firstError = document.querySelector('.error-message:not(:empty)');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }
});