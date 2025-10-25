// Modal functionality
document.addEventListener('DOMContentLoaded', function() {
    
    // Open Employee Modal
    const openModalBtn = document.getElementById('openEmployeeModal');
    if (openModalBtn) {
        openModalBtn.addEventListener('click', function() {
            document.getElementById('EmployeeModal').style.display = 'flex';
        });
    }

    // Close Employee Modal
    const closeModalBtn = document.getElementById('closeEmployeeModal');
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', function() {
            document.getElementById('EmployeeModal').style.display = 'none';
        });
    }

    // Close modal when clicking outside
    const modal = document.getElementById('EmployeeModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }

    // Archive Employee with AJAX
    const archiveBtns = document.querySelectorAll('.archive-btn');
    archiveBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const employeeId = this.getAttribute('data-id');
            
            if (confirm('Are you sure you want to archive this employee?')) {
                fetch(`/admin/employee/${employeeId}/archive`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Employee archived successfully!');
                        location.reload();
                    } else {
                        alert('Error archiving employee');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error archiving employee');
                });
            }
        });
    });
});