function parseJsonSafe(payload) {
  if (typeof payload === "string") {
    try {
      return JSON.parse(payload);
    } catch (error) {
      return null;
    }
  }
  return payload;
}

function getDadosPlanos() {
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 1 },
    function (response) {
      const data = parseJsonSafe(response);
      if (!data) return;
      if (data.success) {
        $("#PlanosAtual").html(`
          <div class='stat-icon'><i class='fas fa-crown' style='color: #ffffff;'></i></div>
          <div class='stat-content'><div class='stat-label'>Plano Atual</div><div class='stat-value'>${data.plano}</div></div>
        `);
      } else {
        $("#PlanosAtual").html(`
          <div class='stat-icon'><i class='fas fa-crown' style='color: #ffffff;'></i></div>
          <div class='stat-content'><div class='stat-label'>Plano Atual</div><div class='stat-value'>N/A</div></div>
        `);
      }
    },
  ).fail(function (jqXHR, textStatus) {
  });
}

function CarregaProdutos() {
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 2 },
    function (response) {
      const data = parseJsonSafe(response);
      if (!data) return;
      $("#ProdutoStock").html(`
        <div class='stat-icon'><i class='fas fa-box' style='color: #ffffff;'></i></div>
        <div class='stat-content'><div class='stat-label'>Total Produtos</div><div class='stat-value'>${data.total}</div></div>
      `);
    },
  ).fail(function (jqXHR, textStatus) {
  });
}

function CarregaPontos() {
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 3 },
    function (response) {
      const data = parseJsonSafe(response);
      if (!data) return;
      $("#PontosConfianca").html(`
        <div class='stat-icon'><i class='fas fa-star' style='color: #ffffff;'></i></div>
        <div class='stat-content'><div class='stat-label'>Pontos Confiança</div><div class='stat-value'>${data.pontos}</div></div>
      `);
    },
  ).fail(function (jqXHR, textStatus) {
  });
}
function getGastos() {
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 4 },
    function (response) {
      const data = parseJsonSafe(response);
      if (!data) return;
      const gastos = parseFloat(data.total).toFixed(2);
      $("#GastosCard").html(`
        <div class='stat-icon'><i class='fas fa-wallet' style='color: #ffffff;'></i></div>
        <div class='stat-content'><div class='stat-label'>Gastos Totais</div><div class='stat-value'>€${gastos}</div></div>
      `);
    },
  ).fail(function (jqXHR, textStatus) {
  });
}

function getVendasMensais() {
  $.ajax({
    url: "src/controller/controllerDashboardAnunciante.php",
    type: "POST",
    data: { op: 5 },
    dataType: "json",
    success: function (response) {
      const chartElement = document.getElementById("salesChart");

      const ctx3 = chartElement.getContext("2d");

      if (window.chartVendas) {
        window.chartVendas.destroy();
      }

      window.chartVendas = new Chart(ctx3, {
        type: "line",
        data: {
          labels: response.dados1,
          datasets: [
            {
              label: "Vendas (€)",
              data: response.dados2,
              borderColor: "#3cb371",
              backgroundColor: "rgba(60, 179, 113, 0.1)",
              borderWidth: 3,
              tension: 0.4,
              fill: true,
              pointRadius: 4,
              pointBackgroundColor: "#3cb371",
              pointBorderColor: "#fff",
              pointBorderWidth: 2,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: true,
              labels: {
                color: "#888",
              },
            },
            tooltip: {
              callbacks: {
                label: function (context) {
                  return "Vendas: €" + context.parsed.y.toFixed(2);
                },
              },
            },
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                color: "#888",
                callback: function (value) {
                  return "€" + value.toFixed(2);
                },
              },
              grid: {
                color: "rgba(136, 136, 136, 0.1)",
              },
            },
            x: {
              ticks: {
                color: "#888",
              },
              grid: {
                color: "rgba(136, 136, 136, 0.1)",
              },
            },
          },
        },
      });
    },
    error: function (xhr, status, error) {},
  });
}

function renderTopProductsChart() {
  let dados = new FormData();
  dados.append("op", 6);

  $.ajax({
    url: "src/controller/controllerDashboardAnunciante.php",
    method: "POST",
    data: dados,
    dataType: "json",
    cache: false,
    contentType: false,
    processData: false,
  }).done(function (resp) {
    const ctx = document.getElementById("topProductsChart");
    if (!ctx) {
      return;
    }
    if (window.topProductsChartInstance)
      window.topProductsChartInstance.destroy();

    window.topProductsChartInstance = new Chart(ctx, {
      type: "doughnut",
      data: {
        labels: resp.map((p) => p.nome),
        datasets: [
          {
            data: resp.map((p) => p.vendidos),
            backgroundColor: [
              "#3cb371",
              "#2d2d2d",
              "#64748b",
              "#2e8b57",
              "#1a1a1a",
              "#5fd8a0",
              "#3a3a3a",
              "#45b8ac",
            ],
            borderColor: "#ffffff",
            borderWidth: 3,
            hoverOffset: 10,
            hoverBorderWidth: 4,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "bottom",
            labels: {
              color: "#4a5568",
              padding: 15,
              font: {
                size: 12,
                weight: "500",
              },
              usePointStyle: true,
              pointStyle: "circle",
            },
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                const label = context.label || "";
                const value = context.parsed || 0;
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percentage = ((value / total) * 100).toFixed(1);
                return label + ": " + value + " vendas (" + percentage + "%)";
              },
            },
            backgroundColor: "rgba(0, 0, 0, 0.8)",
            padding: 12,
            titleFont: { size: 14, weight: "bold" },
            bodyFont: { size: 13 },
            cornerRadius: 6,
          },
        },
        cutout: "65%",
      },
    });
  });
}

function renderRecentProducts() {
  $.ajax({
    url: "src/controller/controllerDashboardAnunciante.php",
    method: "POST",
    data: { op: 7 },
    dataType: "json",
  })
    .done(function (produtos) {
      const container = $("#recentProducts");

      if (!produtos || produtos.length === 0) {
        container.html(`
          <div style="text-align: center; padding: 40px; color: #718096;">
            <i class="fas fa-box-open" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
            <p style="font-size: 16px; margin: 10px 0;">Nenhum produto adicionado</p>
            <small>Adicione produtos para vê-los aqui</small>
          </div>
        `);
        return;
      }

      let html = "";

      produtos.forEach((produto) => {
        const foto = produto.foto || "src/img/no-image.png";
        const stock = produto.stock || 0;
        const produtoId = Number(produto.Produto_id || produto.id || 0);

        let statusClass = "status-badge ";
        let statusText = "";
        if (stock > 10) {
          statusClass += "status-entregue";
          statusText = "Em Stock";
        } else if (stock > 0) {
          statusClass += "status-enviado";
          statusText = `Stock Baixo (${stock})`;
        } else {
          statusClass += "status-cancelado";
          statusText = "Sem Stock";
        }

        const preco = parseFloat(produto.preco).toLocaleString("pt-PT", {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        });

        const produtoNome =
          produto.nome.length > 50
            ? produto.nome.substring(0, 50) + "..."
            : produto.nome;

        html += `
          <div class="produto-card-recent">
            <img src="${foto}" alt="${produto.nome}" class="produto-card-img"
                 onclick="visualizarFoto('${foto}', '${produto.nome}')"
                 onerror="this.src='src/img/no-image.png'">
            <div class="produto-card-content">
              <div class="produto-card-header">
                <h4 class="produto-card-title" title="${produto.nome}">${produtoNome}</h4>
                <span class="${statusClass}">${statusText}</span>
              </div>
              <div class="produto-card-meta">
                <span><i class="fas fa-tag"></i> ${produto.tipo_produto || "Produto"}</span>
                <span><i class="fas fa-calendar"></i> ${produto.data_formatada}</span>
                <span><i class="fas fa-box"></i> Stock: ${stock}</span>
              </div>
              <div class="produto-card-footer">
                <div class="produto-card-price">€${preco}</div>
                <button class="btn-edit-produto" onclick="visualizarProduto(${produtoId})">
                  <i class="fas fa-eye"></i> Ver Detalhes
                </button>
              </div>
            </div>
          </div>
        `;
      });

      container.html(html);
    })
    .fail(function (xhr, status, error) {
      $("#recentProducts").html(
        '<div style="color: #E53E3E; text-align: center; padding: 20px;">Erro ao carregar produtos</div>',
      );
    });
}

function visualizarFoto(fotoUrl, nomeProduto) {
  Swal.fire({
    title: nomeProduto,
    html: `
      <div style="max-height: 55vh; display: flex; align-items: center; justify-content: center;">
        <img src="${fotoUrl}"
             alt="${nomeProduto}"
             style="max-width: 100%; max-height: 55vh; object-fit: contain; border-radius: 8px;">
      </div>
    `,
    width: "45%",
    maxWidth: "450px",
    showCloseButton: true,
    showConfirmButton: false,
    background: "#ffffff",
    padding: "0",
    customClass: {
      popup: "photo-gallery-modal",
      title: "photo-gallery-title",
      htmlContainer: "photo-gallery-content",
    },
    didOpen: () => {
      const title = document.querySelector(".photo-gallery-title");
      if (title) {
        title.style.background =
          "linear-gradient(135deg, #3cb371 0%, #2e8b57 100%)";
        title.style.color = "#ffffff";
        title.style.padding = "16px 28px";
        title.style.margin = "0";
        title.style.borderRadius = "16px 16px 0 0";
      }

      const closeBtn = document.querySelector(
        ".photo-gallery-modal .swal2-close",
      );
      if (closeBtn) {
        closeBtn.style.color = "#ffffff";
      }
    },
  });
}

function renderProfitChart() {
  let dados = new FormData();
  dados.append("op", 10);

  $.ajax({
    url: "src/controller/controllerDashboardAnunciante.php",
    method: "POST",
    data: dados,
    dataType: "json",
    cache: false,
    contentType: false,
    processData: false,
  }).done(function (resp) {
    const ctx = document.getElementById("profitChart");
    if (!ctx) {
      return;
    }
    if (window.profitChartInstance) window.profitChartInstance.destroy();

    window.profitChartInstance = new Chart(ctx, {
      type: "polarArea",
      data: {
        labels: resp.map((p) => p.nome),
        datasets: [
          {
            data: resp.map((p) => p.lucro),
            backgroundColor: [
              "#ffd700",
              "#ffed4e",
              "#ffe066",
              "#fff176",
              "#fff59d",
            ],
            borderColor: "#1a1a1a",
            borderWidth: 2,
          },
        ],
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: "bottom",
            labels: { color: "#888", padding: 15 },
          },
        },
      },
    });
  });
}

function renderMarginChart() {
  let dados = new FormData();
  dados.append("op", 11);

  $.ajax({
    url: "src/controller/controllerDashboardAnunciante.php",
    method: "POST",
    data: dados,
    dataType: "json",
    cache: false,
    contentType: false,
    processData: false,
  }).done(function (resp) {
    const ctx = document.getElementById("marginChart");
    if (!ctx) {
      return;
    }
    if (window.marginChartInstance) window.marginChartInstance.destroy();

    window.marginChartInstance = new Chart(ctx, {
      type: "bar",
      data: {
        labels: resp.map((p) => p.nome),
        datasets: [
          {
            label: "Margem (%)",
            data: resp.map((p) => p.margem),
            backgroundColor: "#ffd700",
            borderRadius: 5,
          },
        ],
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          y: {
            ticks: { color: "#888", callback: (v) => v + "%" },
            grid: { color: "#333" },
            beginAtZero: true,
          },
          x: { ticks: { color: "#888" }, grid: { color: "#333" } },
        },
      },
    });
  });
}

function updateDashboard() {
  getDadosPlanos();
  CarregaProdutos();
  CarregaPontos();
  getGastos();
  getVendasMensais();
  renderTopProductsChart();
  renderRecentProducts();
  renderProfitChart();
  renderMarginChart();
}

function carregarTiposProduto() {
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 13 },
    function (data) {
      if (Array.isArray(data)) {
        data.forEach((t) =>
          $("#tipo_produto_id").append(
            `<option value="${t.id}">${t.descricao}</option>`,
          ),
        );
      }
    },
    "json",
  );
}

function loadReportStats() {
  const periodo = $("#reportPeriod").val();
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 19, periodo: periodo },
    function (res) {
      $("#totalRevenue").text("€" + parseFloat(res).toFixed(2));
    },
  );
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 20, periodo: periodo },
    function (res) {
      $("#totalOrders").text(res);
    },
  );
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 21, periodo: periodo },
    function (res) {
      $("#avgTicket").text("€" + parseFloat(res).toFixed(2));
    },
  );
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 22, periodo: periodo },
    function (res) {
      $("#profitMargin").text(parseFloat(res).toFixed(2) + "%");
    },
  );
}

function loadCategorySalesChart() {
  const ctx = document.getElementById("categorySalesChart");
  if (!ctx) {
    return;
  }
  if (
    window.categoryChart &&
    typeof window.categoryChart.destroy === "function"
  )
    window.categoryChart.destroy();
  const periodo = $("#reportPeriod").val();
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 23, periodo: periodo },
    function (data) {
      window.categoryChart = new Chart(ctx, {
        type: "bar",
        data: {
          labels: data.map((d) => d.categoria),
          datasets: [
            {
              label: "Vendas (unidades)",
              data: data.map((d) => d.vendas),
              backgroundColor: "#3cb371",
              borderColor: "#2d3748",
              borderWidth: 1,
            },
          ],
        },
        options: {
          responsive: true,
          plugins: { legend: { display: false } },
        },
      });
    },
    "json",
  );
}

function loadDailyRevenueChart() {
  const ctx = document.getElementById("dailyRevenueChart");
  if (!ctx) {
    return;
  }
  if (
    window.dailyRevenueChart &&
    typeof window.dailyRevenueChart.destroy === "function"
  )
    window.dailyRevenueChart.destroy();
  const periodo = $("#reportPeriod").val();

  if (periodo === "month") {
    $("#revenueChartTitle").text("Receita Diária");
  } else if (periodo === "year") {
    $("#revenueChartTitle").text("Receita Mensal");
    $("#revenueChartSubtitle").text("Evolução da receita nos últimos 12 meses");
  } else {
    $("#revenueChartTitle").text("Receita Mensal");
    $("#revenueChartSubtitle").text("Evolução da receita em todo o período");
  }

  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 24, periodo: periodo },
    function (data) {
      const chartType = periodo === "month" ? "line" : "bar";
      const chartLabel =
        periodo === "month" ? "Receita Diária (€)" : "Receita Mensal (€)";

      const chartConfig = {
        type: chartType,
        data: {
          labels: data.map((d) => d.data),
          datasets: [
            {
              label: chartLabel,
              data: data.map((d) => d.receita),
              borderColor: "#3cb371",
              backgroundColor:
                chartType === "bar" ? "#3cb371" : "rgba(60, 179, 113, 0.15)",
              borderWidth: 2,
              hoverBackgroundColor: "#90c207",
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          plugins: {
            legend: { display: true },
            tooltip: {
              callbacks: {
                label: function (context) {
                  return chartLabel + ": €" + context.parsed.y.toFixed(2);
                },
              },
            },
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function (value) {
                  return "€" + value;
                },
              },
            },
          },
        },
      };

      if (chartType === "line") {
        chartConfig.data.datasets[0].tension = 0.3;
        chartConfig.data.datasets[0].fill = true;
        chartConfig.data.datasets[0].pointRadius = data.length <= 3 ? 6 : 3;
        chartConfig.data.datasets[0].pointHoverRadius =
          data.length <= 3 ? 8 : 5;
        chartConfig.data.datasets[0].pointBackgroundColor = "#2d3748";
        chartConfig.data.datasets[0].pointBorderColor = "#3cb371";
        chartConfig.data.datasets[0].pointBorderWidth = 2;
        chartConfig.data.datasets[0].pointHoverBackgroundColor = "#3cb371";
        chartConfig.data.datasets[0].pointHoverBorderColor = "#2d3748";
      }

      window.dailyRevenueChart = new Chart(ctx, chartConfig);
    },
    "json",
  );
}

function loadReportsTable() {
  const periodo = $("#reportPeriod").val();
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 25, periodo: periodo },
    function (data) {
      $("#reportsTable").DataTable({
        data: data,
        columns: [
          { data: "produto" },
          { data: "vendas" },
          { data: "receita", render: (v) => "€" + parseFloat(v).toFixed(2) },
          { data: "lucro", render: (v) => "€" + parseFloat(v).toFixed(2) },
        ],
        destroy: true,
        pageLength: 10,
        language: {
          url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Portuguese.json",
        },
        drawCallback: function () {
          $("#reportsTable tbody tr")
            .removeClass("even odd")
            .css("background", "#ffffff");
        },
      });
    },
    "json",
  );
}

function showPage(pageId, target) {
  document
    .querySelectorAll(".page")
    .forEach((p) => p.classList.remove("active"));
  document.getElementById(pageId).classList.add("active");

  if (target) {
    document
      .querySelectorAll(".nav-link")
      .forEach((link) => link.classList.remove("active"));
    target.closest(".nav-link").classList.add("active");
  }

  const paginas = {
    dashboard: { titulo: "Dashboard", icone: "fa-chart-line" },
    products: { titulo: "Gestão de Produtos", icone: "fa-tshirt" },
    sales: { titulo: "Encomendas", icone: "fa-shopping-bag" },
    analytics: { titulo: "Relatórios", icone: "fa-chart-bar" },
    profile: { titulo: "Meu Perfil", icone: "fa-user" },
  };
  const pagina = paginas[pageId] || paginas["dashboard"];
  document.getElementById("pageBreadcrumb").innerHTML =
    `<i class="navbar-icon fas ${pagina.icone}" id="pageIcon"></i> ${pagina.titulo}`;
  document.getElementById("pageIcon").className =
    "navbar-icon fas " + pagina.icone;

  if (pageId === "dashboard") {
    updateDashboard();
  }

  if (pageId === "analytics") {
    loadReportStats();
    loadCategorySalesChart();
    loadDailyRevenueChart();
    loadReportsTable();
  }

  if (pageId === "products") {
    carregarProdutos();
  }

  if (pageId === "sales") {
    carregarEncomendas();
  }

  if (pageId === "profile") {
    carregarPerfil();
  }
}

function visualizarProduto(id) {
  const produtoId = Number(id);
  if (!Number.isFinite(produtoId) || produtoId <= 0) {
    return;
  }

  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 15, id: produtoId },
    function (dados) {
      if (dados && dados.Produto_id) {
        const foto = dados.foto ? dados.foto : "src/img/no-image.png";
        const ativo = dados.ativo ? "Sim" : "Não";
        const fotosGaleria = foto.split(",").map((f) => f.trim());
        const galeriaHTML =
          fotosGaleria.length > 0
            ? `
        <div class="modal-gallery">
          <div class="gallery-main">
            <img id="mainImage" src="${fotosGaleria[0]}" alt="${dados.nome}" />
          </div>
          ${
            fotosGaleria.length > 1
              ? `
          <div class="gallery-thumbs">
            ${fotosGaleria
              .map(
                (f, i) =>
                  `<img src="${f}" onclick="document.getElementById('mainImage').src='${f}'" class="${
                    i === 0 ? "active" : ""
                  }" />`,
              )
              .join("")}
          </div>
          `
              : ""
          }
        </div>
      `
            : "";

        Swal.fire({
          title: dados.nome,
          html: `
          <div class="modal-view-container">
            <div class="modal-view-left">${galeriaHTML}</div>
            <div class="modal-view-right">
              <div class="info-row">
                <div class="info-item">
                  <i class="fas fa-euro-sign" style="color: #3cb371;"></i>
                  <div>
                    <label>Preço</label>
                    <span class="price">€${parseFloat(dados.preco).toFixed(2)}</span>
                  </div>
                </div>
                <div class="info-item">
                  <i class="fas fa-list" style="color: #3cb371;"></i>
                  <div>
                    <label>Tipo</label>
                    <span>${dados.tipo_descricao || "N/A"}</span>
                  </div>
                </div>
              </div>
              <div class="info-row">
                <div class="info-item">
                  <i class="fas fa-boxes" style="color: #3cb371;"></i>
                  <div>
                    <label>Stock</label>
                    <span>${dados.stock} unidades</span>
                  </div>
                </div>
                <div class="info-item">
                  <i class="fas fa-star" style="color: #3cb371;"></i>
                  <div>
                    <label>Estado</label>
                    <span>${dados.estado}</span>
                  </div>
                </div>
              </div>
              <div class="info-row">
                <div class="info-item">
                  <i class="fas fa-venus-mars" style="color: #3cb371;"></i>
                  <div>
                    <label>Género</label>
                    <span>${dados.genero || "N/A"}</span>
                  </div>
                </div>
                <div class="info-item">
                  <i class="fas fa-copyright" style="color: #3cb371;"></i>
                  <div>
                    <label>Marca</label>
                    <span>${dados.marca || "N/A"}</span>
                  </div>
                </div>
              </div>
              <div class="info-row">
                <div class="info-item">
                  <i class="fas fa-ruler" style="color: #3cb371;"></i>
                  <div>
                    <label>Tamanho</label>
                    <span>${dados.tamanho || "N/A"}</span>
                  </div>
                </div>
                <div class="info-item">
                  <i class="fas fa-toggle-on" style="color: #3cb371;"></i>
                  <div>
                    <label>Ativo</label>
                    <span>${ativo}</span>
                  </div>
                </div>
              </div>
              <div class="info-description">
                <i class="fas fa-align-left" style="color: #3cb371;"></i>
                <div>
                  <label>Descrição</label>
                  <p>${dados.descricao || "Sem descrição"}</p>
                </div>
              </div>
            </div>
          </div>
        `,
          showCloseButton: true,
          showConfirmButton: false,
          width: 800,
          customClass: {
            popup: "product-modal-view",
            htmlContainer: "modal-view-wrapper",
          },
        });
      }
    },
    "json",
  );
}

function editarProduto(id) {
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 15, id: id },
    function (dados) {
      if (dados && dados.Produto_id) {
        abrirModalProduto("Editar Produto", dados);
      } else {
        wgError("Erro", "Erro ao carregar dados do produto");
      }
    },
    "json",
  ).fail(function () {
    wgError("Erro", "Erro na requisição");
  });
}

function removerProduto(id) {
  wgConfirm("Remover produto?", "Esta ação não pode ser desfeita!", {
    confirmText: '<i class="fas fa-check"></i> Sim, remover',
    icon: "fa-trash-alt",
    iconBg: "background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%);",
  }).then((resultado) => {
    if (resultado.isConfirmed) {
      $.post(
        "src/controller/controllerDashboardAnunciante.php",
        { op: 16, id: id },
        function () {
          wgSuccess("Removido!", "Produto removido com sucesso.");
          carregarProdutos();
        },
      );
    }
  });
}

function confirmarRemoverProduto(id) {
  removerProduto(id);
}

function obterProdutosSelecionados() {
  return $(".product-checkbox:checked")
    .map(function () {
      return $(this).data("id");
    })
    .get();
}

function atualizarAcoesEmMassa() {
  const selecionados = obterProdutosSelecionados();
  $("#selectedCount").text(selecionados.length + " selecionados");
  if (selecionados.length > 0) {
    $("#bulkActions").slideDown();
  } else {
    $("#bulkActions").slideUp();
  }
}

function editarSelecionado() {
  const ids = obterProdutosSelecionados();
  if (ids.length === 0) {
    wgWarning("Atenção", "Selecione um produto para editar.");
    return;
  }
  if (ids.length > 1) {
    wgWarning("Atenção", "Selecione apenas um produto para editar.");
    return;
  }
  editarProduto(ids[0]);
}

function removerEmMassa() {
  const ids = obterProdutosSelecionados();
  if (ids.length === 0) {
    wgWarning("Atenção", "Selecione pelo menos um produto para remover.");
    return;
  }
  wgConfirm(
    `Remover ${ids.length} produto${ids.length > 1 ? "s" : ""}?`,
    "Esta ação não pode ser desfeita!",
    {
      confirmText: '<i class="fas fa-check"></i> Sim, remover',
      icon: "fa-trash-alt",
      iconBg: "background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%);",
    },
  ).then((resultado) => {
    if (resultado.isConfirmed) {
      $.ajax({
        url: "src/controller/controllerDashboardAnunciante.php",
        method: "POST",
        data: { op: 36, ids: ids },
        traditional: true,
        dataType: "json",
        success: function (response) {
          if (response.success) {
            window.isReloading = true;
            wgSuccess(
              "Removido!",
              response.message || "Produtos removidos/desativados com sucesso.",
            ).then(() => {
              window.location.reload();
            });
          } else {
            wgError(
              "Erro",
              response.message || "Não foi possível remover os produtos.",
            );
          }
        },
        error: function (xhr, status, error) {
          wgError("Erro", "Não foi possível remover os produtos.");
        },
      });
    }
  });
}

function carregarProdutos() {
  if (window.isReloading) {
    return;
  }

  const waitForTable = setInterval(function () {
    if ($("#productsTable").length) {
      clearInterval(waitForTable);
      carregarProdutosNow();
    }
  }, 100);

  setTimeout(function () {
    clearInterval(waitForTable);
  }, 5000);
}

function carregarProdutosNow() {
  $.ajax({
    url: "src/controller/controllerDashboardAnunciante.php",
    method: "POST",
    data: { op: 8 },
    dataType: "json",
  })
    .done(function (dados) {
      if ($.fn.DataTable.isDataTable("#productsTable")) {
        const table = $("#productsTable").DataTable();
        table.clear().rows.add(dados).draw();
        return;
      }

      window.tabelaProdutos = $("#productsTable").DataTable({
        data: dados,
        columns: [
          {
            data: null,
            orderable: false,
            render: (dados) =>
              `<input type="checkbox" class="product-checkbox" data-id="${dados.Produto_id}">`,
          },
          {
            data: "foto",
            orderable: false,
            render: (foto) => {
              const imgSrc = foto ? foto : "src/img/no-image.png";
              return `<img src="${imgSrc}" alt="Produto" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 2px solid #e2e8f0;">`;
            },
          },
          { data: "nome" },
          { data: "tipo_descricao" },
          { data: "preco", render: (v) => "€" + parseFloat(v).toFixed(2) },
          {
            data: "stock",
            render: (v) => {
              const stock = parseInt(v);
              if (stock < 5) {
                return `<span class="stock-low"><i class="fas fa-exclamation-triangle"></i> ${stock}</span>`;
              }
              return stock;
            },
          },
          { data: "estado" },
          {
            data: "ativo",
            render: (v) =>
              v
                ? '<span class="status-badge badge-ativo"><i class="fas fa-check-circle"></i> Ativo</span>'
                : '<span class="status-badge badge-inativo"><i class="fas fa-times-circle"></i> Inativo</span>',
          },
        ],
        destroy: true,
        pageLength: 10,
        language: {
          url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Portuguese.json",
        },
        drawCallback: function () {
          $("#productsTable tbody tr")
            .removeClass("even odd")
            .css("background", "#ffffff");
        },
      });

      $("#productsTable tbody")
        .off("click", "tr")
        .on("click", "tr", function (e) {
          if ($(e.target).closest(".product-checkbox, button").length) return;
          const dados = window.tabelaProdutos.row(this).data();
          if (dados && dados.Produto_id) {
            visualizarProduto(dados.Produto_id);
          }
        });
    })
    .fail(function (xhr, status, error) {
      wgError("Erro", "Não foi possível carregar os produtos.");
    });
}

function abrirModalProduto(titulo, dados = {}) {
  let selectedFiles = [];
  const maxPhotos = 5;
  const isEdicao = titulo.includes("Editar");

  Swal.fire({
    html: `
      <div style="background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%); padding: 18px 20px; margin: -20px -20px 20px -20px; border-radius: 12px 12px 0 0;">
        <div style="display: flex; align-items: center; gap: 10px; justify-content: center;">
          <i class="fas fa-${titulo.includes("Editar") ? "edit" : "plus-circle"}" style="font-size: 22px; color: white;"></i>
          <h2 style="margin: 0; color: white; font-size: 20px; font-weight: 600;">${titulo}</h2>
        </div>
      </div>
      <form id="productFormSwal" style="text-align: left; padding: 0 10px;">
        <input type="hidden" id="productId" value="${dados.Produto_id || ""}">
        <div class="form-row">
          <div class="form-col">
            <label><i class="fas fa-tag" style="color: #3cb371; margin-right: 6px;"></i>Nome</label>
            <input type="text" id="nome" value="${dados.nome || ""}" required>
          </div>
          <div class="form-col">
            <label><i class="fas fa-list" style="color: #3cb371; margin-right: 6px;"></i>Tipo</label>
            <select id="tipo_produto_id" required></select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-col">
            <label><i class="fas fa-euro-sign" style="color: #3cb371; margin-right: 6px;"></i>Preço (€)</label>
            <input type="number" step="0.01" id="preco" value="${
              dados.preco || ""
            }" required>
          </div>
          <div class="form-col">
            <label><i class="fas fa-boxes" style="color: #3cb371; margin-right: 6px;"></i>Stock</label>
            <input type="number" id="stock" value="${
              dados.stock || ""
            }" min="0">
          </div>
        </div>
        <div class="form-row">
          <div class="form-col">
            <label><i class="fas fa-copyright" style="color: #3cb371; margin-right: 6px;"></i>Marca</label>
            <input type="text" id="marca" value="${dados.marca || ""}">
          </div>
          <div class="form-col">
            <label><i class="fas fa-ruler" style="color: #3cb371; margin-right: 6px;"></i>Tamanho</label>
            <select id="tamanho">
              <option value="" disabled>Selecionar Tamanho</option>
              <option value="XXS" ${dados.tamanho === "XXS" ? "selected" : ""}>XXS</option>
              <option value="XS" ${dados.tamanho === "XS" ? "selected" : ""}>XS</option>
              <option value="S" ${dados.tamanho === "S" ? "selected" : ""}>S</option>
              <option value="M" ${dados.tamanho === "M" ? "selected" : ""}>M</option>
              <option value="L" ${dados.tamanho === "L" ? "selected" : ""}>L</option>
              <option value="XL" ${dados.tamanho === "XL" ? "selected" : ""}>XL</option>
              <option value="XXL" ${dados.tamanho === "XXL" ? "selected" : ""}>XXL</option>
              <option value="XXXL" ${dados.tamanho === "XXXL" ? "selected" : ""}>XXXL</option>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-col">
            <label><i class="fas fa-star" style="color: #3cb371; margin-right: 6px;"></i>Estado</label>
            <select id="estado">
              <option ${
                dados.estado === "Excelente" ? "selected" : ""
              }>Excelente</option>
              <option ${
                dados.estado === "Como Novo" ? "selected" : ""
              }>Como Novo</option>
              <option ${dados.estado === "Novo" ? "selected" : ""}>Novo</option>
            </select>
          </div>
          <div class="form-col">
            <label><i class="fas fa-venus-mars" style="color: #3cb371; margin-right: 6px;"></i>Género</label>
            <select id="genero">
              <option ${
                dados.genero === "Mulher" ? "selected" : ""
              }>Mulher</option>
              <option ${
                dados.genero === "Homem" ? "selected" : ""
              }>Homem</option>
              <option ${
                dados.genero === "Criança" ? "selected" : ""
              }>Criança</option>
            </select>
          </div>
        </div>
        <div class="form-row" style="margin-top: 6px;">
          <div class="form-col">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; margin-top: 30px;">
              <input type="checkbox" id="sustentavel" ${
                Number(dados.sustentavel) === 1 ? "checked" : ""
              } style="width: 16px; height: 16px; accent-color: #3cb371;">
              <span><i class="fas fa-leaf" style="color: #3cb371; margin-right: 6px;"></i>Produto Sustentável</span>
            </label>
          </div>
          <div class="form-col" id="materialSustentavelContainer">
            <label><i class="fas fa-recycle" style="color: #3cb371; margin-right: 6px;"></i>Nível de Material Reciclável</label>
            <select id="tipo_material">
            <option value="">Selecionar nível</option>
            <option value="100_reciclavel" ${
              dados.tipo_material === "100_reciclavel" ? "selected" : ""
            }>100% reciclável (Comissão 4%)</option>
            <option value="70_reciclavel" ${
              dados.tipo_material === "70_reciclavel" ? "selected" : ""
            }>70% reciclável (Comissão 5%)</option>
            <option value="50_reciclavel" ${
              dados.tipo_material === "50_reciclavel" ? "selected" : ""
            }>50% reciclável (Comissão 5%)</option>
            <option value="30_reciclavel" ${
              dados.tipo_material === "30_reciclavel" ? "selected" : ""
            }>30% reciclável (Comissão 6%)</option>
            </select>
          </div>
        </div>
        <div class="form-row-full">
          <div id="sustentabilidadeInfo" style="padding: 10px 12px; border-radius: 8px; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; font-size: 12px;"></div>
        </div>
        <div class="form-row-full">
          <label><i class="fas fa-align-left" style="color: #3cb371; margin-right: 6px;"></i>Descrição</label>
          <textarea id="descricao" rows="3">${dados.descricao || ""}</textarea>
        </div>
        <div class="form-row-full">
          <label><i class="fas fa-camera" style="color: #3cb371; margin-right: 6px;"></i>Fotos do Produto <span id="photoCount" style="color: #3cb371; font-weight: 600;">(0/${maxPhotos})</span></label>
          <div style="position: relative;">
            <input type="file" id="foto" accept="image/*" multiple style="display: none;">
            <button type="button" id="selectPhotosBtn" style="width: 100%; padding: 12px; background: linear-gradient(135deg, #3cb371, #2e8b57); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 13px; transition: all 0.3s;">
              <i class="fas fa-images"></i> Selecionar Fotos (até ${maxPhotos})
            </button>
          </div>
          <small style="color: #64748b; margin-top: 6px; display: block; font-size: 11px;">💡 Formatos aceitos: JPG, PNG, WEBP, GIF</small>
        </div>
        <div id="photoPreview" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px; margin-top: 12px;"></div>
      </form>
    `,
    showCancelButton: true,
    confirmButtonText: isEdicao
      ? '<i class="fas fa-edit"></i> Editar Produto'
      : '<i class="fas fa-plus"></i> Adicionar Produto',
    cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
    width: 480,
    customClass: {
      popup: "product-modal-view",
      htmlContainer: "modal-view-wrapper",
      confirmButton: "btn-primary",
      cancelButton: "btn-secondary",
    },
    didOpen: () => {
      function atualizarInfoSustentabilidade() {
        const sustentavel = $("#sustentavel").is(":checked");
        const material = $("#tipo_material").val();
        const info = $("#sustentabilidadeInfo");
        const container = $("#materialSustentavelContainer");

        if (!sustentavel) {
          $("#tipo_material").prop("disabled", true);
          container.css({ opacity: "0.65" });
          info
            .css({
              background: "#fff7ed",
              border: "1px solid #fed7aa",
              color: "#9a3412",
            })
            .html(
              '<i class="fas fa-info-circle" style="margin-right:6px;"></i>Produto não sustentável — comissão aplicada: <strong>6%</strong>.',
            );
          return;
        }

        $("#tipo_material").prop("disabled", false);
        container.css({ opacity: "1" });

        const mapa = {
          "100_reciclavel": {
            nivel: "Excelente",
            comissao: "4%",
            detalhe: "100% reciclável",
          },
          "70_reciclavel": {
            nivel: "Elevado",
            comissao: "5%",
            detalhe: "70% reciclável",
          },
          "50_reciclavel": {
            nivel: "Intermédio",
            comissao: "5%",
            detalhe: "50% reciclável",
          },
          "30_reciclavel": {
            nivel: "Base",
            comissao: "6%",
            detalhe: "30% reciclável",
          },
        };

        const atual = mapa[material] || null;
        if (!atual) {
          info
            .css({
              background: "#ecfeff",
              border: "1px solid #a5f3fc",
              color: "#155e75",
            })
            .html(
              '<i class="fas fa-leaf" style="margin-right:6px;"></i>Selecione o nível de sustentabilidade para calcular a comissão.',
            );
          return;
        }

        info
          .css({
            background: "#f0fdf4",
            border: "1px solid #bbf7d0",
            color: "#166534",
          })
          .html(
            `<i class="fas fa-seedling" style="margin-right:6px;"></i>Nível: <strong>${atual.nivel}</strong> (${atual.detalhe}) — comissão aplicada: <strong>${atual.comissao}</strong>.`,
          );
      }

      carregarTiposProduto();
      if (dados.tipo_produto_id) {
        setTimeout(() => $("#tipo_produto_id").val(dados.tipo_produto_id), 100);
      }

      $("#sustentavel, #tipo_material").on(
        "change",
        atualizarInfoSustentabilidade,
      );
      atualizarInfoSustentabilidade();

      if (dados.fotos_array && dados.fotos_array.length > 0) {
        dados.fotos_array.forEach((fotoUrl) => {
          if (fotoUrl && fotoUrl.trim()) {
            fetch(fotoUrl)
              .then((res) => res.blob())
              .then((blob) => {
                const file = new File([blob], fotoUrl.split("/").pop(), {
                  type: blob.type,
                });
                file.isExisting = true;
                file.existingUrl = fotoUrl;
                selectedFiles.push(file);
                renderPhotoPreview();
              })
              .catch((err) => {
              });
          }
        });
      }

      $("#selectPhotosBtn").on("click", function () {
        $("#foto").click();
      });

      $("#foto").on("change", function (e) {
        const files = Array.from(e.target.files);

        if (files.length + selectedFiles.length > maxPhotos) {
          Swal.showValidationMessage(
            `Você pode adicionar no máximo ${maxPhotos} fotos`,
          );
          e.target.value = "";
          return;
        }

        files.forEach((file) => {
          if (!file.type.startsWith("image/")) {
            Swal.showValidationMessage("Apenas imagens são permitidas");
            return;
          }

          if (file.size > 5 * 1024 * 1024) {
            Swal.showValidationMessage("Imagem muito grande (máx 5MB)");
            return;
          }

          selectedFiles.push(file);
        });

        renderPhotoPreview();
        e.target.value = "";
      });

      function renderPhotoPreview() {
        const preview = $("#photoPreview");
        preview.empty();

        $("#photoCount").text(`(${selectedFiles.length}/${maxPhotos})`);

        selectedFiles.forEach((file, index) => {
          if (file.isExisting && file.existingUrl) {
            const photoCard = $(`
              <div class="photo-preview-card" data-index="${index}" style="position: relative; border-radius: 8px; overflow: hidden; aspect-ratio: 1; border: 2px solid #3cb371; box-shadow: 0 2px 8px rgba(0,0,0,0.2); cursor: zoom-in; transition: all 0.2s;">
                <img src="${file.existingUrl}"
                     data-photo-url="${file.existingUrl}"
                     style="width: 100%; height: 100%; object-fit: cover;">
                <button type="button" class="remove-photo" data-index="${index}" style="position: absolute; top: 5px; right: 5px; background: #E53E3E; color: white; border: none; border-radius: 50%; width: 25px; height: 25px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.3); z-index: 10;">
                  <i class="fas fa-times"></i>
                </button>
                ${
                  index === 0
                    ? '<div style="position: absolute; bottom: 5px; left: 5px; background: #3cb371; color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700;">Principal</div>'
                    : ""
                }
              </div>
            `);
            preview.append(photoCard);
          } else {
            const reader = new FileReader();
            reader.onload = function (e) {
              const photoCard = $(`
                <div class="photo-preview-card" data-index="${index}" style="position: relative; border-radius: 8px; overflow: hidden; aspect-ratio: 1; border: 2px solid #3cb371; box-shadow: 0 2px 8px rgba(0,0,0,0.2); cursor: zoom-in; transition: all 0.2s;">
                  <img src="${e.target.result}"
                       data-photo-url="${e.target.result}"
                       style="width: 100%; height: 100%; object-fit: cover;">
                  <button type="button" class="remove-photo" data-index="${index}" style="position: absolute; top: 5px; right: 5px; background: #E53E3E; color: white; border: none; border-radius: 50%; width: 25px; height: 25px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.3); z-index: 10;">
                    <i class="fas fa-times"></i>
                  </button>
                  ${
                    index === 0
                      ? '<div style="position: absolute; bottom: 5px; left: 5px; background: #3cb371; color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700;">Principal</div>'
                      : ""
                  }
                </div>
              `);
              preview.append(photoCard);
            };
            reader.readAsDataURL(file);
          }
        });

        setTimeout(() => {
          $(".remove-photo").on("click", function (e) {
            e.stopPropagation();
            const index = $(this).data("index");
            selectedFiles.splice(index, 1);
            renderPhotoPreview();
          });

          $(".photo-preview-card").on("click", function () {
            const index = $(this).data("index");
            mostrarGaleriaFotos(index);
          });

          $(".photo-preview-card")
            .on("mouseenter", function () {
              $(this).css({
                transform: "scale(1.05)",
                boxShadow: "0 4px 16px rgba(60, 179, 113, 0.4)",
              });
            })
            .on("mouseleave", function () {
              $(this).css({
                transform: "scale(1)",
                boxShadow: "0 2px 8px rgba(0,0,0,0.2)",
              });
            });
        }, 100);
      }

      function mostrarGaleriaFotos(startIndex) {
        let currentIndex = startIndex;

        function exibirFoto(index) {
          const reader = new FileReader();
          reader.onload = function (e) {
            const totalFotos = selectedFiles.length;
            const navButtons =
              totalFotos > 1
                ? `
                <div style="display: flex; justify-content: center; gap: 15px; margin-top: 20px;">
                  <button id="prevPhoto" style="padding: 10px 20px; background: #3cb371; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; transition: all 0.2s;">
                    <i class="fas fa-chevron-left"></i> Anterior
                  </button>
                  <span style="padding: 10px 20px; background: rgba(60, 179, 113, 0.1); border-radius: 6px; color: #3cb371; font-weight: 600;">
                    ${index + 1} / ${totalFotos}
                  </span>
                  <button id="nextPhoto" style="padding: 10px 20px; background: #3cb371; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; transition: all 0.2s;">
                    Próxima <i class="fas fa-chevron-right"></i>
                  </button>
                </div>
              `
                : `<div style="text-align: center; margin-top: 15px; color: #94a3b8;">Foto 1 de 1</div>`;

            Swal.fire({
              title: `Foto ${index + 1} de ${totalFotos}${
                index === 0 ? " (Principal)" : ""
              }`,
              html: `
                <div style="max-height: 50vh; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                  <img src="${e.target.result}"
                       style="max-width: 100%; max-height: 50vh; object-fit: contain; border-radius: 8px;">
                </div>
                ${navButtons}
              `,
              width: "80%",
              maxWidth: "800px",
              showConfirmButton: false,
              showCloseButton: true,
              background: "#ffffff",
              padding: "0",
              customClass: {
                popup: "photo-gallery-modal",
                title: "photo-gallery-title",
                htmlContainer: "photo-gallery-content",
              },
              didOpen: () => {
                if (totalFotos > 1) {
                  $("#prevPhoto").on("click", function () {
                    currentIndex =
                      currentIndex > 0 ? currentIndex - 1 : totalFotos - 1;
                    exibirFoto(currentIndex);
                  });

                  $("#nextPhoto").on("click", function () {
                    currentIndex =
                      currentIndex < totalFotos - 1 ? currentIndex + 1 : 0;
                    exibirFoto(currentIndex);
                  });

                  $(document).on("keydown.gallery", function (e) {
                    if (e.key === "ArrowLeft") {
                      $("#prevPhoto").click();
                    } else if (e.key === "ArrowRight") {
                      $("#nextPhoto").click();
                    }
                  });
                }

                if (!$("#galleryStyles").length) {
                  $("<style>")
                    .attr("id", "galleryStyles")
                    .text(
                      `
                      .photo-gallery-modal {
                        border-radius: 16px !important;
                        overflow: hidden !important;
                      }
                      .photo-gallery-title {
                        background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%) !important;
                        color: #ffffff !important;
                        font-size: 18px !important;
                        font-weight: 600 !important;
                        padding: 16px 28px !important;
                        margin: 0 !important;
                        border-radius: 16px 16px 0 0 !important;
                      }
                      .photo-gallery-content {
                        overflow: visible !important;
                        max-height: none !important;
                        padding: 24px !important;
                      }
                      .swal2-html-container {
                        overflow: visible !important;
                      }
                      .photo-gallery-modal .swal2-close {
                        color: #ffffff !important;
                        opacity: 0.9 !important;
                      }
                      .photo-gallery-modal .swal2-close:hover {
                        opacity: 1 !important;
                      }
                      #prevPhoto:hover, #nextPhoto:hover {
                        background: #2e8b57 !important;
                        transform: scale(1.05);
                      }
                    `,
                    )
                    .appendTo("head");
                }
              },
              willClose: () => {
                $(document).off("keydown.gallery");
              },
            });
          };
          reader.readAsDataURL(selectedFiles[index]);
        }

        exibirFoto(currentIndex);
      }

      $("<style>")
        .text(
          `
        #selectPhotosBtn:hover {
          transform: translateY(-2px);
          box-shadow: 0 4px 12px rgba(60, 179, 113, 0.4);
        }
        .remove-photo:hover {
          background: #DC2626 !important;
          transform: scale(1.1);
        }
      `,
        )
        .appendTo("head");
    },
    preConfirm: () => {
      if (!$("#nome").val()) {
        Swal.showValidationMessage("Nome do produto é obrigatório");
        return false;
      }
      if (!$("#tipo_produto_id").val()) {
        Swal.showValidationMessage("Tipo de produto é obrigatório");
        return false;
      }
      if (!$("#preco").val() || $("#preco").val() <= 0) {
        Swal.showValidationMessage("Preço deve ser maior que zero");
        return false;
      }
      if (selectedFiles.length === 0) {
        Swal.showValidationMessage("Adicione pelo menos 1 foto do produto");
        return false;
      }

      const formData = new FormData();
      formData.append("op", $("#productId").val() ? 17 : 18);
      formData.append("id", $("#productId").val());
      formData.append("nome", $("#nome").val());
      formData.append("tipo_produto_id", $("#tipo_produto_id").val());
      formData.append("preco", $("#preco").val());
      formData.append("stock", $("#stock").val() || 0);
      formData.append("marca", $("#marca").val());
      formData.append("tamanho", $("#tamanho").val());
      formData.append("estado", $("#estado").val());
      formData.append("genero", $("#genero").val());
      formData.append("descricao", $("#descricao").val());

      const sustentavel = $("#sustentavel").is(":checked") ? 1 : 0;
      const tipoMaterial = sustentavel ? $("#tipo_material").val() : "";

      if (sustentavel && !tipoMaterial) {
        Swal.showValidationMessage(
          "Selecione o nível de material reciclável para produto sustentável",
        );
        return false;
      }

      formData.append("sustentavel", sustentavel);
      formData.append("tipo_material", tipoMaterial);

      selectedFiles.forEach((file, index) => {
        formData.append("foto[]", file);
      });

      return $.ajax({
        url: "src/controller/controllerDashboardAnunciante.php",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
      })
        .then((response) => {
          let dados;
          try {
            dados =
              typeof response === "string" ? JSON.parse(response) : response;
          } catch (e) {
            Swal.showValidationMessage(
              "Erro ao processar resposta do servidor",
            );
            return false;
          }

          const sucesso =
            dados && (dados.success === true || dados.flag === true);
          if (sucesso) {
            carregarProdutos();
            carregarEstatisticasProdutos();
            const tituloSucesso = isEdicao
              ? "Produto editado!"
              : "Produto adicionado!";
            const mensagemSucesso =
              dados.message ||
              dados.msg ||
              (isEdicao
                ? "O produto foi editado com sucesso."
                : "O produto foi adicionado com sucesso.");
            wgSuccess(tituloSucesso, mensagemSucesso, { timer: 2000 });
            return true;
          } else {
            Swal.showValidationMessage(
              dados.message || dados.msg || "Erro ao guardar produto",
            );
            return false;
          }
        })
        .catch((error) => {
          Swal.showValidationMessage(
            "Erro ao guardar produto. Tente novamente.",
          );
          return false;
        });
    },
  });
}

function aplicarFeaturesPlanoNaUI(dados) {
  if (!dados) return;

  const planoNome = (dados.plano_nome || "").toString().trim();
  const podeExportarPdf = Number(dados.plano_relatorio_pdf || 0) === 1;
  const rastreioTipo = (dados.plano_rastreio_tipo || "Básico").toString();

  if (planoNome !== "Plano Profissional Eco+") {
    $("#upgradeBtn").show();
  } else {
    $("#upgradeBtn").hide();
  }

  $("#exportProductsBtn, #exportEncomendasBtn, #exportDevolucoesBtn").each(
    function () {
      if (podeExportarPdf) {
        $(this).show();
      } else {
        $(this).hide();
      }
    },
  );

  document.body.setAttribute("data-plano-rastreio", rastreioTipo);
}

function verificarPlanoUpgrade() {
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 27 },
    function (resp) {
      const dados = parseJsonSafe(resp);
      if (!dados || dados.error) return;
      aplicarFeaturesPlanoNaUI(dados);
    },
  );
}

function carregarPerfil() {
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 27 },
    function (resp) {
      const dados = parseJsonSafe(resp);
      if (!dados) return;
      if (dados.error) return wgError("Erro", dados.error);

      aplicarFeaturesPlanoNaUI(dados);

      const foto = dados.foto || "src/img/default_user.png";

      $("#profileCard").html(`
      <div class='profile-header-card'>
        <div class='profile-avatar-large'>
          <img src='${foto}' alt='Foto de Perfil' id='userPhoto'>
          <button class='avatar-edit-btn' type='button'>
            <i class='fas fa-camera'></i>
            <input type='file' id='avatarUpload' class='avatar-file-input' accept='image/jpeg,image/jpg,image/png,image/gif,image/webp' onchange='adicionarFotoPerfil()' />
          </button>
        </div>
        <div class='profile-header-info'>
          <div class='profile-header-left'>
            <h1>${dados.nome}</h1>
            <span class='role-badge'>📦 ${
              dados.plano_nome || "Anunciante"
            }</span>
          </div>
          <div class='profile-stats'>
            <div class='profile-stat'><div class='profile-stat-value'>${
              dados.total_produtos || 0
            }</div><div class='profile-stat-label'>Produtos Ativos</div></div>
            <div class='profile-stat'><div class='profile-stat-value'>${
              dados.ranking_nome || "N/A"
            }</div><div class='profile-stat-label'>Classificação</div></div>
            <div class='profile-stat'><div class='profile-stat-value'>${
              dados.pontos_conf || 0
            }</div><div class='profile-stat-label'>Pontos de Confiança</div></div>
          </div>
        </div>
      </div>
    `);

      $("#profileInfo").html(`
      <div class='section-header'><h3><i class='fas fa-user'></i> Informações Pessoais</h3></div>
      <div class='info-item'><label>Nome Completo</label><input type='text' id='nomeAnunciante' value='${
        dados.nome
      }'></div>
      <div class='info-item'><label>Email</label><input type='email' id='emailAnunciante' value='${
        dados.email
      }'></div>
      <div class='info-item'><label>NIF</label><input type='text' id='nifAnunciante' value='${
        dados.nif || ""
      }' placeholder='000000000' maxlength='9'></div>
      <div class='info-item'><label>Telefone</label><input type='text' id='telefoneAnunciante' value='${
        dados.telefone || ""
      }' placeholder='900000000' maxlength='9'></div>
      <div class='info-item'><label>Morada</label><input type='text' id='moradaAnunciante' value='${
        dados.morada || ""
      }' placeholder='Rua, Número, Código Postal, Cidade'></div>
      <button class='btn btn-primary' onclick='guardarDadosPerfil()' style='margin-top: 20px; width: 100%;'><i class='fas fa-save'></i> Guardar Alterações</button>
    `);

      const planoLimite = dados.plano_limite ? dados.plano_limite : "Ilimitado";

      let progressoPct = 0;
      let pontosTexto = `${dados.pontos_conf} pontos`;
      let proximoRankingInfo = "";

      if (dados.proximo_ranking_pontos) {
        const pontosAtuais = dados.pontos_conf;
        const pontosRankingAtual = dados.ranking_pontos_atuais || 0;
        const pontosProximoRanking = dados.proximo_ranking_pontos;
        const pontosNecessarios = pontosProximoRanking - pontosRankingAtual;
        const pontosProgresso = pontosAtuais - pontosRankingAtual;

        progressoPct = Math.min(
          (pontosProgresso / pontosNecessarios) * 100,
          100,
        );
        pontosTexto = `${pontosAtuais} / ${pontosProximoRanking} pontos`;
        proximoRankingInfo = `<span class='badge-next'>Próximo: ${dados.proximo_ranking_nome}</span>`;
      } else {
        progressoPct = 100;
        pontosTexto = `${dados.pontos_conf} pontos (Máximo)`;
      }

      $("#profilePlan").html(`
      <div class='section-header'><h3><i class='fas fa-crown'></i> Plano & Ranking</h3></div>
      <div class='plan-info-card'>
        <div class='plan-current'>
          <span class='plan-label'>Plano Atual</span>
          <span class='plan-name'>${dados.plano_nome || "Free"}</span>
          <span class='plan-price'>€${parseFloat(
            dados.plano_preco || 0,
          ).toFixed(2)}/mês</span>
        </div>
        <div class='plan-limits'><div class='limit-item'><i class='fas fa-box'></i><span>Produtos: ${
          dados.total_produtos
        }/${planoLimite}</span></div></div>
      </div>
      <div class='ranking-progress'>
        <div class='ranking-header'><span class='ranking-label'>Progresso do Ranking</span><span class='ranking-points'>${pontosTexto}</span></div>
        <div class='progress-bar'><div class='progress-fill' style='width: ${progressoPct}%'></div></div>
        <div class='ranking-badges'><span class='badge-current'>${
          dados.ranking_nome || "Iniciante"
        }</span>${proximoRankingInfo}</div>
      </div>
    `);

      $("#profileSecurity").html(`
      <div class='section-header'><h3><i class='fas fa-shield-alt'></i> Segurança</h3></div>
      <div class='security-content'>
        <div class='security-item'><div class='security-icon'><i class='fas fa-key'></i></div><div class='security-info'><h4>Alterar Password</h4><p>Mantenha sua conta segura atualizando sua senha regularmente</p></div></div>
        <button class='btn btn-secondary' onclick='showPasswordModal()' style='width: 100%; margin-top: 15px;'><i class='fas fa-lock'></i> Alterar Password</button>
      </div>
    `);
    },
  ).fail(function (jqXHR, textStatus, errorThrown) {
    Swal.fire("Erro", "Não foi possível carregar o perfil.", "error");
  });
}

function switchProfileTab(tabName, element) {
  document
    .querySelectorAll(".profile-tab")
    .forEach((tab) => tab.classList.remove("active"));
  document
    .querySelectorAll(".tab-pane")
    .forEach((pane) => pane.classList.remove("active"));
  element.classList.add("active");
  document.getElementById("tab-" + tabName).classList.add("active");
}

function guardarDadosPerfil() {
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    {
      op: 28,
      nome: $("#nomeAnunciante").val(),
      email: $("#emailAnunciante").val(),
      nif: $("#nifAnunciante").val(),
      telefone: $("#telefoneAnunciante").val(),
      morada: $("#moradaAnunciante").val(),
    },
    function (resp) {
      const dados = parseJsonSafe(resp);
      if (!dados) return;
      if (dados.success) {
        Swal.fire("Sucesso", dados.message, "success");
        carregarPerfil();
      } else {
        Swal.fire("Erro", dados.message, "error");
      }
    },
  );
}

function mostrarPlanosUpgrade() {
  Swal.fire({
    title: "Planos Disponíveis",
    html: `
      <div style="text-align: left; padding: 20px;">
        <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 15px; border-left: 4px solid #3cb371;">
          <h3 style="margin: 0 0 10px 0; color: #3cb371;">🌱 Free</h3>
          <p style="font-size: 24px; font-weight: bold; margin: 5px 0;">€0/mês</p>
          <ul style="margin: 10px 0; padding-left: 20px;"><li>Até 3 produtos</li><li>Rastreio básico</li></ul>
        </div>
        <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 15px; border-left: 4px solid #ffa500;">
          <h3 style="margin: 0 0 10px 0; color: #ffa500;">⭐ Premium</h3>
          <p style="font-size: 24px; font-weight: bold; margin: 5px 0;">€25/mês</p>
          <ul style="margin: 10px 0; padding-left: 20px;"><li>Até 10 produtos</li><li>Rastreio básico</li><li>Exportação de relatórios em PDF</li></ul>
        </div>
        <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-left: 4px solid #6a4c93;">
          <h3 style="margin: 0 0 10px 0; color: #6a4c93;">💎 Enterprise</h3>
          <p style="font-size: 24px; font-weight: bold; margin: 5px 0;">€100/mês</p>
          <ul style="margin: 10px 0; padding-left: 20px;"><li>Produtos ilimitados</li><li>Rastreio avançado</li><li>Exportação de relatórios em PDF</li><li>Suporte prioritário</li></ul>
        </div>
      </div>
    `,
    confirmButtonText: "Fechar",
    confirmButtonColor: "#3cb371",
    width: 600,
  });
}

function adicionarFotoPerfil() {
  const fileInput = document.getElementById("avatarUpload");
  const file = fileInput.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append("op", 29);
  formData.append("foto", file);

  $.ajax({
    url: "src/controller/controllerDashboardAnunciante.php",
    method: "POST",
    data: formData,
    processData: false,
    contentType: false,
  }).done(function (resp) {
    const dados = parseJsonSafe(resp);
    if (!dados) return;
    if (dados.success) {
      Swal.fire("Sucesso", dados.message, "success");
      $("#userPhoto").attr("src", dados.foto);
      carregarPerfil();
    } else {
      Swal.fire("Erro", dados.message, "error");
    }
  });
}

function showPasswordModal() {
  $("#passwordModal").addClass("active");
  closeUserDropdown();
}

function closePasswordModal() {
  $("#passwordModal").removeClass("active");
  $("#passwordForm")[0].reset();
}

function closeUserDropdown() {
  $("#userDropdown").removeClass("active");
}

function carregarEstatisticasProdutos() {
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 31 },
    function (stats) {
      $("#produtosAtivosCard").html(`
      <div class='stat-icon'><i class='fas fa-check-circle' style='color: #ffffff;'></i></div>
      <div class='stat-content'><div class='stat-label'>Produtos Ativos</div><div class='stat-value'>${stats.ativos}</div></div>
    `);

      $("#produtosInativosCard").html(`
      <div class='stat-icon'><i class='fas fa-exclamation-circle' style='color: #ffffff;'></i></div>
      <div class='stat-content'><div class='stat-label'>Produtos Inativos</div><div class='stat-value'>${stats.inativos}</div></div>
    `);

      $("#stockCriticoCard").html(`
      <div class='stat-icon'><i class='fas fa-exclamation-triangle' style='color: #ffffff;'></i></div>
      <div class='stat-content'><div class='stat-label'>Stock Crítico (&lt;5)</div><div class='stat-value'>${stats.stockBaixo}</div></div>
    `);

      $("#totalProdutosCard").html(`
      <div class='stat-icon'><i class='fas fa-box' style='color: #ffffff;'></i></div>
      <div class='stat-content'><div class='stat-label'>Total de Produtos</div><div class='stat-value'>${stats.total}</div></div>
    `);

      $.post(
        "src/controller/controllerDashboardAnunciante.php",
        { op: 14 },
        function (limite) {
          const max = Number(limite.max) || 0;
          const current = Number(limite.current) || 0;
          const temLimite = max > 0;

          if (temLimite && current >= max) {
            $("#addProductBtn").prop("disabled", true).css({
              "background-color": "#ccc",
              cursor: "not-allowed",
              opacity: "0.6",
            });
          } else {
            $("#addProductBtn").prop("disabled", false).css({
              "background-color": "",
              cursor: "",
              opacity: "",
            });
          }
        },
        "json",
      ).fail(function (xhr, status, error) {
        wgError("Erro", "Não foi possível carregar o limite de produtos.");
      });
    },
    "json",
  ).fail(function (xhr, status, error) {
    wgError("Erro", "Não foi possível carregar os produtos.");
  });
}

$(document).ready(function () {
  $("#reportPeriod").change(function () {
    loadReportStats();
    loadCategorySalesChart();
    loadDailyRevenueChart();
    loadReportsTable();
  });

  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 13 },
    function (data) {
      if (Array.isArray(data)) {
        data.forEach((t) =>
          $("#filterTipo, #tipo_produto_id").append(
            `<option value="${t.id}">${t.descricao}</option>`,
          ),
        );
      }
    },
    "json",
  );

  $("#filterTipo").on("change", function () {
    $("#productsTable").DataTable().column(3).search($(this).val()).draw();
  });

  $("#filterEstado").on("change", function () {
    $("#productsTable").DataTable().column(6).search($(this).val()).draw();
  });

  $("#filterGenero").on("change", function () {
    const valor = $(this).val();
    if (window.tabelaProdutos) {
      window.tabelaProdutos.rows().every(function () {
        const dadosLinha = this.data();
        const corresponde =
          !valor || (dadosLinha.genero && dadosLinha.genero === valor);
        $(this.node()).toggle(corresponde);
      });
    }
  });

  $("#filterAtivo").on("change", function () {
    const valor = $(this).val();
    if (valor === "") {
      $("#productsTable").DataTable().column(7).search("").draw();
    } else {
      const termoPesquisa = valor === "1" ? "Sim" : "Não";
      $("#productsTable").DataTable().column(7).search(termoPesquisa).draw();
    }
  });

  $("#searchProduct").on("keyup", function () {
    $("#productsTable").DataTable().search($(this).val()).draw();
  });

  $(document).on("change", "#selectAll", function () {
    $(".product-checkbox").prop("checked", $(this).prop("checked"));
    atualizarAcoesEmMassa();
  });

  $(document).on("change", ".product-checkbox", function () {
    atualizarAcoesEmMassa();
    const total = $(".product-checkbox").length;
    const marcados = $(".product-checkbox:checked").length;
    $("#selectAll").prop("checked", total === marcados);
  });

  $("#exportProductsBtn").on("click", function () {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    const tabela = $("#productsTable").DataTable();
    const dados = tabela.rows({ search: "applied" }).data();

    doc.setFontSize(22);
    doc.setFont("helvetica", "bold");
    doc.setTextColor(60, 179, 113);
    doc.text("WeGreen", 14, 20);

    doc.setFontSize(16);
    doc.setFont("helvetica", "normal");
    doc.setTextColor(45, 55, 72);
    doc.text("Relatório de Produtos", 14, 30);

    doc.setFontSize(9);
    doc.setTextColor(100, 116, 139);
    doc.text(
      "Data de Geração: " +
        new Date().toLocaleDateString("pt-PT", {
          year: "numeric",
          month: "long",
          day: "numeric",
          hour: "2-digit",
          minute: "2-digit",
        }),
      14,
      38,
    );
    doc.text("Total de Produtos: " + dados.length, 14, 44);

    const linhasTabela = [];
    let totalStock = 0;
    let totalAtivos = 0;

    dados.each(function (linha) {
      const stock = parseInt(linha.stock) || 0;
      totalStock += stock;
      if (linha.ativo) totalAtivos++;

      linhasTabela.push([
        linha.nome || "N/A",
        linha.tipo_descricao || "N/A",
        "€" + parseFloat(linha.preco || 0).toFixed(2),
        stock.toString(),
        linha.estado || "N/A",
        linha.ativo ? "Sim" : "Não",
      ]);
    });

    doc.autoTable({
      startY: 50,
      head: [["Nome", "Tipo", "Preço", "Stock", "Estado", "Ativo"]],
      body: linhasTabela,
      theme: "grid",
      headStyles: {
        fillColor: [60, 179, 113],
        textColor: [255, 255, 255],
        fontStyle: "bold",
        fontSize: 10,
        halign: "center",
      },
      styles: {
        fontSize: 9,
        cellPadding: 4,
        lineColor: [220, 220, 220],
        lineWidth: 0.5,
      },
      columnStyles: {
        0: { cellWidth: 55, halign: "left" },
        1: { cellWidth: 30, halign: "center" },
        2: { cellWidth: 25, halign: "right" },
        3: { cellWidth: 20, halign: "center" },
        4: { cellWidth: 30, halign: "center" },
        5: { cellWidth: 20, halign: "center" },
      },
      alternateRowStyles: {
        fillColor: [248, 250, 252],
      },
      didDrawPage: function (data) {
        const pageHeight = doc.internal.pageSize.height;
        doc.setFontSize(8);
        doc.setTextColor(100, 116, 139);
        doc.text(
          "WeGreen - Moda Sustentável | Página " +
            doc.internal.getNumberOfPages(),
          14,
          pageHeight - 10,
        );
      },
    });

    const finalY = doc.lastAutoTable.finalY + 10;
    doc.setFontSize(11);
    doc.setFont("helvetica", "bold");
    doc.setTextColor(45, 55, 72);
    doc.text("Resumo:", 14, finalY);

    doc.setFontSize(9);
    doc.setFont("helvetica", "normal");
    doc.setTextColor(100, 116, 139);
    doc.text("Total de Produtos: " + dados.length, 14, finalY + 6);
    doc.text("Produtos Ativos: " + totalAtivos, 14, finalY + 12);
    doc.text("Stock Total: " + totalStock + " unidades", 14, finalY + 18);

    doc.save(
      "WeGreen_Produtos_" + new Date().toISOString().split("T")[0] + ".pdf",
    );
    wgSuccess("PDF exportado", "Relatório de produtos gerado com sucesso.", {
      timer: 1400,
    });
  });

  $("#exportEncomendasBtn").on("click", function () {
    $.post(
      "src/controller/controllerDashboardAnunciante.php",
      { op: 32 },
      function (resp) {
        const encomendas = parseJsonSafe(resp);
        if (!encomendas) return;

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF("l");

        doc.setFontSize(22);
        doc.setFont("helvetica", "bold");
        doc.setTextColor(60, 179, 113);
        doc.text("WeGreen", 14, 20);

        doc.setFontSize(16);
        doc.setFont("helvetica", "normal");
        doc.setTextColor(45, 55, 72);
        doc.text("Relatório de Encomendas", 14, 30);

        doc.setFontSize(9);
        doc.setTextColor(100, 116, 139);
        doc.text(
          "Data de Geração: " +
            new Date().toLocaleDateString("pt-PT", {
              year: "numeric",
              month: "long",
              day: "numeric",
              hour: "2-digit",
              minute: "2-digit",
            }),
          14,
          38,
        );
        doc.text("Total de Encomendas: " + encomendas.length, 14, 44);

        const linhasTabela = [];
        let totalReceita = 0;

        encomendas.forEach(function (encomenda) {
          const total = parseFloat(
            encomenda.lucro_liquido || encomenda.valor || 0,
          );
          totalReceita += total;

          linhasTabela.push([
            encomenda.codigo || "N/A",
            encomenda.cliente_nome || "N/A",
            "€" + total.toFixed(2),
            encomenda.estado || "N/A",
            encomenda.data || "N/A",
            (encomenda.payment_method || "N/A").toUpperCase(),
            encomenda.transportadora || "N/A",
          ]);
        });

        doc.autoTable({
          startY: 50,
          head: [
            [
              "Código",
              "Cliente",
              "Valor",
              "Estado",
              "Data",
              "Pagamento",
              "Transportadora",
            ],
          ],
          body: linhasTabela,
          theme: "grid",
          headStyles: {
            fillColor: [60, 179, 113],
            textColor: [255, 255, 255],
            fontStyle: "bold",
            fontSize: 10,
            halign: "center",
          },
          styles: {
            fontSize: 8,
            cellPadding: 3,
            lineColor: [220, 220, 220],
            lineWidth: 0.5,
          },
          columnStyles: {
            0: { cellWidth: 35, halign: "center" },
            1: { cellWidth: 50, halign: "left" },
            2: { cellWidth: 30, halign: "right" },
            3: { cellWidth: 35, halign: "center" },
            4: { cellWidth: 35, halign: "center" },
            5: { cellWidth: 35, halign: "center" },
            6: { cellWidth: 40, halign: "center" },
          },
          alternateRowStyles: {
            fillColor: [248, 250, 252],
          },
          didDrawPage: function (data) {
            const pageHeight = doc.internal.pageSize.height;
            doc.setFontSize(8);
            doc.setTextColor(100, 116, 139);
            doc.text(
              "WeGreen - Moda Sustentável | Página " +
                doc.internal.getNumberOfPages(),
              14,
              pageHeight - 10,
            );
          },
        });

        const finalY = doc.lastAutoTable.finalY + 10;
        doc.setFontSize(11);
        doc.setFont("helvetica", "bold");
        doc.setTextColor(45, 55, 72);
        doc.text("Resumo:", 14, finalY);

        doc.setFontSize(9);
        doc.setFont("helvetica", "normal");
        doc.setTextColor(100, 116, 139);
        doc.text("Total de Encomendas: " + encomendas.length, 14, finalY + 6);
        doc.text("Receita Total: €" + totalReceita.toFixed(2), 14, finalY + 12);

        doc.save(
          "WeGreen_Encomendas_" +
            new Date().toISOString().split("T")[0] +
            ".pdf",
        );
        wgSuccess(
          "PDF exportado",
          "Relatório de encomendas gerado com sucesso.",
          {
            timer: 1400,
          },
        );
      },
    ).fail(function () {
      wgError("Erro", "Não foi possível gerar o PDF. Tente novamente.");
    });
  });

  $("#exportDevolucoesBtn").on("click", function () {
    $.ajax({
      url: "src/controller/controllerDevolucoes.php?op=3",
      method: "GET",
      dataType: "text",
      success: function (rawResponse) {
        let devolucoes = [];

        const respostaTexto = (rawResponse || "").toString().trim();

        if (respostaTexto.startsWith("{") || respostaTexto.startsWith("[")) {
          const parsed = parseJsonSafe(respostaTexto);
          if (parsed && Array.isArray(parsed.data)) {
            devolucoes = parsed.data;
          } else if (Array.isArray(parsed)) {
            devolucoes = parsed;
          }
        }

        if (!devolucoes.length && respostaTexto) {
          if (typeof extrairDevolucoesDeHtml === "function") {
            const extraidas = extrairDevolucoesDeHtml(respostaTexto);
            devolucoes = (extraidas && extraidas.devolucoes) || [];
          } else {
            const $rows = $("<tbody>").html(respostaTexto).find("tr");
            devolucoes = $rows
              .map(function () {
                const data = this.dataset || {};
                return {
                  codigo_devolucao: data.codigoDevolucao || "",
                  codigo_encomenda: data.codigoEncomenda || "",
                  produto_nome: data.produtoNome || "",
                  cliente_nome: data.clienteNome || "",
                  motivo: data.motivo || "",
                  valor_reembolso: data.valorReembolso || "0",
                  data_solicitacao: data.dataSolicitacao || "",
                  estado: data.estado || "",
                };
              })
              .get();
          }
        }

        if (!devolucoes.length) {
          wgInfo(
            "Sem dados",
            "Não existem devoluções disponíveis para exportar no momento.",
          );
          return;
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF("l");

        doc.setFontSize(22);
        doc.setFont("helvetica", "bold");
        doc.setTextColor(60, 179, 113);
        doc.text("WeGreen", 14, 20);

        doc.setFontSize(16);
        doc.setFont("helvetica", "normal");
        doc.setTextColor(45, 55, 72);
        doc.text("Relatório de Devoluções", 14, 30);

        doc.setFontSize(9);
        doc.setTextColor(100, 116, 139);
        doc.text(
          "Data de Geração: " +
            new Date().toLocaleDateString("pt-PT", {
              year: "numeric",
              month: "long",
              day: "numeric",
              hour: "2-digit",
              minute: "2-digit",
            }),
          14,
          38,
        );
        doc.text("Total de Devoluções: " + devolucoes.length, 14, 44);

        const linhasTabela = [];
        let valorTotal = 0;

        devolucoes.forEach(function (dev) {
          const valor = parseFloat(dev.valor_reembolso || 0);
          valorTotal += valor;

          const motivoTexto = {
            defeituoso: "Produto Defeituoso",
            tamanho_errado: "Tamanho Errado",
            nao_como_descrito: "Não como Descrito",
            arrependimento: "Arrependimento",
            outro: "Outro",
          };
          const motivoFormatado =
            motivoTexto[dev.motivo] || dev.motivo || "N/A";

          const estadoTexto = {
            solicitada: "Solicitada",
            aprovada: "Aprovada",
            enviada: "Enviada",
            recebida: "Recebida",
            produto_enviado: "Produto Enviado",
            produto_recebido: "Produto Recebido",
            rejeitada: "Rejeitada",
            reembolsada: "Reembolsada",
            cancelada: "Cancelada",
          };
          const estadoFormatado =
            estadoTexto[dev.estado] || dev.estado || "N/A";

          const dataFormatada = dev.data_solicitacao
            ? (() => {
                const valor = String(dev.data_solicitacao);
                if (/^\d{2}\/\d{2}\/\d{4}/.test(valor)) {
                  return valor;
                }
                const parsedDate = new Date(valor);
                return Number.isNaN(parsedDate.getTime())
                  ? valor
                  : parsedDate.toLocaleDateString("pt-PT");
              })()
            : "N/A";

          linhasTabela.push([
            dev.codigo_devolucao || "N/A",
            dev.codigo_encomenda || "N/A",
            dev.produto_nome || "N/A",
            dev.cliente_nome || "N/A",
            motivoFormatado,
            "€" + valor.toFixed(2),
            dataFormatada,
            estadoFormatado,
          ]);
        });

        doc.autoTable({
          startY: 50,
          head: [
            [
              "Cód. Devolução",
              "Encomenda",
              "Produto",
              "Cliente",
              "Motivo",
              "Valor",
              "Data",
              "Estado",
            ],
          ],
          body: linhasTabela,
          theme: "grid",
          headStyles: {
            fillColor: [60, 179, 113],
            textColor: [255, 255, 255],
            fontStyle: "bold",
            fontSize: 10,
            halign: "center",
          },
          styles: {
            fontSize: 8,
            cellPadding: 3,
            lineColor: [220, 220, 220],
            lineWidth: 0.5,
          },
          columnStyles: {
            0: { cellWidth: 32, halign: "center" },
            1: { cellWidth: 32, halign: "center" },
            2: { cellWidth: 45, halign: "left" },
            3: { cellWidth: 40, halign: "left" },
            4: { cellWidth: 35, halign: "left" },
            5: { cellWidth: 25, halign: "right" },
            6: { cellWidth: 28, halign: "center" },
            7: { cellWidth: 28, halign: "center" },
          },
          alternateRowStyles: {
            fillColor: [248, 250, 252],
          },
          didDrawPage: function (data) {
            const pageHeight = doc.internal.pageSize.height;
            doc.setFontSize(8);
            doc.setTextColor(100, 116, 139);
            doc.text(
              "WeGreen - Moda Sustentável | Página " +
                doc.internal.getNumberOfPages(),
              14,
              pageHeight - 10,
            );
          },
        });

        const finalY = doc.lastAutoTable.finalY + 10;
        doc.setFontSize(11);
        doc.setFont("helvetica", "bold");
        doc.setTextColor(45, 55, 72);
        doc.text("Resumo:", 14, finalY);

        doc.setFontSize(9);
        doc.setFont("helvetica", "normal");
        doc.setTextColor(100, 116, 139);
        doc.text("Total de Devoluções: " + devolucoes.length, 14, finalY + 6);
        doc.text(
          "Valor Total Devolvido: €" + valorTotal.toFixed(2),
          14,
          finalY + 12,
        );

        doc.save(
          "WeGreen_Devolucoes_" +
            new Date().toISOString().split("T")[0] +
            ".pdf",
        );
        wgSuccess(
          "PDF exportado",
          "Relatório de devoluções gerado com sucesso.",
          {
            timer: 1400,
          },
        );
      },
      error: function () {
        wgError("Erro", "Não foi possível gerar o PDF. Tente novamente.");
      },
    });
  });

  carregarEstatisticasProdutos();

  $("#addProductBtn").click(function () {
    if ($(this).prop("disabled")) {
      wgWarning("Limite atingido", "Limite de produtos atingido!");
      return;
    }
    abrirModalProduto("Adicionar Produto");
  });

  verificarPlanoUpgrade();

  const paginaAtiva = window.location.hash.replace("#", "") || "dashboard";
  const botaoAtivo = document.querySelector(
    `.nav-link[onclick*="${paginaAtiva}"]`,
  );
  if (botaoAtivo) {
    showPage(paginaAtiva, botaoAtivo);
  } else {
    carregarProdutos();
  }

  $("#userMenuBtn").click(function (e) {
    e.stopPropagation();
    $("#userDropdown").toggleClass("active");
  });

  $(document).click(function (e) {
    if (!$(e.target).closest(".navbar-user, .user-dropdown").length) {
      $("#userDropdown").removeClass("active");
    }
  });

  $("#passwordForm").submit(function (e) {
    e.preventDefault();
    const senhaAtual = $("#currentPassword").val();
    const senhaNova = $("#newPassword").val();
    const senhaConfirm = $("#confirmPassword").val();

    if (senhaNova !== senhaConfirm) {
      if (typeof showModernErrorModal === "function") {
        showModernErrorModal("Erro", "As senhas não correspondem");
      } else {
        Swal.fire("Erro", "As senhas não correspondem", "error");
      }
      return;
    }

    $.post(
      "src/controller/controllerDashboardAnunciante.php",
      {
        op: 30,
        senha_atual: senhaAtual,
        senha_nova: senhaNova,
      },
      function (resp) {
        const dados = parseJsonSafe(resp);
        if (!dados) return;
        if (dados.success) {
          if (typeof showModernSuccessModal === "function") {
            showModernSuccessModal("Sucesso", dados.message);
          } else {
            Swal.fire("Sucesso", dados.message, "success");
          }
          $("#passwordForm")[0].reset();
          closePasswordModal();
        } else {
          if (typeof showModernErrorModal === "function") {
            showModernErrorModal("Erro", dados.message);
          } else {
            Swal.fire("Erro", dados.message, "error");
          }
        }
      },
    );
  });
});

let encomendasTable;
let detalhesProdutosEncomenda = {};

function wgError(title, message) {
  if (typeof showModernErrorModal === "function") {
    return showModernErrorModal(title, message);
  }
  return Swal.fire(title, message, "error");
}

function wgSuccess(title, message, opts = {}) {
  if (typeof showModernSuccessModal === "function") {
    return showModernSuccessModal(title, message, opts);
  }
  return Swal.fire(title, message, "success");
}

function wgInfo(title, message) {
  if (typeof showModernInfoModal === "function") {
    return showModernInfoModal(title, message);
  }
  return Swal.fire(title, message, "info");
}

function wgWarning(title, message) {
  if (typeof showModernWarningModal === "function") {
    return showModernWarningModal(title, message);
  }
  return Swal.fire(title, message, "warning");
}

function wgConfirm(title, message, opts = {}) {
  if (typeof showModernConfirmModal === "function") {
    return showModernConfirmModal(title, message, opts);
  }
  return Swal.fire({
    title,
    text: message,
    icon: "question",
    showCancelButton: true,
    confirmButtonText: opts.confirmText || "Confirmar",
    cancelButtonText: "Cancelar",
  });
}

function carregarEncomendas() {
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 32 },
    function (resp) {
      try {
        const encomendas = parseJsonSafe(resp);
        if (!encomendas) return;
        renderEncomendas(encomendas);
      } catch (e) {
        wgError("Erro", "Não foi possível carregar as encomendas");
      }
    },
  );
}

function renderEncomendas(encomendas) {
  const tbody = $("#encomendasTable tbody");
  detalhesProdutosEncomenda = {};

  atualizarEstatisticasEncomendas(encomendas);

  if ($.fn.DataTable.isDataTable("#encomendasTable")) {
    try {
      const table = $("#encomendasTable").DataTable();
      table.clear();

      if (!encomendas || encomendas.length === 0) {
        tbody.html(`
          <tr>
            <td colspan="9" style="text-align: center; padding: 40px; color: #718096;">
              <i class="fas fa-shopping-bag" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
              <p>Nenhuma encomenda encontrada</p>
              <small>As encomendas dos seus produtos aparecerão aqui</small>
            </td>
          </tr>
        `);
        return;
      }

      encomendas.forEach((encomenda) => {
        const row = criarLinhaEncomenda(encomenda);
        table.row.add($(row)[0]);
      });

      table.draw();
      aplicarFiltrosEncomendas();
      return;
    } catch (e) {
      try {
        $("#encomendasTable").DataTable().destroy();
      } catch (e2) {}
    }
  }

  tbody.empty();

  if (!encomendas || encomendas.length === 0) {
    tbody.html(`
      <tr>
        <td colspan="9" style="text-align: center; padding: 40px; color: #718096;">
          <i class="fas fa-shopping-bag" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
          <p>Nenhuma encomenda encontrada</p>
          <small>As encomendas dos seus produtos aparecerão aqui</small>
        </td>
      </tr>
    `);
    return;
  }

  encomendas.forEach((encomenda) => {
    tbody.append(criarLinhaEncomenda(encomenda));
  });

  initEncomendasTable();
  aplicarFiltrosEncomendas();
}

function criarLinhaEncomenda(encomenda) {
  const statusClass = getStatusClass(encomenda.estado);
  const estadoTexto = encomenda.estado || "Pendente";
  const statusBadge = `<span class="badge badge-${statusClass}">${estadoTexto}</span>`;

  const dataEncomenda = new Date(encomenda.data_completa);
  const hoje = new Date();
  const diasDesdeEncomenda = Math.floor(
    (hoje - dataEncomenda) / (1000 * 60 * 60 * 24),
  );

  const badgeNovo = "";
  const classeUrgente = "";

  const moradaTooltip =
    encomenda.morada_completa || encomenda.morada || "Morada não disponível";

  let paymentIcon = '<i class="fas fa-credit-card"></i>';
  if (encomenda.payment_method === "paypal") {
    paymentIcon = '<i class="fab fa-paypal" style="color: #0070ba;"></i>';
  } else if (encomenda.payment_method === "klarna") {
    paymentIcon =
      '<i class="fas fa-money-check-alt" style="color: #ffb3c7;"></i>';
  }

  const comissao = encomenda.comissao || 0;
  const lucroLiquido = encomenda.lucro_liquido || 0;
  const lucroTooltip = `Valor Bruto: €${encomenda.valor.toFixed(
    2,
  )}\nComissão (6%): €${comissao.toFixed(
    2,
  )}\nLucro Líquido: €${lucroLiquido.toFixed(2)}`;

  let produtosHtml = "";

  if (encomenda.produtos && Array.isArray(encomenda.produtos)) {
    if (encomenda.produtos.length === 1) {
      const prod = encomenda.produtos[0];
      produtosHtml = `
        <div class="product-info" style="cursor: pointer;" onclick="visualizarProduto(${prod.id})">
          <img src="${prod.foto || "src/img/no-image.png"}" alt="${
            prod.nome
          }" class="product-thumb">
          <div>
            <div class="product-name">${prod.nome}</div>
            <div class="product-qty">Qtd: ${prod.quantidade}</div>
          </div>
        </div>
      `;
    } else {
      const primeiraFoto =
        encomenda.produtos[0]?.foto || "src/img/no-image.png";
      const totalProdutos = encomenda.produtos.length;

      detalhesProdutosEncomenda[encomenda.id] = `
        <div style="padding: 16px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; margin: 8px 0;">
          <h4 style="margin: 0 0 12px 0; color: #2d3748; font-size: 14px; font-weight: 700;">
            <i class="fas fa-box" style="margin-right: 8px; color: #3cb371;"></i>
            Produtos da Encomenda
          </h4>
          <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 10px;">
            ${encomenda.produtos
              .map(
                (prod) => `
              <div style="display: flex; gap: 10px; padding: 10px; background: white; border-radius: 8px; border: 1px solid #e5e7eb; cursor: pointer;" onclick="visualizarProduto(${prod.id})">
                <img src="${prod.foto || "src/img/no-image.png"}" alt="${prod.nome}" style="width: 56px; height: 56px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0;" onclick="event.stopPropagation(); visualizarProduto(${prod.id})">
                <div style="flex: 1; min-width: 0;">
                  <div style="font-weight: 600; color: #1a1a1a; margin-bottom: 4px; font-size: 13px; line-height: 1.3;">${prod.nome}</div>
                  <div style="color: #718096; font-size: 12px;">
                    <i class="fas fa-cubes" style="color: #3cb371; margin-right: 5px;"></i>
                    Quantidade: <strong>${prod.quantidade}</strong>
                  </div>
                </div>
              </div>
            `,
              )
              .join("")}
          </div>
        </div>
      `;

      produtosHtml = `
        <div class="product-info" style="cursor: pointer;" onclick="toggleProdutosEncomenda(${encomenda.id})">
          <img src="${primeiraFoto}" alt="Produtos" class="product-thumb" onclick="event.stopPropagation(); visualizarProduto(${encomenda.produtos[0]?.id || 0})">
          <div>
            <div class="product-name" onclick="event.stopPropagation(); visualizarProduto(${encomenda.produtos[0]?.id || 0})" style="cursor: pointer;">
              ${totalProdutos} produtos
            </div>
            <div class="product-qty">Qtd total: ${encomenda.quantidade}</div>
            <div style="margin-top: 4px; line-height: 1;">
              <i class="fas fa-chevron-down" id="arrow-${encomenda.id}" style="font-size: 12px; color: #3cb371; transition: transform 0.3s;"></i>
            </div>
          </div>
        </div>
      `;
    }
  } else {
    produtosHtml = `
      <div class="product-info" style="cursor: pointer;" onclick="visualizarProduto(${encomenda.produto_id || 0})">
        <img src="${
          encomenda.produto_foto || "src/img/no-image.png"
        }" alt="${encomenda.produto_nome}" class="product-thumb">
        <div>
          <div class="product-name">${encomenda.produto_nome}</div>
          <div class="product-qty">Qtd: ${encomenda.quantidade}</div>
        </div>
      </div>
    `;
  }

  const row = `
            <tr data-encomenda-id="${encomenda.id}" class="${classeUrgente}">
          <td></td>
                <td>
                    ${badgeNovo}<strong>${encomenda.codigo}</strong>
                    ${
                      diasDesdeEncomenda > 3 && encomenda.estado === "Pendente"
                        ? '<span class="badge badge-danger" style="margin-left: 5px; font-size: 10px;">Urgente</span>'
                        : ""
                    }
                </td>
                <td>${encomenda.data}</td>
                <td>
                    <div class="customer-info" title="${moradaTooltip}">
                        <div class="customer-name">${
                          encomenda.cliente_nome
                        }</div>
                        <div class="customer-email">${
                          encomenda.cliente_email
                        }</div>
                    </div>
                </td>
                <td>${produtosHtml}</td>
                <td>
                    <div class="transportadora-info">
                        <i class="fas fa-truck" style="color: #3cb371; margin-right: 5px;"></i>
                        <span>${encomenda.transportadora || "N/A"}</span>
                    </div>
                </td>
                <td>
                    <div title="${lucroTooltip}">
                        <strong>€${lucroLiquido.toFixed(2)}</strong>
                        <div style="font-size: 10px; color: #999;">
                            ${paymentIcon} ${encomenda.payment_method.toUpperCase()}
                        </div>
                    </div>
                </td>
                <td>${statusBadge}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-action btn-view" onclick="verDetalhesEncomenda(${
                          encomenda.id
                        })" title="Ver Detalhes">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-action btn-history" onclick="verHistoricoEncomenda(${
                          encomenda.id
                        })" title="Ver Histórico">
                            <i class="fas fa-history"></i>
                        </button>
                        <button class="btn-action btn-edit" onclick="editarStatusEncomenda(${
                          encomenda.id
                        }, '${encomenda.estado}')" title="Alterar Status">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-action btn-print" onclick="imprimirGuiaEnvio(${
                          encomenda.id
                        })" title="Guia de Envio">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;

  return row;
}

function atualizarEstatisticasEncomendas(encomendas) {
  const pendentes = encomendas.filter((e) => e.estado === "Pendente").length;
  const processando = encomendas.filter(
    (e) => e.estado === "Processando",
  ).length;
  const enviadas = encomendas.filter((e) => e.estado === "Enviado").length;
  const entregues = encomendas.filter((e) => e.estado === "Entregue").length;
  const devolvidas = encomendas.filter((e) => e.estado === "Devolvido").length;

  $("#totalPendentesCard").html(`
    <div class='stat-icon'><i class='fas fa-clock' style='color: #ffffff;'></i></div>
    <div class='stat-content'><div class='stat-label'>Pendentes</div><div class='stat-value'>${pendentes}</div></div>
  `);

  $("#totalProcessandoCard").html(`
    <div class='stat-icon'><i class='fas fa-box-open' style='color: #ffffff;'></i></div>
    <div class='stat-content'><div class='stat-label'>Processando</div><div class='stat-value'>${processando}</div></div>
  `);

  $("#totalEnviadasCard").html(`
    <div class='stat-icon'><i class='fas fa-shipping-fast' style='color: #ffffff;'></i></div>
    <div class='stat-content'><div class='stat-label'>Enviadas</div><div class='stat-value'>${enviadas}</div></div>
  `);

  $("#totalEntreguesCard").html(`
    <div class='stat-icon'><i class='fas fa-check-circle' style='color: #ffffff;'></i></div>
    <div class='stat-content'><div class='stat-label'>Entregues</div><div class='stat-value'>${entregues}</div></div>
  `);
}

function initEncomendasTable() {
  if (
    $("#encomendasTable").length &&
    !$.fn.DataTable.isDataTable("#encomendasTable")
  ) {
    try {
      encomendasTable = $("#encomendasTable").DataTable({
        language: {
          url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-PT.json",
        },
        order: [[1, "desc"]],
        pageLength: 10,
        autoWidth: false,
        columnDefs: [
          { targets: 4, width: "26%" },
          { targets: 5, width: "9%" },
        ],
        responsive: true,
      });
    } catch (e) {
    }
  }
}

function getStatusClass(estado) {
  const statusMap = {
    Pendente: "warning",
    Processando: "info",
    Enviado: "primary",
    Entregue: "success",
    Devolvido: "dark",
    Cancelado: "danger",
    Cancelada: "danger",
  };
  return statusMap[estado] || "secondary";
}

function getStatusIcon(estado) {
  const iconMap = {
    Pendente: "fa-clock",
    Processando: "fa-cog",
    Enviado: "fa-shipping-fast",
    Entregue: "fa-check-circle",
    Devolvido: "fa-undo",
    Cancelado: "fa-times-circle",
    Cancelada: "fa-times-circle",
  };
  return iconMap[estado] || "fa-circle";
}

function aplicarFiltrosEncomendas() {
  $("#filterEncomendaStatus, #filterDateFrom, #filterDateTo").on(
    "change",
    function () {
      filtrarEncomendas();
    },
  );
}

function filtrarEncomendas() {
  const status = $("#filterEncomendaStatus").val().toLowerCase();
  const dateFrom = $("#filterDateFrom").val();
  const dateTo = $("#filterDateTo").val();

  encomendasTable.rows().every(function () {
    const row = this.node();
    const encomendaStatus = $(row).find(".badge").text().toLowerCase();
    const encomendaData = $(row).find("td:eq(1)").text();

    let show = true;

    if (status && !encomendaStatus.includes(status)) {
      show = false;
    }

    if (dateFrom || dateTo) {
      const dataParts = encomendaData.split("/");
      const encomendaDate = new Date(
        dataParts[2],
        dataParts[1] - 1,
        dataParts[0],
      );

      if (dateFrom) {
        const fromDate = new Date(dateFrom);
        if (encomendaDate < fromDate) show = false;
      }

      if (dateTo) {
        const toDate = new Date(dateTo);
        if (encomendaDate > toDate) show = false;
      }
    }

    if (show) {
      $(row).show();
    } else {
      $(row).hide();
    }
  });
}

function verDetalhesEncomenda(encomendaId) {
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 32 },
    function (resp) {
      const encomendas = parseJsonSafe(resp);
      if (!encomendas) return;
      const encomenda = encomendas.find((e) => e.id === encomendaId);

      if (!encomenda) {
        wgError("Erro", "Encomenda não encontrada");
        return;
      }

      const statusClass = getStatusClass(encomenda.estado);

      const dataEncomenda = new Date(encomenda.data_completa);
      const hoje = new Date();
      const diasDesdeEncomenda = Math.floor(
        (hoje - dataEncomenda) / (1000 * 60 * 60 * 24),
      );

      let prazoEntrega = "N/A";
      if (encomenda.estado === "Enviado" || encomenda.estado === "Entregue") {
        const dataEstimada = new Date(dataEncomenda);
        dataEstimada.setDate(dataEstimada.getDate() + 3);
        prazoEntrega = dataEstimada.toLocaleDateString("pt-PT");
      }

      Swal.fire({
        title: `Encomenda #${encomenda.codigo}`,
        html: `
                    <div style="text-align: left; max-width: 100%; margin: 0;">
                        ${
                          diasDesdeEncomenda > 3 &&
                          encomenda.estado === "Pendente"
                            ? '<div style="padding: 12px 16px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 6px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;"><i class="fas fa-exclamation-triangle" style="color: #856404; font-size: 18px;"></i><span style="color: #856404; font-size: 14px; font-weight: 500;">Esta encomenda está pendente há ' +
                              diasDesdeEncomenda +
                              " dias</span></div>"
                            : ""
                        }

                        <!-- GRID PRINCIPAL: DADOS + MAPA -->
                        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px; margin-bottom: 5px;">

                        <!-- COLUNA ESQUERDA: INFORMAÇÕES -->
                        <div style="display: flex; flex-direction: column; gap: 16px;">

                        <!-- Cliente (Topo - Largura Total) -->
                        <div style="padding: 20px; background: linear-gradient(135deg, #f7fafc 0%, #ffffff 100%); border-radius: 10px; border-left: 4px solid #3cb371; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
                            <h4 style="margin: 0 0 14px 0; color: #2d3748; font-size: 16px; font-weight: 700;">
                                <i class="fas fa-user" style="margin-right: 8px; color: #3cb371; font-size: 18px;"></i>
                                Cliente
                            </h4>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                                <p style="margin: 6px 0; font-size: 15px; color: #4a5568;"><strong style="color: #2d3748;">Nome:</strong> ${
                                  encomenda.cliente_nome
                                }</p>
                                <p style="margin: 6px 0; font-size: 15px; color: #4a5568;">
                                    <strong style="color: #2d3748;">Email:</strong>
                                    <a href="mailto:${
                                      encomenda.cliente_email
                                    }" style="color: #3b82f6; text-decoration: none;">
                                        ${encomenda.cliente_email}
                                    </a>
                                </p>
                            </div>
                            <div style="margin-top: 12px; padding: 14px; background: linear-gradient(135deg, #e6f7ed 0%, #f0fdf4 100%); border-radius: 8px; border: 1px solid #3cb371;">
                                <div style="display: flex; align-items: flex-start; gap: 8px;">
                                    <i class="fas fa-map-marker-alt" style="color: #3cb371; font-size: 16px; margin-top: 2px;"></i>
                                    <div style="flex: 1;">
                                        <strong style="color: #2d3748; display: block; margin-bottom: 6px;">Endereço de Entrega (${
                                          encomenda.tipo_entrega ===
                                          "ponto_recolha"
                                            ? "Ponto de Recolha"
                                            : "Domicílio"
                                        }):</strong>
                                        <span style="color: #4a5568; font-size: 14px; line-height: 1.6; display: block; word-wrap: break-word;">${
                                          encomenda.morada_completa ||
                                          encomenda.morada
                                        }</span>
                                    </div>
                                    <i class="fas fa-copy" onclick="navigator.clipboard.writeText('${(
                                      encomenda.morada_completa ||
                                      encomenda.morada
                                    ).replace(/'/g, "\\'")}')
.then(() => (typeof showModernSuccessModal === 'function' ? showModernSuccessModal('Copiado!', 'Morada copiada para a área de transferência', { timer: 1500 }) : Swal.fire({icon: 'success', title: 'Copiado!', text: 'Morada copiada para a área de transferência', timer: 1500, showConfirmButton: false})))
.catch(() => (typeof showModernErrorModal === 'function' ? showModernErrorModal('Erro', 'Não foi possível copiar') : Swal.fire({icon: 'error', title: 'Erro', text: 'Não foi possível copiar', timer: 1500, showConfirmButton: false})))"
                                       style="color: #3cb371; cursor: pointer; font-size: 14px; flex-shrink: 0;"
                                       title="Copiar morada"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Grid 3 Colunas: Encomenda | Envio | Financeiros -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px;">

                        <!-- Encomenda -->
                        <div style="padding: 18px; background: linear-gradient(135deg, #f7fafc 0%, #ffffff 100%); border-radius: 10px; border-left: 4px solid #3cb371; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
                            <h4 style="margin: 0 0 12px 0; color: #2d3748; font-size: 15px; font-weight: 700;">
                                <i class="fas fa-box" style="margin-right: 8px; color: #3cb371; font-size: 16px;"></i>
                                Encomenda
                            </h4>
                            <p style="margin: 6px 0; font-size: 14px; color: #4a5568;"><strong style="color: #2d3748;">Produto:</strong> ${
                              encomenda.produto_nome ||
                              (encomenda.produtos
                                ? encomenda.produtos.length + " produtos"
                                : "N/A")
                            }</p>
                            <p style="margin: 6px 0; font-size: 14px; color: #4a5568;"><strong style="color: #2d3748;">Qtd:</strong> ${
                              encomenda.quantidade
                            } un.</p>
                            <p style="margin: 6px 0; font-size: 14px; color: #4a5568;"><strong style="color: #2d3748;">Valor:</strong> <span style="color: #3cb371; font-weight: bold; font-size: 15px;">€${encomenda.valor.toFixed(
                              2,
                            )}</span></p>
                            <p style="margin: 6px 0; font-size: 14px; color: #4a5568;"><strong style="color: #2d3748;">Data:</strong> ${
                              encomenda.data
                            }</p>
                            <p style="margin: 6px 0; font-size: 14px; color: #4a5568;">
                                <strong style="color: #2d3748;">Status:</strong>
                                <span class="badge badge-${statusClass}" style="font-size: 13px; padding: 5px 10px; border-radius: 6px; display: inline-block; margin-top: 4px;">
                                    ${
                                      encomenda.estado === "Pendente"
                                        ? "⏳"
                                        : encomenda.estado === "Entregue"
                                          ? "✔️"
                                          : encomenda.estado === "Enviado"
                                            ? "🚚"
                                            : encomenda.estado === "Processando"
                                              ? "📦"
                                              : "❌"
                                    }
                                    ${encomenda.estado || "Pendente"}
                                </span>
                            </p>
                        </div>

                        <!-- Envio -->
                        <div style="padding: 18px; background: linear-gradient(135deg, #f7fafc 0%, #ffffff 100%); border-radius: 10px; border-left: 4px solid #3cb371; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
                            <h4 style="margin: 0 0 12px 0; color: #2d3748; font-size: 15px; font-weight: 700;">
                                <i class="fas fa-shipping-fast" style="margin-right: 8px; color: #3cb371; font-size: 16px;"></i>
                                Envio
                            </h4>
                            <p style="margin: 6px 0; font-size: 14px; color: #4a5568;"><strong style="color: #2d3748;">Transportadora:</strong> ${
                              encomenda.transportadora || "N/A"
                            }</p>
                            <p style="margin: 6px 0; font-size: 14px; color: #4a5568;"><strong style="color: #2d3748;">Código de confirmação:</strong> ${
                              encomenda.codigo_confirmacao_recepcao ||
                              "Será gerado quando o estado for Enviado"
                            }</p>
                            <p style="margin: 6px 0; font-size: 14px; color: #4a5568;"><strong style="color: #2d3748;">Prazo:</strong> ${prazoEntrega}</p>
                            <p style="margin: 6px 0; font-size: 14px; color: #4a5568;"><strong style="color: #2d3748;">Decorrido:</strong> ${diasDesdeEncomenda} dia(s)</p>
                        </div>

                        <!-- Financeiros -->
                        <div style="padding: 18px; background: linear-gradient(135deg, #f7fafc 0%, #ffffff 100%); border-radius: 10px; border-left: 4px solid #3cb371; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
                            <h4 style="margin: 0 0 12px 0; color: #2d3748; font-size: 15px; font-weight: 700;">
                                <i class="fas fa-euro-sign" style="margin-right: 8px; color: #3cb371; font-size: 16px;"></i>
                                Financeiros
                            </h4>
                            <p style="margin: 6px 0; font-size: 14px; color: #4a5568;"><strong style="color: #2d3748;">Pagamento:</strong> ${encomenda.payment_method.toUpperCase()}</p>
                            <p style="margin: 6px 0; font-size: 14px; color: #4a5568;"><strong style="color: #2d3748;">Bruto:</strong> <span style="font-weight: 600;">€${encomenda.valor.toFixed(
                              2,
                            )}</span></p>
                            <p style="margin: 6px 0; font-size: 14px;"><strong style="color: #2d3748;">Comissão:</strong> <span style="color: #ef4444; font-weight: 600; font-size: 14px;">-€${encomenda.comissao.toFixed(
                              2,
                            )}</span></p>
                            <p style="margin: 6px 0; font-size: 14px;"><strong style="color: #2d3748;">Líquido:</strong> <span style="color: #10b981; font-weight: 700; font-size: 16px;">€${encomenda.lucro_liquido.toFixed(
                              2,
                            )}</span></p>
                        </div>

                        </div>
                        <!-- FIM GRID 3 COLUNAS -->

                        </div>
                        <!-- FIM COLUNA ESQUERDA -->

                        <!-- COLUNA DIREITA: MAPA -->
                        ${
                          encomenda.morada
                            ? `
                        <div style="padding: 15px; background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%); border-radius: 10px; border: 2px solid #3cb371; box-shadow: 0 4px 8px rgba(166,217,12,0.15);">
                            <h4 style="margin: 0 0 12px 0; color: #2d3748; font-size: 16px; font-weight: 700;">
                                <i class="fas fa-map-marker-alt" style="margin-right: 8px; color: #3cb371; font-size: 18px;"></i>
                                Localização de Entrega
                            </h4>
                            <div style="border-radius: 6px; overflow: hidden; border: 2px solid #e5e7eb;">
                                <iframe
                                    width="100%"
                                    height="300"
                                    frameborder="0"
                                    style="border:0"
                                    src="https://maps.google.com/maps?q=${encodeURIComponent(
                                      encomenda.morada_completa ||
                                        encomenda.morada,
                                    )}&t=&z=15&ie=UTF8&iwloc=&output=embed"
                                    allowfullscreen>
                                </iframe>
                            </div>
                            <div style="margin-top: 10px; text-align: center;">
                                <a href="https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(
                                  encomenda.morada_completa || encomenda.morada,
                                )}"
                                   target="_blank"
                                   style="display: inline-block; padding: 8px 16px; background: #3cb371; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 13px; border-radius: 6px; box-shadow: 0 2px 6px rgba(60, 179, 113, 0.3); transition: all 0.3s ease;"
                                   onmouseover="this.style.background='#2e8b57'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 10px rgba(60, 179, 113, 0.4)';"
                                   onmouseout="this.style.background='#3cb371'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(60, 179, 113, 0.3)';">
                                    <i class="fas fa-external-link-alt" style="margin-right: 6px;"></i>
                                    Ver mapa maior
                                </a>
                            </div>
                        </div>
                        `
                            : `<div></div>`
                        }

                        </div>
                        <!-- FIM GRID -->
                    </div>
                `,
        padding: "0",
        heightAuto: false,
        customClass: {
          popup: "product-modal-view",
          title: "modal-title-green",
          htmlContainer: "modal-view-wrapper",
        },
        showCloseButton: true,
        confirmButtonText: "Fechar",
        confirmButtonColor: "#3cb371",
        didOpen: () => {
          const title = document.querySelector(
            ".product-modal-view .swal2-title",
          );
          if (title) {
            title.style.background =
              "linear-gradient(135deg, #3cb371 0%, #2e8b57 100%)";
            title.style.color = "#ffffff";
            title.style.padding = "18px 32px";
            title.style.margin = "0";
            title.style.borderRadius = "16px 16px 0 0";
            title.style.fontSize = "20px";
            title.style.fontWeight = "600";
          }

          const closeBtn = document.querySelector(
            ".product-modal-view .swal2-close",
          );
          if (closeBtn) {
            closeBtn.style.color = "#ffffff";
            closeBtn.style.fontSize = "28px";
          }
        },
      });
    },
  );
}

function editarStatusEncomenda(encomendaId, statusAtual) {
  const estadoAtualTexto = (statusAtual || "").toString().trim();
  const estadoAtualNormalizado = estadoAtualTexto.toLowerCase();

  if (estadoAtualNormalizado.includes("entreg")) {
    wgWarning(
      "Não é possível alterar",
      "Esta encomenda já se encontra com estado Entregue. Motivo: estado final concluído.",
    );
    return;
  }

  if (estadoAtualNormalizado.includes("cancelad")) {
    wgWarning(
      "Não é possível alterar",
      "Esta encomenda foi cancelada pelo cliente. Motivo: a encomenda já foi encerrada pelo cliente.",
    );
    return;
  }

  const fluxoStatus = {
    Pendente: ["Pendente", "Processando", "Cancelado"],
    Processando: ["Processando", "Enviado", "Cancelado"],
    Enviado: ["Enviado", "Entregue"],
    Entregue: ["Entregue"],
    Cancelado: ["Cancelado"],
  };

  const opcoesPermitidas = fluxoStatus[statusAtual] || [
    statusAtual || "Pendente",
  ];
  const iconeStatus = {
    Pendente: "⏳",
    Processando: "📦",
    Enviado: "🚚",
    Entregue: "✔️",
    Cancelado: "❌",
  };

  const opcoesStatusHtml = opcoesPermitidas
    .map(
      (estado) =>
        `<option value="${estado}" ${statusAtual === estado ? "selected" : ""}>${iconeStatus[estado] || "•"} ${estado}</option>`,
    )
    .join("");

  Swal.fire({
    title: "Alterar Status da Encomenda",
    html: `
            <div style="text-align: left; padding: 10px;">
                <!-- Informação do status atual -->
                <div style="background: linear-gradient(135deg, #f7fafc 0%, #ffffff 100%); border-left: 4px solid #3cb371; padding: 14px 16px; border-radius: 8px; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                        <i class="fas fa-info-circle" style="font-size: 18px; color: #3cb371;"></i>
                        <div>
                            <p style="margin: 0; font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Status Atual</p>
                            <p style="margin: 0; font-size: 16px; color: #1e293b; font-weight: 700;">${statusAtual}</p>
                        </div>
                    </div>
                </div>

                <!-- Seletor de novo status -->
                <div style="margin-bottom: 18px;">
                    <label style="display: block; margin-bottom: 8px; color: #2d3748; font-weight: 600; font-size: 14px;">
                        <i class="fas fa-exchange-alt" style="margin-right: 6px; color: #3cb371;"></i>
                        Novo Status
                    </label>
                  <select id="novoStatus" style="width: 100%; padding: 12px 16px; font-size: 15px; border: 2px solid #e5e7eb; border-radius: 8px; background: white; color: #1e293b; font-weight: 500; transition: all 0.3s;" onfocus="this.style.borderColor='#3cb371'" onblur="this.style.borderColor='#e5e7eb'">
                    ${opcoesStatusHtml}
                    </select>
                </div>

                <!-- Observações -->
                <div style="margin-bottom: 10px;">
                    <label style="display: block; margin-bottom: 8px; color: #2d3748; font-weight: 600; font-size: 14px;">
                        <i class="fas fa-comment-alt" style="margin-right: 6px; color: #3cb371;"></i>
                        Observações
                        <span style="color: #64748b; font-weight: 400; font-size: 12px;">(opcional)</span>
                    </label>
                    <textarea id="observacao" placeholder="Adicione informações adicionais sobre a alteração..." style="width: 100%; min-height: 100px; padding: 12px 16px; font-size: 14px; border: 2px solid #e5e7eb; border-radius: 8px; resize: vertical; font-family: inherit; transition: all 0.3s;" onfocus="this.style.borderColor='#3cb371'" onblur="this.style.borderColor='#e5e7eb'"></textarea>
                </div>
            </div>
        `,
    showCancelButton: true,
    confirmButtonText:
      '<i class="fas fa-check" style="margin-right: 6px;"></i> Atualizar Status',
    cancelButtonText:
      '<i class="fas fa-times" style="margin-right: 6px;"></i> Cancelar',
    confirmButtonColor: "#3cb371",
    cancelButtonColor: "#64748b",
    width: 650,
    customClass: {
      popup: "status-modal-custom",
      title: "modal-title-green",
      confirmButton: "btn-confirm-green",
      cancelButton: "btn-cancel-gray",
    },
    didOpen: () => {
      const title = document.querySelector(".status-modal-custom .swal2-title");
      if (title) {
        title.style.background =
          "linear-gradient(135deg, #3cb371 0%, #2e8b57 100%)";
        title.style.color = "white";
        title.style.padding = "20px 28px";
        title.style.margin = "0";
        title.style.borderRadius = "12px 12px 0 0";
        title.style.fontSize = "20px";
        title.style.fontWeight = "700";
      }
    },
    preConfirm: () => {
      const status = document.getElementById("novoStatus").value;

      if (!opcoesPermitidas.includes(status)) {
        Swal.showValidationMessage(
          '<i class="fas fa-exclamation-triangle"></i> Só é possível avançar para estados seguintes.',
        );
        return false;
      }

      return {
        status: status,
        observacao: document.getElementById("observacao").value,
      };
    },
  }).then((result) => {
    if (result.isConfirmed) {
      $.post(
        "src/controller/controllerDashboardAnunciante.php",
        {
          op: 33,
          encomenda_id: encomendaId,
          novo_estado: result.value.status,
          observacao: result.value.observacao,
        },
        function (resp) {
          const dados = parseJsonSafe(resp);
          if (!dados) return;
          if (dados.success) {
            wgSuccess("Status Atualizado!", dados.message).then(() => {
              carregarEncomendas();
            });
          } else {
            wgError("Erro", dados.message);
          }
        },
      ).fail(function () {
        wgError("Erro", "Falha ao comunicar com o servidor");
      });
    }
  });
}

function verHistoricoEncomenda(encomendaId) {
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 34, encomenda_id: encomendaId },
    function (resp) {
      const historico = parseJsonSafe(resp);
      if (!historico) return;

      if (!historico || historico.length === 0) {
        wgInfo("Informação", "Nenhum histórico encontrado para esta encomenda");
        return;
      }

      const timelineHTML = historico
        .map((item, index) => {
          const estadoTexto = item.estado || "Pendente";
          const statusClass = getStatusClass(estadoTexto);
          const isLast = index === historico.length - 1;
          const iconClass = getStatusIcon(estadoTexto);

          return `
          <div style="position: relative; padding-left: 60px; padding-bottom: ${
            isLast ? "0" : "30px"
          }; text-align: left;">
            ${
              !isLast
                ? '<div style="position: absolute; left: 22px; top: 50px; bottom: 0; width: 3px; background: linear-gradient(180deg, #e2e8f0 0%, #f1f5f9 100%);"></div>'
                : ""
            }
            <div style="position: absolute; left: 0; top: 0; width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, var(--badge-${statusClass}-bg, #f3f4f6), var(--badge-${statusClass}-light, #fafafa)); border: 2px solid var(--badge-${statusClass}-color, #6b7280); box-shadow: 0 4px 12px rgba(0,0,0,0.08); display: flex; align-items: center; justify-content: center;">
              <i class="fas ${iconClass}" style="font-size: 18px; color: var(--badge-${statusClass}-color, #6b7280);"></i>
            </div>
            <div style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); padding: 18px 20px; border-radius: 12px; border-left: 4px solid var(--badge-${statusClass}-color, #6b7280); box-shadow: 0 2px 8px rgba(0,0,0,0.05); transition: all 0.3s ease;">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <span class="badge badge-${statusClass}" style="font-size: 13px; font-weight: 600; padding: 6px 14px; border-radius: 8px;">${estadoTexto}</span>
                <span style="font-size: 13px; color: #64748b; font-weight: 500; display: flex; align-items: center; gap: 6px;">
                  <i class="fas fa-clock" style="font-size: 11px;"></i>
                  ${item.data}
                </span>
              </div>
              <p style="margin: 0; color: #1e293b; font-size: 15px; line-height: 1.6;">${
                item.descricao
              }</p>
            </div>
          </div>
        `;
        })
        .join("");

      Swal.fire({
        title: "",
        html: `
          <style>
            :root {
              --badge-warning-bg: #fef3c7;
              --badge-warning-color: #d97706;
              --badge-warning-light: #fffbeb;
              --badge-info-bg: #dbeafe;
              --badge-info-color: #2563eb;
              --badge-info-light: #eff6ff;
              --badge-primary-bg: #e0e7ff;
              --badge-primary-color: #4f46e5;
              --badge-primary-light: #eef2ff;
              --badge-success-bg: #d1fae5;
              --badge-success-color: #059669;
              --badge-success-light: #ecfdf5;
              --badge-danger-bg: #fee2e2;
              --badge-danger-color: #dc2626;
              --badge-danger-light: #fef2f2;
            }
            .swal2-html-container {
              padding: 0 !important;
              margin: 0 !important;
              overflow-x: hidden !important;
            }
            .swal2-popup {
              overflow-x: hidden !important;
            }
          </style>
          <div style="background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%); padding: 15px 20px; margin: 0; border-radius: 0; text-align: center;">
            <h2 style="margin: 0; color: white; font-size: 18px; font-weight: 600;">Histórico da Encomenda</h2>
          </div>
          <div style="max-height: 400px; overflow-y: auto; overflow-x: hidden; padding: 20px 16px; background: #fafbfc; width: 100%; box-sizing: border-box;">
            ${timelineHTML}
          </div>
        `,
        width: 520,
        showConfirmButton: true,
        confirmButtonText: '<i class="fas fa-times"></i> Fechar',
        confirmButtonColor: "#3cb371",
        customClass: {
          confirmButton: "swal2-confirm-modern-history",
          popup: "swal2-border-radius",
        },
        buttonsStyling: false,
        didOpen: () => {
          const popup = Swal.getPopup();
          popup.style.borderRadius = "0";
          popup.style.padding = "0";
          popup.style.overflow = "hidden";
          popup.style.overflowX = "hidden";
          popup.style.width = "520px";
          popup.style.maxWidth = "520px";

          const htmlContainer = popup.querySelector(".swal2-html-container");
          if (htmlContainer) {
            htmlContainer.style.overflowX = "hidden";
          }

          const style = document.createElement("style");
          style.textContent = `
            .swal2-confirm-modern-history {
              padding: 12px 30px !important;
              border-radius: 8px !important;
              font-weight: 600 !important;
              font-size: 14px !important;
              cursor: pointer !important;
              transition: all 0.3s ease !important;
              border: none !important;
              background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%) !important;
              color: white !important;
            }
            .swal2-confirm-modern-history:hover {
              transform: translateY(-2px) !important;
              box-shadow: 0 6px 20px rgba(60, 179, 113, 0.4) !important;
            }
          `;
          document.head.appendChild(style);
        },
      });
    },
  ).fail(function () {
    wgError("Erro", "Falha ao carregar histórico");
  });
}

function initDashboardPage() {
  if (typeof updateDashboard === "function") {
    updateDashboard();
  } else {
    if (typeof getDadosPlanos === "function") getDadosPlanos();
    if (typeof CarregaProdutos === "function") CarregaProdutos();
    if (typeof CarregaPontos === "function") CarregaPontos();
    if (typeof getGastos === "function") getGastos();
    if (typeof getVendasMensais === "function") getVendasMensais();
    if (typeof renderTopProductsChart === "function") renderTopProductsChart();
    if (typeof renderRecentProducts === "function") renderRecentProducts();
  }
  if (typeof verificarPlanoUpgrade === "function") verificarPlanoUpgrade();
}

function initProductsPage() {
  if (typeof getDadosPlanos === "function") getDadosPlanos();
  if (typeof carregarProdutos === "function") carregarProdutos();
  if (typeof verificarPlanoUpgrade === "function") verificarPlanoUpgrade();
}

function initSalesPage() {
  if (typeof getDadosPlanos === "function") getDadosPlanos();
  if (typeof initEncomendasTable === "function") initEncomendasTable();
  if (typeof carregarEncomendas === "function") carregarEncomendas();
  if (typeof aplicarFiltrosEncomendas === "function")
    aplicarFiltrosEncomendas();
  if (typeof verificarPlanoUpgrade === "function") verificarPlanoUpgrade();
}

function initAnalyticsPage() {
  if (typeof getDadosPlanos === "function") getDadosPlanos();
  if (typeof loadReportStats === "function") loadReportStats();
  if (typeof loadCategorySalesChart === "function") loadCategorySalesChart();
  if (typeof loadDailyRevenueChart === "function") loadDailyRevenueChart();
  if (typeof loadReportsTable === "function") loadReportsTable();
  if (typeof verificarPlanoUpgrade === "function") verificarPlanoUpgrade();
}

function initProfilePage() {
  if (typeof getDadosPlanos === "function") getDadosPlanos();
  if (typeof carregarPerfil === "function") carregarPerfil();
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

      $.ajax({
        url: "src/controller/controllerPerfil.php?op=2",
        method: "GET",
      }).always(function () {
        window.location.href = "index.html";
      });
    }
  });
}

function logoutIncomplete() {
  Swal.fire({
    html: `
      <div style="text-align: center;">
        <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);">
          <i class="fas fa-sign-out-alt" style="font-size: 40px; color: white;"></i>
        </div>
        <h2 style="margin: 0 0 10px 0; color: #2d3748; font-size: 24px; font-weight: 700;">Terminar Sessão?</h2>
        <p style="color: #64748b; font-size: 15px; margin: 0;">Tem a certeza que pretende sair?</p>
      </div>
    `,
    showCancelButton: true,
    confirmButtonColor: "#dc3545",
    cancelButtonColor: "#6c757d",
    confirmButtonText: '<i class="fas fa-check"></i> Sim, sair',
    cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
    customClass: {
      confirmButton: "swal2-confirm-modern",
      cancelButton: "swal2-cancel-modern",
      popup: "swal2-border-radius",
    },
    buttonsStyling: false,
    didOpen: () => {
      const style = document.createElement("style");
      style.textContent = `
        .swal2-confirm-modern, .swal2-cancel-modern {
          padding: 12px 30px !important;
          border-radius: 8px !important;
          font-weight: 600 !important;
          font-size: 14px !important;
          cursor: pointer !important;
          transition: all 0.3s ease !important;
          border: none !important;
          margin: 5px !important;
        }
        .swal2-confirm-modern {
          background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%) !important;
          color: white !important;
        }
        .swal2-confirm-modern:hover {
          transform: translateY(-2px) !important;
          box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4) !important;
        }
        .swal2-cancel-modern {
          background: #6c757d !important;
          color: white !important;
        }
        .swal2-cancel-modern:hover {
          background: #5a6268 !important;
          transform: translateY(-2px) !important;
        }
        .swal2-border-radius {
          border-radius: 12px !important;
        }
      `;
      document.head.appendChild(style);
    },
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "src/controller/controllerPerfil.php?op=2",
        method: "GET",
      }).always(function () {
        window.location.href = "index.html";
      });
    }
  });
}

function logoutIncomplete() {
  Swal.fire({
    html: `
      <div style="text-align: center;">
        <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);">
          <i class="fas fa-sign-out-alt" style="font-size: 40px; color: white;"></i>
        </div>
        <h2 style="margin: 0 0 10px 0; color: #2d3748; font-size: 24px; font-weight: 700;">Terminar Sessão?</h2>
        <p style="color: #64748b; font-size: 15px; margin: 0;">Tem a certeza que pretende sair?</p>
      </div>
    `,
    showCancelButton: true,
    confirmButtonColor: "#dc3545",
    cancelButtonColor: "#6c757d",
    confirmButtonText: '<i class="fas fa-check"></i> Sim, sair',
    cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
    customClass: {
      confirmButton: "swal2-confirm-modern",
      cancelButton: "swal2-cancel-modern",
      popup: "swal2-border-radius",
    },
    buttonsStyling: false,
    didOpen: () => {
      const style = document.createElement("style");
      style.textContent = `
        .swal2-confirm-modern, .swal2-cancel-modern {
          padding: 12px 30px !important;
          border-radius: 8px !important;
          font-weight: 600 !important;
          font-size: 14px !important;
          cursor: pointer !important;
          transition: all 0.3s ease !important;
          border: none !important;
          margin: 5px !important;
        }
        .swal2-confirm-modern {
          background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%) !important;
          color: white !important;
        }
        .swal2-confirm-modern:hover {
          transform: translateY(-2px) !important;
          box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4) !important;
        }
        .swal2-cancel-modern {
          background: #6c757d !important;
          color: white !important;
        }
        .swal2-cancel-modern:hover {
          background: #5a6268 !important;
          transform: translateY(-2px) !important;
        }
        .swal2-border-radius {
          border-radius: 12px !important;
        }
      `;
      document.head.appendChild(style);
    },
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = "src/controller/controllerPerfil.php?op=2";
    }
  });
}

function toggleProdutosEncomenda(encomendaId) {
  const arrow = document.getElementById(`arrow-${encomendaId}`);
  const tabela = $("#encomendasTable");

  if (!$.fn.DataTable.isDataTable("#encomendasTable")) {
    return;
  }

  const dataTable = tabela.DataTable();
  const tr = tabela
    .find(`tbody tr[data-encomenda-id="${encomendaId}"]`)
    .first();

  if (!tr.length) return;

  const rowApi = dataTable.row(tr);
  if (!rowApi) return;

  if (rowApi.child.isShown()) {
    rowApi.child.hide();
    tr.removeClass("shown");
    if (arrow) arrow.style.transform = "rotate(0deg)";
  } else {
    const detalheHtml =
      detalhesProdutosEncomenda[encomendaId] ||
      '<div style="padding: 12px;">Sem detalhes de produtos.</div>';
    rowApi.child(detalheHtml, "produtos-child-row").show();
    tr.addClass("shown");
    if (arrow) arrow.style.transform = "rotate(180deg)";
  }
}

function limparFiltrosProdutos() {
  $("#searchProduct").val("");
  $("#filterTipo").val("").trigger("change");
  $("#filterEstado").val("").trigger("change");
  $("#filterGenero").val("").trigger("change");
  $("#filterAtivo").val("").trigger("change");

  if ($("#productsTable").DataTable()) {
    $("#productsTable").DataTable().search("").columns().search("").draw();
  }
}

function limparFiltrosEncomendas() {
  $("#filterEncomendaStatus").val("");
  $("#filterDateFrom").val("");
  $("#filterDateTo").val("");

  if ($("#encomendasTable").DataTable()) {
    $("#encomendasTable").DataTable().search("").columns().search("").draw();
  }
}
