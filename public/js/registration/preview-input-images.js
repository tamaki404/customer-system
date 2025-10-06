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
                
                // Check file size (2MB limit - strict)
                if (file.size > 2 * 1024 * 1024) {
                    errorMsg.textContent = "Each file must be exactly 2MB or less.";
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
                
                // Validate file size (2MB limit - strict)
                if (file.size > 2 * 1024 * 1024) {
                    if (logoErrorMsg) {
                        logoErrorMsg.textContent = "Company logo must be exactly 2MB or less.";
                        logoErrorMsg.style.display = "block";
                    }
                    e.target.value = "";
                    return;
                }
                
                // No preview needed for company logo
            }
        });
    }

    // Real-time email checking
    const emailInput = document.getElementById('email_address');
    if (emailInput) {
        let emailTimeout;
        emailInput.addEventListener('input', function() {
            clearTimeout(emailTimeout);
            const email = this.value.trim();
            
            if (email && email.includes('@')) {
                emailTimeout = setTimeout(() => {
                    fetch('/check-email', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ email: email })
                    })
                    .then(response => response.json())
                    .then(data => {
                        const errorMsg = this.parentElement.querySelector('.error-message');
                        if (data.exists) {
                            if (!errorMsg) {
                                const error = document.createElement('p');
                                error.className = 'error-message';
                                error.style.cssText = 'color: #e74c3c; font-size: 12px; margin-top: 5px;';
                                this.parentElement.appendChild(error);
                            }
                            this.parentElement.querySelector('.error-message').textContent = 'This email address is already registered.';
                            this.parentElement.querySelector('.error-message').style.display = 'block';
                        } else {
                            if (errorMsg) {
                                errorMsg.style.display = 'none';
                            }
                        }
                    })
                    .catch(error => console.error('Error checking email:', error));
                }, 500);
            }
        });
    }

    // Password strength validation
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const passwordMatchError = document.getElementById('password-match-error');
    
    if (passwordInput) {
        const lengthCheck = document.getElementById('length-check');
        const uppercaseCheck = document.getElementById('uppercase-check');
        const lowercaseCheck = document.getElementById('lowercase-check');
        const numberCheck = document.getElementById('number-check');
        const specialCheck = document.getElementById('special-check');
        
        function updatePasswordStrength() {
            const password = passwordInput.value;
            
            // Length check
            if (password.length >= 8) {
                lengthCheck.style.color = '#28a745';
            } else {
                lengthCheck.style.color = '#ccc';
            }
            
            // Uppercase check
            if (/[A-Z]/.test(password)) {
                uppercaseCheck.style.color = '#28a745';
            } else {
                uppercaseCheck.style.color = '#ccc';
            }
            
            // Lowercase check
            if (/[a-z]/.test(password)) {
                lowercaseCheck.style.color = '#28a745';
            } else {
                lowercaseCheck.style.color = '#ccc';
            }
            
            // Number check
            if (/\d/.test(password)) {
                numberCheck.style.color = '#28a745';
            } else {
                numberCheck.style.color = '#ccc';
            }
            
            // Special character check
            if (/[@$!%*?&]/.test(password)) {
                specialCheck.style.color = '#28a745';
            } else {
                specialCheck.style.color = '#ccc';
            }
        }
        
        passwordInput.addEventListener('input', updatePasswordStrength);
    }
    
    if (passwordInput && confirmPasswordInput && passwordMatchError) {
        function validatePasswordMatch() {
            if (confirmPasswordInput.value && passwordInput.value !== confirmPasswordInput.value) {
                passwordMatchError.textContent = 'Passwords do not match.';
                passwordMatchError.style.display = 'block';
                return false;
            } else {
                passwordMatchError.style.display = 'none';
                return true;
            }
        }
        
        passwordInput.addEventListener('input', validatePasswordMatch);
        confirmPasswordInput.addEventListener('input', validatePasswordMatch);
    }

    // Form submission validation
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            let hasErrors = false;

            // Server-required fields list (must match server validation rules); ignore HTML required flags
            const requiredFieldNames = [
                // User
                'email_address', 'password', 'password_confirmation',
                // Supplier core
                'company_name','home_street','home_subdivision','home_barangay','home_city',
                'office_street','office_subdivision','office_barangay','office_city',
                'mobile_no','telephone_no','civil_status','citizenship','payment_method',
                
                // Representative
                'rep_last_name','rep_first_name','rep_relationship','rep_contact_no',
                // Signatory
                'signatory_last_name','signatory_first_name','signatory_relationship','signatory_contact_no',
                // Agreement checkbox
                'agreement'
            ];
            
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
            
            // Check password confirmation
            if (passwordInput && confirmPasswordInput) {
                if (!validatePasswordMatch()) {
                    hasErrors = true;
                }
            }
            
            // Check required fields by name list (ignoring hidden step state)
            requiredFieldNames.forEach(name => {
                const input = document.querySelector(`[name="${name}"]`);
                if (!input) return;
                const isCheckbox = input.type === 'checkbox';
                const valueOk = isCheckbox ? input.checked : Boolean(input.value && input.value.toString().trim());
                if (!valueOk) {
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