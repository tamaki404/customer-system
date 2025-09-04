       // Image upload functionality
        document.getElementById('image').addEventListener('change', function() {
            const saveBtn = document.querySelector('.save-btn');
            if (this.files.length > 0) {
                saveBtn.style.display = 'inline-block';
            } else {
                saveBtn.style.display = 'none';
            }
        });

        // Modal functions
        function openEditModal() {
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function openPasswordModal() {
            document.getElementById('passwordModal').style.display = 'block';
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').style.display = 'none';
        }

                 function openStatusModal() {
             document.getElementById('statusModal').style.display = 'block';
         }
 
         function closeStatusModal() {
             document.getElementById('statusModal').style.display = 'none';
         }
 
         function openDeactivateModal() {
             document.getElementById('deactivateModal').style.display = 'block';
         }
 
         function closeDeactivateModal() {
             document.getElementById('deactivateModal').style.display = 'none';
         }

        function openDeleteModal() {
            document.getElementById('deleteModal').style.display = 'block';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }

        // Auto-hide success/error messages after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s ease';
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 500);
            });
        }, 5000);