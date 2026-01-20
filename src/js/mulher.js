function getFiltrosMulherCategoria() {
  let dados = new FormData();
  dados.append("op", 1);

  $.ajax({
    url: "src/controller/controllerMulher.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      console.log(msg);
      $("#CategoriaSelect").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function getFiltrosMulherTamanho() {
  let dados = new FormData();
  dados.append("op", 7);

  $.ajax({
    url: "src/controller/controllerMulher.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      console.log(msg);
      $("#tamanhoSelect").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function getFiltrosMulherEstado() {
  let dados = new FormData();
  dados.append("op", 8);

  $.ajax({
    url: "src/controller/controllerMulher.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      console.log(msg);
      $("#estadoSelect").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function getFiltrosLimparFiltro() {
  $("#CategoriaSelect").val("-1");
  $("#tamanhoSelect").val("-1");
  $("#estadoSelect").val("-1");
  getProdutosMulher();
}
function getProdutosMulher() {
  let categoria = $("#CategoriaSelect").val();
  let tamanho = $("#tamanhoSelect").val();
  let estado = $("#estadoSelect").val();

  let dados = new FormData();
  dados.append("op", 2);
  dados.append("categoria", categoria);
  dados.append("tamanho", tamanho);
  dados.append("estado", estado);
  $.ajax({
    url: "src/controller/controllerMulher.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#ProdutoMulherVenda").html(msg);

      // Verificar quais produtos estão nos favoritos
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

function getProdutoMulherMostrar() {
  const params = new URLSearchParams(window.location.search);
  const produtoID = params.get("id");

  let dados = new FormData();
  dados.append("op", 3);
  dados.append("id", produtoID);

  $.ajax({
    url: "src/controller/controllerMulher.php",
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
      $(".btnComprarAgora").on("click", function () {
        const produtoId = $(this).data("id");
        comprarAgora(produtoId);
      });
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function ErrorSession() {
  alerta("Inicie Sessão", "É necessario iniciar sessão avançar!", "error");
}
function ErrorSession2() {
  alerta("Mesma Pessoa", "Não pode conversa com voce mesmo!", "error");
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
  getProdutoMulherMostrar();
  getProdutosMulher();
  getFiltrosMulherCategoria();
  getFiltrosMulherTamanho();
  getFiltrosMulherEstado();
  $("#CategoriaSelect, #tamanhoSelect, #estadoSelect").on(
    "change",
    function () {
      getProdutosMulher();
    },
  );
});
