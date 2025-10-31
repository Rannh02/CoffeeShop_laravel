document.addEventListener('DOMContentLoaded', () => {
  const deleteButtons = document.querySelectorAll('.delete-btn');

  deleteButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const ingredientId = btn.dataset.id;

      if (confirm('Are you sure you want to delete this ingredient?')) {
        fetch('delete_ingredient.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `Ingredient_id=${ingredientId}`
        })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              alert('Ingredient deleted successfully!');
              location.reload();
            } else {
              alert('Error: ' + data.message);
            }
          })
          .catch(error => console.error('Error:', error));
      }
    });
  });
});
