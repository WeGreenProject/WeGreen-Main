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
      console.log(msg);
      $("#ProdutoHomemVenda").html(msg);

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
function getFiltrosLimparFiltro() {
  $("#CategoriaSelect").val("-1");
  $("#tamanhoSelect").val("-1");
  $("#estadoSelect").val("-1");
  getProdutosHomem();
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
      console.log(msg);
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
      console.log(msg);
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
      console.log(msg);
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
