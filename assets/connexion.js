const form = document.getElementById("connectForm");

form.addEventListener('submit', async (e) => {
  e.preventDefault();

  const formData = new FormData(form);
  const data = Object.fromEntries(formData);
  const response = await AuthService.connect(data);

  if (response.status) {

    window.location.href = "http://localhost/gestion_stock_magasin/";
  } else {
    // document.querySelector(".card-body").insertAdjacentHTML(
    //     "afterbegin",
    //     `<div class="alert alert-danger">${response.message}</div>`
    // );
    // $error = response.message;
    document.querySelector(".alert").innerHTML = response.message;
  }
});