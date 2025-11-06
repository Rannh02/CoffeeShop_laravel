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

  // Extract order items
  function getOrderItems() {
    const list = document.getElementById('orderList');
    const items = [];
    if (!list) return items;

    const structured = list.querySelectorAll('.order-item');
    if (structured.length) {
      structured.forEach(el => {
        const nameEl = el.querySelector('.product-name');
        const qtyEl = el.querySelector('.quantity');
        const priceEl = el.querySelector('.product-price');

        const name = nameEl ? nameEl.textContent.trim() : el.dataset.name || el.textContent.trim();
        const qty = qtyEl ? parseInt(qtyEl.textContent) || 1 : parseInt(el.dataset.qty) || 1;
        const price = priceEl ? parseFloat(priceEl.textContent.replace(/[^0-9.-]+/g, '')) || 0 : parseFloat(el.dataset.price) || 0;

        items.push({ name, qty, price });
      });
      return items;
    }

    Array.from(list.children).forEach(ch => {
      const name = ch.dataset.name || ch.textContent.trim();
      const qty = parseInt(ch.dataset.qty) || 1;
      const price = parseFloat(ch.dataset.price) || 0;
      items.push({ name, qty, price });
    });

    return items;
  }

  // Build receipt HTML
  function buildReceiptHtml() {
    const customerName = (document.getElementById('customerName')?.value.trim()) || 'Guest';
    const cashierName = (document.querySelector('.staff-name')?.textContent.trim()) || 'Cashier';
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
        <div class="item-row" style="display:flex; justify-content:space-between;">
          <div style="width:50%;">${escapeHtml(it.name)}</div>
          <div style="width:25%; text-align:right;">${it.qty} x ${fmt(it.price)}</div>
          <div style="width:25%; text-align:right;">${fmt(lineTotal)}</div>
        </div>`;
    });

    if (!items.length) itemRowsHtml = `<div>(No items)</div>`;

    return `
      <div style="width:320px; margin:auto; font-family:monospace;">
        <div style="text-align:center; font-weight:bold; font-size:16px;">BERDE KOPI</div>
        <div style="text-align:center; font-size:10px;">Davao Branch</div>
        <hr>
        <div>Date: ${dateStr} ${timeStr}<br>Cashier: ${cashierName}<br>Customer: ${customerName}</div>
        <hr>
        ${itemRowsHtml}
        <hr>
        <div style="display:flex; justify-content:space-between;">Subtotal: <span>${fmt(subtotal)}</span></div>
        <div style="display:flex; justify-content:space-between;">VAT (12%): <span>${fmt(vat)}</span></div>
        <div style="display:flex; justify-content:space-between; font-weight:bold;">TOTAL: <span>${fmt(total)}</span></div>
        <hr>
        <div style="display:flex; justify-content:space-between;">Paid: <span>${fmt(amountPaid)}</span></div>
        <div style="display:flex; justify-content:space-between;">Change: <span>${fmt(change)}</span></div>
        <hr>
        <div style="text-align:center;">*** Thank you! ***</div>
      </div>`;
  }

  // Send order to backend
  async function saveOrderToDatabase() {
    const customerName = document.getElementById('customerName')?.value.trim() || 'Guest';
    const orderType = 'Dine-in';
    const items = getOrderItems();

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

    try {
      const response = await fetch('/orders/store', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(orderData)
      });

      const data = await response.json();
      if (data.success) {
        console.log('✅ Order saved successfully!');
      } else {
        console.error('❌ Failed to save order', data);
      }
    } catch (err) {
      console.error('Error saving order:', err);
    }
  }

  // Place Order
  placeOrderBtn.addEventListener('click', async () => {
    receiptDiv.innerHTML = buildReceiptHtml();
    modal.style.display = 'flex';
    await saveOrderToDatabase(); // 🟢 save to backend
  });

  // Print Receipt
  printBtn.addEventListener('click', () => {
    const printWindow = window.open('', '', 'height=800,width=400');
    printWindow.document.write('<html><head><title>Receipt</title></head><body>');
    printWindow.document.write(receiptDiv.innerHTML);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => printWindow.print(), 300);
  });

  // Continue Order
  continueBtn.addEventListener('click', () => {
    modal.style.display = 'none';
    document.getElementById('orderList').innerHTML = '';
    document.getElementById('totalPrice').textContent = '₱0';
    document.getElementById('changePrice').textContent = '₱0';
    document.getElementById('amountPaid').value = '';
    document.getElementById('customerName').value = '';
  });

  // Close modal on outside click
  modal.addEventListener('click', (e) => {
    if (e.target === modal) modal.style.display = 'none';
  });
});
