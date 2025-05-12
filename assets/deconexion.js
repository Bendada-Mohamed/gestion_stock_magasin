document.getElementById('logoutBtn').addEventListener('click', async function () {
  try {
    const response = await AuthService.logout();
    if (response.status) {
      // Redirect to login page on successful logout
      window.location.href = 'http://localhost/gestion_stock_magasin/index.php';
    } else {
      alert('Erreur lors de la déconnexion: ' + response.message);
    }
  } catch (error) {
    console.error('Erreur:', error);
    alert('Une erreur est survenue lors de la déconnexion');
  }
});