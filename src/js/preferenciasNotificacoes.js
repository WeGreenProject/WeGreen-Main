$(document).ready(function () {
  
  carregarPreferencias();

  
  $("#btnSalvar").click(function () {
    salvarPreferencias();
  });

  
  $("#btnAtivarTodas").click(function () {
    showModernConfirmModal(
      "Ativar todas as notificações?",
      "Receberá todos os tipos de notificações por email",
      {
        confirmText: '<i class="fas fa-bell"></i> Sim, ativar todas',
        icon: "fa-bell",
        iconBg:
          "background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);",
      },
    ).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "src/controller/controllerNotificacoes.php?op=ativarTodas",
          type: "POST",
          success: function (response) {
            const data = JSON.parse(response);
            if (data.success) {
              showModernSuccessModal("Sucesso!", data.message);
              carregarPreferencias();
            } else {
              showModernErrorModal("Erro!", data.message);
            }
          },
        });
      }
    });
  });

  
  $("#btnDesativarTodas").click(function () {
    showModernConfirmModal(
      "Desativar todas as notificações?",
      "Não receberá mais emails sobre encomendas",
      {
        confirmText: '<i class="fas fa-bell-slash"></i> Sim, desativar todas',
        icon: "fa-bell-slash",
        iconBg:
          "background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);",
      },
    ).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "src/controller/controllerNotificacoes.php?op=desativarTodas",
          type: "POST",
          success: function (response) {
            const data = JSON.parse(response);
            if (data.success) {
              showModernSuccessModal("Sucesso!", data.message);
              carregarPreferencias();
            } else {
              showModernErrorModal("Erro!", data.message);
            }
          },
        });
      }
    });
  });

  function carregarPreferencias() {
    $.ajax({
      url: "src/controller/controllerNotificacoes.php?op=getPreferencias",
      type: "GET",
      success: function (response) {
        const data = JSON.parse(response);
        if (data.success) {
          const pref = data.preferencias;

          
          $("#email_confirmacao").prop("checked", pref.email_confirmacao == 1);
          $("#email_processando").prop("checked", pref.email_processando == 1);
          $("#email_enviado").prop("checked", pref.email_enviado == 1);
          $("#email_entregue").prop("checked", pref.email_entregue == 1);
          $("#email_cancelamento").prop(
            "checked",
            pref.email_cancelamento == 1,
          );
          $("#email_novas_encomendas_anunciante").prop(
            "checked",
            pref.email_novas_encomendas_anunciante == 1,
          );
          $("#email_encomendas_urgentes").prop(
            "checked",
            pref.email_encomendas_urgentes == 1,
          );
        }
      },
    });
  }

  function salvarPreferencias() {
    const preferencias = {
      email_confirmacao: $("#email_confirmacao").is(":checked") ? 1 : 0,
      email_processando: $("#email_processando").is(":checked") ? 1 : 0,
      email_enviado: $("#email_enviado").is(":checked") ? 1 : 0,
      email_entregue: $("#email_entregue").is(":checked") ? 1 : 0,
      email_cancelamento: $("#email_cancelamento").is(":checked") ? 1 : 0,
      email_novas_encomendas_anunciante: $(
        "#email_novas_encomendas_anunciante",
      ).is(":checked")
        ? 1
        : 0,
      email_encomendas_urgentes: $("#email_encomendas_urgentes").is(":checked")
        ? 1
        : 0,
      op: "salvarPreferencias",
    };

    $.ajax({
      url: "src/controller/controllerNotificacoes.php",
      type: "POST",
      data: preferencias,
      success: function (response) {
        const data = JSON.parse(response);
        if (data.success) {
          showModernSuccessModal("Preferências Salvas!", data.message, {
            timer: 2000,
          });
        } else {
          showModernErrorModal("Erro!", data.message);
        }
      },
    });
  }
});
