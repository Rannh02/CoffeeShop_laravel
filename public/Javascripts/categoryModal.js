document.addEventListener('DOMContentLoaded', function() {
    const categoryModal = document.getElementById('CategoryModal');
    const openModalBtn = document.getElementById('openCategoryModal');
    const closeModalBtn = document.getElementById('closeCategoryModal');
    const cancelBtn = document.getElementById('cancelCategoryBtn');

    // Open modal
    if (openModalBtn && categoryModal) {
        openModalBtn.addEventListener('click', function() {
            categoryModal.style.display = 'flex';
        });
    }

    // Close modal when clicking X
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', function() {
            categoryModal.style.display = 'none';
        });
    }

    // Close modal when clicking Cancel
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            categoryModal.style.display = 'none';
        });
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === categoryModal) {
            categoryModal.style.display = 'none';
        }
    });
    
    // --- Update Category Modal Handlers ---
    const updateModal = document.getElementById('UpdateCategoryModal');
    const updateForm = document.getElementById('updateCategoryForm');
    const updateNameInput = document.getElementById('update_Category_name');
    const closeUpdateBtn = document.getElementById('closeUpdateCategoryModal');
    const cancelUpdateBtn = document.getElementById('cancelUpdateCategoryBtn');

    // Open update modal when clicking update buttons
    const updateButtons = document.querySelectorAll('.update-btn');
    if (updateButtons && updateModal) {
        updateButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const categoryId = this.getAttribute('data-category-id');
                const categoryName = this.getAttribute('data-category-name');

                if (updateNameInput) updateNameInput.value = categoryName || '';
                if (updateForm) {
                    // set a sensible action; backend route may vary — adjust if needed
                    updateForm.action = `/admin/category/${categoryId}/update`;
                }
                updateModal.style.display = 'flex';
            });
        });
    }

    if (closeUpdateBtn) {
        closeUpdateBtn.addEventListener('click', function() {
            if (updateModal) updateModal.style.display = 'none';
        });
    }

    if (cancelUpdateBtn) {
        cancelUpdateBtn.addEventListener('click', function() {
            if (updateModal) updateModal.style.display = 'none';
        });
    }

    window.addEventListener('click', function(event) {
        if (event.target === updateModal) {
            updateModal.style.display = 'none';
        }
    });

    // --- Delete Category Modal Handlers ---
    const deleteModal = document.getElementById('DeleteCategoryModal');
    const deleteForm = document.getElementById('deleteCategoryForm');
    const deleteCategoryNameSpan = document.getElementById('deleteCategoryName');
    const cancelDeleteBtn = document.getElementById('cancelDeleteCategoryBtn');

    const deleteButtons = document.querySelectorAll('.delete-btn');
    if (deleteButtons && deleteModal) {
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const categoryId = this.getAttribute('data-category-id');
                const categoryName = this.getAttribute('data-category-name');
                if (deleteCategoryNameSpan) deleteCategoryNameSpan.textContent = categoryName || '';
                if (deleteForm) {
                    // set action to delete endpoint; adjust if your routes differ
                    deleteForm.action = `/admin/category/${categoryId}`;
                }
                deleteModal.style.display = 'flex';
            });
        });
    }

    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', function() {
            if (deleteModal) deleteModal.style.display = 'none';
        });
    }

    window.addEventListener('click', function(event) {
        if (event.target === deleteModal) {
            deleteModal.style.display = 'none';
        }
    });
});
