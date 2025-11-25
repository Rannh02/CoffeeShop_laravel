document.addEventListener('DOMContentLoaded', () => {
    const updateButtons = document.querySelectorAll('.update-btn');
    const updateModal = document.getElementById('UpdateProductModal');
    const updateForm = document.getElementById('updateForm');
    const closeButtons = updateModal.querySelectorAll('.CancelBtn');

    updateButtons.forEach(button => {
        button.addEventListener('click', () => {
            const productId = button.getAttribute('data-product-id');
            const row = button.closest('tr');
            const name = row.querySelector('td:nth-child(3)').textContent.trim();
            const price = row.querySelector('td:nth-child(5)').textContent.replace('₱', '').trim();
            const categoryId = row.querySelector('td:nth-child(2)').textContent.trim();

            // Fill modal fields
            document.getElementById('updateProductId').value = productId;
            document.getElementById('updateProductName').value = name;
            document.getElementById('updateProductPrice').value = price;
            document.getElementById('updateProductCategory').value = categoryId;

            // Set form action dynamically
            updateForm.action = `/admin/products/${productId}`;

            // Show modal
            updateModal.style.display = 'flex';
        });
    });

    // Close modal on cancel or outside click
    document.getElementById('closeProductModal')?.addEventListener('click', () => {
        updateModal.style.display = 'none';
    });
    document.getElementById('cancelUpdate')?.addEventListener('click', () => {
        updateModal.style.display = 'none';
    });
    updateModal.addEventListener('click', e => {
        if (e.target === updateModal) {
            updateModal.style.display = 'none';
        }
    });
});
