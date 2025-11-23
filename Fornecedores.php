<?php
    session_start();

    if($_SESSION['tipo'] == 1){ 
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fornecedores - Dashboard</title>
    <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
    <link rel="stylesheet" href="src/css/fornecedores.css">
    <link rel="stylesheet" href="src/css/lib/datatables.css">
    <link rel="stylesheet" href="src/css/lib/select2.css">

    <script src="src/js/lib/jquery.js"></script>
    <script src="src/js/lib/datatables.js"></script>
    <script src="src/js/lib/select2.js"></script>
    <script src="src/js/lib/sweatalert.js"></script>
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <a href="index.html" style="text-decoration: none;">
                <div class="logo">
                    <span class="logo-icon">👔</span>
                    <div class="logo-text">
                        <h1>Wegreen</h1>
                        <p>Painel do Adminstrador</p>
                    </div>
                </div>
            </a>
            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="DashboardAdmin.php">
                            <span class="nav-icon">📊</span>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestaoProdutosAdmin.php">
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
                        <a class="nav-link" href="ProdutosAdmin.php">
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
                        <a class="nav-link active" href="fornecedores.php">
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
                <div class="profile-info" id="ProfileUser">
                </div>
                <button class="profile-settings-btn" onclick="showPage('settings')" title="Configurações">
                    <span>⚙️</span>
                </button>
            </div>
        </aside>

        <main class="main-content">
            <div class="page-header">
                <h2>🚚 Fornecedores</h2>
                <p>Gerencie seus fornecedores e parceiros comerciais</p>
            </div>

            <div class="action-bar">
                <div class="search-bar">
                    <input type="text" id="searchInput" placeholder="Pesquisar fornecedores...">
                    <span class="search-icon">🔍</span>
                </div>
                <button class="btn-primary" onclick="openModal()">
                    ➕ Adicionar Fornecedor
                </button>
            </div>

            <div class="table-container">
                <table id="suppliersTable">
                    <thead>
                        <tr>
                            <th>Fornecedor</th>
                            <th>Contato</th>
                            <th>Produtos</th>
                            <th>Avaliação</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="suppliersTableBody">

                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <div class="modal" id="supplierModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">➕ Novo Fornecedor</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>

            <form id="supplierForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nome do Fornecedor *</label>
                        <input type="text" id="supplierName" required>
                    </div>
                    <div class="form-group">
                        <label>Categoria</label>
                        <select id="supplierCategory">
                            <option value="Eletrônicos">Eletrônicos</option>
                            <option value="Alimentos">Alimentos</option>
                            <option value="Têxtil">Têxtil</option>
                            <option value="Materiais">Materiais</option>
                            <option value="Serviços">Serviços</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" id="supplierEmail" required>
                    </div>
                    <div class="form-group">
                        <label>Telefone *</label>
                        <input type="tel" id="supplierPhone" required>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Produtos Fornecidos</label>
                        <input type="text" id="supplierProducts" placeholder="Ex: Smartphones, Tablets">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select id="supplierStatus">
                            <option value="Ativo">Ativo</option>
                            <option value="Inativo">Inativo</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Endereço</label>
                    <textarea id="supplierAddress" rows="3"></textarea>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;">
                    💾 Salvar Fornecedor
                </button>
            </form>
        </div>
    </div>

    <script>
    // Dados de exemplo
    let suppliers = [{
            id: 1,
            name: "Tech Solutions Ltda",
            category: "Eletrônicos",
            email: "contato@techsolutions.com",
            phone: "+351 21 123 4567",
            products: "Smartphones, Tablets, Notebooks",
            rating: 5,
            status: "Ativo",
            address: "Av. da Liberdade, 123 - Lisboa"
        },
        {
            id: 2,
            name: "Food Masters",
            category: "Alimentos",
            email: "vendas@foodmasters.pt",
            phone: "+351 22 987 6543",
            products: "Alimentos Orgânicos, Cereais",
            rating: 4,
            status: "Ativo",
            address: "Rua do Comércio, 45 - Porto"
        },
        {
            id: 3,
            name: "Textile Pro",
            category: "Têxtil",
            email: "info@textilepro.com",
            phone: "+351 21 555 1234",
            products: "Tecidos, Roupas, Acessórios",
            rating: 4,
            status: "Inativo",
            address: "Zona Industrial, Lote 12 - Braga"
        },
        {
            id: 4,
            name: "BuildMat Materiais",
            category: "Materiais",
            email: "comercial@buildmat.pt",
            phone: "+351 23 456 7890",
            products: "Cimento, Tijolos, Ferramentas",
            rating: 5,
            status: "Ativo",
            address: "Estrada Nacional 10, Km 5 - Coimbra"
        }
    ];

    let currentEditId = null;

    // Renderizar tabela
    function renderTable(data = suppliers) {
        const tbody = document.getElementById('suppliersTableBody');

        if (data.length === 0) {
            tbody.innerHTML = `
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-state-icon">📦</div>
                                <h3>Nenhum fornecedor encontrado</h3>
                                <p>Adicione seu primeiro fornecedor para começar</p>
                            </div>
                        </td>
                    </tr>
                `;
            return;
        }

        tbody.innerHTML = data.map(supplier => {
            const stars = '⭐'.repeat(supplier.rating);
            const statusClass = supplier.status === 'Ativo' ? 'status-active' : 'status-inactive';
            const icon = getIconForCategory(supplier.category);

            return `
                    <tr>
                        <td>
                            <div class="supplier-info">
                                <div class="supplier-avatar">${icon}</div>
                                <div class="supplier-details">
                                    <div class="supplier-name">${supplier.name}</div>
                                    <div class="supplier-category">${supplier.category}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>${supplier.email}</div>
                            <div style="color: #888; font-size: 13px; margin-top: 4px;">${supplier.phone}</div>
                        </td>
                        <td>${supplier.products}</td>
                        <td>
                            <div class="rating-stars">${stars}</div>
                        </td>
                        <td>
                            <span class="status-badge ${statusClass}">${supplier.status}</span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-icon btn-edit" onclick="editSupplier(${supplier.id})" title="Editar">
                                    ✏️
                                </button>
                                <button class="btn-icon btn-delete" onclick="deleteSupplier(${supplier.id})" title="Excluir">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
        }).join('');
    }



    // Abrir modal
    function openModal(editId = null) {
        const modal = document.getElementById('supplierModal');
        const modalTitle = document.getElementById('modalTitle');
        const form = document.getElementById('supplierForm');

        currentEditId = editId;

        if (editId) {
            const supplier = suppliers.find(s => s.id === editId);
            modalTitle.textContent = '✏️ Editar Fornecedor';
            document.getElementById('supplierName').value = supplier.name;
            document.getElementById('supplierCategory').value = supplier.category;
            document.getElementById('supplierEmail').value = supplier.email;
            document.getElementById('supplierPhone').value = supplier.phone;
            document.getElementById('supplierProducts').value = supplier.products;
            document.getElementById('supplierStatus').value = supplier.status;
            document.getElementById('supplierAddress').value = supplier.address;
        } else {
            modalTitle.textContent = '➕ Novo Fornecedor';
            form.reset();
        }

        modal.classList.add('active');
    }

    // Fechar modal
    function closeModal() {
        const modal = document.getElementById('supplierModal');
        modal.classList.remove('active');
        currentEditId = null;
    }

    // Salvar fornecedor
    document.getElementById('supplierForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const supplierData = {
            name: document.getElementById('supplierName').value,
            category: document.getElementById('supplierCategory').value,
            email: document.getElementById('supplierEmail').value,
            phone: document.getElementById('supplierPhone').value,
            products: document.getElementById('supplierProducts').value,
            status: document.getElementById('supplierStatus').value,
            address: document.getElementById('supplierAddress').value,
            rating: 5
        };

        if (currentEditId) {
            const index = suppliers.findIndex(s => s.id === currentEditId);
            suppliers[index] = {
                ...suppliers[index],
                ...supplierData
            };
        } else {
            const newId = suppliers.length > 0 ? Math.max(...suppliers.map(s => s.id)) + 1 : 1;
            suppliers.push({
                id: newId,
                ...supplierData
            });
        }

        renderTable();
        closeModal();
    });

    // Editar fornecedor
    function editSupplier(id) {
        openModal(id);
    }

    // Excluir fornecedor
    function deleteSupplier(id) {
        if (confirm('Tem certeza que deseja excluir este fornecedor?')) {
            suppliers = suppliers.filter(s => s.id !== id);
            renderTable();
        }
    }

    // Pesquisa
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const filtered = suppliers.filter(supplier =>
            supplier.name.toLowerCase().includes(searchTerm) ||
            supplier.category.toLowerCase().includes(searchTerm) ||
            supplier.products.toLowerCase().includes(searchTerm)
        );
        renderTable(filtered);
    });

    // Fechar modal ao clicar fora
    document.getElementById('supplierModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Renderizar tabela inicial
    renderTable();
    </script>
    <script src="src/js/fornecedor.js"></script>
    <?php 
}else{
    echo "sem permissão!";
}

?>
</body>

</html>