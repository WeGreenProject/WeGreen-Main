function getVendedor() {
  let dados = new FormData();
  dados.append("op", 1);
  const params = new URLSearchParams(window.location.search);
  const utilizadorID = params.get("id");
  dados.append("utilizadorID", utilizadorID);

  $.ajax({
    url: "src/controller/controllerVendedor.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      $("#PerfilVendedora").html(msg);
      
      getProdutosVendedora();
    })
    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}

function getProdutosVendedora() {
  let dados = new FormData();
  dados.append("op", 2);
  const params = new URLSearchParams(window.location.search);
  const utilizadorID = params.get("id");
  dados.append("utilizadorID", utilizadorID);

  $.ajax({
    url: "src/controller/controllerVendedor.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      $("#produtos-container").html(msg);
    })
    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}

$(document).on("change", "#filtroEstado, #filtroOrdenacao", function () {
  const estado = $("#filtroEstado").val();
  const ordenacao = $("#filtroOrdenacao").val();

  let produtos = $(".produto-item").toArray();

  
  produtos.forEach((produto) => {
    const produtoEstado = $(produto).data("estado");
    if (estado === "" || produtoEstado === estado) {
      $(produto).show();
    } else {
      $(produto).hide();
    }
  });

  
  produtos = $(".produto-item:visible").toArray();
  produtos.sort((a, b) => {
    if (ordenacao === "preco_asc") {
      return parseFloat($(a).data("preco")) - parseFloat($(b).data("preco"));
    } else if (ordenacao === "preco_desc") {
      return parseFloat($(b).data("preco")) - parseFloat($(a).data("preco"));
    } else {
      return new Date($(b).data("data")) - new Date($(a).data("data"));
    }
  });

  
  $("#produtos-grid").html(produtos);
});

$(function () {
  getVendedor();
  
});
