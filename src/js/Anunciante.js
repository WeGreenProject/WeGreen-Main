// Sistema de notificações carregado via notifications.js

function getDadosPlanos() {
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 1 },
    function (response) {
      const data = JSON.parse(response);
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
    console.error("Erro ao carregar Plano:", textStatus);
  });
}

function CarregaProdutos() {
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 2 },
    function (response) {
      const data = JSON.parse(response);
      $("#ProdutoStock").html(`
        <div class='stat-icon'><i class='fas fa-box' style='color: #ffffff;'></i></div>
        <div class='stat-content'><div class='stat-label'>Total Produtos</div><div class='stat-value'>${data.total}</div></div>
      `);
    },
  ).fail(function (jqXHR, textStatus) {
    console.error("Erro ao carregar Produtos:", textStatus);
  });
}

function CarregaPontos() {
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 3 },
    function (response) {
      const data = JSON.parse(response);
      $("#PontosConfianca").html(`
        <div class='stat-icon'><i class='fas fa-star' style='color: #ffffff;'></i></div>
        <div class='stat-content'><div class='stat-label'>Pontos Confiança</div><div class='stat-value'>${data.pontos}</div></div>
      `);
    },
  ).fail(function (jqXHR, textStatus) {
    console.error("Erro ao carregar Pontos:", textStatus);
  });
}
function getGastos() {
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 4 },
    function (response) {
      const data = JSON.parse(response);
      const gastos = parseFloat(data.total).toFixed(2);
      $("#GastosCard").html(`
        <div class='stat-icon'><i class='fas fa-wallet' style='color: #ffffff;'></i></div>
        <div class='stat-content'><div class='stat-label'>Gastos Totais</div><div class='stat-value'>€${gastos}</div></div>
      `);
    },
  ).fail(function (jqXHR, textStatus) {
    console.error("Erro ao carregar Gastos:", textStatus);
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

      // Destruir gráfico anterior se existir
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
      console.warn("Elemento topProductsChart não encontrado");
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
              "#3cb371", // Verde WeGreen principal
              "#2d2d2d", // Preto/cinza escuro
              "#64748b", // Azul-cinza (terceira cor distintiva)
              "#2e8b57", // Verde escuro
              "#1a1a1a", // Preto
              "#5fd8a0", // Verde claro
              "#3a3a3a", // Cinza escuro
              "#45b8ac", // Verde-água
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

        // Status badge similar às encomendas
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
                <button class="btn-edit-produto" onclick="window.location.href='gestaoProdutosAnunciante.php'">
                  <i class="fas fa-edit"></i> Editar
                </button>
              </div>
            </div>
          </div>
        `;
      });

      container.html(html);
    })
    .fail(function (xhr, status, error) {
      console.error("Erro ao carregar produtos recentes:", error);
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
      // Forçar estilos do cabeçalho verde com letras brancas
      const title = document.querySelector(".photo-gallery-title");
      if (title) {
        title.style.background =
          "linear-gradient(135deg, #3cb371 0%, #2e8b57 100%)";
        title.style.color = "#ffffff";
        title.style.padding = "16px 28px";
        title.style.margin = "0";
        title.style.borderRadius = "16px 16px 0 0";
      }

      // Botão X branco
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

// Carregar estatísticas de relatórios
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

// Vendas por Categoria
function loadCategorySalesChart() {
  const ctx = document.getElementById("categorySalesChart");
  if (!ctx) {
    console.warn("Elemento categorySalesChart não encontrado");
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

// Receita Diária
function loadDailyRevenueChart() {
  const ctx = document.getElementById("dailyRevenueChart");
  if (!ctx) {
    console.warn("Elemento dailyRevenueChart não encontrado");
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

// Tabela de Relatórios
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
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 15, id: id },
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
        Swal.fire("Erro", "Erro ao carregar dados do produto", "error");
      }
    },
    "json",
  ).fail(function () {
    Swal.fire("Erro", "Erro na requisição", "error");
  });
}

function removerProduto(id) {
  Swal.fire({
    title: "Remover produto?",
    text: "Esta ação não pode ser desfeita!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Sim, remover!",
    cancelButtonText: "Cancelar",
  }).then((resultado) => {
    if (resultado.isConfirmed) {
      $.post(
        "src/controller/controllerDashboardAnunciante.php",
        { op: 16, id: id },
        function () {
          Swal.fire("Removido!", "Produto removido com sucesso.", "success");
          carregarProdutos();
        },
      );
    }
  });
}

// Alias para remover produto (usado na coluna de ações)
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
    Swal.fire("Atenção", "Selecione um produto para editar.", "warning");
    return;
  }
  if (ids.length > 1) {
    Swal.fire("Atenção", "Selecione apenas um produto para editar.", "warning");
    return;
  }
  editarProduto(ids[0]);
}

function removerEmMassa() {
  const ids = obterProdutosSelecionados();
  if (ids.length === 0) {
    Swal.fire(
      "Atenção",
      "Selecione pelo menos um produto para remover.",
      "warning",
    );
    return;
  }
  Swal.fire({
    title: `Remover ${ids.length} produto${ids.length > 1 ? "s" : ""}?`,
    text: "Esta ação não pode ser desfeita!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Sim, remover!",
    cancelButtonText: "Cancelar",
  }).then((resultado) => {
    if (resultado.isConfirmed) {
      $.ajax({
        url: "src/controller/controllerDashboardAnunciante.php",
        method: "POST",
        data: { op: 36, ids: ids },
        traditional: true,
        dataType: "json",
        success: function (response) {
          console.log("Response:", response);
          if (response.success) {
            // Marcar que estamos recarregando
            window.isReloading = true;
            Swal.fire(
              "Removido!",
              response.message || "Produtos removidos/desativados com sucesso.",
              "success",
            ).then(() => {
              // Recarregar a página completa para evitar problemas com DataTables
              window.location.reload();
            });
          } else {
            Swal.fire(
              "Erro",
              response.message || "Não foi possível remover os produtos.",
              "error",
            );
          }
        },
        error: function (xhr, status, error) {
          console.error("Erro ao remover:", error, xhr.responseText);
          Swal.fire("Erro", "Não foi possível remover os produtos.", "error");
        },
      });
    }
  });
}

function carregarProdutos() {
  // Evitar recarregar se estamos prestes a fazer reload da página
  if (window.isReloading) {
    console.log("Cancelando carregarProdutos: página vai recarregar");
    return;
  }

  // Aguardar até que a tabela esteja no DOM
  const waitForTable = setInterval(function () {
    if ($("#productsTable").length) {
      clearInterval(waitForTable);
      carregarProdutosNow();
    }
  }, 100);

  // Timeout de segurança (5 segundos)
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
      // Se a DataTable já existe, apenas atualiza os dados
      if ($.fn.DataTable.isDataTable("#productsTable")) {
        const table = $("#productsTable").DataTable();
        table.clear().rows.add(dados).draw();
        return;
      }

      // Criar nova DataTable
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
      console.error("Erro ao carregar produtos:", error);
      Swal.fire("Erro", "Não foi possível carregar os produtos.", "error");
    });
}

function abrirModalProduto(titulo, dados = {}) {
  // Array para armazenar arquivos selecionados
  let selectedFiles = [];
  const maxPhotos = 5; // Pode ser ajustado baseado no plano

  Swal.fire({
    title: titulo,
    html: `
      <form id="productFormSwal" style="text-align: left;">
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
            <input type="text" id="tamanho" value="${dados.tamanho || ""}">
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
    confirmButtonText: '<i class="fas fa-save"></i> Guardar Produto',
    cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
    width: 750,
    customClass: {
      popup: "product-modal-view",
      htmlContainer: "modal-view-wrapper",
      confirmButton: "btn-primary",
      cancelButton: "btn-secondary",
    },
    didOpen: () => {
      carregarTiposProduto();
      if (dados.tipo_produto_id) {
        setTimeout(() => $("#tipo_produto_id").val(dados.tipo_produto_id), 100);
      }

      // Handler para o botão de selecionar fotos
      $("#selectPhotosBtn").on("click", function () {
        $("#foto").click();
      });

      // Handler para mudança de arquivos
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
                    ? '<div style="position: absolute; bottom: 5px; left: 5px; background: #3cb371; color: #000; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700;">Principal</div>'
                    : ""
                }
              </div>
            `);
            preview.append(photoCard);
          };
          reader.readAsDataURL(file);
        });

        // Handler para remover foto
        setTimeout(() => {
          $(".remove-photo").on("click", function (e) {
            e.stopPropagation();
            const index = $(this).data("index");
            selectedFiles.splice(index, 1);
            renderPhotoPreview();
          });

          // Handler para pré-visualizar foto
          $(".photo-preview-card").on("click", function () {
            const index = $(this).data("index");
            mostrarGaleriaFotos(index);
          });

          // Hover effect
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

                  // Navegação por teclado
                  $(document).on("keydown.gallery", function (e) {
                    if (e.key === "ArrowLeft") {
                      $("#prevPhoto").click();
                    } else if (e.key === "ArrowRight") {
                      $("#nextPhoto").click();
                    }
                  });
                }

                // Estilos
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

      // Adicionar estilos ao modal
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
      // Validação
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

      // Adicionar arquivos selecionados
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
            console.error("Erro ao processar resposta:", e);
            Swal.showValidationMessage(
              "Erro ao processar resposta do servidor",
            );
            return false;
          }

          if (dados.success) {
            carregarProdutos();
            carregarEstatisticasProdutos();
            Swal.fire({
              icon: "success",
              title: "Produto salvo!",
              text: dados.message || "O produto foi adicionado com sucesso.",
              confirmButtonText: "OK",
              timer: 2000,
            });
            return true;
          } else {
            Swal.showValidationMessage(
              dados.message || "Erro ao guardar produto",
            );
            return false;
          }
        })
        .catch((error) => {
          console.error("Erro ao guardar produto:", error);
          Swal.showValidationMessage(
            "Erro ao guardar produto. Tente novamente.",
          );
          return false;
        });
    },
  });
}

function verificarPlanoUpgrade() {
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 27 },
    function (resp) {
      const dados = JSON.parse(resp);
      if (dados && dados.plano_nome !== "Plano Profissional Eco+") {
        $("#upgradeBtn").show();
      } else {
        $("#upgradeBtn").hide();
      }
    },
  );
}

function carregarPerfil() {
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 27 },
    function (resp) {
      const dados = JSON.parse(resp);
      if (dados.error) return Swal.fire("Erro", dados.error, "error");

      const foto = dados.foto || "src/img/default_user.png";

      if (dados.plano_nome !== "Plano Profissional Eco+") {
        $("#upgradeBtn").show();
      } else {
        $("#upgradeBtn").hide();
      }

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

      // Calcular progresso para o próximo ranking
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
    console.error("Erro ao carregar perfil:", textStatus, errorThrown);
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
      const dados = JSON.parse(resp);
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
          <ul style="margin: 10px 0; padding-left: 20px;"><li>Até 10 produtos</li><li>Rastreio básico</li><li>Relatórios em PDF</li></ul>
        </div>
        <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-left: 4px solid #6a4c93;">
          <h3 style="margin: 0 0 10px 0; color: #6a4c93;">💎 Enterprise</h3>
          <p style="font-size: 24px; font-weight: bold; margin: 5px 0;">€100/mês</p>
          <ul style="margin: 10px 0; padding-left: 20px;"><li>Produtos ilimitados</li><li>Rastreio avançado</li><li>Relatórios em PDF</li><li>Suporte prioritário</li></ul>
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
    const dados = JSON.parse(resp);
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
      <div class='stat-content'><div class='stat-label'>Total de Produtos</div><div class='stat-value'>${stats.total}</div><div class='stat-progress' id='totalProgress'></div></div>
    `);

      $.post(
        "src/controller/controllerDashboardAnunciante.php",
        { op: 14 },
        function (limite) {
          const percentagem = (limite.current / limite.max) * 100;
          let corBarra = "#3cb371";
          if (percentagem >= 90) corBarra = "#ef4444";
          else if (percentagem >= 70) corBarra = "#fbbf24";

          const progressHTML = `<div class='stat-progress-bar'><div class='stat-progress-fill' style='width: ${percentagem}%; background-color: ${corBarra};'></div></div>`;
          $("#totalProgress").html(progressHTML);

          if (limite.current >= limite.max) {
            $("#addProductBtn").prop("disabled", true).css({
              "background-color": "#ccc",
              cursor: "not-allowed",
              opacity: "0.6",
            });
          }
        },
        "json",
      ).fail(function (xhr, status, error) {
        console.error("Erro ao carregar limite:", error, xhr.responseText);
      });
    },
    "json",
  ).fail(function (xhr, status, error) {
    console.error("Erro ao carregar estatísticas:", error, xhr.responseText);
  });
}

$(document).ready(function () {
  // updateDashboard será chamado ao mostrar a página dashboard

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

  // Pesquisa global
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

    doc.setFontSize(18);
    doc.setTextColor(166, 217, 12);
    doc.text("WeGreen - Lista de Produtos", 14, 22);
    doc.setFontSize(10);
    doc.setTextColor(100);
    doc.text("Data: " + new Date().toLocaleDateString("pt-PT"), 14, 30);

    const linhasTabela = [];
    dados.each(function (linha) {
      linhasTabela.push([
        linha.nome,
        linha.tipo_descricao,
        "€" + parseFloat(linha.preco).toFixed(2),
        linha.stock,
        linha.estado,
        linha.ativo ? "Sim" : "Não",
      ]);
    });

    doc.autoTable({
      startY: 35,
      head: [["Nome", "Tipo", "Preço", "Stock", "Estado", "Ativo"]],
      body: linhasTabela,
      theme: "striped",
      headStyles: {
        fillColor: [166, 217, 12],
        textColor: [255, 255, 255],
        fontStyle: "bold",
      },
      styles: { fontSize: 9, cellPadding: 3 },
      columnStyles: {
        0: { cellWidth: 50 },
        1: { cellWidth: 35 },
        2: { cellWidth: 25 },
        3: { cellWidth: 20 },
        4: { cellWidth: 30 },
        5: { cellWidth: 20 },
      },
    });

    doc.save("produtos_" + new Date().toISOString().split("T")[0] + ".pdf");
  });

  // Exportar Encomendas PDF
  $("#exportEncomendasBtn").on("click", function () {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF("l"); // landscape
    const tabela = $("#encomendasTable").DataTable();
    const dados = tabela.rows({ search: "applied" }).data();

    doc.setFontSize(18);
    doc.setTextColor(166, 217, 12);
    doc.text("WeGreen - Lista de Encomendas", 14, 22);
    doc.setFontSize(10);
    doc.setTextColor(100);
    doc.text("Data: " + new Date().toLocaleDateString("pt-PT"), 14, 30);

    const linhasTabela = [];
    dados.each(function (linha) {
      linhasTabela.push([
        linha.codigo_encomenda || "",
        linha.cliente_nome || "",
        "€" + parseFloat(linha.total || 0).toFixed(2),
        linha.estado || "",
        linha.data_encomenda || "",
        linha.metodo_pagamento || "",
        linha.codigo_rastreio || "N/A",
      ]);
    });

    doc.autoTable({
      startY: 35,
      head: [
        [
          "Código",
          "Cliente",
          "Total",
          "Estado",
          "Data",
          "Pagamento",
          "Rastreio",
        ],
      ],
      body: linhasTabela,
      theme: "striped",
      headStyles: {
        fillColor: [166, 217, 12],
        textColor: [255, 255, 255],
        fontStyle: "bold",
      },
      styles: { fontSize: 8, cellPadding: 2 },
      columnStyles: {
        0: { cellWidth: 35 },
        1: { cellWidth: 45 },
        2: { cellWidth: 25 },
        3: { cellWidth: 30 },
        4: { cellWidth: 30 },
        5: { cellWidth: 30 },
        6: { cellWidth: 35 },
      },
    });

    doc.save("encomendas_" + new Date().toISOString().split("T")[0] + ".pdf");
  });

  // Exportar Devoluções PDF
  $("#exportDevolucoesBtn").on("click", function () {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF("l"); // landscape
    const tabela = $("#tabelaDevolucoes").DataTable();
    const dados = tabela.rows({ search: "applied" }).data();

    doc.setFontSize(18);
    doc.setTextColor(166, 217, 12);
    doc.text("WeGreen - Lista de Devoluções", 14, 22);
    doc.setFontSize(10);
    doc.setTextColor(100);
    doc.text("Data: " + new Date().toLocaleDateString("pt-PT"), 14, 30);

    const linhasTabela = [];
    dados.each(function (linha) {
      linhasTabela.push([
        linha.codigo_devolucao || "",
        linha.codigo_encomenda || "",
        linha.produto_nome || "",
        linha.cliente_nome || "",
        linha.motivo || "",
        "€" + parseFloat(linha.valor || 0).toFixed(2),
        linha.data_criacao || "",
        linha.estado || "",
      ]);
    });

    doc.autoTable({
      startY: 35,
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
      theme: "striped",
      headStyles: {
        fillColor: [166, 217, 12],
        textColor: [255, 255, 255],
        fontStyle: "bold",
      },
      styles: { fontSize: 8, cellPadding: 2 },
      columnStyles: {
        0: { cellWidth: 30 },
        1: { cellWidth: 30 },
        2: { cellWidth: 40 },
        3: { cellWidth: 35 },
        4: { cellWidth: 30 },
        5: { cellWidth: 25 },
        6: { cellWidth: 25 },
        7: { cellWidth: 25 },
      },
    });

    doc.save("devolucoes_" + new Date().toISOString().split("T")[0] + ".pdf");
  });

  carregarEstatisticasProdutos();

  $("#addProductBtn").click(function () {
    if ($(this).prop("disabled")) return alert("Limite de produtos atingido!");
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
      return Swal.fire("Erro", "As senhas não correspondem", "error");
    }

    $.post(
      "src/controller/controllerDashboardAnunciante.php",
      {
        op: 30,
        senha_atual: senhaAtual,
        senha_nova: senhaNova,
      },
      function (resp) {
        const dados = JSON.parse(resp);
        if (dados.success) {
          Swal.fire("Sucesso", dados.message, "success");
          $("#passwordForm")[0].reset();
          closePasswordModal();
        } else {
          Swal.fire("Erro", dados.message, "error");
        }
      },
    );
  });
});

// ==========================================
// GESTÃO DE ENCOMENDAS
// ==========================================

let encomendasTable;

function carregarEncomendas() {
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 32 },
    function (resp) {
      try {
        const encomendas = JSON.parse(resp);
        renderEncomendas(encomendas);
      } catch (e) {
        console.error("Erro ao carregar encomendas:", e);
        Swal.fire("Erro", "Não foi possível carregar as encomendas", "error");
      }
    },
  );
}

function renderEncomendas(encomendas) {
  const tbody = $("#encomendasTable tbody");

  // Calcular estatísticas
  atualizarEstatisticasEncomendas(encomendas);

  // Se DataTable existe, apenas limpar os dados
  if ($.fn.DataTable.isDataTable("#encomendasTable")) {
    try {
      const table = $("#encomendasTable").DataTable();
      table.clear();

      if (!encomendas || encomendas.length === 0) {
        tbody.html(`
          <tr>
            <td colspan="8" style="text-align: center; padding: 40px; color: #718096;">
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
      console.warn("Erro ao atualizar tabela, recriando:", e);
      // Se falhar, destruir e recriar abaixo
      try {
        $("#encomendasTable").DataTable().destroy();
      } catch (e2) {
        // Ignorar erro de destruição
      }
    }
  }

  // Criar tabela do zero
  tbody.empty();

  if (!encomendas || encomendas.length === 0) {
    tbody.html(`
      <tr>
        <td colspan="8" style="text-align: center; padding: 40px; color: #718096;">
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

  // Calcular dias desde a encomenda
  const dataEncomenda = new Date(encomenda.data_completa);
  const hoje = new Date();
  const diasDesdeEncomenda = Math.floor(
    (hoje - dataEncomenda) / (1000 * 60 * 60 * 24),
  );

  // Badge "Novo" para últimas 24h
  const badgeNovo =
    diasDesdeEncomenda === 0
      ? '<span class="badge badge-new">Novo</span> '
      : "";

  // Classe de urgência (mais de 3 dias pendente)
  const classeUrgente =
    diasDesdeEncomenda > 3 && encomenda.estado === "Pendente"
      ? "row-urgent"
      : "";

  // Tooltip morada
  const moradaTooltip =
    encomenda.morada_completa || encomenda.morada || "Morada não disponível";

  // Ícone do método de pagamento
  let paymentIcon = '<i class="fas fa-credit-card"></i>';
  if (encomenda.payment_method === "paypal") {
    paymentIcon = '<i class="fab fa-paypal" style="color: #0070ba;"></i>';
  } else if (encomenda.payment_method === "klarna") {
    paymentIcon =
      '<i class="fas fa-money-check-alt" style="color: #ffb3c7;"></i>';
  }

  // Comissão e lucro líquido
  const comissao = encomenda.comissao || 0;
  const lucroLiquido = encomenda.lucro_liquido || 0;
  const lucroTooltip = `Valor Bruto: €${encomenda.valor.toFixed(
    2,
  )}\nComissão (6%): €${comissao.toFixed(
    2,
  )}\nLucro Líquido: €${lucroLiquido.toFixed(2)}`;

  // Renderizar produtos (múltiplos produtos ou produto único)
  let produtosHtml = "";
  let produtosExpandidosHtml = "";
  let temMultiplosProdutos = false;

  if (encomenda.produtos && Array.isArray(encomenda.produtos)) {
    if (encomenda.produtos.length === 1) {
      // Apenas 1 produto - mostrar normalmente
      const prod = encomenda.produtos[0];
      produtosHtml = `
        <div class="product-info">
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
      // Múltiplos produtos - mostrar resumo com botão de expandir
      temMultiplosProdutos = true;
      const primeiraFoto =
        encomenda.produtos[0]?.foto || "src/img/no-image.png";
      const totalProdutos = encomenda.produtos.length;

      produtosHtml = `
        <div class="product-info" style="cursor: pointer;" onclick="toggleProdutosEncomenda(${encomenda.id})">
          <img src="${primeiraFoto}" alt="Produtos" class="product-thumb">
          <div>
            <div class="product-name">
              ${totalProdutos} produtos
              <i class="fas fa-chevron-down" id="arrow-${encomenda.id}" style="margin-left: 8px; font-size: 12px; color: #3cb371; transition: transform 0.3s;"></i>
            </div>
            <div class="product-qty">Qtd total: ${encomenda.quantidade}</div>
          </div>
        </div>
      `;

      // HTML dos produtos expandidos (hidden por padrão)
      produtosExpandidosHtml = `
        <tr id="produtos-expand-${encomenda.id}" class="produtos-expand-row" style="display: none;">
          <td colspan="8" style="padding: 0; background: #f9fafb; border-top: 2px solid #3cb371;">
            <div style="padding: 20px; max-height: 400px; overflow-y: auto;">
              <h4 style="margin: 0 0 15px 0; color: #2d3748; font-size: 16px; font-weight: 700;">
                <i class="fas fa-box" style="margin-right: 8px; color: #3cb371;"></i>
                Produtos da Encomenda
              </h4>
              <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px;">
                ${encomenda.produtos
                  .map(
                    (prod) => `
                  <div style="display: flex; gap: 12px; padding: 15px; background: white; border-radius: 8px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <img src="${prod.foto || "src/img/no-image.png"}" alt="${prod.nome}" style="width: 70px; height: 70px; object-fit: cover; border-radius: 6px; border: 2px solid #e2e8f0;">
                    <div style="flex: 1;">
                      <div style="font-weight: 600; color: #1a1a1a; margin-bottom: 5px; font-size: 14px;">${prod.nome}</div>
                      <div style="color: #718096; font-size: 13px;">
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
          </td>
        </tr>
      `;
    }
  } else {
    // Fallback para formato antigo (retrocompatibilidade)
    produtosHtml = `
      <div class="product-info">
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

  // Se tem múltiplos produtos, adicionar linha de expansão
  if (temMultiplosProdutos) {
    return row + produtosExpandidosHtml;
  }

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
        responsive: true,
      });
    } catch (e) {
      console.error("Erro ao inicializar tabela de encomendas:", e);
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
      const encomendas = JSON.parse(resp);
      const encomenda = encomendas.find((e) => e.id === encomendaId);

      if (!encomenda) {
        Swal.fire("Erro", "Encomenda não encontrada", "error");
        return;
      }

      const statusClass = getStatusClass(encomenda.estado);

      // Calcular dias desde encomenda
      const dataEncomenda = new Date(encomenda.data_completa);
      const hoje = new Date();
      const diasDesdeEncomenda = Math.floor(
        (hoje - dataEncomenda) / (1000 * 60 * 60 * 24),
      );

      // Prazo estimado de entrega (3 dias após envio)
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
.then(() => Swal.fire({icon: 'success', title: 'Copiado!', text: 'Morada copiada para a área de transferência', timer: 1500, showConfirmButton: false}))
.catch(() => Swal.fire({icon: 'error', title: 'Erro', text: 'Não foi possível copiar', timer: 1500, showConfirmButton: false}))"
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
                                    ${encomenda.estado}
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
                            <p style="margin: 6px 0; font-size: 14px; color: #4a5568;"><strong style="color: #2d3748;">Rastreio:</strong> ${
                              encomenda.codigo_rastreio || "N/A"
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
          // Aplicar estilos do cabeçalho verde
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

          // Botão X branco
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
                    <select id="novoStatus" style="width: 100%; padding: 12px 16px; font-size: 15px; border: 2px solid #e5e7eb; border-radius: 8px; background: white; color: #1e293b; font-weight: 500; transition: all 0.3s;" onchange="toggleCodigoRastreio()" onfocus="this.style.borderColor='#3cb371'" onblur="this.style.borderColor='#e5e7eb'">
                        <option value="Pendente" ${statusAtual === "Pendente" ? "selected" : ""}>⏳ Pendente</option>
                        <option value="Processando" ${statusAtual === "Processando" ? "selected" : ""}>📦 Processando</option>
                        <option value="Enviado" ${statusAtual === "Enviado" ? "selected" : ""}>🚚 Enviado</option>
                        <option value="Entregue" ${statusAtual === "Entregue" ? "selected" : ""}>✔️ Entregue</option>
                        <option value="Cancelado" ${statusAtual === "Cancelado" ? "selected" : ""}>❌ Cancelado</option>
                    </select>
                </div>

                <!-- Código de rastreio (condicional) -->
                <div id="codigoRastreioContainer" style="display: ${statusAtual === "Enviado" ? "block" : "none"}; margin-bottom: 18px;">
                    <label style="display: block; margin-bottom: 8px; color: #2d3748; font-weight: 600; font-size: 14px;">
                        <i class="fas fa-barcode" style="margin-right: 6px; color: #3cb371;"></i>
                        Código de Rastreio
                        <span style="color: #ef4444; margin-left: 4px;">*</span>
                    </label>
                    <input type="text" id="codigoRastreio" placeholder="Ex: BR123456789PT" style="width: 100%; padding: 12px 16px; font-size: 14px; border: 2px solid #e5e7eb; border-radius: 8px; transition: all 0.3s;" onfocus="this.style.borderColor='#3cb371'" onblur="this.style.borderColor='#e5e7eb'">
                    <small style="color: #64748b; font-size: 12px; display: block; margin-top: 6px;">
                        <i class="fas fa-exclamation-circle" style="margin-right: 4px;"></i>
                        Obrigatório ao marcar como "Enviado"
                    </small>
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

            <script>
                function toggleCodigoRastreio() {
                    const status = document.getElementById('novoStatus').value;
                    const container = document.getElementById('codigoRastreioContainer');
                    if (status === 'Enviado') {
                        container.style.display = 'block';
                        container.style.animation = 'slideDown 0.3s ease-out';
                    } else {
                        container.style.display = 'none';
                    }
                }
            </script>
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
      // Aplicar estilos ao título
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
      const codigoRastreio = document.getElementById("codigoRastreio").value;

      if (status === "Enviado" && !codigoRastreio.trim()) {
        Swal.showValidationMessage(
          '<i class="fas fa-exclamation-triangle"></i> Código de rastreio é obrigatório ao marcar como "Enviado"',
        );
        return false;
      }

      return {
        status: status,
        observacao: document.getElementById("observacao").value,
        codigo_rastreio: codigoRastreio,
      };
    },
  }).then((result) => {
    if (result.isConfirmed) {
      // Atualiza no backend
      $.post(
        "src/controller/controllerDashboardAnunciante.php",
        {
          op: 33,
          encomenda_id: encomendaId,
          novo_estado: result.value.status,
          observacao: result.value.observacao,
          codigo_rastreio: result.value.codigo_rastreio,
        },
        function (resp) {
          const dados = JSON.parse(resp);
          if (dados.success) {
            Swal.fire({
              title: "Status Atualizado!",
              text: dados.message,
              icon: "success",
              confirmButtonColor: "#3cb371",
            }).then(() => {
              carregarEncomendas();
            });
          } else {
            Swal.fire("Erro", dados.message, "error");
          }
        },
      ).fail(function () {
        Swal.fire("Erro", "Falha ao comunicar com o servidor", "error");
      });
    }
  });
}

function verHistoricoEncomenda(encomendaId) {
  $.post(
    "src/controller/controllerDashboardAnunciante.php",
    { op: 34, encomenda_id: encomendaId },
    function (resp) {
      const historico = JSON.parse(resp);

      if (!historico || historico.length === 0) {
        Swal.fire(
          "Info",
          "Nenhum histórico encontrado para esta encomenda",
          "info",
        );
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
          <div style="background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%); padding: 25px 20px; margin: -20px -20px 20px -20px; border-radius: 12px 12px 0 0;">
            <div style="display: flex; align-items: center; gap: 12px; justify-content: center;">
              <div style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                <i class="fas fa-history" style="font-size: 24px; color: white;"></i>
              </div>
              <div style="text-align: left;">
                <h3 style="margin: 0; color: white; font-size: 20px; font-weight: 700;">Histórico da Encomenda</h3>
                <p style="margin: 4px 0 0 0; color: rgba(255,255,255,0.9); font-size: 13px;">Timeline completa de estados</p>
              </div>
            </div>
          </div>
          <div style="max-height: 500px; overflow-y: auto; overflow-x: hidden; padding: 20px 16px; background: #fafbfc; width: 100%; box-sizing: border-box;">
            ${timelineHTML}
          </div>
        `,
        width: 500,
        showConfirmButton: true,
        confirmButtonText: '<i class="fas fa-times"></i> Fechar',
        confirmButtonColor: "#3cb371",
        customClass: {
          confirmButton: "swal2-confirm-modern",
        },
        didOpen: () => {
          const popup = Swal.getPopup();
          popup.style.borderRadius = "16px";
          popup.style.padding = "0";
          popup.style.overflow = "hidden";
          popup.style.overflowX = "hidden";
          popup.style.width = "500px";
          popup.style.maxWidth = "500px";

          const htmlContainer = popup.querySelector(".swal2-html-container");
          if (htmlContainer) {
            htmlContainer.style.overflowX = "hidden";
          }
        },
      });
    },
  ).fail(function () {
    Swal.fire("Erro", "Falha ao carregar histórico", "error");
  });
}

// ========== FUNÇÕES DE INICIALIZAÇÃO POR PÁGINA ==========

// Inicializar página Dashboard
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
}

// Inicializar página Produtos
function initProductsPage() {
  if (typeof getDadosPlanos === "function") getDadosPlanos();
  if (typeof carregarProdutos === "function") carregarProdutos();
}

// Inicializar página Encomendas
function initSalesPage() {
  if (typeof getDadosPlanos === "function") getDadosPlanos();
  if (typeof initEncomendasTable === "function") initEncomendasTable();
  if (typeof carregarEncomendas === "function") carregarEncomendas();
  if (typeof aplicarFiltrosEncomendas === "function")
    aplicarFiltrosEncomendas();
}

// Inicializar página Relatórios/Analytics
function initAnalyticsPage() {
  if (typeof getDadosPlanos === "function") getDadosPlanos();
  if (typeof loadReportStats === "function") loadReportStats();
  if (typeof loadCategorySalesChart === "function") loadCategorySalesChart();
  if (typeof loadDailyRevenueChart === "function") loadDailyRevenueChart();
  if (typeof loadReportsTable === "function") loadReportsTable();
}

// Inicializar página Perfil
function initProfilePage() {
  if (typeof getDadosPlanos === "function") getDadosPlanos();
  if (typeof carregarPerfil === "function") carregarPerfil();
}

// Função de Logout
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
    title: "Terminar Sessão?",
    text: "Tem a certeza que pretende sair?",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sim, sair",
    cancelButtonText: "Cancelar",
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
    title: "Terminar Sessão?",
    text: "Tem a certeza que pretende sair?",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Sim, sair",
    cancelButtonText: "Cancelar",
    confirmButtonColor: "#3cb371",
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = "src/controller/controllerPerfil.php?op=2";
    }
  });
}

// Toggle produtos expandidos na encomenda
function toggleProdutosEncomenda(encomendaId) {
  const expandRow = document.getElementById(`produtos-expand-${encomendaId}`);
  const arrow = document.getElementById(`arrow-${encomendaId}`);

  if (expandRow.style.display === "none") {
    expandRow.style.display = "table-row";
    arrow.style.transform = "rotate(180deg)";
  } else {
    expandRow.style.display = "none";
    arrow.style.transform = "rotate(0deg)";
  }
}

// Limpar filtros de produtos
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

// Limpar filtros de encomendas
function limparFiltrosEncomendas() {
  $("#filterEncomendaStatus").val("");
  $("#filterDateFrom").val("");
  $("#filterDateTo").val("");

  if ($("#encomendasTable").DataTable()) {
    $("#encomendasTable").DataTable().search("").columns().search("").draw();
  }
}
