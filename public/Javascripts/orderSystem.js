document.addEventListener("DOMContentLoaded", () => {
    const productCards = document.querySelectorAll(".product-card");
    const orderList = document.getElementById("orderList");
    const totalPriceEl = document.getElementById("totalPrice");
    const changePriceEl = document.getElementById("changePrice");
    const amountPaidInput = document.getElementById("amountPaid");
    const clearOrderBtn = document.getElementById("clearOrder");

    if (!orderList || !totalPriceEl || !changePriceEl || !amountPaidInput || !clearOrderBtn) {
        console.error("⚠️ Some elements not found in the DOM. Please check your HTML IDs.");
        return;
    }

    let total = parseFloat(localStorage.getItem("total")) || 0;
    let savedOrders = JSON.parse(localStorage.getItem("orders")) || [];

    // Initial render
    renderOrderList();
    totalPriceEl.textContent = `₱${total.toFixed(2)}`;
    calculateChange();

    console.log("✅ Product cards found:", productCards.length);

    // Add product to order
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
                    name: name,
                    price: price,
                    quantity: 1,
                    totalPrice: price
                });
            }

            updateOrderState();
        });
    });

    // Render the order list with controls
    function renderOrderList() {
        orderList.innerHTML = '';

        savedOrders.forEach((order, index) => {
            const itemCard = document.createElement("div");
            itemCard.classList.add("order-item-card");

            itemCard.innerHTML = `
                <div class="item-top">
                    <strong class="item-name">${order.name}</strong>
                    <span class="item-price">₱${order.totalPrice.toFixed(2)}</span>
                </div>
                <div class="item-controls">
                    <button class="qty-btn minus">-</button>
                    <span class="quantity">x${order.quantity}</span>
                    <button class="qty-btn plus">+</button>
                    <button class="remove-btn">❌</button>
                </div>
            `;

            const minusBtn = itemCard.querySelector(".minus");
            const plusBtn = itemCard.querySelector(".plus");
            const removeBtn = itemCard.querySelector(".remove-btn");

            minusBtn.addEventListener("click", () => {
                if (order.quantity > 1) {
                    order.quantity -= 1;
                    order.totalPrice = order.quantity * order.price;
                } else {
                    savedOrders.splice(index, 1);
                }
                updateOrderState();
            });

            plusBtn.addEventListener("click", () => {
                order.quantity += 1;
                order.totalPrice = order.quantity * order.price;
                updateOrderState();
            });

            removeBtn.addEventListener("click", () => {
                savedOrders.splice(index, 1);
                updateOrderState();
            });

            orderList.appendChild(itemCard);
        });
    }

    // Update total, UI, and storage
    function updateOrderState() {
        total = savedOrders.reduce((sum, item) => sum + item.totalPrice, 0);
        totalPriceEl.textContent = `₱${total.toFixed(2)}`;
        calculateChange();
        renderOrderList();
        localStorage.setItem("orders", JSON.stringify(savedOrders));
        localStorage.setItem("total", total);
    }

    // Calculate change
    function calculateChange() {
        const paid = parseFloat(amountPaidInput.value) || 0;
        const change = paid - total;
        changePriceEl.textContent = change >= 0 ? `₱${change.toFixed(2)}` : "₱0.00";
    }

    // Recalculate change on input
    amountPaidInput.addEventListener("input", calculateChange);

    // Clear all orders
    clearOrderBtn.addEventListener("click", () => {
        orderList.innerHTML = '';
        amountPaidInput.value = '';
        total = 0;
        savedOrders = [];
        totalPriceEl.textContent = '₱0.00';
        changePriceEl.textContent = '₱0.00';
        localStorage.removeItem("orders");
        localStorage.removeItem("total");
    });
});
