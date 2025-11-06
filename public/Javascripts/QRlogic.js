document.addEventListener("DOMContentLoaded", () => {
      const ewalletBtn = document.querySelector(".payment-btn.gcash");
      const modal = document.getElementById("ewalletModal");
      const closeBtn = document.getElementById("closeEwalletModal");

      ewalletBtn.addEventListener("click", () => {
        modal.style.display = "flex";
      });

      closeBtn.addEventListener("click", () => {
        modal.style.display = "none";
      });

      modal.addEventListener("click", (e) => {
        if (e.target === modal) {
          modal.style.display = "none";
        }
      });
    });