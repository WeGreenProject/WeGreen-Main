$(document).ready(function () {
  
  const btnFavorito = document.getElementById("btnFavorito");
  if (btnFavorito) {
    const produtoId = btnFavorito.getAttribute("data-produto-id");
    if (produtoId) {
      verificarFavorito(produtoId, btnFavorito);
    }
  }
});
