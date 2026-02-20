function getFiltrosDesignerCategoria() {
  let dados = new FormData();
  dados.append("op", 1);

  $.ajax({
    url: "src/controller/controllerDesigner.php",
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
function getFiltrosDesignerTamanho() {
  let dados = new FormData();
  dados.append("op", 7);

  $.ajax({
    url: "src/controller/controllerDesigner.php",
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
function getFiltrosDesignerEstado() {
  let dados = new FormData();
  dados.append("op", 8);

  $.ajax({
    url: "src/controller/controllerDesigner.php",
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
function getFiltrosLimparFiltro() {
  $("#CategoriaSelect").val("-1");
  $("#tamanhoSelect").val("-1");
  $("#estadoSelect").val("-1");
  getProdutosDesigner();
}
function getProdutosDesigner() {
  let categoria = $("#CategoriaSelect").val();
  let tamanho = $("#tamanhoSelect").val();
  let estado = $("#estadoSelect").val();

  let dados = new FormData();
  dados.append("op", 2);
  dados.append("categoria", categoria);
  dados.append("tamanho", tamanho);
  dados.append("estado", estado);
  $.ajax({
    url: "src/controller/controllerDesigner.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#ProdutoDesignerVenda").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}

function getProdutoDesignerMostrar() {
  const params = new URLSearchParams(window.location.search);
  const produtoID = params.get("id");

  let dados = new FormData();
  dados.append("op", 3);
  dados.append("id", produtoID);

  $.ajax({
    url: "src/controller/controllerDesigner.php",
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
      Swal.fire({
        title: "Erro!",
        text: "Não foi possível adicionar o produto ao carrinho",
        icon: "error",
        confirmButtonColor: "#d33",
        confirmButtonText: "OK",
      });
    });
}

$(function () {
  getProdutoDesignerMostrar();
  getProdutosDesigner();
  getFiltrosDesignerCategoria();
  getFiltrosDesignerTamanho();
  getFiltrosDesignerEstado();
  $("#CategoriaSelect, #tamanhoSelect, #estadoSelect").on(
    "change",
    function () {
      getProdutosDesigner();
    }
  );
});
