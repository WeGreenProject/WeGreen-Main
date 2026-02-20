function getCardLog() {
  let dados = new FormData();
  dados.append("op", 1);

  $.ajax({
    url: "src/controller/controllerLogAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#CardsLogs").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function getTabelaLog() {
  if ($.fn.DataTable.isDataTable("#LogAdminTable")) {
    $("#LogAdminTable").DataTable().destroy();
  }

  let dados = new FormData();
  dados.append("op", 2);

  $.ajax({
    url: "src/controller/controllerLogAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      $("#LogAdminBody").html(msg);
      $("#LogAdminTable").DataTable({
        language: {
          url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json",
        },
        order: [[0, "desc"]],
        pageLength: 10,
      });
    })
    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
$(function () {
  getTabelaLog();
  getCardLog();
});

function logout() {
  showModernConfirmModal(
    "Terminar Sessão",
    "Tem a certeza que deseja sair da plataforma?",
    {
      confirmText: '<i class="fas fa-check"></i> Sim, sair',
      icon: "fa-sign-out-alt",
      iconBg:
        "background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%); box-shadow: 0 8px 20px rgba(60, 179, 113, 0.3);",
    },
  ).then((result) => {
    if (result.isConfirmed) {
      let dados = new FormData();
      dados.append("op", 10);

      $.ajax({
        url: "src/controller/controllerDashboardAdmin.php",
        method: "POST",
        data: dados,
        dataType: "json",
        cache: false,
        contentType: false,
        processData: false,
      })
        .done(function (response) {
          if (response.success) {
            window.location.href = "index.html";
          }
        })
        .fail(function () {
          window.location.href = "index.html";
        });
    }
  });
}
