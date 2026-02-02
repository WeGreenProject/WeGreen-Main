function getCards() {
  let dados = new FormData();
  dados.append("op", 1);

  $.ajax({
    url: "src/controller/controllergestaoComentarios.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $(".stats-grid-compact").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function getProdutos() {
  if ($.fn.DataTable.isDataTable("#comentariosTable")) {
    $("#comentariosTable").DataTable().destroy();
  }

  let dados = new FormData();
  dados.append("op", 2);

  $.ajax({
    url: "src/controller/controllergestaoComentarios.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#comentariosTableBody").html(msg);
      $("#comentariosTable").DataTable();
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function getDadosFornecedores(ID_Fornecedores) {
  let dados = new FormData();
  dados.append("op", 9);
  dados.append("id", ID_Fornecedores);

  $.ajax({
    url: "src/controller/controllergestaoComentarios.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      let obj = JSON.parse(msg);
      $("#numfornecedorEdit").val(obj.id);
      $("#fornecedorNomeEdit").val(obj.nome);
      $("#fornecedorCategoriaEdit").val(obj.tipo_produtos_id);
      $("#fornecedorEmailEdit").val(obj.email);
      $("#fornecedortelefoneEdit").val(obj.telefone);
      $("#fornecedorSedeEdit").val(obj.morada);
      $("#observacoesEdit").val(obj.descricao);
      $("#btnGuardar3").attr(
        "onclick",
        "guardaEditDadosFornecedores(" + obj.id + ")",
      );

      $("#formEditFornecedores").fadeIn();
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}

function getButaoNav() {
  let dados = new FormData();
  dados.append("op", 3);

  $.ajax({
    url: "src/controller/controllergestaoComentarios.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#badgeComentarios").text(msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function getButaoReports() {
  let dados = new FormData();
  dados.append("op", 5);

  $.ajax({
    url: "src/controller/controllergestaoComentarios.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#badgeReports").text(msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function getReports() {
  if ($.fn.DataTable.isDataTable("#reportsTable")) {
    $("#reportsTable").DataTable().destroy();
  }

  let dados = new FormData();
  dados.append("op", 6);

  $.ajax({
    url: "src/controller/controllergestaoComentarios.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#reportsTableBody").html(msg);
      $("#reportsTable").DataTable();
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
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

function getComentariosModal(idProduto) {
  let dados = new FormData();
  dados.append("op", 4);
  dados.append("idProduto", idProduto);

  $.ajax({
    url: "src/controller/controllergestaoComentarios.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      $("#comentarioModalBody").html(msg);
      $("#comentarioModal").css("display", "flex");
    })
    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}

function fecharModalComentario() {
  $("#comentarioModal").css("display", "none");
}

function fecharModalReport() {
  $("#reportModal").css("display", "none");
}

$(function () {
  getReports();
  getButaoReports();
  getButaoNav();
  getProdutos();
  getCards();
});
document.querySelectorAll(".tab-button").forEach((button) => {
  button.addEventListener("click", function () {
    const tabName = this.dataset.tab;

    document
      .querySelectorAll(".tab-button")
      .forEach((btn) => btn.classList.remove("active"));
    document
      .querySelectorAll(".tab-content")
      .forEach((content) => content.classList.remove("active"));

    this.classList.add("active");
    document.getElementById("tab-" + tabName).classList.add("active");
  });
});
