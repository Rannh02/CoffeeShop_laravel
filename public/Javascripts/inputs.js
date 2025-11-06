document.addEventListener('DOMContentLoaded', () => {
    const cardBtn = document.querySelector('.payment-btn.card');
    const gcashBtn = document.querySelector('.payment-btn.gcash');
    const cardInput = document.getElementById('cardNumber');
    const referenceInput = document.getElementById('referenceNumber');

    // 🔹 Hide both inputs initially
    if (cardInput) cardInput.style.display = 'none';
    if (referenceInput) referenceInput.style.display = 'none';

    // 🔹 Load saved payment type if exists
    const savedPaymentType = localStorage.getItem('paymentType');
    if (savedPaymentType === 'Card' && cardBtn) {
        cardInput.style.display = 'block';
    } else if (savedPaymentType === 'GCash' && gcashBtn) {
        referenceInput.style.display = 'block';
    }

    // 🔹 Helper to save payment type
    function setPaymentType(type) {
        localStorage.setItem('paymentType', type);
        // 🔸 Notify other scripts (like orderingcoffee.js)
        document.dispatchEvent(new CustomEvent('paymentTypeChanged', {
            detail: { type }
        }));
    }

    // 🔹 Click Card button
    if (cardBtn) {
        cardBtn.addEventListener('click', () => {
            if (cardInput) cardInput.style.display = 'block';
            if (referenceInput) referenceInput.style.display = 'none';
            setPaymentType('Card');
        });
    }

    // 🔹 Click GCash button
    if (gcashBtn) {
        gcashBtn.addEventListener('click', () => {
            if (cardInput) cardInput.style.display = 'none';
            if (referenceInput) referenceInput.style.display = 'block';
            setPaymentType('GCash');
        });
    }
});
