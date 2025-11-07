<?php
    session_start();

    if($_SESSION['tipo'] == 1){ 
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Adminstrador - Fashion Store</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="src/css/Admin.css">
    <link rel="stylesheet" href="src/css/lib/datatables.css">
    <link rel="stylesheet" href="src/css/lib/select2.css">


    <script src="src/js/lib/datatables.js"></script>
    <script src="src/js/lib/select2.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
    <script src="src/js/lib/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <a href="index.html" style="text-decoration: none;">
                <div class="logo">
                    <span class="logo-icon">👔</span>
                    <div class="logo-text">
                        <h1>Fashion Store</h1>
                        <p>Painel do Adminstrador</p>
                    </div>
                </div>
            </a>
            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <button class="nav-link active" onclick="showPage('dashboard')">
                            <span class="nav-icon">📊</span>
                            <span class="nav-text">Dashboard</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" onclick="showPage('products')">
                            <span class="nav-icon">📦</span>
                            <span class="nav-text">Produtos</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" onclick="showPage('sales')">
                            <span class="nav-icon">🛍️</span>
                            <span class="nav-text">Vendas</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" onclick="showPage('analytics')">
                            <span class="nav-icon">📈</span>
                            <span class="nav-text">Análises</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" onclick="showPage('settings')">
                            <span class="nav-icon">⚙️</span>
                            <span class="nav-text">Configurações</span>
                        </button>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <div id="dashboard" class="page active">
                <div class="page-header">
                    <h2>Dashboard</h2>
                    <p>Visão geral do seu negócio</p>
                </div>

                <div class="stats-grid">
                    <div class="stat-card" id="RendimentosCard">
                    </div>
                    <div class="stat-card" id="GastosCard">
                    </div>
                    <div class="stat-card" id="UtilizadoresCard">
                    </div>
                    <div class="stat-card" id="PlanosAtivos">
                    </div>
                </div>

                <div class="charts-grid">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Vendas Mensais</h3>
                            <p>Evolução das vendas nos últimos meses</p>
                        </div>
                        <canvas id="salesChart"></canvas>
                    </div>
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Top Produtos</h3>
                            <p>Produtos mais vendidos</p>
                        </div>
                        <canvas id="topProductsChart"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Produtos Recentes</h3>
                        <p>Últimos produtos adicionados</p>
                    </div>
                    <div id="recentProducts"></div>
                </div>
            </div>

            <div id="products" class="page">
                <div class="action-bar">
                    <div class="page-header">
                        <h2>Produtos</h2>
                        <p>Gerir todos os seus produtos</p>
                    </div>
                    <button class="btn-primary" onclick="openModal()">
                        <span>➕</span>
                        Adicionar Produto
                    </button>
                </div>
                <div class="products-grid" id="productsGrid"></div>
            </div>

            <div id="sales" class="page">
                <div class="page-header">
                    <h2>Vendas</h2>
                    <p>Histórico e análise de vendas</p>
                </div>
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Evolução de Vendas</h3>
                        <p>Gráfico de vendas ao longo do tempo</p>
                    </div>
                    <canvas id="salesTimelineChart"></canvas>
                </div>
            </div>

            <div id="analytics" class="page">
                <div class="page-header">
                    <h2>Análises</h2>
                    <p>Insights detalhados do seu negócio</p>
                </div>
                <div class="charts-grid">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Distribuição de Lucro</h3>
                            <p>Lucro por categoria de produto</p>
                        </div>
                        <canvas id="profitChart"></canvas>
                    </div>
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3>Margem de Lucro</h3>
                            <p>Análise de margens</p>
                        </div>
                        <canvas id="marginChart"></canvas>
                    </div>
                </div>
            </div>

            <div id="settings" class="page">
                <div class="page-header">
                    <h2>Configurações</h2>
                    <p>Gerir as definições da sua loja</p>
                </div>
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Perfil da Loja</h3>
                    </div>
                    <div class="form-group">
                        <label>Nome da Loja</label>
                        <input type="text" value="Fashion Store" />
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" value="contato@fashionstore.com" />
                    </div>
                    <button class="btn-primary">Guardar Alterações</button>
                </div>
            </div>
        </main>
    </div>

    <div id="productModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Adicionar Produto</h3>
                <button class="close-btn" onclick="closeModal()">×</button>
            </div>
            <form id="productForm">
                <div class="form-group">
                    <label>Nome do Produto</label>
                    <input type="text" id="productName" required placeholder="Ex: Camisa Casual">
                </div>
                <div class="form-group">
                    <label>Descrição</label>
                    <textarea id="productDesc" rows="3" placeholder="Descrição do produto..."></textarea>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Preço (€)</label>
                        <input type="number" id="productPrice" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Custo (€)</label>
                        <input type="number" id="productCost" step="0.01" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Stock Inicial</label>
                    <input type="number" id="productStock" required>
                </div>
                <div class="form-group">
                    <label>Ícone (emoji)</label>
                    <input type="text" id="productIcon" value="👔" maxlength="2">
                </div>
                <button type="submit" class="btn-primary" style="width: 100%;">Adicionar Produto</button>
            </form>
        </div>
    </div>

    <script>
    let products = [{
            id: 1,
            name: 'T-Shirt Básica',
            price: 29.90,
            cost: 15.00,
            stock: 45,
            sold: 23,
            image: '👕',
            desc: 'T-shirt confortável em algodão'
        },
        {
            id: 2,
            name: 'Calças Jeans',
            price: 89.90,
            cost: 45.00,
            stock: 30,
            sold: 15,
            image: '👖',
            desc: 'Jeans de alta qualidade'
        },
        {
            id: 3,
            name: 'Vestido Verão',
            price: 79.90,
            cost: 35.00,
            stock: 20,
            sold: 12,
            image: '👗',
            desc: 'Vestido leve para o verão'
        }
    ];

    function showPage(pageId) {
        document.querySelectorAll('.page').forEach(page => page.classList.remove('active'));
        document.getElementById(pageId).classList.add('active');

        document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
        event.target.closest('.nav-link').classList.add('active');

        if (pageId === 'dashboard') {
            updateDashboard();
        } else if (pageId === 'products') {
            renderProducts();
        } else if (pageId === 'sales') {
            renderSalesChart();
        } else if (pageId === 'analytics') {
            renderAnalyticsCharts();
        }
    }

    function updateDashboard() {
        const totalRevenue = products.reduce((acc, p) => acc + (p.price * p.sold), 0);
        const totalProfit = products.reduce((acc, p) => acc + ((p.price - p.cost) * p.sold), 0);
        const totalStock = products.reduce((acc, p) => acc + p.stock, 0);

        document.getElementById('totalRevenue').textContent = '€' + totalRevenue.toFixed(2);
        document.getElementById('totalProfit').textContent = '€' + totalProfit.toFixed(2);
        document.getElementById('totalStock').textContent = totalStock;

        renderRecentProducts();
        renderSalesChart();
        renderTopProductsChart();
    }

    function renderRecentProducts() {
        const container = document.getElementById('recentProducts');
        container.innerHTML = products.slice(0, 3).map(p => `
                <div class="product-info-row">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <span style="font-size: 30px;">${p.image}</span>
                        <div>
                            <div style="font-weight: 600; color: #fff;">${p.name}</div>
                            <div style="color: #888; font-size: 14px;">${p.stock} em stock</div>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="color: #ffd700; font-weight: 600;">€${p.price.toFixed(2)}</div>
                        <div style="color: #888; font-size: 14px;">${p.sold} vendidos</div>
                    </div>
                </div>
            `).join('');
    }

    function renderProducts() {
        const container = document.getElementById('productsGrid');
        container.innerHTML = products.map(p => `
                <div class="product-card">
                    <div class="product-image">${p.image}</div>
                    <div class="product-name">${p.name}</div>
                    ${p.desc ? `<div class="product-desc">${p.desc}</div>` : ''}
                    <div class="product-info">
                        <div class="product-info-row">
                            <span class="product-info-label">Preço:</span>
                            <span class="product-info-value">€${p.price.toFixed(2)}</span>
                        </div>
                        <div class="product-info-row">
                            <span class="product-info-label">Custo:</span>
                            <span class="product-info-value">€${p.cost.toFixed(2)}</span>
                        </div>
                        <div class="product-info-row">
                            <span class="product-info-label">Lucro/un:</span>
                            <span class="product-info-value product-profit">€${(p.price - p.cost).toFixed(2)}</span>
                        </div>
                        <div class="product-info-row">
                            <span class="product-info-label">Stock:</span>
                            <span class="product-info-value">${p.stock}</span>
                        </div>
                        <div class="product-info-row">
                            <span class="product-info-label">Vendidos:</span>
                            <span class="product-info-value">${p.sold}</span>
                        </div>
                    </div>
                </div>
            `).join('');
    }

    function renderSalesChart() {
        const ctx = document.getElementById('salesChart');
        if (!ctx) return;

        if (window.salesChartInstance) {
            window.salesChartInstance.destroy();
        }

        window.salesChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                datasets: [{
                    label: 'Vendas (€)',
                    data: [1200, 1900, 1500, 2200, 2800, 3200],
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
    }

    function renderTopProductsChart() {
        const ctx = document.getElementById('topProductsChart');
        if (!ctx) return;

        if (window.topProductsChartInstance) {
            window.topProductsChartInstance.destroy();
        }

        const sortedProducts = [...products].sort((a, b) => b.sold - a.sold).slice(0, 3);

        window.topProductsChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: sortedProducts.map(p => p.name),
                datasets: [{
                    data: sortedProducts.map(p => p.sold),
                    backgroundColor: ['#ffd700', '#ffed4e', '#ffe066'],
                    borderColor: '#1a1a1a',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#888',
                            padding: 15
                        }
                    }
                }
            }
        });
    }

    function renderSalesTimelineChart() {
        const ctx = document.getElementById('salesTimelineChart');
        if (!ctx) return;

        if (window.salesTimelineChartInstance) {
            window.salesTimelineChartInstance.destroy();
        }

        window.salesTimelineChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                datasets: [{
                    label: 'Vendas',
                    data: [45, 52, 38, 65, 72, 88, 95, 82, 78, 92, 105, 98],
                    backgroundColor: '#ffd700',
                    borderRadius: 8
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
                        },
                        beginAtZero: true
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
    }

    function renderAnalyticsCharts() {
        const profitCtx = document.getElementById('profitChart');
        const marginCtx = document.getElementById('marginChart');

        if (window.profitChartInstance) window.profitChartInstance.destroy();
        if (window.marginChartInstance) window.marginChartInstance.destroy();

        window.profitChartInstance = new Chart(profitCtx, {
            type: 'polarArea',
            data: {
                labels: products.map(p => p.name),
                datasets: [{
                    data: products.map(p => (p.price - p.cost) * p.sold),
                    backgroundColor: ['#ffd700', '#ffed4e', '#ffe066'],
                    borderColor: '#1a1a1a',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#888',
                            padding: 15
                        }
                    }
                },
                scales: {
                    r: {
                        ticks: {
                            color: '#888',
                            backdropColor: 'transparent'
                        },
                        grid: {
                            color: '#333'
                        }
                    }
                }
            }
        });

        window.marginChartInstance = new Chart(marginCtx, {
            type: 'bar',
            data: {
                labels: products.map(p => p.name),
                datasets: [{
                    label: 'Margem (%)',
                    data: products.map(p => ((p.price - p.cost) / p.price * 100)),
                    backgroundColor: '#ffd700',
                    borderRadius: 8
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
                            color: '#888',
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                        grid: {
                            color: '#333'
                        },
                        beginAtZero: true
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
    }

    function openModal() {
        document.getElementById('productModal').classList.add('active');
    }

    function closeModal() {
        document.getElementById('productModal').classList.remove('active');
        document.getElementById('productForm').reset();
    }

    document.getElementById('productForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const newProduct = {
            id: products.length + 1,
            name: document.getElementById('productName').value,
            desc: document.getElementById('productDesc').value,
            price: parseFloat(document.getElementById('productPrice').value),
            cost: parseFloat(document.getElementById('productCost').value),
            stock: parseInt(document.getElementById('productStock').value),
            sold: 0,
            image: document.getElementById('productIcon').value || '👔'
        };

        products.push(newProduct);
        closeModal();
        renderProducts();
        updateDashboard();

        alert('Produto adicionado com sucesso!');
    });

    // Fechar modal ao clicar fora
    document.getElementById('productModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Inicializar dashboard
    updateDashboard();
    </script>
</body>
<?php 
}else{
    echo "sem permissão!";
}

?>
<script src="src/js/Adminstrador.js"></script>

</html>