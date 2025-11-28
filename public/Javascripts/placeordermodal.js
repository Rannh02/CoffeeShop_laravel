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

    // First try to parse our structured order-item-card markup
    const cards = list.querySelectorAll('.order-item-card');
    if (cards.length) {
      cards.forEach(card => {
        // name is inside <strong> tag
        const nameEl = card.querySelector('strong');
        // quantity is inside a span.quantity with text like "x2"
        const qtyEl = card.querySelector('.quantity');
        // price is the right-side span in the top row (contains currency)
        const priceSpan = card.querySelector('div > div > span');

        const name = nameEl ? nameEl.textContent.trim() : (card.dataset.name || '');
        let qty = 1;
        if (qtyEl) {
          const raw = qtyEl.textContent.replace(/[^0-9]/g, '');
          qty = parseInt(raw) || 1;
        } else if (card.dataset.qty) {
          qty = parseInt(card.dataset.qty) || 1;
        }

        let price = 0;
        if (priceSpan) {
          price = parseFloat(priceSpan.textContent.replace(/[^0-9.-]+/g, '')) || 0;
        } else if (card.dataset.price) {
          price = parseFloat(card.dataset.price) || 0;
        }

        items.push({ name, qty, price });
      });
      return items;
    }

    // Generic fallback: try dataset on children
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
  placeOrderBtn.addEventListener('click', async () => {
    // Build and display receipt
    receiptDiv.innerHTML = buildReceiptHtml();
    receiptDiv.style.display = 'none';
    modal.style.display = 'flex';

    // Send order to server
    try {
      await sendOrderToServer();
    } catch (err) {
      console.error('Error sending order to server:', err);
      // optionally show user-facing error
      alert('Failed to record order on server: ' + (err.message || err));
    }
  });

  // Send order JSON to server API
  async function sendOrderToServer() {
    const items = getOrderItems();
    if (!items.length) return;

    const subtotal = items.reduce((s, it) => s + (it.price * it.qty), 0);
    const vat = subtotal * 0.12;
    const total = subtotal + vat;

    const payload = {
      customerName: (document.getElementById('customerName') && document.getElementById('customerName').value.trim()) || 'Guest',
      orderType: localStorage.getItem('orderType') || 'Dine In',
      totalAmount: parseFloat(total.toFixed(2)),
      // Convert item line totals to unit prices (unitPrice = lineTotal / qty)
      orders: items.map(it => ({ name: it.name, quantity: it.qty, price: parseFloat((it.price / Math.max(1, it.qty)).toFixed(2)) })),
      paymentMethod: localStorage.getItem('paymentType') || 'Cash',
      amountPaid: parseFloat((document.getElementById('amountPaid') && document.getElementById('amountPaid').value) || total),
      transactionReference: (document.getElementById('referenceNumber') && document.getElementById('referenceNumber').value) || null
    };

    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    const headers = {
      'Content-Type': 'application/json'
    };
    if (tokenMeta) headers['X-CSRF-TOKEN'] = tokenMeta.getAttribute('content');

    const resp = await fetch('/api/orders/payment', {
      method: 'POST',
      headers,
      body: JSON.stringify(payload)
    });

    if (!resp.ok) {
      const body = await resp.json().catch(() => ({}));
      throw new Error(body.message || 'Server error');
    }

    const data = await resp.json();
    console.log('Order saved', data);

    // Clear client-side order after successful save
    localStorage.removeItem('orders');
    localStorage.removeItem('total');
    // Optionally clear UI elements (keep modal open for print)
    if (document.getElementById('orderList')) document.getElementById('orderList').innerHTML = '';
    if (document.getElementById('totalPrice')) document.getElementById('totalPrice').textContent = '₱0';
    if (document.getElementById('changePrice')) document.getElementById('changePrice').textContent = '₱0';
  }

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
