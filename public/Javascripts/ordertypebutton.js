document.addEventListener("DOMContentLoaded", () => {
    const typeButtons = document.querySelectorAll(".order-type-btn");
    const customerNameInput = document.getElementById("customerName");

    // 🔹 Load saved order type
    const savedType = localStorage.getItem("orderType");
    if (savedType) {
        typeButtons.forEach(btn => {
            if (btn.dataset.type === savedType) {
                btn.classList.add("active");
            }
        });
    }

    // 🔹 Load saved customer name
    const savedName = localStorage.getItem("customerName");
    if (savedName && customerNameInput) {
        customerNameInput.value = savedName;
    }

    // 🔹 Save order type on click + trigger event for other scripts
    typeButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            typeButtons.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");
            localStorage.setItem("orderType", btn.dataset.type);

            // 🔸 Notify other scripts that order type changed
            document.dispatchEvent(new CustomEvent("orderTypeChanged", {
                detail: { type: btn.dataset.type }
            }));
        });
    });

    // 🔹 Save customer name while typing + trigger event
    if (customerNameInput) {
        customerNameInput.addEventListener("input", () => {
            localStorage.setItem("customerName", customerNameInput.value);

            // 🔸 Notify other scripts that customer name changed
            document.dispatchEvent(new CustomEvent("customerNameChanged", {
                detail: { name: customerNameInput.value }
            }));
        });
    }
});
