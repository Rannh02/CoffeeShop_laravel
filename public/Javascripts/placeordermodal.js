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

  // Extract order items from #orderList
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

    // fallback (if not structured)
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
    const customerName = (document.getElementById('customerName') && document.getElementById('customerName').value.trim()) || 'Guest';
    const cashierName = (document.querySelector('.staff-name') && document.querySelector('.staff-name').textContent.trim()) || 'Cashier';
    const items = getOrderItems();

    // Retrieve order type and payment method
    const orderType = localStorage.getItem('orderType') || 'Dine-In';
    const paymentType = localStorage.getItem('paymentType') || 'Cash';

    // --- Subtotal ---
    let subtotal = 0;
    items.forEach(it => subtotal += it.price * it.qty);

    // --- VAT 12% ---
    const vat = subtotal * 0.12;

    // --- Total ---
    const total = subtotal + vat;

    // Paid & Change
    const amountPaidRaw = (document.getElementById('amountPaid') && document.getElementById('amountPaid').value) || '0';
    const amountPaid = parseFloat(amountPaidRaw) || 0;
    const change = Math.max(0, amountPaid - total);

    const now = new Date();
    const dateStr = now.toLocaleDateString();
    const timeStr = now.toLocaleTimeString();

    // Items list
    let itemRowsHtml = '';
    items.forEach(it => {
      const name = escapeHtml(it.name);
      const qty = it.qty;
      const price = it.price;
      const lineTotal = qty * price;

      itemRowsHtml += `
<div class="item-row" style="display:flex; justify-content:space-between; margin:4px 0;">
  <div style="width:50%; word-break:break-word;">${name}</div>
  <div style="width:25%; text-align:right;">${qty} x ${fmt(price)}</div>
  <div style="width:25%; text-align:right; font-weight:bold;">${fmt(lineTotal)}</div>
</div>`;
    });

    if (!items.length) {
      itemRowsHtml = `<div>(No items)</div>`;
    }

    return `
<style>
  body { background:#fff; padding:10px; font-family:"Courier New", monospace; font-size:12px; }
  hr { border:none; border-top:1px dashed #000; margin:6px 0; }
</style>
<div style="width:320px; margin:0 auto;">
  <div style="text-align:center; font-weight:bold; font-size:16px; margin-bottom:6px;">BERDE KOPI</div>
  <div style="text-align:center; font-size:10px; margin-bottom:6px;">Berde Kopi - Davao Branch</div>
  <div style="text-align:center; font-size:10px; margin-bottom:6px;">TEL: 0943-394-8572</div>
  <hr>
  <div style="font-size:10px; margin-bottom:6px;">
    Date: ${dateStr} &nbsp;&nbsp; Time: ${timeStr}<br>
    Cashier: ${escapeHtml(cashierName)}<br>
    Customer: ${escapeHtml(customerName)}<br>
    Order Type: ${escapeHtml(orderType)}<br>
    Payment Method: ${escapeHtml(paymentType)}
  </div>
  <hr>
  <div>${itemRowsHtml}</div>
  <hr>
  <div style="display:flex; justify-content:space-between; font-size:12px;">
    <div>Subtotal</div><div>${fmt(subtotal)}</div>
  </div>
  <div style="display:flex; justify-content:space-between; font-size:12px;">
    <div>VAT (12%)</div><div>${fmt(vat)}</div>
  </div>
  <div style="display:flex; justify-content:space-between; font-weight:bold; font-size:14px; margin-top:6px;">
    <div>TOTAL</div><div>${fmt(total)}</div>
  </div>
  <hr>
  <div style="display:flex; justify-content:space-between; font-size:12px; margin-top:6px;">
    <div>Paid</div><div>${fmt(amountPaid)}</div>
  </div>
  <div style="display:flex; justify-content:space-between; font-size:12px; margin-top:2px;">
    <div>Change</div><div>${fmt(change)}</div>
  </div>
  <div style="text-align:center; margin-top:12px; font-size:11px;">
    *** Thank you for your purchase! ***<br>Visit again!
  </div>
</div>`;
  }

  // Show modal when Place Order clicked
  placeOrderBtn.addEventListener('click', () => {
    receiptDiv.innerHTML = buildReceiptHtml();
    receiptDiv.style.display = 'none';
    modal.style.display = 'flex';
  });

  // Print receipt
  printBtn.addEventListener('click', () => {
    const content = receiptDiv.innerHTML;
    if (!content.trim()) {
      alert('No receipt content to print.');
      return;
    }
    const printWindow = window.open('', '', 'height=900,width=420');
    printWindow.document.write('<html><head><title>Print Receipt</title></head><body>');
    printWindow.document.write(content);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => printWindow.print(), 300);
  });

  // Continue - reset order
  continueBtn.addEventListener('click', () => {
    modal.style.display = 'none';
    if (document.getElementById('orderList')) document.getElementById('orderList').innerHTML = '';
    if (document.getElementById('totalPrice')) document.getElementById('totalPrice').textContent = '₱0';
    if (document.getElementById('changePrice')) document.getElementById('changePrice').textContent = '₱0';
    if (document.getElementById('amountPaid')) document.getElementById('amountPaid').value = '';
    if (document.getElementById('customerName')) document.getElementById('customerName').value = '';

    // Reset payment input visibility
    const cardNumber = document.getElementById('cardNumber');
    const referenceNumber = document.getElementById('referenceNumber');
    if (cardNumber) cardNumber.style.display = 'none';
    if (referenceNumber) referenceNumber.style.display = 'none';

    // Clear localStorage
    localStorage.removeItem('orderType');
    localStorage.removeItem('paymentType');
    localStorage.removeItem('customerName');
  });

  // Close modal on outside click
  modal.addEventListener('click', (e) => {
    if (e.target === modal) modal.style.display = 'none';
  });
});
