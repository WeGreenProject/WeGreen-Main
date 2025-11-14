// ========================
// FUNÇÕES PARA CARREGAR DADOS
// ========================

function getDadosPlanos() {
    let dados = new FormData();
    dados.append("op", 1);

    $.ajax({
        url: "src/controller/controllerDashboardAnunciante.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
        $('#PlanosAtual').html(msg);
    })
    .fail(function(jqXHR, textStatus) {
        alert("Erro ao carregar Plano: " + textStatus);
    });
}

function CarregaProdutos() {
    let dados = new FormData();
    dados.append("op", 2);

    $.ajax({
        url: "src/controller/controllerDashboardAnunciante.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
        $('#ProdutoStock').html(msg);
    })
    .fail(function(jqXHR, textStatus) {
        alert("Erro ao carregar Produtos: " + textStatus);
    });
}

function CarregaPontos() {
    let dados = new FormData();
    dados.append("op", 3);

    $.ajax({
        url: "src/controller/controllerDashboardAnunciante.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(msg) {
        $('#PontosConfianca').html(msg);
    })
    .fail(function(jqXHR, textStatus) {
        alert("Erro ao carregar Pontos: " + textStatus);
    });
}

// ========================
// FUNÇÕES PARA GRÁFICOS
// ========================

function getVendasGrafico() {
    $.ajax({
        url: "src/controller/controllerDashboardAdmin.php",
        type: "POST",
        data: { op: 5 },
        dataType: "json",
        success: function(response) {
            console.log("Resposta AJAX:", response);
            const ctx3 = document.getElementById('salesChart').getContext('2d');
                
                chartVendas = new Chart(ctx3, {
                    type: 'line',
                    data: {
                        labels: response.dados1,
                        datasets: [{
                            label: 'Vendas (€)',
                            data: response.dados2,
                            borderColor: '#ffd700',
                            backgroundColor: 'rgba(255, 215, 0, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                ticks: {
                                    color: '#888'
                                },
                                grid: {
                                    color: '#333'
                                }
                            },
                            x: {
                                ticks: {
                                    color: '#888'
                                },
                                grid: {
                                    color: '#333'
                                }
                            }
                        }
                    }
                });
        },
        error: function(xhr, status, error) {
            console.error("Erro AJAX:", error);
            console.error("Resposta do servidor:", xhr.responseText);
        }
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
        processData: false
    })
    .done(function(resp) {
        const ctx = document.getElementById('topProductsChart');
        if (window.topProductsChartInstance) window.topProductsChartInstance.destroy();

        window.topProductsChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: resp.map(p => p.nome),
                datasets: [{
                    data: resp.map(p => p.vendidos),
                    backgroundColor: ['#ffd700', '#ffed4e', '#ffe066', '#fff176', '#fff59d'],
                    borderColor: '#1a1a1a',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#888', padding: 15 } }
                }
            }
        });
    });
}

function renderRecentProducts() {
    let dados = new FormData();
    dados.append("op", 7);

    $.ajax({
        url: "src/controller/controllerDashboardAnunciante.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false
    })
    .done(function(resp) {
        $('#recentProducts').html(resp);
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
        processData: false
    })
    .done(function(resp) {
        const ctx = document.getElementById('profitChart');
        if (window.profitChartInstance) window.profitChartInstance.destroy();

        window.profitChartInstance = new Chart(ctx, {
            type: 'polarArea',
            data: {
                labels: resp.map(p => p.nome),
                datasets: [{
                    data: resp.map(p => p.lucro),
                    backgroundColor: ['#ffd700', '#ffed4e', '#ffe066', '#fff176', '#fff59d'],
                    borderColor: '#1a1a1a',
                    borderWidth: 2
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { color: '#888', padding: 15 } } } }
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
        processData: false
    })
    .done(function(resp) {
        const ctx = document.getElementById('marginChart');
        if (window.marginChartInstance) window.marginChartInstance.destroy();

        window.marginChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: resp.map(p => p.nome),
                datasets: [{
                    label: 'Margem (%)',
                    data: resp.map(p => p.margem),
                    backgroundColor: '#ffd700',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { ticks: { color: '#888', callback: v => v + '%' }, grid: { color: '#333' }, beginAtZero: true },
                    x: { ticks: { color: '#888' }, grid: { color: '#333' } }
                }
            }
        });
    });
}

// ========================
// FUNÇÃO PRINCIPAL PARA ATUALIZAR DASHBOARD
// ========================

function updateDashboard() {
    getDadosPlanos();
    CarregaProdutos();
    CarregaPontos();
    renderSalesChart();
    renderTopProductsChart();
    renderRecentProducts();
    renderProfitChart();
    renderMarginChart();
}

// ========================
// INICIALIZAÇÃO
// ========================
$(document).ready(function() {
    updateDashboard();
});
