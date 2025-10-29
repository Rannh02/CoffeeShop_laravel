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
});
