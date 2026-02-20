function alerta(titulo, msg, icon) {
  const tipo = String(icon || "info").toLowerCase();

  if (tipo === "success") {
    return showModernSuccessModal(titulo, msg);
  }

  if (tipo === "error") {
    return showModernErrorModal(titulo, msg);
  }

  if (tipo === "warning") {
    return showModernWarningModal(titulo, msg);
  }

  return showModernInfoModal(titulo, msg);
}

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
    .fail(function () {
      alerta("Erro", "Falha ao carregar estatísticas.", "error");
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
    .fail(function () {
      alerta("Erro", "Falha ao carregar comentários.", "error");
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
    .fail(function () {
      alerta("Erro", "Falha ao carregar badge de comentários.", "error");
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
    .fail(function () {
      alerta("Erro", "Falha ao carregar badge de reports.", "error");
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
    .fail(function () {
      alerta("Erro", "Falha ao carregar reports.", "error");
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
    .fail(function () {
      alerta("Erro", "Falha ao carregar detalhes do comentário.", "error");
    });
}

function openReportModal(idReport) {
  let dados = new FormData();
  dados.append("op", 7);
  dados.append("idReport", idReport);

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
      $("#reportModalBody").html(msg);
      $("#reportModal").css("display", "flex");
    })
    .fail(function () {
      alerta("Erro", "Falha ao abrir detalhes do report.", "error");
    });
}

function atualizarEstadoReport(idReport, estado, mensagemConfirmacao) {
  fecharModalReport();

  showModernConfirmModal("Confirmar ação", mensagemConfirmacao, {
    icon: "fa-circle-question",
    iconBg: "background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);",
    confirmText: '<i class="fas fa-check"></i> Confirmar',
  }).then((result) => {
    if (!result.isConfirmed) {
      openReportModal(idReport);
      return;
    }

    let dados = new FormData();
    dados.append("op", 8);
    dados.append("idReport", idReport);
    dados.append("estado", estado);

    $.ajax({
      url: "src/controller/controllergestaoComentarios.php",
      method: "POST",
      data: dados,
      dataType: "json",
      cache: false,
      contentType: false,
      processData: false,
    })
      .done(function (resp) {
        if (resp.success) {
          alerta("Sucesso", resp.message, "success");
          getReports();
          getButaoReports();
          getCards();
          return;
        }

        alerta("Atenção", resp.message || "Operação não concluída.", "warning");
      })
      .fail(function () {
        alerta("Erro", "Falha ao atualizar estado do report.", "error");
      });
  });
}

function resolverReport(idReport) {
  atualizarEstadoReport(
    idReport,
    "Resolvido",
    "Marcar este report como resolvido?",
  );
}

function rejeitarReport(idReport) {
  atualizarEstadoReport(idReport, "Rejeitado", "Rejeitar este report?");
}

function eliminarComentarioDoReport(idReport) {
  fecharModalReport();

  showModernConfirmModal(
    "Eliminar comentário",
    "Esta ação remove o comentário denunciado de forma permanente.",
    {
      icon: "fa-trash-alt",
      iconBg: "background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%);",
      confirmText: '<i class="fas fa-trash-alt"></i> Eliminar',
    },
  ).then((result) => {
    if (!result.isConfirmed) {
      openReportModal(idReport);
      return;
    }

    let dados = new FormData();
    dados.append("op", 9);
    dados.append("idReport", idReport);

    $.ajax({
      url: "src/controller/controllergestaoComentarios.php",
      method: "POST",
      data: dados,
      dataType: "json",
      cache: false,
      contentType: false,
      processData: false,
    })
      .done(function (resp) {
        if (resp.success) {
          alerta("Sucesso", resp.message, "success");
          getReports();
          getButaoReports();
          getButaoNav();
          getProdutos();
          getCards();
          return;
        }

        alerta(
          "Atenção",
          resp.message || "Não foi possível eliminar o comentário.",
          "warning",
        );
      })
      .fail(function () {
        alerta("Erro", "Falha ao eliminar comentário denunciado.", "error");
      });
  });
}

function fecharModalComentario() {
  $("#comentarioModal").css("display", "none");
}

function fecharModalReport() {
  $("#reportModal").css("display", "none");
}

window.openReportModal = openReportModal;
window.resolverReport = resolverReport;
window.rejeitarReport = rejeitarReport;
window.eliminarComentarioDoReport = eliminarComentarioDoReport;
window.fecharModalReport = fecharModalReport;
window.fecharModalComentario = fecharModalComentario;

$(function () {
  getReports();
  getButaoReports();
  getButaoNav();
  getProdutos();
  getCards();

  $(document).on("click", ".js-report-resolver", function (e) {
    e.preventDefault();
    e.stopPropagation();
    const idReport = Number($(this).data("reportId") || 0);
    if (idReport > 0) {
      resolverReport(idReport);
    }
  });

  $(document).on("click", ".js-report-rejeitar", function (e) {
    e.preventDefault();
    e.stopPropagation();
    const idReport = Number($(this).data("reportId") || 0);
    if (idReport > 0) {
      rejeitarReport(idReport);
    }
  });

  $(document).on("click", ".js-report-eliminar", function (e) {
    e.preventDefault();
    e.stopPropagation();
    const idReport = Number($(this).data("reportId") || 0);
    if (idReport > 0) {
      eliminarComentarioDoReport(idReport);
    }
  });
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
