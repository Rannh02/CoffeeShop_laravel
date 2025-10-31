document.addEventListener("DOMContentLoaded", () => {
    const deleteButtons = document.querySelectorAll(".delete-btn");
    const deleteModal = document.getElementById("deleteModal");
    const cancelDelete = document.getElementById("cancelDelete");
    const productNameEl = document.getElementById("productName");
    const deleteProductId = document.getElementById("deleteProductId");
    const deleteForm = document.getElementById("deleteForm");

    deleteButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            const id = btn.getAttribute("data-id");
            const name = btn.getAttribute("data-name");

            productNameEl.textContent = name;
            deleteProductId.value = id;
            deleteModal.style.display = "flex";
        });
    });

    cancelDelete.addEventListener("click", () => {
        deleteModal.style.display = "none";
    });

    // Optional: close modal when clicking outside
    deleteModal.addEventListener("click", (e) => {
        if (e.target === deleteModal) {
            deleteModal.style.display = "none";
        }
    });
});
