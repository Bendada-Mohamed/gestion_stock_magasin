// Charger les mouvements au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
  loadMovements();
  setupFilterForm();
});

async function loadMovements(filters = {}) {
  try {
    const response = await MovementService.getMovements(filters);
    const movements = response.data;
    const tableBody = document.getElementById('MovementsTableBody');
    tableBody.innerHTML = '';
    movements.forEach(movement => {
      const row = document.createElement('tr');
      row.innerHTML = `
      <td>${movement.date_mouvement}</td>
      <td>${movement.product_name}</td><td>
          <span class="badge bg-${movement.type === 'entree' ? 'success' : 'danger'}">
              ${movement.type === 'entree' ? 'Entrée' : 'Sortie'}
          </span>
      </td>
      <td>${movement.quantity}</td>
      <td>${movement.username}</td>
      `;
      tableBody.appendChild(row);
    });
  } catch (error) {
    console.error('Error lors du chargement des mouvements:', error);
    alert('Erreur lors du chargement des mouvements');
  }
}

function setupFilterForm() {
  const form = document.getElementById('filterForm');
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(form);
    const filters = {};
    for (let [key, value] of formData.entries()) {
      if (value) filters[key] = value;
    }
    await loadMovements(filters);
  });
}