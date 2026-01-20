function getProdutoMostrar() {
  const params = new URLSearchParams(window.location.search);
  const produtoID = params.get("id");

  if (!produtoID) {
    window.location.href = "index.html";
    return;
  }

  let dados = new FormData();
  dados.append("op", 1);
  dados.append("id", produtoID);

  $.ajax({
    url: "src/controller/controllerProduto.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      console.log(msg);
      $("#ProdutoInfo").html(msg);

      // Adicionar evento ao botão de comprar
      $(".btnComprarAgora").on("click", function () {
        const produtoId = $(this).data("id");
        comprarAgora(produtoId);
      });
    })
    .fail(function (jqXHR, textStatus) {
      alert("Erro ao carregar o produto: " + textStatus);
      window.location.href = "index.html";
    });
}

function ErrorSession() {
  alerta("Inicie Sessão", "É necessário iniciar sessão para avançar!", "error");
}

function ErrorSession2() {
  alerta("Mesma Pessoa", "Não pode conversar consigo mesmo!", "error");
}

function comprarAgora(produtoId) {
  let dados = new FormData();
  dados.append("op", 7);
  dados.append("produto_id", produtoId);

  $.ajax({
    url: "src/controller/controllerCarrinho.php",
    method: "POST",
    data: dados,
    contentType: false,
    processData: false,
  })
    .done(function (response) {
      console.log("Resposta do servidor:", response);

      if (response.includes("Erro")) {
        Swal.fire({
          title: "Erro!",
          text: response,
          icon: "error",
          confirmButtonColor: "#d33",
          confirmButtonText: "OK",
        });
      } else {
        Swal.fire({
          title: "Sucesso!",
          text: "Produto adicionado ao carrinho",
          icon: "success",
          confirmButtonColor: "#28a745",
          confirmButtonText: "OK",
        });
      }
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
      console.error("Erro AJAX:", textStatus, errorThrown);
      Swal.fire({
        title: "Erro!",
        text: "Não foi possível adicionar o produto ao carrinho",
        icon: "error",
        confirmButtonColor: "#d33",
        confirmButtonText: "OK",
      });
    });
}

function alerta(titulo, msg, icon) {
  Swal.fire({
    position: "center",
    icon: icon,
    title: titulo,
    text: msg,
    showConfirmButton: true,
  });
}

$(function () {
  getProdutoMostrar();
});
