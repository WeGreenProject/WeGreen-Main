function getDadosPlanos() {
  let dados = new FormData();
  dados.append("op", 1);

  $.ajax({
    url: "src/controller/controllerDashboardAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#PlanosAtivos").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function getUtilizadores() {
  let dados = new FormData();
  dados.append("op", 2);

  $.ajax({
    url: "src/controller/controllerDashboardAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#UtilizadoresCard").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function getRendimentos() {
  let dados = new FormData();
  dados.append("op", 3);

  $.ajax({
    url: "src/controller/controllerDashboardAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#RendimentosCard").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function getGastos() {
  let dados = new FormData();
  dados.append("op", 4);

  $.ajax({
    url: "src/controller/controllerDashboardAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#GastosCard").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}

function getVendasGrafico() {
  var canvas = document.getElementById("salesChart");
  if (!canvas) return;
  $.ajax({
    url: "src/controller/controllerDashboardAdmin.php",
    type: "POST",
    data: { op: 5 },
    dataType: "json",
    success: function (response) {
      const ctx3 = canvas.getContext("2d");

      if (typeof salesChartInstance !== "undefined" && salesChartInstance) {
        salesChartInstance.destroy();
      }

      chartVendas = new Chart(ctx3, {
        type: "line",
        data: {
          labels: response.dados1,
          datasets: [
            {
              label: "Vendas (€)",
              data: response.dados2,
              borderColor: "#2e8b57",
              backgroundColor: "rgba(60, 179, 113, 0.14)",
              borderWidth: 3,
              tension: 0.4,
              fill: true,
              pointRadius: 4,
              pointHoverRadius: 6,
              pointBackgroundColor: "#3cb371",
              pointBorderColor: "#ffffff",
              pointBorderWidth: 2,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false,
            },
            tooltip: {
              backgroundColor: "rgba(17, 17, 17, 0.94)",
              titleColor: "#ffffff",
              bodyColor: "#ffffff",
              borderColor: "rgba(60, 179, 113, 0.55)",
              borderWidth: 1,
            },
          },
          scales: {
            y: {
              ticks: {
                color: "#111111",
                font: {
                  size: 12,
                },
              },
              grid: {
                color: "rgba(17, 17, 17, 0.14)",
                drawBorder: false,
              },
            },
            x: {
              ticks: {
                color: "#111111",
                font: {
                  size: 12,
                },
              },
              grid: {
                color: "rgba(17, 17, 17, 0.1)",
                drawBorder: false,
              },
            },
          },
        },
      });
    },
    error: function (xhr, status, error) {
    },
  });
}
function getTopTipoGrafico() {
  var canvas = document.getElementById("topProductsChart");
  if (!canvas) return;
  $.ajax({
    url: "src/controller/controllerDashboardAdmin.php",
    type: "POST",
    data: { op: 6 },
    dataType: "json",
    success: function (response) {
      const ctx3 = canvas.getContext("2d");

      if (
        typeof topProductsChartInstance !== "undefined" &&
        topProductsChartInstance
      ) {
        topProductsChartInstance.destroy();
      }

      chartVendas = new Chart(ctx3, {
        type: "doughnut",
        data: {
          labels: response.dados1,
          datasets: [
            {
              data: response.dados2,
              backgroundColor: ["#3cb371", "#111111", "#ffffff", "#2e8b57", "#d1fae5"],
              borderColor: "#111111",
              borderWidth: 3,
              hoverOffset: 8,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: "52%",
          plugins: {
            legend: {
              position: "bottom",
              labels: {
                color: "#111111",
                padding: 14,
                boxWidth: 18,
                boxHeight: 10,
                font: {
                  size: 12,
                  weight: "600",
                },
              },
            },
            tooltip: {
              backgroundColor: "rgba(17, 17, 17, 0.94)",
              titleColor: "#ffffff",
              bodyColor: "#ffffff",
              borderColor: "rgba(60, 179, 113, 0.55)",
              borderWidth: 1,
            },
          },
        },
      });
    },
    error: function (xhr, status, error) {
    },
  });
}
function getDadosPerfil() {
  let dados = new FormData();
  dados.append("op", 7);

  $.ajax({
    url: "src/controller/controllerDashboardAdmin.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#ProfileUser").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
$(function () {
  getDadosPerfil();
  getTopTipoGrafico();
  getVendasGrafico();
  getRendimentos();
  getGastos();
  getUtilizadores();
  getDadosPlanos();
});
