
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
        <td>${parseFloat(product.price).toFixed(2)} MAD</td>
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

function editProducts() {
  const form = document.getElementById('edit-form');
  form.addEventListener('submit', async e => {
    e.preventDefault();
    const productId = document.getElementById('product_id').value;
    const nomProduit = document.getElementById('name').value;
    const description = document.getElementById('description').value;
    const categorieId = document.getElementById('category_id').value;
    const prix = document.getElementById('price').value;
    const quantite = document.getElementById('quantity').value;
    const produit = { nomProduit, description, categorieId, prix, quantite, productId };
    const response = await ProductService.updateProduct(produit);
    if (response.status === "success") {
      alert(response.message)
      setTimeout(() => {
        window.location.href = "http://localhost/gestion_stock_magasin/products/index.php";
      }, 1500);
    } else if (response.status === "error") {
      alert(response.message);
    }
  })
}