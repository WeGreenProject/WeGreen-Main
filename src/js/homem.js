function getProdutosHomem() {
  let categoria = $("#CategoriaSelect").val();
  let tamanho = $("#tamanhoSelect").val();
  let estado = $("#estadoSelect").val();

  let dados = new FormData();
  dados.append("op", 1);
  dados.append("categoria", categoria);
  dados.append("tamanho", tamanho);
  dados.append("estado", estado);

  $.ajax({
    url: "src/controller/controllerHomem.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#ProdutoHomemVenda").html(msg);

      
      setTimeout(function () {
        $(".btn-favorito").each(function () {
          const produtoId = $(this).data("produto-id");
          verificarFavorito(produtoId, this);
        });
      }, 100);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function getFiltrosLimparFiltro() {
  $("#CategoriaSelect").val("-1");
  $("#tamanhoSelect").val("-1");
  $("#estadoSelect").val("-1");
  getProdutosHomem();
}
function ErrorSession() {
  const modal =
    typeof showModernConfirmModal === "function"
      ? showModernConfirmModal(
          "Inicie Sessão",
          "É necessário iniciar sessão para conversar com o vendedor!",
          {
            confirmText: '<i class="fas fa-sign-in-alt"></i> Ir para Login',
            icon: "fa-sign-in-alt",
            iconBg:
              "background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%);",
          },
        )
      : Swal.fire({
          icon: "warning",
          title: "Inicie Sessão",
          text: "É necessário iniciar sessão para conversar com o vendedor!",
          showCancelButton: true,
          confirmButtonText: "Ir para Login",
          cancelButtonText: "Cancelar",
          confirmButtonColor: "#3cb371",
          cancelButtonColor: "#6c757d",
          reverseButtons: true,
        });

  modal.then((result) => {
    if (result.isConfirmed) {
      window.location.href = "login.html";
    }
  });
}
function ErrorSession2() {
  if (typeof showModernInfoModal === "function") {
    showModernInfoModal(
      "Ação Inválida",
      "Não pode iniciar uma conversa consigo mesmo!",
    );
  } else {
    Swal.fire(
      "Ação Inválida",
      "Não pode iniciar uma conversa consigo mesmo!",
      "info",
    );
  }
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
function getProdutoHomemMostrar() {
  const params = new URLSearchParams(window.location.search);
  const produtoID = params.get("id");

  let dados = new FormData();
  dados.append("op", 2);
  dados.append("id", produtoID);

  $.ajax({
    url: "src/controller/controllerHomem.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#ProdutoInfo").html(msg);

      $(".btnComprarAgora").on("click", function () {
        const produtoId = $(this).data("id");
        comprarAgora(produtoId);
      });
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function getFiltrosHomemCategoria() {
  let dados = new FormData();
  dados.append("op", 3);

  $.ajax({
    url: "src/controller/controllerHomem.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      $("#CategoriaSelect").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function getFiltrosHomemTamanho() {
  let dados = new FormData();
  dados.append("op", 4);

  $.ajax({
    url: "src/controller/controllerHomem.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      $("#tamanhoSelect").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function getFiltrosHomemEstado() {
  let dados = new FormData();
  dados.append("op", 5);

  $.ajax({
    url: "src/controller/controllerHomem.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      $("#estadoSelect").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
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
  }).done(function () {
    Swal.fire({
      title: "Sucesso!",
      text: "Produto adicionado ao carrinho",
      icon: "success",
      confirmButtonColor: "#28a745",
      confirmButtonText: "OK",
    });
  });
}
$(function () {
  getProdutoHomemMostrar();
  getProdutosHomem();
  getFiltrosHomemEstado();
  getFiltrosHomemTamanho();
  getFiltrosHomemCategoria();

  $("#CategoriaSelect, #tamanhoSelect, #estadoSelect").on(
    "change",
    function () {
      getProdutosHomem();
    },
  );
});
