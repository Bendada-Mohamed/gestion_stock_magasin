// Configuration de l'API
const API_URL = 'http://localhost/gestion_stock_magasin/api';

// Service pour les produits
const ProductService = {
    // Récupérer tous les produits avec filtres
    async getProducts(filters = {}) {
        try {
            const queryParams = new URLSearchParams(filters).toString();
            const response = await fetch(`${API_URL}/products/?${queryParams}`);
            if (!response.ok) throw new Error('Erreur réseau');
            return await response.json();
        } catch (error) {
            console.error('Erreur lors de la récupération des produits:', error);
            throw error;
        }
    },

    // Récupérer un produit par ID
    async getProduct(id) {
        try {
            const response = await fetch(`${API_URL}/products/?id=${id}`);
            if (!response.ok) throw new Error('Erreur réseau');
            return await response.json();
        } catch (error) {
            console.error('Erreur lors de la récupération du produit:', error);
            throw error;
        }
    },

    // Créer un nouveau produit
    async createProduct(productData) {
        try {
            const response = await fetch(`${API_URL}/products/`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(productData)
            });
            if (!response.ok) throw new Error('Erreur réseau');
            return await response.json();
        } catch (error) {
            console.error('Erreur lors de la création du produit:', error);
            throw error;
        }
    },

    // Mettre à jour un produit
    async updateProduct(id, productData) {
        try {
            const response = await fetch(`${API_URL}/products/`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ ...productData, id })
            });
            if (!response.ok) throw new Error('Erreur réseau');
            return await response.json();
        } catch (error) {
            console.error('Erreur lors de la mise à jour du produit:', error);
            throw error;
        }
    },

    // Supprimer un produit
    async deleteProduct(id) {
        try {
            const response = await fetch(`${API_URL}/products/?id=${id}`, {
                method: 'DELETE'
            });
            if (!response.ok) throw new Error('Erreur réseau');
            return await response.json();
        } catch (error) {
            console.error('Erreur lors de la suppression du produit:', error);
            throw error;
        }
    }
};

// Example usage:
/*
// Get all products
ProductService.getAllProducts()
    .then(response => {
        console.log('Products:', response.data);
    })
    .catch(error => {
        console.error('Error:', error);
    });

// Create new product
const newProduct = {
    name: 'New Product',
    description: 'Product description',
    quantity: 10,
    price: 99.99
};

ProductService.createProduct(newProduct)
    .then(response => {
        console.log('Created:', response);
    })
    .catch(error => {
        console.error('Error:', error);
    });
*/ 