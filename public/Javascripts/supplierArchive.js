document.addEventListener("DOMContentLoaded", () => {
    const archiveButtons = document.querySelectorAll(".archive-btn, .restore-btn");

    archiveButtons.forEach((button) => {
        button.addEventListener("click", async () => {
        const supplierId = button.getAttribute("data-id");

            if (!supplierId) return;

        const action = button.classList.contains("archive-btn") ? "archive" : "restore";
        const confirmMsg =
            action === "archive"
            ? "Are you sure you want to archive this supplier?"
            : "Are you sure you want to restore this supplier?";

                if (!confirm(confirmMsg)) return;

                try {
                    const response = await fetch("/admin/suppliers/archive", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                        },
                        
                        body: JSON.stringify({ Supplier_id: supplierId }),
    });

            const data = await response.json();

                if (data.success) {
                    alert(`Supplier ${action === "archive" ? "archived" : "restored"} successfully!`);
                        location.reload();
                } else {
                    alert("Something went wrong. Please try again.");
                }
        } catch (error) {
                console.error("Error:", error);
                alert("Error connecting to the server.");
                }
});
});
});
