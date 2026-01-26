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
  Swal.fire({
    icon: "warning",
    title: '<span style="color: #2e8b57;">Inicie Sessão</span>',
    html: '<p style="color: #64748b; font-size: 15px;">É necessário iniciar sessão para conversar com o vendedor!</p>',
    showCancelButton: true,
    confirmButtonText: '<i class="fas fa-sign-in-alt"></i> Ir para Login',
    cancelButtonText: "Cancelar",
    confirmButtonColor: "#3cb371",
    cancelButtonColor: "#6c757d",
    customClass: {
      popup: "swal-custom-popup",
      confirmButton: "swal-confirm-green",
      cancelButton: "swal-cancel",
    },
    buttonsStyling: true,
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = "login.html";
    }
  });
}

function ErrorSession2() {
  Swal.fire({
    icon: "info",
    title: '<span style="color: #2e8b57;">Ação Inválida</span>',
    html: '<p style="color: #64748b; font-size: 15px;">Não pode iniciar uma conversa consigo mesmo!</p>',
    confirmButtonText: "Entendi",
    confirmButtonColor: "#3cb371",
    customClass: {
      popup: "swal-custom-popup",
      confirmButton: "swal-confirm-green",
    },
  });
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
