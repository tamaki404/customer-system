       document.addEventListener('DOMContentLoaded', function () {
            
            // --- Your existing flash message script ---
            const flashMessage = document.getElementById('flash-message');
            if (flashMessage) {
                flashMessage.style.opacity = 1;
                setTimeout(() => {
                    flashMessage.style.opacity = 0;
                    setTimeout(() => {
                        flashMessage.remove();
                    }, 400);
                }, 3000);
            }


            // --- Constants (matching your controller's validation) ---
            const IMAGE_MAX_SIZE = 2 * 1024 * 1024; // 2MB (2048 KB)
            const PDF_MAX_SIZE = 5 * 1024 * 1024;   // 5MB (5120 KB)


            function showFileError(inputElement, message) {
                clearFileError(inputElement); // Remove old error first
                const errorElement = document.createElement('p');
                errorElement.className = 'file-error-message';
                errorElement.style.color = '#D8000C'; // Red color for error
                errorElement.style.fontSize = '0.9em';
                errorElement.style.marginTop = '5px';
                errorElement.textContent = message;
                inputElement.parentNode.insertBefore(errorElement, inputElement.nextSibling);
            }


            function clearFileError(inputElement) {
                const errorElement = inputElement.parentNode.querySelector('.file-error-message');
                if (errorElement) {
                    errorElement.remove();
                }
            }

            // --- 1. ID Image Handler ---
            const idImageInput = document.getElementById('id-image');
            const idImagePreviewImg = document.getElementById('preview-img');
            const idImagePreviewText = document.getElementById('preview-text');
            const originalImageSrc = idImagePreviewImg ? idImagePreviewImg.src : ''; // Store original image

            if (idImageInput) {
                idImageInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    clearFileError(this); // Clear previous errors

                    if (!file) {
                        // No file selected, reset to original
                        idImagePreviewImg.src = originalImageSrc;
                        idImagePreviewText.style.display = originalImageSrc ? 'none' : 'block';
                        return;
                    }

                    // Get file size in MB for display
                    const fileSizeMB = (file.size / 1024 / 1024).toFixed(2);

                    // Check file size
                    if (file.size > IMAGE_MAX_SIZE) {
                        showFileError(this, `File must not exceed 2MB.`);
                        this.value = ''; // Clear the invalid input
                        idImagePreviewImg.src = originalImageSrc; // Reset preview
                        idImagePreviewText.style.display = originalImageSrc ? 'none' : 'block';
                        return;
                    }

                    // Check file type (client-side)
                    if (!file.type.startsWith('image/')) {
                         showFileError(this, 'Invalid file type. Please select an image (JPEG, PNG, JPG, GIF).');
                        this.value = ''; // Clear the invalid input
                        idImagePreviewImg.src = originalImageSrc; // Reset preview
                        idImagePreviewText.style.display = originalImageSrc ? 'none' : 'block';
                        return;
                    }

                    // If valid, show preview
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        idImagePreviewImg.src = e.target.result;
                        idImagePreviewImg.style.display = 'block';
                        idImagePreviewText.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                });
            }


            // --- 2. PDF Handlers ---
            

            function handlePdfInput(event, originalHtml) {
                const input = event.target;
                const file = input.files[0];
                const previewBox = input.closest('.doc-preview-box');
                const previewContainer = previewBox.querySelector('.preview-container');
                const fileNameDisplay = previewBox.querySelector('.selected-file');
                
                clearFileError(input); // Clear previous errors

                if (!file) {
                    fileNameDisplay.textContent = 'No file selected';
                    previewContainer.innerHTML = originalHtml; // Reset to original preview
                    return;
                }

                // Get file size in MB for display
                const fileSizeMB = (file.size / 1024 / 1024).toFixed(2);

                // Check file size
                if (file.size > PDF_MAX_SIZE) {
                    showFileError(input, `File must not exceed 5MB.`);
                    input.value = ''; // Clear invalid input
                    fileNameDisplay.textContent = 'No file selected';
                    previewContainer.innerHTML = originalHtml; // Reset to original preview
                    return;
                }

                // Check file type
                if (file.type !== 'application/pdf') {
                    showFileError(input, 'Invalid file type. Please select a PDF.');
                    input.value = ''; // Clear invalid input
                    fileNameDisplay.textContent = 'No file selected';
                    previewContainer.innerHTML = originalHtml; // Reset to original preview
                    return;
                }

                // If valid, show file name and PDF preview
                fileNameDisplay.textContent = file.name;
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContainer.innerHTML = `
                        <iframe
                            src="${e.target.result}"
                            width="100%"
                            height="200"
                            style="border: 1px solid #ccc; border-radius: 6px;">
                        </iframe>
                    `;
                };
                reader.readAsDataURL(file);
            }

            // Get PDF inputs and their original preview states
            const pdfInputOne = document.getElementById('valid_one');
            const pdfInputTwo = document.getElementById('valid_two');
            
            let originalPdfHtmlOne = '<p class="no-preview">No document uploaded yet</p>';
            let originalPdfHtmlTwo = '<p class="no-preview">No document uploaded yet</p>';

            // Store the original HTML (which might be an iframe or the "no preview" text)
            if (pdfInputOne) {
                originalPdfHtmlOne = pdfInputOne.closest('.doc-preview-box').querySelector('.preview-container').innerHTML;
            }
             if (pdfInputTwo) {
                originalPdfHtmlTwo = pdfInputTwo.closest('.doc-preview-box').querySelector('.preview-container').innerHTML;
            }

            // Attach event listeners
            if (pdfInputOne) {
                pdfInputOne.addEventListener('change', (e) => handlePdfInput(e, originalPdfHtmlOne));
            }
            if (pdfInputTwo) {
                pdfInputTwo.addEventListener('change', (e) => handlePdfInput(e, originalPdfHtmlTwo));
            }

        });