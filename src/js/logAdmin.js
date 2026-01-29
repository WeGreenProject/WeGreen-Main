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
