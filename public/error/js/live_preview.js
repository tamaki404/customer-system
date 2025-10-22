        document.addEventListener('DOMContentLoaded', function() {
            const idImageInput = document.getElementById('id-image');
            
            if (idImageInput) {
                idImageInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    const previewImg = document.getElementById('preview-img');
                    const previewText = document.getElementById('preview-text');

                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImg.src = e.target.result;
                            previewImg.style.display = 'block';
                            previewText.style.display = 'none';
                        }
                        reader.readAsDataURL(file);
                    } else {
                        previewImg.style.display = 'none';
                        previewText.style.display = 'block';
                        previewText.textContent = 'Invalid file type. Please select an image.';
                    }
                });
            }

            // PDF Preview Handler
            const pdfInputs = document.querySelectorAll('.pdf-input');
            
            pdfInputs.forEach(input => {
                input.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    const box = e.target.closest('.doc-preview-box');
                    const fileName = box.querySelector('.selected-file');
                    const previewContainer = box.querySelector('.preview-container');

                    if (file) {
                        fileName.textContent = file.name;

                        if (file.type === "application/pdf") {
                            const fileURL = URL.createObjectURL(file);
                            
                            previewContainer.innerHTML = '';
                            
                            const newFrame = document.createElement('iframe');
                            newFrame.src = fileURL;
                            newFrame.width = "100%";
                            newFrame.height = "200";
                            newFrame.style.border = "1px solid #ccc";
                            newFrame.style.borderRadius = "6px";
                            
                            previewContainer.appendChild(newFrame);
                        } else {
                            previewContainer.innerHTML = '<p class="no-preview">Invalid file type. Please select a PDF.</p>';
                        }
                    } else {
                        fileName.textContent = 'No file selected';
                    }
                });
            });
        });
    