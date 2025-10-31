document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('EditSupplyModal');
    const closeEditSupplierModal = document.getElementById('closeEditSupplierModal');
    const editForm = document.getElementById('editSupplierForm');
    const supplierIdInput = document.getElementById('editSupplierId');
    const supplierNameInput = document.getElementById('editSupplierName');
    const contactNumberInput = document.getElementById('editContact');
    const addressInput = document.getElementById('editAddress');

    // Handle edit (update) button clicks
    document.querySelectorAll('.update-btn').forEach(button => {
        button.addEventListener('click', function() {
            const supplierId = this.getAttribute('data-id');
            const supplierName = this.getAttribute('data-name');
            const contactNumber = this.getAttribute('data-contact');
            const address = this.getAttribute('data-address');

            // Fill form fields
            supplierIdInput.value = supplierId;
            supplierNameInput.value = supplierName;
            contactNumberInput.value = contactNumber;
            addressInput.value = address;

            // FIX: Add 'admin/' prefix to match your route
            editForm.action = `/admin/suppliers/${supplierId}/update`;

            // Show modal
            editModal.style.display = 'flex'; 
        });
    });

    // Handle cancel/close button
    closeEditSupplierModal.addEventListener('click', () => {
        editModal.style.display = 'none';
    });

    // Close modal when clicking outside
    editModal.addEventListener('click', (e) => {
        if (e.target === editModal) {
            editModal.style.display = 'none';
        }
    });
});