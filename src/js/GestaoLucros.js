function getCards() {
  let dados = new FormData();
  dados.append("op", 1);

  $.ajax({
    url: "src/controller/controllerGestaoLucros.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#ReceitasCard").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}

function getCardsDespesas() {
  let dados = new FormData();
  dados.append("op", 11);

  $.ajax({
    url: "src/controller/controllerGestaoLucros.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#DespesasCard").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}

function getCardsLucro() {
  let dados = new FormData();
  dados.append("op", 12);

  $.ajax({
    url: "src/controller/controllerGestaoLucros.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#LucroCard").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}

function getCardsMargem() {
  let dados = new FormData();
  dados.append("op", 13);

  $.ajax({
    url: "src/controller/controllerGestaoLucros.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#MargemCard").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}

function getTransicoes() {
  if ($.fn.DataTable.isDataTable("#transacoesTable")) {
    $("#transacoesTable").DataTable().destroy();
  }

  let dados = new FormData();
  dados.append("op", 4);

  $.ajax({
    url: "src/controller/controllerGestaoLucros.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#transacoesBody").html(msg);
      $("#transacoesTable").DataTable();
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function getRendimentos() {
  if ($.fn.DataTable.isDataTable("#tblRendimentos")) {
    $("#tblRendimentos").DataTable().destroy();
  }

  let dados = new FormData();
  dados.append("op", 5);

  $.ajax({
    url: "src/controller/controllerGestaoLucros.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      console.log(msg);
      $("#listagemRendimentos").html(msg);
      $("#tblRendimentos").DataTable();
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function getGastos() {
  if ($.fn.DataTable.isDataTable("#tblGastos")) {
    $("#tblGastos").DataTable().destroy();
  }

  let dados = new FormData();
  dados.append("op", 6);

  $.ajax({
    url: "src/controller/controllerGestaoLucros.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      console.log(msg);
      $("#listagemGastos").html(msg);
      $("#tblGastos").DataTable();
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
function removerGastos(id) {
  let dados = new FormData();
  dados.append("op", 7);
  dados.append("ID_Gasto", id);

  $.ajax({
    url: "src/controller/controllerGestaoLucros.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      let obj = JSON.parse(msg);
      if (obj.flag) {
        alerta("Gastos", obj.msg, "success");
        getGastos();
      } else {
        alerta("Gastos", obj.msg, "error");
      }
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function removerRendimentos(id) {
  let dados = new FormData();
  dados.append("op", 8);
  dados.append("ID_Rendimento", id);

  $.ajax({
    url: "src/controller/controllerGestaoLucros.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      let obj = JSON.parse(msg);
      if (obj.flag) {
        alerta("Rendimentos", obj.msg, "success");
        closeModalRendimento();
        getRendimentos();
      } else {
        alerta("Rendimentos", obj.msg, "error");
      }
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function registaGastos() {
  let dados = new FormData();

  // Verificar se está editando ou adicionando
  if (window.editandoGastoId) {
    dados.append("op", 16);
    dados.append("id", window.editandoGastoId);
  } else {
    dados.append("op", 10);
  }

  dados.append("descricao", $("#descricaoGasto").val());
  dados.append("valor", $("#valorGasto").val());
  dados.append("data", $("#dataGasto").val());

  $.ajax({
    url: "src/controller/controllerGestaoLucros.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      let obj = JSON.parse(msg);
      if (obj.flag) {
        alerta("Gastos", obj.msg, "success");
        window.editandoGastoId = null;
        closeModalGasto();
        getGastos();
        getCards();
        getCardsDespesas();
        getCardsLucro();
        getCardsMargem();
      } else {
        alerta("Gastos", obj.msg, "error");
      }
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function registaRendimentos() {
  let dados = new FormData();

  // Verificar se está editando ou adicionando
  if (window.editandoRendimentoId) {
    dados.append("op", 17);
    dados.append("id", window.editandoRendimentoId);
  } else {
    dados.append("op", 9);
  }

  dados.append("descricao", $("#descricaoRendimento").val());
  dados.append("valor", $("#valorRendimento").val());
  dados.append("data", $("#dataRendimento").val());

  $.ajax({
    url: "src/controller/controllerGestaoLucros.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      let obj = JSON.parse(msg);
      if (obj.flag) {
        alerta("Rendimentos", obj.msg, "success");
        window.editandoRendimentoId = null;
        closeModalRendimento();
        getRendimentos();
        getCards();
        getCardsDespesas();
        getCardsLucro();
        getCardsMargem();
      } else {
        alerta("Rendimentos", obj.msg, "error");
      }
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
$(function () {
  getCards();
  getCardsDespesas();
  getCardsLucro();
  getCardsMargem();
  getTransicoes();
  getGastos();
  getRendimentos();
});
