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
  const nome = String($("#clientNome").val() || "").trim();
  const email = String($("#clientEmail").val() || "").trim();
  const telefone = String($("#clientTelefone").val() || "").trim();
  const tipo = String($("#clientTipo").val() || "").trim();
  const nif = String($("#clientNif").val() || "").trim();
  const morada = String($("#clientMorada").val() || "").trim();
  const password = String($("#clientPassword").val() || "");
  const passwordConfirm = String($("#clientPasswordConfirm").val() || "");
  const foto = $("#imagemClient").prop("files")[0] || null;

  if (!nome || !email || !tipo || !password || !passwordConfirm) {
    alerta(
      "Utilizador",
      "Preencha os campos obrigatórios: nome, email, tipo de utilizador, senha e confirmação.",
      "warning",
    );
    return;
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    alerta("Utilizador", "Introduza um email válido.", "warning");
    return;
  }

  if (password.length < 6) {
    alerta(
      "Utilizador",
      "A senha deve ter pelo menos 6 caracteres.",
      "warning",
    );
    return;
  }

  if (password !== passwordConfirm) {
    alerta("Utilizador", "As senhas não coincidem.", "warning");
    return;
  }

  let dados = new FormData();
  dados.append("op", 3);
  dados.append("clientNome", nome);
  dados.append("clientEmail", email);
  dados.append("clientTelefone", telefone);
  dados.append("clientTipo", tipo);
  dados.append("clientNif", nif);
  dados.append("clientMorada", morada);
  dados.append("clientPassword", password);
  if (foto) {
    dados.append("foto", foto);
  }

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
      const tipoNomes = {
        1: "Administrador",
        2: "Cliente",
        3: "Anunciante",
      };
      const tipoNome = tipoNomes[Number(tipo)] || "Utilizador";

      if (resp.flag) {
        closeModal();
        alerta(
          `${tipoNome} criado`,
          resp.msg || `${tipoNome} criado com sucesso.`,
          "success",
        );
        getClientes();
        getCardUtilizadores();
      } else {
        alerta(
          `Erro ao criar ${tipoNome.toLowerCase()}`,
          resp.msg ||
            resp.message ||
            `Não foi possível criar ${tipoNome.toLowerCase()}.`,
          "error",
        );
      }
    })

    .fail(function (jqXHR, textStatus) {
      const tipoNomes = {
        1: "Administrador",
        2: "Cliente",
        3: "Anunciante",
      };
      const tipoNome = tipoNomes[Number(tipo)] || "Utilizador";
      let mensagemErro = `Erro ao criar ${tipoNome.toLowerCase()}.`;

      try {
        const response = JSON.parse(jqXHR.responseText || "{}");
        mensagemErro = response.msg || response.message || mensagemErro;
      } catch (e) {
        if (textStatus === "parsererror") {
          mensagemErro =
            "Erro ao processar resposta do servidor. Verifique os dados do formulário e tente novamente.";
        }
      }

      alerta(`Erro ao criar ${tipoNome.toLowerCase()}`, mensagemErro, "error");
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
  const tipoNomes = {
    1: "Administrador",
    2: "Cliente",
    3: "Anunciante",
  };
  const tipoSelecionado = Number($("#viewTipo").val());
  const tipoTitulo = tipoNomes[tipoSelecionado] || "Utilizador";

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
        alerta(tipoTitulo, resp.msg || "Sucesso", "success");
        alerta2(resp.msg || "Sucesso", "success");
        getClientes();
      } else {
        alerta2(resp.msg || "Erro", "error");
        alerta(tipoTitulo, resp.msg || "Erro", "error");
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
