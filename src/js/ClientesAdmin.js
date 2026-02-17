function getClientes() {
  if ($.fn.DataTable.isDataTable("#clientsTable")) {
    $("#clientsTable").DataTable().destroy();
  }

  let dados = new FormData();
  dados.append("op", 1);

  $.ajax({
    url: "src/controller/controllerClientesAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#clientsTableBody").html(msg);
      $("#clientsTable").DataTable();
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function showModal() {
  $("#clientModal").addClass("active");
}
function closeModal() {
  $("#clientModal").removeClass("active");
}
function closeModal2() {
  $("#viewModal").removeClass("active");
}
function getCardUtilizadores() {
  let dados = new FormData();
  dados.append("op", 2);

  $.ajax({
    url: "src/controller/controllerClientesAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#CardTipoutilizadores").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function removerClientes(id) {
  showModernConfirmModal(
    "Eliminar utilizador?",
    "Tem a certeza que pretende eliminar este utilizador? Esta ação pode não ser reversível.",
    {
      confirmText: '<i class="fas fa-trash"></i> Sim, eliminar',
      icon: "fa-user-times",
      iconBg:
        "background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%); box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);",
    },
  ).then((result) => {
    if (!result.isConfirmed) return;

    let dados = new FormData();
    dados.append("op", 4);
    dados.append("ID_Cliente", id);

    $.ajax({
      url: "src/controller/controllerClientesAdmin.php",
      method: "POST",
      data: dados,
      dataType: "json",
      cache: false,
      contentType: false,
      processData: false,
    })
      .done(function (resp) {
        if (resp.flag) {
          showModernSuccessModal(
            "Clientes",
            resp.msg || "Utilizador removido com sucesso",
          );
          getClientes();
          getCardUtilizadores();
        } else {
          showModernErrorModal(
            "Clientes",
            resp.msg || "Não foi possível remover o utilizador",
          );
        }
      })
      .fail(function (jqXHR) {
        let mensagemErro = "Erro ao remover utilizador.";
        try {
          const response = JSON.parse(jqXHR.responseText || "{}");
          mensagemErro = response.msg || response.message || mensagemErro;
        } catch (e) {
          if (jqXHR.responseText) {
            mensagemErro = jqXHR.responseText;
          }
        }
        showModernErrorModal("Clientes", mensagemErro);
      });
  });
}
function registaClientes() {
  let dados = new FormData();
  dados.append("op", 3);
  dados.append("clientNome", $("#clientNome").val());
  dados.append("clientEmail", $("#clientEmail").val());
  dados.append("clientTelefone", $("#clientTelefone").val());
  dados.append("clientTipo", $("#clientTipo").val());
  dados.append("clientNif", $("#clientNif").val());
  dados.append("clientPassword", $("#clientPassword").val());
  dados.append("foto", $("#imagemClient").prop("files")[0]);

  $.ajax({
    url: "src/controller/controllerClientesAdmin.php",
    method: "POST",
    data: dados,
    dataType: "json",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (resp) {
      if (resp.flag) {
        closeModal();
        alerta("Utilizador", resp.msg || "Sucesso", "success");
        getClientes();
        getCardUtilizadores();
      } else {
        alerta("Utilizador", resp.msg || "Erro", "error");
      }
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
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
function getDadosCliente(id) {
  let dados = new FormData();
  dados.append("op", 5);
  dados.append("id", id);

  $.ajax({
    url: "src/controller/controllerClientesAdmin.php",
    method: "POST",
    data: dados,
    dataType: "json",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (resp) {
      $("#viewIDedit").val(resp.id || "");
      $("#viewNome").val(resp.nome || "");
      $("#viewEmail").val(resp.email || "");
      $("#viewTelefone").val(resp.telefone || "");
      $("#viewTipo").val(resp.tipo_utilizador_id || "");
      $("#viewNif").val(resp.nif || "");
      $("#viewPlano").val(resp.plano_id || "");
      $("#viewRanking").val(resp.ranking_id || "");
      $("#btnGuardar").attr(
        "onclick",
        "guardaEditCliente(" + (resp.id || 0) + ")",
      );

      $("#viewModal").addClass("active");
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}

function guardaEditCliente(ID_Utilizador) {
  let dados = new FormData();
  dados.append("op", 6);
  dados.append("viewIDedit", $("#viewIDedit").val());
  dados.append("viewNome", $("#viewNome").val());
  dados.append("viewEmail", $("#viewEmail").val());
  dados.append("viewTelefone", $("#viewTelefone").val());
  dados.append("viewTipo", $("#viewTipo").val());
  dados.append("viewNif", $("#viewNif").val());
  dados.append("viewPlano", $("#viewPlano").val());
  dados.append("viewRanking", $("#viewRanking").val());
  dados.append("ID_Utilizador", ID_Utilizador);

  $.ajax({
    url: "src/controller/controllerClientesAdmin.php",
    method: "POST",
    data: dados,
    dataType: "json",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (resp) {
      closeModal2();
      if (resp.flag) {
        alerta("Fornecedor", resp.msg || "Sucesso", "success");
        alerta2(resp.msg || "Sucesso", "success");
        getClientes();
      } else {
        alerta2(resp.msg || "Erro", "error");
        alerta("Fornecedor", resp.msg || "Erro", "error");
      }
    })
    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function alerta2(msg, icon) {
  let customClass = "";
  if (icon === "success") {
    customClass = "toast-success";
  } else if (icon === "error") {
    customClass = "toast-error";
  }
  const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    customClass: {
      popup: "custom-toast",
    },
    didOpen: (toast) => {
      toast.onmouseenter = Swal.stopTimer;
      toast.onmouseleave = Swal.resumeTimer;
    },
  });
  Toast.fire({
    icon: icon,
    title: msg,
  });
}
$(function () {
  getCardUtilizadores();
  getClientes();
});
