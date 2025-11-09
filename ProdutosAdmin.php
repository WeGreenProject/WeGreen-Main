<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aprovação de Produtos - Fashion Store</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #0a0a0a;
        color: #fff;
        overflow-x: hidden;
    }

    .container {
        display: flex;
        min-height: 100vh;
    }

    .sidebar {
        width: 280px;
        background: linear-gradient(180deg, #1c1c1c 0%, #121212 100%);
        border-right: 1px solid #2a2a2a;
        position: fixed;
        height: 100vh;
        z-index: 100;
        box-shadow: 2px 0 20px rgba(0, 0, 0, 0.5);
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 35px 25px;
        margin-bottom: 20px;
        border-bottom: 1px solid #2a2a2a;
        background: #1a1a1a;
    }

    .logo-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        box-shadow: 0 4px 12px rgba(255, 215, 0, 0.25);
    }

    .logo-text h1 {
        font-size: 20px;
        color: #ffffff;
        font-weight: 600;
        letter-spacing: 0.3px;
        margin-bottom: 2px;
    }

    .logo-text p {
        font-size: 11px;
        color: #6b6b6b;
        font-weight: 400;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .nav-menu {
        list-style: none;
        padding: 15px 0;
    }

    .nav-item {
        margin-bottom: 2px;
        padding: 0 15px;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 18px;
        background: transparent;
        border: none;
        color: #9a9a9a;
        cursor: pointer;
        border-radius: 10px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        width: 100%;
        text-align: left;
        font-size: 15px;
        font-weight: 500;
        letter-spacing: 0.2px;
        position: relative;
        text-decoration: none;
    }

    .nav-link::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 0;
        background: #ffd700;
        border-radius: 0 3px 3px 0;
        transition: height 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .nav-link:hover {
        background: rgba(255, 215, 0, 0.08);
        color: #ffd700;
    }

    .nav-link:hover::before {
        height: 24px;
    }

    .nav-link.active {
        background: rgba(255, 215, 0, 0.12);
        color: #ffd700;
        font-weight: 600;
    }

    .nav-link.active::before {
        height: 32px;
    }

    .nav-icon {
        font-size: 18px;
        width: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .user-profile {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 20px 15px;
        background: linear-gradient(180deg, transparent 0%, #0a0a0a 50%);
        border-top: 1px solid #2a2a2a;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .profile-info {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
    }

    .profile-avatar {
        position: relative;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid #ffd700;
        box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);
        font-size: 20px;
    }

    .profile-details {
        flex: 1;
        min-width: 0;
    }

    .profile-name {
        font-size: 14px;
        font-weight: 600;
        color: #fff;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 2px;
    }

    .profile-role {
        font-size: 11px;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .profile-settings-btn {
        width: 36px;
        height: 36px;
        background: rgba(255, 215, 0, 0.1);
        border: 1px solid #333;
        border-radius: 8px;
        color: #ffd700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .profile-settings-btn:hover {
        background: rgba(255, 215, 0, 0.2);
        border-color: #ffd700;
        transform: rotate(90deg);
    }

    .main-content {
        flex: 1;
        margin-left: 280px;
        padding: 40px;
        background: #0a0a0a;
    }

    .page-header {
        margin-bottom: 40px;
    }

    .page-header h2 {
        font-size: 36px;
        color: #ffd700;
        margin-bottom: 10px;
    }

    .page-header p {
        color: #888;
        font-size: 16px;
    }

    .approval-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .approval-stat-card {
        background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
        border: 2px solid #333;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.3s ease;
    }

    .approval-stat-card:hover {
        border-color: #ffd700;
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(255, 215, 0, 0.2);
    }

    .approval-stat-icon {
        font-size: 40px;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 215, 0, 0.1);
        border-radius: 12px;
    }

    .approval-stat-info {
        flex: 1;
    }

    .approval-stat-value {
        font-size: 32px;
        font-weight: 700;
        color: #ffd700;
        margin-bottom: 5px;
    }

    .approval-stat-label {
        font-size: 13px;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .approval-filters {
        display: flex;
        gap: 10px;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }

    .filter-btn {
        background: #1a1a1a;
        border: 2px solid #333;
        color: #888;
        padding: 12px 24px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-btn:hover {
        border-color: #ffd700;
        color: #ffd700;
    }

    .filter-btn.active {
        background: linear-gradient(90deg, #ffd700 0%, #ffed4e 100%);
        border-color: #ffd700;
        color: #000;
    }

    .filter-badge {
        background: rgba(255, 255, 255, 0.2);
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 700;
    }

    .filter-btn.active .filter-badge {
        background: rgba(0, 0, 0, 0.2);
    }

    .table-container {
        background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
        border: 2px solid #333;
        border-radius: 20px;
        padding: 30px;
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: rgba(255, 215, 0, 0.1);
    }

    th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        color: #ffd700;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #ffd700;
    }

    td {
        padding: 15px;
        border-bottom: 1px solid #333;
        color: #fff;
    }

    tr:hover {
        background: rgba(255, 215, 0, 0.05);
    }

    .product-cell {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .product-icon {
        font-size: 35px;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 215, 0, 0.1);
        border-radius: 10px;
    }

    .product-info h4 {
        font-size: 16px;
        margin-bottom: 4px;
        color: #fff;
    }

    .product-info p {
        font-size: 12px;
        color: #888;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
    }

    .status-pending {
        background: rgba(255, 193, 7, 0.2);
        color: #ffc107;
        border: 1px solid #ffc107;
    }

    .status-approved {
        background: rgba(76, 175, 80, 0.2);
        color: #4caf50;
        border: 1px solid #4caf50;
    }

    .status-rejected {
        background: rgba(244, 67, 54, 0.2);
        color: #f44336;
        border: 1px solid #f44336;
    }

    .price-cell {
        font-weight: 600;
        color: #ffd700;
    }

    .profit-cell {
        font-weight: 600;
        color: #4caf50;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-approve,
    .btn-reject,
    .btn-details {
        padding: 8px 12px;
        border: none;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-approve {
        background: linear-gradient(90deg, #4caf50 0%, #66bb6a 100%);
        color: #fff;
    }

    .btn-approve:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.4);
    }

    .btn-reject {
        background: linear-gradient(90deg, #f44336 0%, #e57373 100%);
        color: #fff;
    }

    .btn-reject:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(244, 67, 54, 0.4);
    }

    .btn-details {
        background: rgba(255, 215, 0, 0.1);
        color: #ffd700;
        border: 1px solid #ffd700;
    }

    .btn-details:hover {
        background: rgba(255, 215, 0, 0.2);
    }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
        display: none;
    }

    .empty-icon {
        font-size: 100px;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .empty-state h3 {
        font-size: 24px;
        color: #fff;
        margin-bottom: 10px;
    }

    .empty-state p {
        color: #888;
        font-size: 16px;
    }

    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(10px);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
        border: 2px solid #ffd700;
        border-radius: 20px;
        padding: 40px;
        max-width: 700px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .modal-header h3 {
        font-size: 28px;
        color: #ffd700;
    }

    .close-btn {
        background: transparent;
        border: none;
        color: #888;
        font-size: 30px;
        cursor: pointer;
        transition: color 0.3s;
    }

    .close-btn:hover {
        color: #ffd700;
    }

    .modal-detail-section {
        margin-bottom: 25px;
    }

    .modal-detail-section h4 {
        color: #ffd700;
        margin-bottom: 15px;
        font-size: 18px;
    }

    .modal-detail-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }

    .modal-detail-item {
        background: #0a0a0a;
        padding: 15px;
        border-radius: 10px;
        border: 1px solid #333;
    }

    .modal-detail-label {
        font-size: 12px;
        color: #888;
        margin-bottom: 5px;
        text-transform: uppercase;
    }

    .modal-detail-value {
        font-size: 16px;
        color: #fff;
        font-weight: 600;
    }

    .modal-actions {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }

    .modal-actions button {
        flex: 1;
        padding: 14px;
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 80px;
        }

        .logo-text {
            display: none;
        }

        .nav-text {
            display: none;
        }

        .main-content {
            margin-left: 80px;
            padding: 20px;
        }

        .modal-detail-grid {
            grid-template-columns: 1fr;
        }

        .profile-details {
            display: none;
        }

        .user-profile {
            justify-content: center;
            gap: 15px;
        }

        .table-container {
            overflow-x: auto;
        }

        .action-buttons {
            flex-direction: column;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <a href="index.html" style="text-decoration: none;">
                <div class="logo">
                    <span class="logo-icon">👔</span>
                    <div class="logo-text">
                        <h1>Fashion Store</h1>
                        <p>Painel do Administrador</p>
                    </div>
                </div>
            </a>
            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php">
                            <span class="nav-icon">📊</span>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="produtos.php">
                            <span class="nav-icon">📦</span>
                            <span class="nav-text">Produtos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="vendas.php">
                            <span class="nav-icon">🛍️</span>
                            <span class="nav-text">Vendas</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="aprovar.php">
                            <span class="nav-icon">🛒</span>
                            <span class="nav-text">Aprovar Produtos</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="analytics.php">
                            <span class="nav-icon">📈</span>
                            <span class="nav-text">Análises</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="chats.php">
                            <span class="nav-icon">💬</span>
                            <span class="nav-text">Chats</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="fornecedores.php">
                            <span class="nav-icon">🚚</span>
                            <span class="nav-text">Gestão de Fornecedores</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="settings.php">
                            <span class="nav-icon">⚙️</span>
                            <span class="nav-text">Configurações</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="user-profile">
                <div class="profile-info">
                    <div class="profile-avatar">👤</div>
                    <div class="profile-details">
                        <div class="profile-name">Administrador</div>
                        <div class="profile-role">Admin</div>
                    </div>
                </div>
                <button class="profile-settings-btn" title="Configurações">
                    <span>⚙️</span>
                </button>
            </div>
        </aside>

        <main class="main-content">
            <div class="page-header">
                <h2>Aprovar Produtos</h2>
                <p>Revise e aprove produtos pendentes de fornecedores</p>
            </div>

            <!-- Estatísticas de Aprovação -->
            <div class="approval-stats">
                <div class="approval-stat-card">
                    <div class="approval-stat-icon">⏳</div>
                    <div class="approval-stat-info">
                        <div class="approval-stat-value" id="pendingCount">0</div>
                        <div class="approval-stat-label">Pendentes</div>
                    </div>
                </div>
                <div class="approval-stat-card">
                    <div class="approval-stat-icon">✅</div>
                    <div class="approval-stat-info">
                        <div class="approval-stat-value" id="approvedCount">0</div>
                        <div class="approval-stat-label">Aprovados Hoje</div>
                    </div>
                </div>
                <div class="approval-stat-card">
                    <div class="approval-stat-icon">❌</div>
                    <div class="approval-stat-info">
                        <div class="approval-stat-value" id="rejectedCount">0</div>
                        <div class="approval-stat-label">Rejeitados Hoje</div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="approval-filters">
                <button class="filter-btn active" onclick="filterApproval('all')">
                    Todos <span class="filter-badge" id="allBadge">0</span>
                </button>
                <button class="filter-btn" onclick="filterApproval('pending')">
                    Pendentes <span class="filter-badge" id="pendingBadge">0</span>
                </button>
                <button class="filter-btn" onclick="filterApproval('approved')">
                    Aprovados <span class="filter-badge" id="approvedBadge">0</span>
                </button>
                <button class="filter-btn" onclick="filterApproval('rejected')">
                    Rejeitados <span class="filter-badge" id="rejectedBadge">0</span>
                </button>
            </div>

            <!-- Tabela de Produtos -->
            <div class="table-container" id="tableContainer">
                <table>
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Fornecedor</th>
                            <th>Preço</th>
                            <th>Custo</th>
                            <th>Lucro</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody">
                    </tbody>
                </table>
            </div>

            <!-- Mensagem quando não há produtos -->
            <div class="empty-state" id="emptyState">
                <div class="empty-icon">📦</div>
                <h3>Nenhum produto para mostrar</h3>
                <p>Não há produtos com este filtro</p>
            </div>
        </main>
    </div>

    <!-- Modal de Detalhes do Produto -->
    <div id="approvalModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Detalhes do Produto</h3>
                <button class="close-btn" onclick="closeApprovalModal()">×</button>
            </div>
            <div id="approvalModalContent"></div>
        </div>
    </div>

    <script>
    let approvalProducts = [{
            id: 101,
            name: 'Casaco de Inverno',
            price: 149.90,
            cost: 75.00,
            stock: 50,
            image: '🧥',
            desc: 'Casaco quente e confortável para o inverno',
            supplier: 'Fashion Supplies Ltd.',
            supplierEmail: 'contact@fashionsupplies.com',
            category: 'Outerwear',
            material: '80% Lã, 20% Poliéster',
            status: 'pending',
            submittedDate: '2025-11-08',
            estimatedProfit: 74.90
        },
        {
            id: 102,
            name: 'Tênis Esportivo',
            price: 119.90,
            cost: 60.00,
            stock: 80,
            image: '👟',
            desc: 'Tênis confortável para corrida e treino',
            supplier: 'SportWear Pro',
            supplierEmail: 'sales@sportwearpro.com',
            category: 'Calçado',
            material: 'Sintético',
            status: 'pending',
            submittedDate: '2025-11-07',
            estimatedProfit: 59.90
        },
        {
            id: 103,
            name: 'Camisa Social',
            price: 89.90,
            cost: 40.00,
            stock: 60,
            image: '👔',
            desc: 'Camisa elegante para ocasiões formais',
            supplier: 'Elegant Fashion',
            supplierEmail: 'info@elegantfashion.com',
            category: 'Camisas',
            material: '100% Algodão',
            status: 'approved',
            submittedDate: '2025-11-06',
            estimatedProfit: 49.90
        },
        {
            id: 104,
            name: 'Saia Plissada',
            price: 69.90,
            cost: 35.00,
            stock: 40,
            image: '👗',
            desc: 'Saia moderna e versátil',
            supplier: 'Fashion Supplies Ltd.',
            supplierEmail: 'contact@fashionsupplies.com',
            category: 'Saias',
            material: 'Poliéster',
            status: 'rejected',
            submittedDate: '2025-11-05',
            estimatedProfit: 34.90
        },
        {
            id: 105,
            name: 'Jaqueta Jeans',
            price: 129.90,
            cost: 65.00,
            stock: 35,
            image: '🧥',
            desc: 'Jaqueta jeans clássica e atemporal',
            supplier: 'Denim World',
            supplierEmail: 'info@denimworld.com',
            category: 'Jaquetas',
            material: '100% Algodão Denim',
            status: 'pending',
            submittedDate: '2025-11-09',
            estimatedProfit: 64.90
        },
        {
            id: 106,
            name: 'Botas de Couro',
            price: 199.90,
            cost: 100.00,
            stock: 25,
            image: '🥾',
            desc: 'Botas de couro premium de alta qualidade',
            supplier: 'Leather Goods Co.',
            supplierEmail: 'sales@leathergoods.com',
            category: 'Calçado',
            material: 'Couro Genuíno',
            status: 'pending',
            submittedDate: '2025-11-09',
            estimatedProfit: 99.90
        },
        {
            id: 107,
            name: 'Vestido Elegante',
            price: 159.90,
            cost: 80.00,
            stock: 30,
            image: '👗',
            desc: 'Vestido sofisticado para ocasiões especiais',
            supplier: 'Elegant Fashion',
            supplierEmail: 'info@elegantfashion.com',
            category: 'Vestidos',
            material: 'Seda',
            status: 'approved',
            submittedDate: '2025-11-04',
            estimatedProfit: 79.90
        }
    ];

    let currentFilter = 'all';

    function updateApprovalStats() {
        const pending = approvalProducts.filter(p => p.status === 'pending').length;
        const approved = approvalProducts.filter(p => p.status === 'approved').length;
        const rejected = approvalProducts.filter(p => p.status === 'rejected').length;

        document.getElementById('pendingCount').textContent =