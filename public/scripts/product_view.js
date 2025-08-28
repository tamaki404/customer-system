        document.addEventListener('DOMContentLoaded', function() {
            /** DELETE MODAL **/
            const openDeleteBtn = document.getElementById('openDeleteModalBtn');
            const deleteModal = document.getElementById('deleteModal');
            const closeDeleteBtn = document.getElementById('closeDeleteModalBtn');
            const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');

            if (openDeleteBtn && deleteModal && closeDeleteBtn) {
                openDeleteBtn.onclick = () => { deleteModal.style.display = 'flex'; };
                closeDeleteBtn.onclick = () => { deleteModal.style.display = 'none'; };
                if (cancelDeleteBtn) cancelDeleteBtn.onclick = () => { deleteModal.style.display = 'none'; };
            }

            /** EDIT PRODUCT MODAL **/
            const editModal = document.getElementById('modalmodifyProduct');
            const closeEditBtn = document.getElementById('closeModifyProduct');

            document.querySelectorAll('.openModifyProduct').forEach(btn => {
                btn.addEventListener('click', () => {
                    editModal.style.display = 'flex';
                });
            });

            if (closeEditBtn) {
                closeEditBtn.onclick = () => { editModal.style.display = 'none'; };
            }

            /** STOCK MODAL **/
            const stockModal = document.getElementById('stockModal');
            const closeStockBtn = document.getElementById('closeStockModal');

            document.querySelectorAll('.openStockBtn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const productId = btn.dataset.productId;
                    const form = document.getElementById('addStockForm');
                    form.action = `/products/${productId}/addStock`;
                    stockModal.style.display = 'flex';
                });
            });

            if (closeStockBtn) {
                closeStockBtn.onclick = () => { stockModal.style.display = 'none'; };
            }

            /** WINDOW CLICK CLOSE **/
            window.onclick = function(event) {
                if (event.target === deleteModal) deleteModal.style.display = 'none';
                if (event.target === editModal) editModal.style.display = 'none';
                if (event.target === stockModal) stockModal.style.display = 'none';
            };

            /** SUBMIT BUTTON LOADER (avoid same id issue) **/
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    let btn = form.querySelector('button[type="submit"]');
                    if (btn) {
                        btn.disabled = true;
                        btn.innerText = "Processing...";
                    }
                });
            });
        });

            document.getElementById('addStockForm').addEventListener('submit', function(event) {
            let button = document.getElementById('submitBtn');
            button.disabled = true;
            button.innerText = "Processing...";
        });