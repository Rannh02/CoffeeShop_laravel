document.addEventListener("DOMContentLoaded", () => {
    console.log("🔍 Delete modal script loaded!");
    
    const deleteButtons = document.querySelectorAll(".delete-btn");
    const deleteModal = document.getElementById("deleteModal");
    const cancelDelete = document.getElementById("cancelDelete");
    const productNameEl = document.getElementById("productName");
    const deleteProductId = document.getElementById("deleteProductId");
    const deleteForm = document.getElementById("deleteForm");

    console.log("Found delete buttons:", deleteButtons.length);

    if (deleteButtons.length === 0) {
        console.error("❌ No delete buttons found! Check if buttons exist in DOM.");
        return;
    }

    if (!deleteModal) {
        console.error("❌ Delete modal not found!");
        return;
    }

    // Attach click event to each delete button
    deleteButtons.forEach((btn, index) => {
        console.log(`Attaching event to button ${index + 1}`);
        
        btn.addEventListener("click", function(e) {
            e.preventDefault(); // Prevent any default action
            e.stopPropagation(); // Stop event bubbling
            
            console.log("✅ Delete button clicked!");
            
            const id = this.getAttribute("data-id");
            const name = this.getAttribute("data-name");

            console.log("Product ID:", id);
            console.log("Product Name:", name);

            if (!id || !name) {
                console.error("❌ Missing product ID or name!");
                alert("Error: Product information is missing!");
                return;
            }

            // Update modal content
            productNameEl.textContent = name;
            deleteProductId.value = id;
            
            // Option 2: If route is /admin/products/{id}
            deleteForm.action = `/admin/products/${id}`;
            
            console.log("Form action set to:", deleteForm.action);
            
            // Show modal
            deleteModal.style.display = "flex";
            
            console.log("Modal should now be visible");
        });
    });

    // Cancel button - close modal
    if (cancelDelete) {
        cancelDelete.addEventListener("click", function(e) {
            e.preventDefault();
            console.log("Cancel clicked");
            deleteModal.style.display = "none";
        });
    }

    // Close modal when clicking outside
    deleteModal.addEventListener("click", function(e) {
        if (e.target === deleteModal) {
            console.log("Clicked outside modal");
            deleteModal.style.display = "none";
        }
    });

    // Optional: Close modal with ESC key
    document.addEventListener("keydown", function(e) {
        if (e.key === "Escape" && deleteModal.style.display === "flex") {
            console.log("ESC pressed");
            deleteModal.style.display = "none";
        }
    });
});