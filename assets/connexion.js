const form = document.getElementById("connectForm");

form.addEventListener('submit', async (e) => {
  e.preventDefault();

  const formData = new FormData(form);
  const data = Object.fromEntries(formData.entries());
  const response = await AuthService.connect(data);

  // Supprimer les anciens messages d'erreur
  document.querySelectorAll(".alert").forEach(el => el.remove());

  if (response.status) {
    window.location.href = "http://localhost/gestion_stock_magasin/";
  } else {
    document.querySelector(".card-body").insertAdjacentHTML(
      "afterbegin",
      `<div class="alert alert-danger">${response.message}</div>`);
  }

});