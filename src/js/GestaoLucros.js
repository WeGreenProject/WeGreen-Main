
function getInfoUserDropdown() {
  
}

document.addEventListener("DOMContentLoaded", function () {
  const tabButtons = document.querySelectorAll(".tab-button");
  const tabContents = document.querySelectorAll(".tab-content");

  tabButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const targetTab = this.getAttribute("data-tab");

      
      tabButtons.forEach((btn) => btn.classList.remove("active"));
      tabContents.forEach((content) => content.classList.remove("active"));

      
      this.classList.add("active");
      document.getElementById("tab-" + targetTab).classList.add("active");
    });
  });
});

function alerta(titulo, msg, icon) {
  if (icon === "success") {
    showModernSuccessModal(titulo, msg);
  } else if (icon === "error") {
    showModernErrorModal(titulo, msg);
  } else if (icon === "warning") {
    showModernWarningModal(titulo, msg);
  } else if (icon === "info") {
    showModernInfoModal(titulo, msg);
  } else {
    showModernWarningModal(titulo, msg);
  }
}

function formatNowForDateTimeLocal() {
  const now = new Date();
  const pad = (value) => String(value).padStart(2, "0");

  return `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(
    now.getDate(),
  )}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
}

function formatDbDateForDateTimeLocal(value) {
  if (!value) return "";
  return String(value).replace(" ", "T").slice(0, 16);
}

function ajaxLucros(op, extras, dataType, onSuccess, onError) {
  const dados = new FormData();
  dados.append("op", op);

  if (extras) {
    Object.keys(extras).forEach((key) => {
      dados.append(key, extras[key]);
    });
  }

  return $.ajax({
    url: "src/controller/controllerGestaoLucros.php",
    method: "POST",
    data: dados,
    dataType: dataType || "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(onSuccess)
    .fail(function (jqXHR, textStatus) {
      if (onError) {
        onError(jqXHR, textStatus);
      } else {
      }
    });
}

let transacoesRequest = null;
let gastosRequest = null;
let rendimentosRequest = null;

function getCards() {
  ajaxLucros(1, null, "html", function (msg) {
    $("#ReceitasCard").html(msg);
  });
}

function getCardsDespesas() {
  ajaxLucros(11, null, "html", function (msg) {
    $("#DespesasCard").html(msg);
  });
}

function getCardsLucro() {
  ajaxLucros(12, null, "html", function (msg) {
    $("#LucroCard").html(msg);
  });
}

function getCardsMargem() {
  ajaxLucros(13, null, "html", function (msg) {
    $("#MargemCard").html(msg);
  });
}

function getTransacoes() {
  if (transacoesRequest && transacoesRequest.readyState !== 4) {
    transacoesRequest.abort();
  }

  if ($.fn.DataTable.isDataTable("#transacoesTable")) {
    $("#transacoesTable").DataTable().destroy();
  }

  transacoesRequest = ajaxLucros(4, null, "html", function (msg) {
    $("#transacoesBody").html(msg);

    if ($.fn.DataTable.isDataTable("#transacoesTable")) {
      $("#transacoesTable").DataTable().destroy();
    }

    $("#transacoesTable").DataTable({
      destroy: true,
      order: [[1, "desc"]],
      pageLength: 10,
    });
  });
}

function getTransicoes() {
  getTransacoes();
}

function getGastos() {
  if (gastosRequest && gastosRequest.readyState !== 4) {
    gastosRequest.abort();
  }

  if ($.fn.DataTable.isDataTable("#tblGastos")) {
    $("#tblGastos").DataTable().destroy();
  }

  gastosRequest = ajaxLucros(6, null, "html", function (msg) {
    $("#listagemGastos").html(msg);

    if ($.fn.DataTable.isDataTable("#tblGastos")) {
      $("#tblGastos").DataTable().destroy();
    }

    $("#tblGastos").DataTable({
      destroy: true,
      order: [[1, "desc"]],
      columnDefs: [{ orderable: false, targets: 0 }],
      pageLength: 10,
    });
    updateBulkActionsGastos();
  });
}

function getRendimentos() {
  if (rendimentosRequest && rendimentosRequest.readyState !== 4) {
    rendimentosRequest.abort();
  }

  if ($.fn.DataTable.isDataTable("#tblRendimentos")) {
    $("#tblRendimentos").DataTable().destroy();
  }

  rendimentosRequest = ajaxLucros(5, null, "html", function (msg) {
    $("#listagemRendimentos").html(msg);

    if ($.fn.DataTable.isDataTable("#tblRendimentos")) {
      $("#tblRendimentos").DataTable().destroy();
    }

    $("#tblRendimentos").DataTable({
      destroy: true,
      order: [[1, "desc"]],
      columnDefs: [{ orderable: false, targets: 0 }],
      pageLength: 10,
    });
    updateBulkActionsRendimentos();
  });
}

function registaGastos() {
  const descricao = $("#descricaoGasto").val().trim();
  const valorRaw = $("#valorGasto").val().toString().replace(",", ".");
  const data = $("#dataGasto").val();
  const valor = parseFloat(valorRaw);

  if (!descricao || !data || Number.isNaN(valor)) {
    alerta(
      "Aten��o",
      "Preencha descri��o, valor e data corretamente.",
      "warning",
    );
    return;
  }

  const isEdit = !!window.editandoGastoId;
  const op = isEdit ? 16 : 10;
  const payload = isEdit
    ? { id: window.editandoGastoId, descricao, valor, data }
    : { descricao, valor, data };

  ajaxLucros(
    op,
    payload,
    "json",
    function (resp) {
      if (resp.flag) {
        alerta("Sucesso", resp.msg, "success");
        closeModalGasto();
        getGastos();
        getTransacoes();
        getCards();
        getCardsDespesas();
        getCardsLucro();
        getCardsMargem();
      } else {
        alerta(
          "Erro",
          resp.msg || "N�o foi poss�vel guardar o gasto.",
          "error",
        );
      }
    },
    function (jqXHR) {
      const detalhe = (jqXHR && jqXHR.responseText)
        ? `\n${jqXHR.responseText}`
        : "";
      alerta("Erro", `Falha ao comunicar com o servidor.${detalhe}`, "error");
    },
  );
}

function registaRendimentos() {
  const descricao = $("#descricaoRendimento").val().trim();
  const valorRaw = $("#valorRendimento").val().toString().replace(",", ".");
  const data = $("#dataRendimento").val();
  const valor = parseFloat(valorRaw);

  if (!descricao || !data || Number.isNaN(valor)) {
    alerta(
      "Aten��o",
      "Preencha descri��o, valor e data corretamente.",
      "warning",
    );
    return;
  }

  const isEdit = !!window.editandoRendimentoId;
  const op = isEdit ? 17 : 9;
  const payload = isEdit
    ? { id: window.editandoRendimentoId, descricao, valor, data }
    : { descricao, valor, data };

  ajaxLucros(
    op,
    payload,
    "json",
    function (resp) {
      if (resp.flag) {
        alerta("Sucesso", resp.msg, "success");
        closeModalRendimento();
        getRendimentos();
        getTransacoes();
        getCards();
        getCardsDespesas();
        getCardsLucro();
        getCardsMargem();
      } else {
        alerta(
          "Erro",
          resp.msg || "N�o foi poss�vel guardar o rendimento.",
          "error",
        );
      }
    },
    function (jqXHR) {
      const detalhe = (jqXHR && jqXHR.responseText)
        ? `\n${jqXHR.responseText}`
        : "";
      alerta("Erro", `Falha ao comunicar com o servidor.${detalhe}`, "error");
    },
  );
}

function openModalGasto() {
  
  document.querySelector("#modalGasto .modal-header-success h2").innerHTML =
    '<i class="fas fa-wallet"></i> Adicionar Gasto';
  document.querySelector("#modalGasto .btn-submit").innerHTML =
    '<i class="fas fa-plus-circle"></i> Registar Gasto';
  window.editandoGastoId = null;
  if (!document.getElementById("dataGasto").value) {
    document.getElementById("dataGasto").value = formatNowForDateTimeLocal();
  }
  document.getElementById("modalGasto").classList.remove("hidden");
}

function closeModalGasto() {
  document.getElementById("modalGasto").classList.add("hidden");
  
  document.getElementById("descricaoGasto").value = "";
  document.getElementById("valorGasto").value = "";
  document.getElementById("dataGasto").value = "";
  window.editandoGastoId = null;
}

function openModalRendimento() {
  
  document.querySelector(
    "#modalRendimento .modal-header-success h2",
  ).innerHTML = '<i class="fas fa-hand-holding-usd"></i> Adicionar Rendimento';
  document.querySelector("#modalRendimento .btn-submit").innerHTML =
    '<i class="fas fa-plus-circle"></i> Registar Rendimento';
  window.editandoRendimentoId = null;
  if (!document.getElementById("dataRendimento").value) {
    document.getElementById("dataRendimento").value =
      formatNowForDateTimeLocal();
  }
  document.getElementById("modalRendimento").classList.remove("hidden");
}

function closeModalRendimento() {
  document.getElementById("modalRendimento").classList.add("hidden");
  
  document.getElementById("descricaoRendimento").value = "";
  document.getElementById("valorRendimento").value = "";
  document.getElementById("dataRendimento").value = "";
  window.editandoRendimentoId = null;
}

window.onclick = function (event) {
  const modalGasto = document.getElementById("modalGasto");
  const modalRendimento = document.getElementById("modalRendimento");

  if (event.target == modalGasto) {
    closeModalGasto();
  }
  if (event.target == modalRendimento) {
    closeModalRendimento();
  }
};

$(document).on("change", "#selectAllGastos", function () {
  const isChecked = $(this).prop("checked");
  $('#tblGastos tbody input[type="checkbox"]').prop("checked", isChecked);
  updateBulkActionsGastos();
});

$(document).on(
  "change",
  '#tblGastos tbody input[type="checkbox"]',
  function () {
    updateBulkActionsGastos();
  },
);

function updateBulkActionsGastos() {
  const checkedCount = $(
    '#tblGastos tbody input[type="checkbox"]:checked',
  ).length;
  const totalCount = $('#tblGastos tbody input[type="checkbox"]').length;

  $("#selectAllGastos").prop(
    "checked",
    checkedCount === totalCount && totalCount > 0,
  );
  $("#selectedCountGastos").text(checkedCount + " selecionados");

  if (checkedCount > 0) {
    $("#bulkActionsGastos").slideDown(200);
  } else {
    $("#bulkActionsGastos").slideUp(200);
  }
}

function editarSelecionadoGastos() {
  const selected = [];
  $('#tblGastos tbody input[type="checkbox"]:checked').each(function () {
    const row = $(this).closest("tr");
    selected.push({
      id: row.find("td:eq(1)").text(),
      descricao: row.find("td:eq(2)").text(),
      valor: row.find("td:eq(3)").text().replace("�", ""),
      data: row.find("td:eq(4)").text(),
    });
  });

  if (selected.length === 0) {
    alerta("Aten��o", "Nenhum gasto selecionado", "warning");
    return;
  }

  if (selected.length > 1) {
    alerta("Aten��o", "Selecione apenas um gasto para editar", "warning");
    return;
  }

  
  const gasto = selected[0];
  $("#descricaoGasto").val(gasto.descricao);
  $("#valorGasto").val(gasto.valor);
  $("#dataGasto").val(formatDbDateForDateTimeLocal(gasto.data));

  
  window.editandoGastoId = gasto.id;

  
  document.querySelector("#modalGasto .modal-header-success h2").innerHTML =
    '<i class="fas fa-edit"></i> Editar Gasto';
  document.querySelector("#modalGasto .btn-submit").innerHTML =
    '<i class="fas fa-save"></i> Atualizar Gasto';

  $("#modalGasto").removeClass("hidden");
}

function removerEmMassaGastos() {
  const ids = [];
  $('#tblGastos tbody input[type="checkbox"]:checked').each(function () {
    ids.push($(this).closest("tr").find("td:eq(1)").text());
  });

  if (ids.length === 0) {
    showModernWarningModal(
      "Aten��o",
      "Selecione pelo menos um gasto para remover.",
    );
    return;
  }

  showModernConfirmModal(
    `Remover ${ids.length} gasto${ids.length > 1 ? "s" : ""}?`,
    "Esta a��o n�o pode ser desfeita!",
    {
      confirmText: '<i class="fas fa-check"></i> Sim, remover',
      icon: "fa-trash-alt",
      iconBg:
        "background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%); box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);",
    },
  ).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "src/controller/controllerGestaoLucros.php",
        method: "POST",
        data: { op: 14, ids: ids },
        traditional: true,
        dataType: "json",
        success: function (response) {
          if (response.flag) {
            showModernSuccessModal("Sucesso!", response.msg);
            getGastos();
            getCards();
            getCardsDespesas();
            getCardsLucro();
            getCardsMargem();
            $("#selectAllGastos").prop("checked", false);
            updateBulkActionsGastos();
          } else {
            showModernErrorModal("Erro", response.msg);
          }
        },
        error: function (xhr, status, error) {
          showModernErrorModal("Erro", "N�o foi poss�vel remover os gastos");
        },
      });
    }
  });
}

$(document).on("change", "#selectAllRendimentos", function () {
  const isChecked = $(this).prop("checked");
  $('#tblRendimentos tbody input[type="checkbox"]').prop("checked", isChecked);
  updateBulkActionsRendimentos();
});

$(document).on(
  "change",
  '#tblRendimentos tbody input[type="checkbox"]',
  function () {
    updateBulkActionsRendimentos();
  },
);

function updateBulkActionsRendimentos() {
  const checkedCount = $(
    '#tblRendimentos tbody input[type="checkbox"]:checked',
  ).length;
  const totalCount = $('#tblRendimentos tbody input[type="checkbox"]').length;

  $("#selectAllRendimentos").prop(
    "checked",
    checkedCount === totalCount && totalCount > 0,
  );
  $("#selectedCountRendimentos").text(checkedCount + " selecionados");

  if (checkedCount > 0) {
    $("#bulkActionsRendimentos").slideDown(200);
  } else {
    $("#bulkActionsRendimentos").slideUp(200);
  }
}

function editarSelecionadoRendimentos() {
  const selected = [];
  $('#tblRendimentos tbody input[type="checkbox"]:checked').each(function () {
    const row = $(this).closest("tr");
    selected.push({
      id: row.find("td:eq(1)").text(),
      descricao: row.find("td:eq(2)").text(),
      valor: row.find("td:eq(3)").text().replace("�", ""),
      data: row.find("td:eq(4)").text(),
    });
  });

  if (selected.length === 0) {
    alerta("Aten��o", "Nenhum rendimento selecionado", "warning");
    return;
  }

  if (selected.length > 1) {
    alerta("Aten��o", "Selecione apenas um rendimento para editar", "warning");
    return;
  }

  
  const rendimento = selected[0];
  $("#descricaoRendimento").val(rendimento.descricao);
  $("#valorRendimento").val(rendimento.valor);
  $("#dataRendimento").val(formatDbDateForDateTimeLocal(rendimento.data));

  
  window.editandoRendimentoId = rendimento.id;

  
  document.querySelector(
    "#modalRendimento .modal-header-success h2",
  ).innerHTML = '<i class="fas fa-edit"></i> Editar Rendimento';
  document.querySelector("#modalRendimento .btn-submit").innerHTML =
    '<i class="fas fa-save"></i> Atualizar Rendimento';

  $("#modalRendimento").removeClass("hidden");
}

function removerEmMassaRendimentos() {
  const ids = [];
  $('#tblRendimentos tbody input[type="checkbox"]:checked').each(function () {
    ids.push($(this).closest("tr").find("td:eq(1)").text());
  });

  if (ids.length === 0) {
    showModernWarningModal(
      "Aten��o",
      "Selecione pelo menos um rendimento para remover.",
    );
    return;
  }

  showModernConfirmModal(
    `Remover ${ids.length} rendimento${ids.length > 1 ? "s" : ""}?`,
    "Esta a��o n�o pode ser desfeita!",
    {
      confirmText: '<i class="fas fa-check"></i> Sim, remover',
      icon: "fa-trash-alt",
      iconBg:
        "background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%); box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);",
    },
  ).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "src/controller/controllerGestaoLucros.php",
        method: "POST",
        data: { op: 15, ids: ids },
        traditional: true,
        dataType: "json",
        success: function (response) {
          if (response.flag) {
            showModernSuccessModal("Sucesso!", response.msg);
            getRendimentos();
            getCards();
            getCardsDespesas();
            getCardsLucro();
            getCardsMargem();
            $("#selectAllRendimentos").prop("checked", false);
            updateBulkActionsRendimentos();
          } else {
            showModernErrorModal("Erro", response.msg);
          }
        },
        error: function (xhr, status, error) {
          showModernErrorModal(
            "Erro",
            "N�o foi poss�vel remover os rendimentos",
          );
        },
      });
    }
  });
}

$(function () {
  getCards();
  getCardsDespesas();
  getCardsLucro();
  getCardsMargem();
  getTransacoes();
  getGastos();
  getRendimentos();
});
