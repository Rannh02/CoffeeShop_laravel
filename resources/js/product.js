document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('UpdateProductModal');
    const updateForm = document.getElementById('updateForm');
    const closeModalBtn = document.getElementById('closeUpdateModal');

    // Open modal and fill fields when "Edit" button is clicked
    document.querySelectorAll('.editBtn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const price = this.dataset.price;
            const category = this.dataset.category;

            // Set form fields
            document.getElementById('updateProductName').value = name;
            document.getElementById('updateProductPrice').value = price;
            document.getElementById('updateProductCategory').value = category;

            // Set dynamic form action URL
            updateForm.action = `/admin/products/${id}`;

            // Show modal
            modal.style.display = 'flex';
        });
    });

    // Close modal when cancel is clicked
    closeModalBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    // Close modal when clicking outside modal
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});

    const deleteForm = document.getElementById('deleteForm');
    const deleteModal = document.getElementById('deleteModal');
    const productNameSpan = document.getElementById('productName');

    document.querySelectorAll('.deleteBtn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;

            // Set product name in modal
            productNameSpan.textContent = name;

            // ✅ Set correct delete route dynamically
            deleteForm.action = `/admin/products/${id}`;

            // Show modal
            deleteModal.style.display = 'flex';
        });
    });

    document.getElementById('cancelDelete').addEventListener('click', function() {
        deleteModal.style.display = 'none';
    });
