// Charger les produits au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
  loadProducts();
  setupFilterForm();
});

// Fonction pour charger les produits

async function loadProducts(filters = {}) {
  try {
    const response = await ProductService.getProducts(filters);
    const products = response.data;
    const tableBody = document.getElementById('productsTableBody');
    tableBody.innerHTML = '';

    products.forEach(product => {
      const row = document.createElement('tr');
      row.innerHTML = `
                        <td>${product.name}</td>
                        <td>${product.description}</td>
                        <td>${product.category_name || 'Non catégorisé'}</td>
                        <td>${parseFloat(product.price).toFixed(2)} €</td>
                        <td>
                            <span class="badge bg-${product.quantity < 10 ? 'danger' : 'success'}">
                                ${product.quantity}
                            </span>
                        </td>
                        <td>
                            <a href="edit.php?id=${product.id}" class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button class="btn btn-sm btn-danger" onclick="deleteProduct(${product.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    `;
      tableBody.appendChild(row);
    });
  } catch (error) {
    console.error('Erreur lors du chargement des produits:', error);
    alert('Erreur lors du chargement des produits');
  }
}


// Configuration du formulaire de filtrage
function setupFilterForm() {
  const form = document.getElementById('filterForm');
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(form);
    const filters = {};
    for (let [key, value] of formData.entries()) {
      if (value) filters[key] = value;
    }
    await loadProducts(filters);
  });
}

// Supprimer un produit
async function deleteProduct(parametre) {
  if (confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
    try {
      await ProductService.deleteProduct(parametre);
      alert('Produit supprimé avec succès');
      loadProducts();
    } catch (error) {
      console.error('Erreur lors de la suppression du produit:', error);
      alert('Erreur lors de la suppression du produit');
    }
  }
}