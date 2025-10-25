document.addEventListener('DOMContentLoaded', function () {
    const openBtn = document.getElementById('openAddSupplierModal'); // ✅ matches your button
    const modal = document.getElementById('SupplyModal');
    const closeBtn = document.getElementById('closeSupplyModal');

    if (openBtn && modal) {
        openBtn.addEventListener('click', function () {
            modal.style.display = 'flex'; // show modal
        });
    }

    if (closeBtn && modal) {
        closeBtn.addEventListener('click', function () {
            modal.style.display = 'none'; // close modal
        });
    }

    // Optional: close when clicking outside modal
    window.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});
