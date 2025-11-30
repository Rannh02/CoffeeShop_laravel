document.addEventListener('DOMContentLoaded', () => {
    const cardBtn = document.querySelector('.payment-btn.card');
    const gcashBtn = document.querySelector('.payment-btn.gcash');
    const cardInput = document.getElementById('cardNumber');
    const referenceInput = document.getElementById('referenceNumber');

    // 🔹 Hide both inputs initially
    if (cardInput) cardInput.style.display = 'none';
    if (referenceInput) referenceInput.style.display = 'none';

    // 🔹 Load saved payment type
    const savedPaymentType = localStorage.getItem('paymentType');
    if (savedPaymentType === 'Card') {
        cardInput.style.display = 'block';
    } else if (savedPaymentType === 'GCash') {
        referenceInput.style.display = 'block';
    }

    // 🔹 Helper to save payment type
    function setPaymentType(type) {
        localStorage.setItem('paymentType', type);
        document.dispatchEvent(new CustomEvent('paymentTypeChanged', {
            detail: { type }
        }));
    }

    // 🔹 Toggle function
    function toggleInput(inputToShow, inputToHide, type) {
        const isVisible = inputToShow.style.display === 'block';

        // Hide both
        inputToShow.style.display = 'none';
        inputToHide.style.display = 'none';

        if (!isVisible) {
            // Show selected input if previously hidden
            inputToShow.style.display = 'block';
            setPaymentType(type);
        } else {
            // If toggled off, clear stored payment
            localStorage.removeItem('paymentType');
        }
    }

    // 🔹 Click Card button (toggle)
    if (cardBtn) {
        cardBtn.addEventListener('click', () => {
            toggleInput(cardInput, referenceInput, 'Card');
        });
    }

    // 🔹 Click GCash button (toggle)
    if (gcashBtn) {
        gcashBtn.addEventListener('click', () => {
            toggleInput(referenceInput, cardInput, 'GCash');
        });
    }
});
