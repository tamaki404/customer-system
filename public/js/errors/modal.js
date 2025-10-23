        document.getElementById('status').addEventListener('change', function() {
            const feedbackContainer = document.getElementById('feedback-container');
            const feedbackTextarea = document.getElementById('feedback');
            
            if (this.value === 'Declined') {
                feedbackContainer.classList.add('show');
                feedbackTextarea.required = true;
            } else {
                feedbackContainer.classList.remove('show');
                feedbackTextarea.required = false;
                feedbackTextarea.value = ''; 
            }
        });

        // Reset modal form on close
        const modal = document.getElementById('request-action');
        modal.addEventListener('hidden.bs.modal', function () {
            const form = this.querySelector('form');
            form.reset();
            document.getElementById('feedback-container').classList.remove('show');
            document.getElementById('feedback').required = false;
        });