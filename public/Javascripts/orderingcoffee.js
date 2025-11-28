document.addEventListener('DOMContentLoaded', () => {
  const placeOrderBtn = document.getElementById('placeOrderBtn');
  const modal = document.getElementById('placeOrderModal');
  const printBtn = document.getElementById('printReceipt');
  const continueBtn = document.getElementById('continueOrder');
  const receiptDiv = document.getElementById('receipt');

  // Format currency
  const fmt = (n) => '₱' + Number(n).toFixed(2);

  // Escape HTML helper
  function escapeHtml(s) {
    if (!s) return '';
    return String(s)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // Get selected order type from buttons
  function getOrderType() {
    const activeBtn = document.querySelector('.order-type-btn.active');
    if (activeBtn) {
      return activeBtn.dataset.type;
    }
    // Fallback: check which button was clicked
    const dineInBtn = document.querySelector('[data-type="Dine In"]');
    const takeOutBtn = document.querySelector('[data-type="Take Out"]');
    if (dineInBtn && dineInBtn.classList.contains('active')) return 'Dine In';
    if (takeOutBtn && takeOutBtn.classList.contains('active')) return 'Take Out';
    return 'Dine In'; // Default
  }

  // ✅ FIXED: Get order items from localStorage (where orderSystem.js stores them)
  function getOrderItems() {
    const savedOrders = JSON.parse(localStorage.getItem("orders")) || [];
    
    console.log('📦 Retrieved orders from localStorage:', savedOrders);
    
    return savedOrders.map(item => ({
      name: item.name,
      qty: item.quantity,
      price: item.price
    }));
  }

  // Build receipt HTML
  function buildReceiptHtml() {
    const customerName = (document.getElementById('customerName')?.value.trim()) || 'Guest';
    // ✅ FIXED: Get ONLY the cashier name from the span inside .staff-name
    const cashierName = (document.querySelector('.staff-name > span')?.textContent.trim()) || 'Cashier';
    const orderType = getOrderType();
    const items = getOrderItems();

    let subtotal = 0;
    items.forEach(it => subtotal += it.price * it.qty);
    const vat = subtotal * 0.12;
    const total = subtotal + vat;

    const amountPaid = parseFloat(document.getElementById('amountPaid')?.value || 0);
    const change = Math.max(0, amountPaid - total);

    const now = new Date();
    const dateStr = now.toLocaleDateString();
    const timeStr = now.toLocaleTimeString();

    let itemRowsHtml = '';
    items.forEach(it => {
      const lineTotal = it.price * it.qty;
      itemRowsHtml += `
        <div class="item-row" style="display:flex; justify-content:space-between; margin:5px 0;">
          <div style="width:50%;">${escapeHtml(it.name)}</div>
          <div style="width:25%; text-align:right;">${it.qty} x ${fmt(it.price)}</div>
          <div style="width:25%; text-align:right;">${fmt(lineTotal)}</div>
        </div>`;
    });

    if (!items.length) itemRowsHtml = `<div style="text-align:center; color:#999;">(No items)</div>`;

    return `
      <div style="width:320px; margin:auto; font-family:monospace; font-size:13px; padding:20px;">
        <div style="text-align:center; font-weight:bold; font-size:18px; margin-bottom:5px;">BERDE KOPI</div>
        <div style="text-align:center; font-size:11px; color:#666;">Davao Branch</div>
        <hr style="border:1px dashed #333; margin:10px 0;">
        <div style="font-size:11px; line-height:1.6;">
          Date: ${dateStr} ${timeStr}<br>
          Cashier: ${escapeHtml(cashierName)}<br>
          Customer: ${customerName}<br>
          Type: ${orderType}
        </div>
        <hr style="border:1px dashed #333; margin:10px 0;">
        ${itemRowsHtml}
        <hr style="border:1px dashed #333; margin:10px 0;">
        <div style="display:flex; justify-content:space-between; margin:3px 0;">
          <span>Subtotal:</span>
          <span>${fmt(subtotal)}</span>
        </div>
        <div style="display:flex; justify-content:space-between; margin:3px 0;">
          <span>VAT (12%):</span>
          <span>${fmt(vat)}</span>
        </div>
        <div style="display:flex; justify-content:space-between; font-weight:bold; font-size:15px; margin:5px 0;">
          <span>TOTAL:</span>
          <span>${fmt(total)}</span>
        </div>
        <hr style="border:1px dashed #333; margin:10px 0;">
        <div style="display:flex; justify-content:space-between; margin:3px 0;">
          <span>Cash:</span>
          <span>${fmt(amountPaid)}</span>
        </div>
        <div style="display:flex; justify-content:space-between; margin:3px 0;">
          <span>Change:</span>
          <span>${fmt(change)}</span>
        </div>
        <hr style="border:1px dashed #333; margin:10px 0;">
        <div style="text-align:center; font-size:12px; margin-top:10px;">*** Thank you! Come again! ***</div>
      </div>`;
  }

  // ✅ FIXED: Send order to backend
  async function saveOrderToDatabase() {
    const customerName = document.getElementById('customerName')?.value.trim() || 'Guest';
    const orderType = getOrderType();
    const items = getOrderItems();

    console.log('📤 Preparing to save order...');
    console.log('Customer:', customerName);
    console.log('Order Type:', orderType);
    console.log('Items:', items);

    if (items.length === 0) {
      console.error('❌ No items in order!');
      alert('Please add items to your order first!');
      return false;
    }

    let subtotal = 0;
    items.forEach(it => subtotal += it.price * it.qty);
    const vat = subtotal * 0.12;
    const total = subtotal + vat;

    const orderData = {
      customer_name: customerName,
      order_type: orderType,
      total: total,
      orders: items.map(it => ({
        name: it.name,
        quantity: it.qty,
        price: it.price
      }))
    };

    console.log('📦 Order data to send:', orderData);

    try {
      // Determine payment method and transaction reference
      const storedPaymentType = localStorage.getItem('paymentType');
      let paymentMethodToSend = 'Cash';
      let transactionRefToSend = null;
      if (storedPaymentType === 'Card') {
        paymentMethodToSend = 'Card';
        transactionRefToSend = document.getElementById('cardNumber')?.value || null;
      } else if (storedPaymentType === 'GCash') {
        paymentMethodToSend = 'GCash';
        transactionRefToSend = document.getElementById('referenceNumber')?.value || null;
      }

      const amountPaidInput = parseFloat(document.getElementById('amountPaid')?.value || orderData.total);

      const payload = {
        customerName: customerName,
        orderType: orderType,
        totalAmount: orderData.total,
        orders: orderData.orders,
        paymentMethod: paymentMethodToSend,
        amountPaid: amountPaidInput,
        transactionReference: transactionRefToSend
      };

      const response = await fetch('/api/orders/payment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(payload)
      });

      console.log('📡 Response status:', response.status);

      const data = await response.json().catch(() => ({}));
      console.log('📥 Response data:', data);

      if (response.ok && data.status === 'success') {
        console.log('✅ Order saved successfully! Order ID:', data.order_id);
        return true;
      } else {
        console.error('❌ Failed to save order:', data.message || data);
        alert('Failed to save order: ' + (data.message || 'Server error'));
        return false;
      }
    } catch (err) {
      console.error('❌ Error saving order:', err);
      alert('Network error. Please check console.');
      return false;
    }
  }

  // ✅ FIXED: Place Order with validation
  if (placeOrderBtn) {
    placeOrderBtn.addEventListener('click', async () => {
      console.log('🔵 Place Order button clicked!');

      const items = getOrderItems();
      const customerName = document.getElementById('customerName')?.value.trim();
      const orderType = getOrderType();

      // Validation
      if (items.length === 0) {
        alert('Please add items to the order!');
        return;
      }

      if (!customerName) {
        alert('Please enter customer name!');
        document.getElementById('customerName')?.focus();
        return;
      }

      if (!orderType) {
        alert('Please select order type (Dine In or Take Out)!');
        return;
      }

      let subtotal = 0;
      items.forEach(it => subtotal += it.price * it.qty);
      const total = subtotal + (subtotal * 0.12);
      const amountPaid = parseFloat(document.getElementById('amountPaid')?.value || 0);

      if (amountPaid < total) {
        alert(`Insufficient payment!\nTotal: ${fmt(total)}\nPaid: ${fmt(amountPaid)}`);
        document.getElementById('amountPaid')?.focus();
        return;
      }

      // Disable button
      placeOrderBtn.disabled = true;
      placeOrderBtn.textContent = 'Processing...';

      // Save to database
      const success = await saveOrderToDatabase();

      if (success) {
        // Show receipt
        receiptDiv.innerHTML = buildReceiptHtml();
        receiptDiv.style.display = 'block';
        modal.style.display = 'flex';
      }

      // Re-enable button
      placeOrderBtn.disabled = false;
      placeOrderBtn.textContent = 'Place Order';
    });
  }

  // Print Receipt
  if (printBtn) {
    printBtn.addEventListener('click', () => {
      const printWindow = window.open('', '', 'height=800,width=400');
      printWindow.document.write('<html><head><title>Receipt</title>');
      printWindow.document.write('<style>body{font-family:monospace; padding:20px;}</style>');
      printWindow.document.write('</head><body>');
      printWindow.document.write(receiptDiv.innerHTML);
      printWindow.document.write('</body></html>');
      printWindow.document.close();
      printWindow.focus();
      setTimeout(() => {
        printWindow.print();
        printWindow.close();
      }, 300);
    });
  }

  // ✅ FIXED: Continue Order - Clear everything
  if (continueBtn) {
    continueBtn.addEventListener('click', () => {
      console.log('🧹 Clearing order...');
      
      modal.style.display = 'none';
      
      // Clear order list DOM
      const orderList = document.getElementById('orderList');
      if (orderList) orderList.innerHTML = '';
      
      // Reset totals
      const totalPrice = document.getElementById('totalPrice');
      const changePrice = document.getElementById('changePrice');
      if (totalPrice) totalPrice.textContent = '₱0.00';
      if (changePrice) changePrice.textContent = '₱0.00';
      
      // Clear inputs
      const amountPaid = document.getElementById('amountPaid');
      const customerName = document.getElementById('customerName');
      if (amountPaid) amountPaid.value = '';
      if (customerName) customerName.value = '';
      
      // ✅ IMPORTANT: Clear localStorage
      localStorage.removeItem("orders");
      localStorage.removeItem("total");
      
      // Reset order type buttons
      document.querySelectorAll('.order-type-btn').forEach(btn => {
        btn.classList.remove('active');
      });
      
      console.log('✅ Order cleared, ready for next customer');
    });
  }

  // Close modal on outside click
  if (modal) {
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.style.display = 'none';
      }
    });
  }
});