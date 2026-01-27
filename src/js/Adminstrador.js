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
// User dropdown toggle
function initializeDropdownEvents() {
  const userMenuBtn = document.getElementById("userMenuBtn");
  const userDropdown = document.getElementById("userDropdown");

  if (userMenuBtn) {
    userMenuBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      userDropdown.classList.toggle("active");
    });
  }

  // Fecha dropdown ao clicar fora
  document.addEventListener("click", function (e) {
    if (!userMenuBtn?.contains(e.target) && !userDropdown?.contains(e.target)) {
      userDropdown?.classList.remove("active");
    }
  });
}

function logout() {
  Swal.fire({
    html: `
      <div style="padding: 20px 0;">
        <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
          <i class="fas fa-sign-out-alt" style="font-size: 32px; color: #dc2626;"></i>
        </div>
        <h2 style="margin: 0 0 12px 0; color: #1e293b; font-size: 24px; font-weight: 700;">Terminar Sessao?</h2>
        <p style="margin: 0; color: #64748b; font-size: 15px; line-height: 1.6;">Tem a certeza que pretende sair da sua conta?</p>
      </div>
    `,
    showCancelButton: true,
    confirmButtonText: '<i class="fas fa-sign-out-alt"></i> Sim, sair',
    cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
    confirmButtonColor: "#dc2626",
    cancelButtonColor: "#64748b",
    customClass: {
      confirmButton: "swal2-confirm-modern",
      cancelButton: "swal2-cancel-modern",
      popup: "swal2-logout-popup",
    },
    buttonsStyling: false,
    reverseButtons: true,
    didOpen: () => {
      const popup = Swal.getPopup();
      popup.style.borderRadius = "16px";
      popup.style.padding = "25px";

      const confirmBtn = popup.querySelector(".swal2-confirm");
      const cancelBtn = popup.querySelector(".swal2-cancel");

      if (confirmBtn) {
        confirmBtn.style.padding = "12px 28px";
        confirmBtn.style.borderRadius = "10px";
        confirmBtn.style.fontSize = "15px";
        confirmBtn.style.fontWeight = "600";
        confirmBtn.style.border = "none";
        confirmBtn.style.cursor = "pointer";
        confirmBtn.style.transition = "all 0.3s ease";
        confirmBtn.style.backgroundColor = "#dc2626";
        confirmBtn.style.color = "#ffffff";
        confirmBtn.style.marginLeft = "10px";
      }

      if (cancelBtn) {
        cancelBtn.style.padding = "12px 28px";
        cancelBtn.style.borderRadius = "10px";
        cancelBtn.style.fontSize = "15px";
        cancelBtn.style.fontWeight = "600";
        cancelBtn.style.border = "2px solid #e2e8f0";
        cancelBtn.style.cursor = "pointer";
        cancelBtn.style.transition = "all 0.3s ease";
        cancelBtn.style.backgroundColor = "#ffffff";
        cancelBtn.style.color = "#64748b";
      }
    },
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        html: `
          <div style="padding: 20px;">
            <div class="swal2-loading-spinner" style="margin: 0 auto 20px;">
              <div style="width: 50px; height: 50px; border: 4px solid #f3f4f6; border-top: 4px solid #3cb371; border-radius: 50%; animation: spin 1s linear infinite;"></div>
            </div>
            <p style="margin: 0; color: #64748b; font-size: 15px;">A terminar sessao...</p>
          </div>
          <style>
            @keyframes spin {
              0% { transform: rotate(0deg); }
              100% { transform: rotate(360deg); }
            }
          </style>
        `,
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
          const popup = Swal.getPopup();
          popup.style.borderRadius = "16px";
        },
      });

      let dados = new FormData();
      dados.append("op", 10);

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
          window.location.href = "index.html";
        })
        .fail(function (jqXHR, textStatus) {
          alert("Request failed: " + textStatus);
        });
    }
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
function getAdminPerfil() {
  let dados = new FormData();
  dados.append("op", 21);

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
      console.log(msg);
      $("#AdminPerfilInfo").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function getVendasGrafico() {
  $.ajax({
    url: "src/controller/controllerDashboardAdmin.php",
    type: "POST",
    data: { op: 5 },
    dataType: "json",
    success: function (response) {
      console.log("Resposta AJAX:", response);
      const ctx3 = document.getElementById("salesChart").getContext("2d");

      // Gradientes para as áreas preenchidas
      const gradientRendimentos = ctx3.createLinearGradient(0, 0, 0, 400);
      gradientRendimentos.addColorStop(0, "rgba(60, 179, 113, 0.5)");
      gradientRendimentos.addColorStop(0.5, "rgba(60, 179, 113, 0.25)");
      gradientRendimentos.addColorStop(1, "rgba(60, 179, 113, 0.05)");

      const gradientGastos = ctx3.createLinearGradient(0, 0, 0, 400);
      gradientGastos.addColorStop(0, "rgba(45, 45, 45, 0.5)");
      gradientGastos.addColorStop(0.5, "rgba(45, 45, 45, 0.25)");
      gradientGastos.addColorStop(1, "rgba(45, 45, 45, 0.05)");

      chartVendas = new Chart(ctx3, {
        type: "line",
        data: {
          labels: response.dados1,
          datasets: [
            {
              label: "Rendimentos",
              data: response.dados2,
              tension: 0.4,
              fill: true,
              backgroundColor: gradientRendimentos,
              borderColor: "rgba(60, 179, 113, 1)",
              borderWidth: 3,
              pointBackgroundColor: "rgba(60, 179, 113, 1)",
              pointBorderColor: "#fff",
              pointBorderWidth: 2,
              pointRadius: 5,
              pointHoverRadius: 7,
              pointHoverBackgroundColor: "rgba(60, 179, 113, 1)",
              pointHoverBorderColor: "#fff",
              pointHoverBorderWidth: 3,
            },
            {
              label: "Gastos",
              data: response.dados3,
              tension: 0.4,
              fill: true,
              backgroundColor: gradientGastos,
              borderColor: "rgba(45, 45, 45, 1)",
              borderWidth: 3,
              pointBackgroundColor: "rgba(45, 45, 45, 1)",
              pointBorderColor: "#fff",
              pointBorderWidth: 2,
              pointRadius: 5,
              pointHoverRadius: 7,
              pointHoverBackgroundColor: "rgba(45, 45, 45, 1)",
              pointHoverBorderColor: "#fff",
              pointHoverBorderWidth: 3,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          interaction: {
            intersect: false,
            mode: "index",
          },
          plugins: {
            legend: {
              display: true,
              position: "top",
              align: "end",
              labels: {
                color: "#888",
                font: {
                  size: 13,
                  family: "'Inter', 'Segoe UI', sans-serif",
                  weight: "500",
                },
                padding: 15,
                usePointStyle: true,
                pointStyle: "circle",
              },
            },
            tooltip: {
              enabled: true,
              backgroundColor: "rgba(26, 26, 26, 0.95)",
              titleColor: "#fff",
              bodyColor: "#fff",
              borderColor: "rgba(255, 255, 255, 0.1)",
              borderWidth: 1,
              padding: 12,
              displayColors: true,
              titleFont: {
                size: 14,
                weight: "bold",
              },
              bodyFont: {
                size: 13,
              },
              callbacks: {
                label: function (context) {
                  let label = context.dataset.label || "";
                  if (label) {
                    label += ": ";
                  }
                  if (context.parsed.y !== null) {
                    label += new Intl.NumberFormat("pt-PT", {
                      style: "currency",
                      currency: "EUR",
                    }).format(context.parsed.y);
                  }
                  return label;
                },
              },
            },
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                color: "#888",
                font: {
                  size: 12,
                  family: "'Inter', 'Segoe UI', sans-serif",
                },
                padding: 10,
                callback: function (value) {
                  return new Intl.NumberFormat("pt-PT", {
                    style: "currency",
                    currency: "EUR",
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0,
                  }).format(value);
                },
              },
              grid: {
                color: "rgba(255, 255, 255, 0.06)",
                drawBorder: false,
                lineWidth: 1,
              },
              border: {
                display: false,
              },
            },
            x: {
              ticks: {
                color: "#888",
                font: {
                  size: 12,
                  family: "'Inter', 'Segoe UI', sans-serif",
                },
                padding: 10,
              },
              grid: {
                color: "rgba(255, 255, 255, 0.03)",
                drawBorder: false,
              },
              border: {
                display: false,
              },
            },
          },
        },
      });
    },
    error: function (xhr, status, error) {
      console.error("Erro AJAX:", error);
      console.error("Resposta do servidor:", xhr.responseText);
    },
  });
}
function getTopTipoGrafico() {
  $.ajax({
    url: "src/controller/controllerDashboardAdmin.php",
    type: "POST",
    data: { op: 6 },
    dataType: "json",
    success: function (response) {
      console.log("Resposta AJAX:", response);

      const ctx3 = document.getElementById("topProductsChart").getContext("2d");

      new Chart(ctx3, {
        type: "doughnut",
        data: {
          labels: response.dados1,
          datasets: [
            {
              data: response.dados2,
              backgroundColor: [
                "#3cb371", // Verde principal
                "#2d3748", // Cinza escuro
                "#2e8b57", // Verde escuro
                "#1a202c", // Preto suave
                "#90c896", // Verde claro
                "#4a5568", // Cinza médio
              ],
              borderColor: "#ffffff",
              borderWidth: 4,
              hoverOffset: 15,
              hoverBorderColor: "#ffffff",
              hoverBorderWidth: 5,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          cutout: "65%",
          plugins: {
            legend: {
              position: "right",
              labels: {
                color: "#64748b",
                padding: 15,
                font: {
                  size: 13,
                  family: "'Inter', 'Segoe UI', sans-serif",
                  weight: "500",
                },
                usePointStyle: true,
                pointStyle: "circle",
              },
            },
            tooltip: {
              enabled: true,
              backgroundColor: "rgba(26, 26, 26, 0.95)",
              titleColor: "#fff",
              bodyColor: "#fff",
              borderColor: "rgba(255, 255, 255, 0.1)",
              borderWidth: 1,
              padding: 12,
              titleFont: {
                size: 14,
                weight: "bold",
              },
              bodyFont: {
                size: 13,
              },
              callbacks: {
                label: function (context) {
                  let label = context.label || "";
                  if (label) {
                    label += ": ";
                  }
                  if (context.parsed !== null) {
                    label += context.parsed + " planos";
                  }
                  return label;
                },
              },
            },
          },
        },
      });
    },
    error: function (xhr, status, error) {
      console.error("Erro AJAX:", error);
      console.error("Resposta do servidor:", xhr.responseText);
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
function getProdutosInvativo() {
  if ($.fn.DataTable.isDataTable("#ProdutosInativosBody")) {
    $("#ProdutosInativosBody").DataTable().destroy();
  }

  let dados = new FormData();
  dados.append("op", 8);

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
      $("#ProdutosInativosBody").html(msg);
      $(".ProdutosInativosTable").DataTable();
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}

function getInfoUserDropdown() {
  let dados = new FormData();
  dados.append("op", 9);

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
      $("#userDropdown").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      console.error("Erro ao carregar dropdown: " + textStatus);
    });
}

$(function () {
  getAdminPerfil();
  getTopTipoGrafico();
  getDadosPerfil();
  getVendasGrafico();
  getRendimentos();
  getGastos();
  getUtilizadores();
  getDadosPlanos();
  getProdutosInvativo();
  getInfoUserDropdown();
  initializeDropdownEvents();
});
