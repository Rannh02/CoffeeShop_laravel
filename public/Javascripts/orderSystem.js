document.addEventListener("DOMContentLoaded", () => {
    const productCards = document.querySelectorAll(".product-card");
    const orderList = document.getElementById("orderList");
    const totalPriceEl = document.getElementById("totalPrice");
    const changePriceEl = document.getElementById("changePrice");
    const amountPaidInput = document.getElementById("amountPaid");
    const clearOrderBtn = document.getElementById("clearOrder");

    if (!orderList || !totalPriceEl || !changePriceEl || !amountPaidInput || !clearOrderBtn) {
        console.error("⚠️ Some elements not found in the DOM.");
        return;
    }

    let savedOrders = JSON.parse(localStorage.getItem("orders")) || [];
    let total = parseFloat(localStorage.getItem("total")) || 0;

    renderOrderList();
    updateTotals();

    // Add product to order
    productCards.forEach(card => {
        card.addEventListener("click", () => {
            const name = card.dataset.name;
            const price = parseFloat(card.dataset.price);
            const img = card.querySelector(".product-image img")?.src || "";

            const existingOrder = savedOrders.find(order => order.name === name);

            if (existingOrder) {
                existingOrder.quantity += 1;
                existingOrder.totalPrice = existingOrder.quantity * existingOrder.price;
            } else {
                savedOrders.push({
                    name: name,
                    price: price,
                    quantity: 1,
                    totalPrice: price,
                    img: img
                });
            }

            updateOrderState();
        });
    });

    // Render the order list
    function renderOrderList() {
        orderList.innerHTML = "";

        savedOrders.forEach((order, index) => {
            const div = document.createElement("div");
            div.classList.add("order-item-card");
            div.style.display = "flex";
            div.style.alignItems = "center";
            div.style.marginBottom = "10px";

            div.innerHTML = `
                <img src="${order.img}" alt="${order.name}" style="width:50px;height:50px;object-fit:cover;margin-right:10px;">
                <div style="flex:1;">
                    <div style="display:flex; justify-content:space-between;">
                        <strong>${order.name}</strong>
                        <span>₱${order.totalPrice.toFixed(2)}</span>
                    </div>
                    <div style="margin-top:5px;">
                        <button class="qty-btn minus">-</button>
                        <span class="quantity">x${order.quantity}</span>
                        <button class="qty-btn plus">+</button>
                        <button class="remove-btn" style="margin-left:10px;">❌</button>
                    </div>
                </div>
            `;

            const minusBtn = div.querySelector(".minus");
            const plusBtn = div.querySelector(".plus");
            const removeBtn = div.querySelector(".remove-btn");

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

            orderList.appendChild(div);
        });
    }

    // Update totals and storage
    function updateTotals() {
        total = savedOrders.reduce((sum, item) => sum + item.totalPrice, 0);
        totalPriceEl.textContent = `₱${total.toFixed(2)}`;

        const paid = parseFloat(amountPaidInput.value) || 0;
        const change = paid - total;
        changePriceEl.textContent = change >= 0 ? `₱${change.toFixed(2)}` : "₱0.00";
    }

    function updateOrderState() {
        renderOrderList();
        updateTotals();
        localStorage.setItem("orders", JSON.stringify(savedOrders));
        localStorage.setItem("total", total);
    }

    // Recalculate change on input
    amountPaidInput.addEventListener("input", updateTotals);

    // Clear order
    clearOrderBtn.addEventListener("click", () => {
        savedOrders = [];
        total = 0;
        orderList.innerHTML = '';
        amountPaidInput.value = '';
        totalPriceEl.textContent = '₱0.00';
        changePriceEl.textContent = '₱0.00';
        localStorage.removeItem("orders");
        localStorage.removeItem("total");
    });
});
