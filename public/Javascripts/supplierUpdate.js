document.addEventListener("DOMContentLoaded", () => {
    const updateButtons = document.querySelectorAll(".update-btn");
    const editModal = document.getElementById("EditSupplyModal");
    const closeEditModal = document.getElementById("closeEditSupplierModal");
    
    function openEditModal(supplier) {
    document.getElementById('editSupplierId').value = supplier.Supplier_id;
    document.getElementById('editSupplierName').value = supplier.Supplier_name;
    document.getElementById('editContact').value = supplier.Contact_number;
    document.getElementById('editAddress').value = supplier.Address;

    // ✅ dynamically set form action
    const form = document.getElementById('editSupplierForm');
    form.action = `/admin/suppliers/${supplier.Supplier_id}/update`;

    document.getElementById('EditSupplyModal').style.display = 'flex';
}


        // Open modal and populate data
        updateButtons.forEach((btn) => {
        btn.addEventListener("click", () => {
            supplierIdInput.value = btn.dataset.id;
            supplierNameInput.value = btn.dataset.name;
            contactInput.value = btn.dataset.contact;
            addressInput.value = btn.dataset.address;
            editModal.style.display = "flex";
        });
});

        // Close modal
    closeEditModal.addEventListener("click", () => {
    editModal.style.display = "none";

    });

// Submit form with AJAX (optional enhancement)
editForm.addEventListener("submit", async (e) => {
e.preventDefault();

        const formData = {
        Supplier_id: supplierIdInput.value,
        Supplier_name: supplierNameInput.value,
        Contact: contactInput.value,
        Address: addressInput.value,
        };

            try {
            const response = await fetch("/admin/suppliers/update", {
                method: "POST",
                headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name=\"csrf-token\"]')
                    .getAttribute("content"),
                },
                body: JSON.stringify(formData),
            });

        const data = await response.json();

        if (data.success) {
            alert("Supplier updated successfully!");
            location.reload();
        } else {
            alert("Failed to update supplier. Try again.");
        }
        } catch (error) {
        console.error("Error:", error);
        alert("Error connecting to the server.");
        }


    });
});
