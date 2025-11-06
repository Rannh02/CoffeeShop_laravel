document.addEventListener("DOMContentLoaded", () => {
    const productCards = document.querySelectorAll(".product-card");
    const orderList = document.getElementById("orderList");
    const totalPriceEl = document.getElementById("totalPrice");
    const changePriceEl = document.getElementById("changePrice");
    const amountPaidInput = document.getElementById("amountPaid");
    const clearOrderBtn = document.getElementById("clearOrder");
    const placeOrderBtn = document.getElementById("placeOrder"); // ✅ Add in your HTML
    const customerNameInput = document.getElementById("customerName"); // ✅ Add in your HTML
    const orderTypeSelect = document.getElementById("order-type"); // ✅ Add in your HTML

    let total = parseFloat(localStorage.getItem("total")) || 0;
    let savedOrders = JSON.parse(localStorage.getItem("orders")) || [];

    renderOrderList();
    totalPriceEl.textContent = `₱${total}`;
    calculateChange();

    // ✅ Add product to order
    productCards.forEach(card => {
        card.addEventListener("click", () => {
            const name = card.dataset.name;
            const price = parseFloat(card.dataset.price);

            const existingOrder = savedOrders.find(order => order.name === name);
            if (existingOrder) {
                existingOrder.quantity += 1;
                existingOrder.totalPrice = existingOrder.quantity * existingOrder.price;
            } else {
                savedOrders.push({
                    name,
                    price,
                    quantity: 1,
                    totalPrice: price
                });
            }
            updateOrderState();
            showToast(`${name} added to order!`);
        });
    });

    // ✅ Render order list
    function renderOrderList() {
        orderList.innerHTML = '';

        savedOrders.forEach((order, index) => {
            const itemCard = document.createElement("div");
            itemCard.classList.add("order-item-card");
            itemCard.innerHTML = `
                <div class="item-top">
                    <strong>${order.name}</strong>
                    <span>₱${order.totalPrice}</span>
                </div>
                <div class="item-controls">
                    <button class="qty-btn minus">-</button>
                    <span>x${order.quantity}</span>
                    <button class="qty-btn plus">+</button>
                    <button class="remove-btn">❌</button>
                </div>
            `;

            itemCard.querySelector(".minus").addEventListener("click", () => {
                if (order.quantity > 1) {
                    order.quantity -= 1;
                    order.totalPrice = order.quantity * order.price;
                } else {
                    savedOrders.splice(index, 1);
                }
                updateOrderState();
            });

            itemCard.querySelector(".plus").addEventListener("click", () => {
                order.quantity += 1;
                order.totalPrice = order.quantity * order.price;
                updateOrderState();
            });

            itemCard.querySelector(".remove-btn").addEventListener("click", () => {
                savedOrders.splice(index, 1);
                updateOrderState();
                showToast(`${order.name} removed.`);
            });

            orderList.appendChild(itemCard);
        });
    }

    // ✅ Update state & save to localStorage
    function updateOrderState() {
        total = savedOrders.reduce((sum, item) => sum + item.totalPrice, 0);
        totalPriceEl.textContent = `₱${total}`;
        calculateChange();
        renderOrderList();
        localStorage.setItem("orders", JSON.stringify(savedOrders));
        localStorage.setItem("total", total);
    }

    // ✅ Calculate change
    function calculateChange() {
        const paid = parseFloat(amountPaidInput.value) || 0;
        const change = paid - total;
        changePriceEl.textContent = change >= 0 ? `₱${change}` : "₱0";
    }

    amountPaidInput.addEventListener("input", calculateChange);

    // ✅ Clear all
    clearOrderBtn.addEventListener("click", () => {
        savedOrders = [];
        total = 0;
        amountPaidInput.value = '';
        totalPriceEl.textContent = '₱0';
        changePriceEl.textContent = '₱0';
        orderList.innerHTML = '';
        localStorage.clear();
        showToast("Order cleared.");
    });

    // ✅ Send order to backend (Laravel)
    placeOrderBtn.addEventListener("click", () => {
        const customerName = customerNameInput.value.trim();
        const orderType = orderTypeSelect.value;

        if (!customerName || savedOrders.length === 0) {
            showToast("Please add items and enter customer name!");
            return;
        }

        fetch("/cashier/place-order", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                customer_name: customerName,
                order_type: orderType,
                total: total,
                orders: savedOrders
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast("✅ Order placed successfully!");
                clearOrderBtn.click(); // reset
            } else {
                showToast("❌ Failed to place order!");
            }
        })
        .catch(err => {
            console.error(err);
            showToast("⚠️ Server error occurred!");
        });
    });

    // ✅ Small toast popup function
    function showToast(message) {
        const toast = document.createElement("div");
        toast.textContent = message;
        toast.style.position = "fixed";
        toast.style.bottom = "20px";
        toast.style.right = "20px";
        toast.style.background = "#333";
        toast.style.color = "#fff";
        toast.style.padding = "10px 15px";
        toast.style.borderRadius = "8px";
        toast.style.zIndex = "1000";
        toast.style.opacity = "0.9";
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    }
});
