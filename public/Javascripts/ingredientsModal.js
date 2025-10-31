document.addEventListener("DOMContentLoaded", () => {
  const productModalBtn = document.querySelector(".add-product-btn");
  const modal = document.getElementById("ProductModal");
  const closeBtn = document.getElementById("closeProductModal");
  const form = modal.querySelector("form");

  // Open modal
  productModalBtn.addEventListener("click", () => {
    modal.style.display = "flex";
  });

  // Close modal by Cancel button
  closeBtn.addEventListener("click", () => {
    modal.style.display = "none";
  });

  // Close modal if clicking outside form
  modal.addEventListener("click", (e) => {
    if (e.target === modal) {
      modal.style.display = "none";
    }
  });
});
