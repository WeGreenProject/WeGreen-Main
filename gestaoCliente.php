    <!DOCTYPE html>
    <html lang="pt">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestão de Clientes - WeGreen Admin</title>
        <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
        <link rel="stylesheet" href="src/css/lib/datatables.css">
        <link rel="stylesheet" href="src/css/lib/select2.css">

        <script src="src/js/lib/jquery.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>
        <script src="src/js/lib/datatables.js"></script>
        <script src="src/js/lib/select2.js"></script>
        <script src="src/js/lib/sweatalert.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
        <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            color: #1a202c;
            overflow-x: hidden;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #2d3748 0%, #1a202c 100%);
            padding: 0;
            position: fixed;
            height: 100vh;
            z-index: 100;
            box-shadow: 2px 0 20px rgba(0, 0, 0, 0.08);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 35px 25px;
            margin-bottom: 20px;
            border-bottom: 1px solid #4a5568;
            background: #2d3748;
            text-decoration: none;
        }

        .logo-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #A6D90C 0%, #90c207 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #1a202c;
            box-shadow: 0 4px 12px rgba(166, 217, 12, 0.3);
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
            color: #a0aec0;
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
            color: #cbd5e0;
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
            background: #A6D90C;
            border-radius: 0 3px 3px 0;
            transition: height 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-link:hover {
            background: rgba(166, 217, 12, 0.1);
            color: #A6D90C;
        }

        .nav-link:hover::before {
            height: 24px;
        }

        .nav-link.active {
            background: rgba(166, 217, 12, 0.15);
            color: #A6D90C;
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

        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 0;
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
        }

        .top-navbar {
            background: #2d3748;
            border-bottom: 1px solid #4a5568;
            padding: 8px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .navbar-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .navbar-icon {
            font-size: 16px;
            color: #A6D90C;
        }

        .navbar-left .navbar-title {
            font-size: 16px;
            color: #ffffff;
            font-weight: 600;
            margin: 0;
            letter-spacing: -0.3px;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .navbar-icon-btn {
            position: relative;
            background: #ffffff;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            color: #4a5568;
        }

        .navbar-icon-btn:hover {
            background: #A6D90C;
            color: #1a202c;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #e53e3e;
            color: #ffffff;
            font-size: 10px;
            font-weight: 600;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #2d3748;
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 12px;
            background: #ffffff;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }

        .navbar-user:hover {
            background: #A6D90C;
        }

        .navbar-user:hover .user-name,
        .navbar-user:hover .user-role,
        .navbar-user:hover i {
            color: #1a202c;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #A6D90C;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1a202c;
            font-size: 18px;
            font-weight: 600;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: #1a202c;
            line-height: 1.2;
            transition: color 0.3s;
        }

        .user-role {
            font-size: 11px;
            color: #718096;
            font-weight: 400;
            line-height: 1.2;
            transition: color 0.3s;
        }

        .page {
            padding: 30px 40px;
            overflow-y: auto;
        }

        .page-header {
            margin-bottom: 40px;
        }

        .page-header h2 {
            font-size: 36px;
            color: #1a202c;
            margin-bottom: 10px;
        }

        .page-header p {
            color: #718096;
            font-size: 16px;
        }

        .page-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(90deg, #A6D90C 0%, #90c207 100%);
            color: #1a202c;
            box-shadow: 0 4px 15px rgba(166, 217, 12, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(166, 217, 12, 0.4);
        }

        .btn-secondary {
            background: #2d3748;
            color: #ffffff;
        }

        .btn-secondary:hover {
            background: #1a202c;
            transform: translateY(-2px);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
            border: 1px solid #4a5568;
            border-left: 4px solid #A6D90C;
            border-radius: 20px;
            padding: 35px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            min-height: 180px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .stat-card:hover {
            border-left-color: #8fb80a;
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(166, 217, 12, 0.2);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(166, 217, 12, 0.12) 0%, transparent 70%);
        }

        .stat-icon {
            font-size: 48px;
            margin-bottom: 18px;
            filter: drop-shadow(0 4px 8px rgba(166, 217, 12, 0.3));
            color: #A6D90C;
        }

        .stat-label {
            color: #cbd5e0;
            font-size: 12px;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 600;
        }

        .stat-value {
            font-size: 48px;
            font-weight: 800;
            color: #A6D90C;
            line-height: 1;
            text-shadow: 0 2px 6px rgba(166, 217, 12, 0.25);
        }

        .filters {
            display: flex;
            gap: 12px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .filters select,
        .filters input {
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            background: #ffffff;
            color: #1a202c;
            font-size: 14px;
            min-width: 200px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .filters select:focus,
        .filters input:focus {
            outline: none;
            border-color: #A6D90C;
            box-shadow: 0 0 0 3px rgba(166, 217, 12, 0.1);
        }

        .table-container {
            background: #ffffff;
            border-radius: 15px;
            padding: 25px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        table.display {
            width: 100% !important;
        }

        table.display thead th {
            background: linear-gradient(135deg, #A6D90C 0%, #90c207 100%) !important;
            color: #1a202c !important;
            font-weight: 600 !important;
            padding: 12px !important;
            text-align: left !important;
            font-size: 13px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }

        table.display tbody td {
            padding: 12px !important;
            border-bottom: 1px solid #e2e8f0 !important;
            vertical-align: middle !important;
            color: #1a202c !important;
            font-size: 14px !important;
        }

        table.display tbody tr:hover {
            background: #f0f9f0 !important;
        }

        .client-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .client-avatar {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid #e2e8f0;
        }

        .client-details {
            display: flex;
            flex-direction: column;
        }

        .client-name {
            font-weight: 600;
            color: #1a202c;
        }

        .client-email {
            font-size: 12px;
            color: #718096;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-cliente {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-anunciante {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-admin {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-active {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-action {
            width: 36px;
            height: 36px;
            border: none;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-view {
            background: #e0e7ff;
            color: #3730a3;
        }

        .btn-view:hover {
            background: #c7d2fe;
            transform: translateY(-2px);
        }

        .btn-edit {
            background: #dbeafe;
            color: #1e40af;
        }

        .btn-edit:hover {
            background: #bfdbfe;
            transform: translateY(-2px);
        }

        .btn-delete {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-delete:hover {
            background: #fecaca;
            transform: translateY(-2px);
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
            background: #ffffff;
            border: 2px solid #A6D90C;
            border-radius: 20px;
            padding: 30px;
            max-width: 700px;
            width: 95%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
            padding: 20px;
            border-radius: 12px;
            margin: -30px -30px 30px -30px;
        }

        .modal-header h3 {
            font-size: 24px;
            color: #ffffff;
        }

        .close-btn {
            background: transparent;
            border: none;
            color: #ffffff;
            font-size: 30px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .close-btn:hover {
            color: #A6D90C;
            transform: rotate(90deg);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 12px;
        }

        .form-row-full {
            margin-bottom: 12px;
        }

        .form-col {
            display: flex;
            flex-direction: column;
        }

        .modal-content label {
            display: block;
            margin-bottom: 6px;
            color: #2d3748;
            font-weight: 600;
            font-size: 14px;
        }

        .modal-content input,
        .modal-content select,
        .modal-content textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            background: #f8f9fa;
            color: #1a202c;
            font-size: 14px;
            transition: all 0.3s;
        }

        .modal-content input:focus,
        .modal-content select:focus,
        .modal-content textarea:focus {
            outline: none;
            border-color: #A6D90C;
            background: #ffffff;
        }

        .client-view-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }

        .info-group {
            margin-bottom: 15px;
        }

        .info-group label {
            font-size: 11px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
            display: block;
        }

        .info-group span {
            font-size: 15px;
            color: #2d3748;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
            }

            .logo-text,
            .nav-text {
                display: none;
            }

            .main-content {
                margin-left: 80px;
            }

            .page {
                padding: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .top-navbar {
                padding: 8px 20px;
            }

            .form-row,
            .client-view-grid {
                grid-template-columns: 1fr;
            }
        }

        #clientModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            z-index: 1000;
            overflow-y: auto;
            /* Permite scroll se o conteúdo for maior que a tela */
        }

        #clientModal.active {
            display: block;
        }

        #clientModal .modal-content {
            background: #ffffff;
            border: 2px solid #A6D90C;
            border-radius: 20px;
            padding: 30px;
            max-width: 700px;
            width: 95%;
            max-height: 90vh;
            overflow-y: auto;
            /* Centralização perfeita */
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            /* Animação suave */
            animation: modalFadeIn 0.3s ease-out;
        }
        </style>
    </head>

    <body>
        <div class="container">
            <aside class="sidebar">
                <a href="index.html" class="logo">
                    <span class="logo-icon"><i class="fas fa-leaf"></i></span>
                    <div class="logo-text">
                        <h1>WeGreen</h1>
                        <p>Painel do Administrador</p>
                    </div>
                </a>
                <nav>
                    <ul class="nav-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="DashboardAdmin.php">
                                <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
                                <span class="nav-text">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="gestaoProdutosAdmin.php">
                                <span class="nav-icon"><i class="fas fa-tshirt"></i></span>
                                <span class="nav-text">Produtos</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="gestaoCliente.php">
                                <span class="nav-icon"><i class="fas fa-users"></i></span>
                                <span class="nav-text">Gestão de Clientes</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="ProdutosAdmin.php">
                                <span class="nav-icon"><i class="fas fa-shopping-bag"></i></span>
                                <span class="nav-text">Encomendas</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="analytics.php">
                                <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
                                <span class="nav-text">Análises</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="Chatadmin.php">
                                <span class="nav-icon"><i class="fas fa-comments"></i></span>
                                <span class="nav-text">Chats</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="fornecedores.php">
                                <span class="nav-icon"><i class="fas fa-truck"></i></span>
                                <span class="nav-text">Fornecedores</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="perfilAdmin.php">
                                <span class="nav-icon"><i class="fas fa-cog"></i></span>
                                <span class="nav-text">Configurações</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </aside>

            <main class="main-content">
                <nav class="top-navbar">
                    <div class="navbar-left">
                        <i class="navbar-icon fas fa-users"></i>
                        <h2 class="navbar-title">Gestão de Clientes</h2>
                    </div>
                    <div class="navbar-right">
                        <button class="navbar-icon-btn">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge">3</span>
                        </button>
                        <div class="navbar-user">
                            <div class="user-avatar">A</div>
                            <div class="user-info">
                                <span class="user-name">Administrador</span>
                                <span class="user-role">Admin</span>
                            </div>
                            <i class="fas fa-chevron-down" style="font-size: 12px; color: #4a5568;"></i>
                        </div>
                    </div>
                </nav>

                <div class="page">
                    <div class="page-header">
                        <h2>Gestão de Clientes</h2>
                        <p>Administre todos os utilizadores da plataforma</p>
                    </div>

                    <div class="page-actions">
                        <button class="btn btn-primary" onclick="showModal()">
                            <i class="fas fa-plus"></i> Adicionar Cliente
                        </button>
                        <button class="btn btn-secondary" onclick="exportClients()">
                            <i class="fas fa-file-export"></i> Exportar Lista
                        </button>
                    </div>

                    <div class="stats-grid" id="CardTipoutilizadores">

                    </div>

                    <div class="filters">
                        <select id="filterTipo" onchange="filterTable()">
                            <option value="">Todos os Tipos</option>
                            <option value="1">Administrador</option>
                            <option value="2">Cliente</option>
                            <option value="3">Anunciante</option>
                        </select>
                    </div>

                    <div class="table-container">
                        <table id="clientsTable" class="display">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Tipo</th>
                                    <th>Telefone</th>
                                    <th>Data Registo</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="clientsTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>

        <div id="clientModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id="modalTitle">Adicionar Cliente</h3>
                    <button class="close-btn" onclick="closeModal()">&times;</button>
                </div>
                <form id="clientForm" onsubmit="saveClient(event)">
                    <input type="hidden" id="clientId">
                    <div class="form-row">
                        <div class="form-col">
                            <label>Nome Completo </label>
                            <input type="text" id="clientNome" required>
                        </div>
                        <div class="form-col">
                            <label>Email</label>
                            <input type="email" id="clientEmail" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-col">
                            <label>Telefone</label>
                            <input type="tel" id="clientTelefone" placeholder="+351 912 345 678">
                        </div>
                        <div class="form-col">
                            <label>Tipo de Utilizador</label>
                            <select id="clientTipo" required>
                                <option value="">Selecione...</option>
                                <option value="1">Administrador</option>
                                <option value="2">Cliente</option>
                                <option value="3">Anunciante</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row-full">
                        <div class="form-col">
                            <label>NIF</label>
                            <input type="text" id="clientNif" placeholder="123456789">
                        </div>
                    </div>
                    <div class="form-row-full">
                        <label>Morada</label>
                        <input type="text" id="clientMorada" placeholder="Rua, número, andar">
                    </div>
                    <div class="form-row-full">
                        <label>Imagem</label>
                        <input type="file" id="imagemClient" placeholder="Rua, número, andar">
                    </div>
                    <div class="form-row">
                    </div>
                    <div class="form-row" id="passwordSection">
                        <div class="form-col">
                            <label>Senha</label>
                            <input type="password" id="clientPassword">
                        </div>
                        <div class="form-col">
                            <label>Confirmar Senha</label>
                            <input type="password" id="clientPasswordConfirm">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" style="width: 100%; margin-top: 15px;"
                        onclick="registaClientes()">
                        <i class="fas fa-save"></i> Salvar Cliente
                    </button>

                </form>
            </div>
        </div>

        <div id="viewModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Detalhes do Cliente</h3>
                    <button class="close-btn" onclick="closeViewModal()">&times;</button>
                </div>
                <div class="client-view-grid">
                    <div class="info-group">
                        <label>Nome Completo</label>
                        <span id="viewNome">-</span>
                    </div>
                    <div class="info-group">
                        <label>Email</label>
                        <span id="viewEmail">-</span>
                    </div>
                    <div class="info-group">
                        <label>Telefone</label>
                        <span id="viewTelefone">-</span>
                    </div>
                    <div class="info-group">
                        <label>Tipo</label>
                        <span id="viewTipo">-</span>
                    </div>
                    <div class="info-group">
                        <label>NIF</label>
                        <span id="viewNif">-</span>
                    </div>
                    <div class="info-group">
                        <label>Data Nascimento</label>
                        <span id="viewDataNascimento">-</span>
                    </div>
                    <div class="info-group" style="grid-column: 1 / -1;">
                        <label>Morada</label>
                        <span id="viewMorada">-</span>
                    </div>
                    <div class="info-group">
                        <label>Código Postal</label>
                        <span id="viewCodigoPostal">-</span>
                    </div>
                    <div class="info-group">
                        <label>Localidade</label>
                        <span id="viewLocalidade">-</span>
                    </div>
                    <div class="info-group">
                        <label>Data Registo</label>
                        <span id="viewDataRegisto">-</span>
                    </div>
                    <div class="info-group">
                        <label>Status</label>
                        <span id="viewStatus">-</span>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="src/js/ClientesAdmin.js"></script>
    </body>

    </html>