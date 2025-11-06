 let products = [
            { id: 1, name: 'T-Shirt Básica', price: 29.90, cost: 15.00, stock: 45, sold: 23, image: '👕', desc: 'T-shirt confortável em algodão' },
            { id: 2, name: 'Calças Jeans', price: 89.90, cost: 45.00, stock: 30, sold: 15, image: '👖', desc: 'Jeans de alta qualidade' },
            { id: 3, name: 'Vestido Verão', price: 79.90, cost: 35.00, stock: 20, sold: 12, image: '👗', desc: 'Vestido leve para o verão' }
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
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            ticks: { color: '#888' },
                            grid: { color: '#333' }
                        },
                        x: {
                            ticks: { color: '#888' },
                            grid: { color: '#333' }
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
                            labels: { color: '#888', padding: 15 }
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
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            ticks: { color: '#888' },
                            grid: { color: '#333' },
                            beginAtZero: true
                        },
                        x: {
                            ticks: { color: '#888' },
                            grid: { color: '#333' }
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
                            labels: { color: '#888', padding: 15 }
                        }
                    },
                    scales: {
                        r: {
                            ticks: { color: '#888', backdropColor: 'transparent' },
                            grid: { color: '#333' }
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
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            ticks: { 
                                color: '#888',
                                callback: function(value) {
                                    return value + '%';
                                }
                            },
                            grid: { color: '#333' },
                            beginAtZero: true
                        },
                        x: {
                            ticks: { color: '#888' },
                            grid: { color: '#333' }
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