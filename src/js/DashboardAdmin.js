function getProdutosParaVerificar() {
  const tableSelector = "#ProdutosInativosTable";
  const bodySelector = "#ProdutosInativosBody";

  $.ajax({
    url: "src/controller/controllerDashboardAdmin.php",
    method: "POST",
    data: { op: 8 },
    dataType: "html",
    success: function (html) {
      $(bodySelector).html(html || "");

      if ($.fn.DataTable && $.fn.DataTable.isDataTable(tableSelector)) {
        $(tableSelector).DataTable().destroy();
      }

      if ($.fn.DataTable) {
        $(tableSelector).DataTable({
          pageLength: 5,
          lengthChange: false,
          order: [[0, "desc"]],
          language: {
            emptyTable: "Sem produtos por verificar",
            info: "A mostrar _START_ a _END_ de _TOTAL_ registos",
            infoEmpty: "A mostrar 0 a 0 de 0 registos",
            infoFiltered: "(filtrado de _MAX_ registos)",
            search: "Pesquisar:",
            paginate: {
              first: "Primeiro",
              last: "Último",
              next: "Seguinte",
              previous: "Anterior",
            },
          },
        });
      }
    },
    error: function () {
      $(bodySelector).html(
        "<tr><td colspan='6' style='text-align:center;color:#ef4444;'>Erro ao carregar produtos por verificar</td></tr>",
      );
    },
  });
}

function getInfoUserDropdown() {
  const $btn = $("#userMenuBtn");
  const $dropdown = $("#userDropdown");

  if (!$btn.length || !$dropdown.length) return;

  $btn.off("click.dashboardAdmin").on("click.dashboardAdmin", function (e) {
    e.stopPropagation();
    $dropdown.toggleClass("active");
  });

  $(document)
    .off("click.dashboardAdmin")
    .on("click.dashboardAdmin", function (e) {
      if (!$(e.target).closest(".navbar-user, .user-dropdown").length) {
        $dropdown.removeClass("active");
      }
    });

  $dropdown
    .off("click.dashboardAdmin")
    .on("click.dashboardAdmin", function (e) {
      e.stopPropagation();
    });
}

function logout() {
  showModernConfirmModal(
    "Terminar Sessão?",
    "Tem a certeza que pretende sair?",
    {
      confirmText: '<i class="fas fa-check"></i> Sim, sair',
      icon: "fa-sign-out-alt",
      iconBg:
        "background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%); box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);",
    },
  ).then((result) => {
    if (!result.isConfirmed) return;

    $.ajax({
      url: "src/controller/controllerDashboardAdmin.php",
      method: "POST",
      dataType: "json",
      data: { op: 10 },
    }).always(function () {
      window.location.href = "index.html";
    });
  });
}

$(document).ready(function () {
  getProdutosParaVerificar();
  getInfoUserDropdown();
});
