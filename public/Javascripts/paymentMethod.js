document.addEventListener('DOMContentLoaded', () => {
  // Get payment method buttons
  const cardBtn = document.getElementById('card');
  const gcashBtn = document.getElementById('gcash');
  
  // Get input fields
  const cardNumberInput = document.getElementById('cardNumber');
  const referenceNumberInput = document.getElementById('referenceNumber');
  const amountPaidInput = document.getElementById('amountPaid');
  
  // Track selected payment method
  let selectedPaymentMethod = 'Cash'; // Default
  
  // ✅ Function to auto-fill amount paid with exact total (DEFINE FIRST)
  function autoFillAmountPaid() {
    // Calculate total the SAME way as Place Order validation
    const items = JSON.parse(localStorage.getItem("orders")) || [];
    
    if (items.length === 0) {
      console.warn('⚠️ No items to calculate total');
      return;
    }
    
    // Calculate subtotal
    let subtotal = 0;
    items.forEach(item => {
      subtotal += item.price * item.quantity;
    });
    
    // Apply discount if any
    let discount = 0;
    const pwdCheckbox = document.getElementById('pwdCheckbox');
    const seniorCheckbox = document.getElementById('seniorCheckbox');
    if ((pwdCheckbox && pwdCheckbox.checked) || (seniorCheckbox && seniorCheckbox.checked)) {
      discount = subtotal * 0.20; // 20% discount
    }
    
    const subtotalAfterDiscount = subtotal - discount;
    const vat = subtotalAfterDiscount * 0.12; // 12% VAT
    const total = subtotalAfterDiscount + vat;
    
    // Set amount paid to exact total
    if (amountPaidInput) {
      amountPaidInput.value = total.toFixed(2);
      
      // Update change display
      const changePriceElement = document.getElementById('changePrice');
      if (changePriceElement) {
        changePriceElement.textContent = '₱0.00'; // Exact amount = no change
      }
      
      console.log('💰 Auto-filled amount paid (with VAT):', total.toFixed(2));
      console.log('   Subtotal:', subtotal.toFixed(2));
      console.log('   Discount:', discount.toFixed(2));
      console.log('   VAT:', vat.toFixed(2));
    }
  }
  
  // ✅ Card Button Click Handler
  if (cardBtn) {
    cardBtn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation(); // Prevent event bubbling
      
      // Set active state
      cardBtn.classList.add('active');
      if (gcashBtn) gcashBtn.classList.remove('active');
      
      // Update selected method
      selectedPaymentMethod = 'Card';
      
      // Show card number input, hide reference number
      if (cardNumberInput) cardNumberInput.style.display = 'block';
      if (referenceNumberInput) referenceNumberInput.style.display = 'none';
      
      // Clear inputs
      if (cardNumberInput) cardNumberInput.value = '';
      if (referenceNumberInput) referenceNumberInput.value = '';
      
      // ✅ AUTO-FILL IMMEDIATELY when button is clicked
      autoFillAmountPaid();
      
      console.log('💳 Payment method: Card - Amount auto-filled');
    });
  }
  
  // ✅ E-Wallet (GCash/QR) Button Click Handler
  if (gcashBtn) {
    gcashBtn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation(); // Prevent event bubbling
      
      // Set active state
      gcashBtn.classList.add('active');
      if (cardBtn) cardBtn.classList.remove('active');
      
      // Update selected method
      selectedPaymentMethod = 'QR';
      
      // Show reference number input, hide card number
      if (referenceNumberInput) referenceNumberInput.style.display = 'block';
      if (cardNumberInput) cardNumberInput.style.display = 'none';
      
      // Clear inputs
      if (cardNumberInput) cardNumberInput.value = '';
      if (referenceNumberInput) referenceNumberInput.value = '';
      
      // ✅ AUTO-FILL IMMEDIATELY when button is clicked
      autoFillAmountPaid();
      
      console.log('📱 Payment method: QR/E-Wallet - Amount auto-filled');
    });
  }
  
  // ✅ Auto-fill amount paid when card number or reference is entered
  if (cardNumberInput) {
    cardNumberInput.addEventListener('input', () => {
      if (cardNumberInput.value.trim() !== '') {
        autoFillAmountPaid();
      }
    });
  }
  
  if (referenceNumberInput) {
    referenceNumberInput.addEventListener('input', () => {
      if (referenceNumberInput.value.trim() !== '') {
        autoFillAmountPaid();
      }
    });
  }
  
  // ✅ Get payment method (export this function for use in placeOrder.js)
  window.getPaymentMethod = function() {
    return selectedPaymentMethod;
  };
  
  // ✅ Get transaction reference based on payment method
  window.getTransactionReference = function() {
    if (selectedPaymentMethod === 'Card') {
      const cardNumber = cardNumberInput?.value.trim();
      if (!cardNumber) {
        throw new Error('Please enter card number!');
      }
      // Use last 4 digits + timestamp
      const last4 = cardNumber.slice(-4);
      return `CARD-${last4}-${Date.now()}`;
    } 
    else if (selectedPaymentMethod === 'QR') {
      const refNumber = referenceNumberInput?.value.trim();
      if (!refNumber) {
        throw new Error('Please enter reference number!');
      }
      return refNumber; // Use the reference number directly
    } 
    else {
      // Cash - auto-generate
      return `CASH-${Date.now()}`;
    }
  };
  
  // ✅ Validate payment method inputs
  window.validatePaymentMethod = function() {
    if (selectedPaymentMethod === 'Card') {
      const cardNumber = cardNumberInput?.value.trim();
      if (!cardNumber) {
        alert('Please enter card number!');
        cardNumberInput?.focus();
        return false;
      }
      if (cardNumber.length < 13 || cardNumber.length > 19) {
        alert('Invalid card number! Must be 13-19 digits.');
        cardNumberInput?.focus();
        return false;
      }
    } 
    else if (selectedPaymentMethod === 'QR') {
      const refNumber = referenceNumberInput?.value.trim();
      if (!refNumber) {
        alert('Please enter reference number!');
        referenceNumberInput?.focus();
        return false;
      }
      if (refNumber.length < 4) {
        alert('Reference number too short!');
        referenceNumberInput?.focus();
        return false;
      }
    }
    return true;
  };
  
  // ✅ Reset payment method (for new orders)
  window.resetPaymentMethod = function() {
    selectedPaymentMethod = 'Cash';
    
    // Remove active states
    if (cardBtn) cardBtn.classList.remove('active');
    if (gcashBtn) gcashBtn.classList.remove('active');
    
    // Hide inputs
    if (cardNumberInput) {
      cardNumberInput.style.display = 'none';
      cardNumberInput.value = '';
    }
    if (referenceNumberInput) {
      referenceNumberInput.style.display = 'none';
      referenceNumberInput.value = '';
    }
    
    console.log('💵 Payment method reset to Cash');
  };
  
  console.log('✅ Payment method logic initialized');
});