<?php
session_start();

if(!isset($_SESSION['utilizador']) || $_SESSION['tipo'] != 2){
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Meus Favoritos - WeGreen</title>
  <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
  <link rel="stylesheet" href="src/css/DashboardCliente.css">
  <link rel="stylesheet" href="assets/css/notifications-dropdown.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <script src="src/js/lib/jquery.js"></script>
  <script src="src/js/lib/sweatalert.js"></script>
  <script src="src/js/notifications.js"></script>
  <script src="assets/js/custom/favoritos.js"></script>

  <style>
  .favoritos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 24px;
    margin-top: 24px;
  }

  .produto-favorito-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    position: relative;
  }

  .produto-favorito-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  }

  .produto-foto {
    width: 100%;
    height: 320px;
    object-fit: cover;
    background: #f5f5f5;
  }

  .produto-info {
    padding: 20px;
  }

  .produto-nome {
    font-size: 16px;
    font-weight: 600;
    color: #111;
    margin-bottom: 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .produto-preco {
    font-size: 22px;
    font-weight: 700;
    color: #3cb371;
    margin-bottom: 12px;
  }

  .produto-detalhes {
    font-size: 13px;
    color: #666;
    margin-bottom: 8px;
  }

  .produto-status {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 16px;
  }

  .status-disponivel {
    background: #d4edda;
    color: #155724;
  }

  .status-indisponivel {
    background: #f8d7da;
    color: #721c24;
  }

  .produto-acoes {
    display: flex;
    gap: 8px;
  }

  .btn-action {
    flex: 1;
    padding: 10px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
  }

  .btn-carrinho {
    background: #3cb371;
    color: white;
  }

  .btn-carrinho:hover {
    background: #2e8b57;
  }

  .btn-remover {
    background: #f5f5f5;
    color: #666;
  }

  .btn-remover:hover {
    background: #ef4444;
    color: white;
  }

  .btn-remover:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  .data-adicao {
    font-size: 12px;
    color: #999;
    margin-top: 12px;
    text-align: center;
  }

  /* Removido CSS antigo de filtros - agora usa inline styles */

  /* Button Continue Shopping */
  .btn-continue-shopping {
    background: linear-gradient(135deg, #3cb371, #2e8b57);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(60, 179, 113, 0.3);
  }

  .btn-continue-shopping:hover {
    background: linear-gradient(135deg, #2e8b57, #228b50);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(60, 179, 113, 0.4);
  }

  .btn-continue-shopping i {
    font-size: 16px;
  }

  /* Button Limpar Inativos */
  .btn-limpar-inativos {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
  }

  .btn-limpar-inativos:hover {
    background: linear-gradient(135deg, #d97706, #b45309);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(245, 158, 11, 0.4);
  }

  .btn-limpar-inativos i {
    font-size: 16px;
  }
  </style>
</head>

<body>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <a href="index.html" class="sidebar-logo" style="text-decoration: none; color: inherit; cursor: pointer;">
        <i class="fas fa-leaf"></i>
        <div class="logo-text">
          <h2>WeGreen</h2>
          <p>Moda Sustentável</p>
        </div>
      </a>

      <nav class="sidebar-menu">
        <div class="menu-section">
          <div class="menu-section-title">Menu</div>
          <a href="DashboardCliente.php" class="menu-item">
            <i class="fas fa-home"></i>
            <span>Início</span>
          </a>
          <a href="minhasEncomendas.php" class="menu-item">
            <i class="fas fa-shopping-bag"></i>
            <span>Minhas Encomendas</span>
          </a>
          <a href="meusFavoritos.php" class="menu-item active">
            <i class="fas fa-heart"></i>
            <span>Meus Favoritos</span>
            <span class="badge" id="sidebarFavCount"
              style="display:none; background:#3cb371; color:white; padding:2px 8px; border-radius:10px; font-size:11px; margin-left:auto;"></span>
          </a>
          <a href="ChatCliente.php" class="menu-item">
            <i class="fas fa-comments"></i>
            <span>Chat</span>
          </a>
        </div>
      </nav>
    </aside>

    <!-- Conteúdo Principal -->
    <main class="main-content">
      <nav class="top-navbar">
        <div class="navbar-left">
          <h1 class="page-title"><i class="fas fa-heart"></i> Meus Favoritos</h1>
        </div>
        <div class="navbar-right">
          <?php include 'src/views/notifications-widget.php'; ?>
          <div class="navbar-user" id="userMenuBtn">
            <img
              src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'Cliente'); ?>&background=3cb371&color=fff"
              alt="User" class="user-avatar">
            <div class="user-info">
              <span class="user-name"><?php echo $_SESSION['nome'] ?? 'Cliente'; ?></span>
              <span class="user-role">Cliente</span>
            </div>
            <i class="fas fa-chevron-down" style="font-size: 12px; color: #64748b;"></i>
          </div>
          <div class="user-dropdown" id="userDropdown">
            <div class="dropdown-header">
              <img
                src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'Cliente'); ?>&background=3cb371&color=fff"
                alt="User" class="dropdown-avatar">
              <div>
                <div class="dropdown-name"><?php echo $_SESSION['nome'] ?? 'Cliente'; ?></div>
                <div class="dropdown-email"><?php echo $_SESSION['email'] ?? ''; ?></div>
              </div>
            </div>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="perfilCliente.php">
              <i class="fas fa-user"></i>
              <span>Meu Perfil</span>
            </a>
            <a class="dropdown-item" href="alterarSenha.php">
              <i class="fas fa-key"></i>
              <span>Alterar Senha</span>
            </a>
            <div class="dropdown-divider"></div>
            <button class="dropdown-item dropdown-item-danger" onclick="logout()">
              <i class="fas fa-sign-out-alt"></i>
              <span>Sair</span>
            </button>
          </div>
        </div>
      </nav>
      <div class="page-content">
        <div class="content-area" style="padding-top: 20px;">
          <!-- Botões de Ação -->
          <div style="margin-bottom: 20px; display: flex; justify-content: flex-end; gap: 12px;">
            <button class="btn-limpar-inativos" onclick="limparInativos()">
              <i class="fas fa-broom"></i>
              <span>Limpar Inativos</span>
            </button>
            <button class="btn-continue-shopping" onclick="window.location.href='marketplace.html'">
              <i class="fas fa-shopping-cart"></i>
              <span>Continuar a Comprar</span>
            </button>
          </div>
          <!-- Filtros -->
          <div class="filtros-favoritos"
            style="background: #fff; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 15px; align-items: end;">
              <div>
                <label style="display: block; margin-bottom: 8px; font-size: 14px; color: #666; font-weight: 500;">
                  <i class="fas fa-search" style="color: #3cb371; margin-right: 4px;"></i> Pesquisar
                </label>
                <input type="text" id="searchFavoritos" placeholder="Nome do produto..."
                  style="width: 100%; padding: 10px; border: 1px solid #e0e0e0; border-radius: 8px;">
              </div>
              <div>
                <label style="display: block; margin-bottom: 8px; font-size: 14px; color: #666; font-weight: 500;">
                  <i class="fas fa-filter" style="color: #3cb371; margin-right: 4px;"></i> Categoria
                </label>
                <select id="filterCategoria"
                  style="width: 100%; padding: 10px; border: 1px solid #e0e0e0; border-radius: 8px;">
                  <option value="">Todas</option>
                  <option value="Roupa">Roupa</option>
                  <option value="Calçado">Calçado</option>
                  <option value="Acessórios">Acessórios</option>
                  <option value="Artesanato">Artesanato</option>
                </select>
              </div>
              <div>
                <label style="display: block; margin-bottom: 8px; font-size: 14px; color: #666; font-weight: 500;">
                  <i class="fas fa-tag" style="color: #3cb371; margin-right: 4px;"></i> Disponibilidade
                </label>
                <select id="filterDisponibilidade"
                  style="width: 100%; padding: 10px; border: 1px solid #e0e0e0; border-radius: 8px;">
                  <option value="">Todos</option>
                  <option value="disponivel">Disponíveis</option>
                  <option value="indisponivel">Indisponíveis</option>
                </select>
              </div>
              <button class="btn-clear-filters" onclick="limparFiltrosFavoritos()"
                style="padding: 10px 20px; background: #3cb371; border: none; border-radius: 8px; cursor: pointer; color: #ffffff; font-size: 16px; display: flex; align-items: center; justify-content: center; height: 42px; box-shadow: 0 2px 8px rgba(60, 179, 113, 0.3); transition: all 0.3s ease;"
                onmouseover="this.style.background='#2ea05f'" onmouseout="this.style.background='#3cb371'">
                <i class="fas fa-redo"></i>
              </button>
            </div>
          </div>

          <!-- Grid de Favoritos -->
          <div class="favoritos-grid" id="favoritosGrid">
            <!-- Carregado via JavaScript -->
          </div>
        </div>
      </div>
    </main>
  </div>

  <script src="src/js/alternancia.js"></script>
  <script>
  let favoritosList = [];

  // Carregar favoritos
  function carregarFavoritos() {
    $.ajax({
      url: 'src/controller/controllerFavoritos.php',
      method: 'GET',
      data: {
        op: 3
      },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          favoritosList = response.data;
          renderizarFavoritos(favoritosList);
          $('#totalFavoritos').text(`${response.total} produto${response.total !== 1 ? 's' : ''}`);
          $('#sidebarFavCount').text(response.total).toggle(response.total > 0);
        }
      },
      error: function() {
        Swal.fire({
          icon: 'error',
          title: 'Erro',
          text: 'Erro ao carregar favoritos.',
          confirmButtonColor: '#ef4444'
        });
      }
    });
  }

  // Renderizar favoritos
  function renderizarFavoritos(favoritos) {
    if (favoritos.length === 0) {
      mostrarMensagemVazio();
      return;
    }

    let html = '';
    favoritos.forEach(fav => {
      const disponivel = fav.ativo == 1 && fav.stock > 0;
      const dataFormatada = new Date(fav.data_adicao).toLocaleDateString('pt-PT');

      html += `
                    <div class="produto-favorito-card" id="produto-${fav.produto_id}">
                        <a href="produto.php?id=${fav.produto_id}" style="text-decoration: none; color: inherit;">
                            <img src="${fav.foto}" alt="${fav.nome}" class="produto-foto" onerror="this.src='src/img/placeholder-produto.jpg'">
                            <div class="produto-info">
                                <h3 class="produto-nome">${fav.nome}</h3>
                                <div class="produto-preco">€${parseFloat(fav.preco).toFixed(2)}</div>
                                <div class="produto-detalhes">
                                    <i class="fas fa-tag"></i> ${fav.marca || 'Sem marca'} |
                                    <i class="fas fa-tshirt"></i> ${fav.tamanho || 'Único'}
                                </div>
                                <div class="produto-detalhes">
                                    <i class="fas fa-store"></i> ${fav.anunciante_nome}
                                </div>
                                <span class="produto-status ${disponivel ? 'status-disponivel' : 'status-indisponivel'}">
                                    ${disponivel ? '✓ Disponível' : '✗ Indisponível'}
                                </span>
                            </div>
                        </a>
                        <div class="produto-info" style="padding-top: 0;">
                            <div class="produto-acoes">
                                ${disponivel ? `
                                    <button class="btn-action btn-carrinho" onclick="event.stopPropagation(); adicionarCarrinho(${fav.produto_id})">
                                        <i class="fas fa-shopping-cart"></i> Comprar
                                    </button>
                                ` : ''}
                                <button class="btn-action btn-remover" onclick="event.stopPropagation(); removerFavorito(${fav.produto_id}, this)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="data-adicao">Adicionado em ${dataFormatada}</div>
                        </div>
                    </div>
                `;
    });

    $('#favoritosGrid').html(html);
  }

  // Adicionar ao carrinho
  function adicionarCarrinho(produtoId) {
    $.ajax({
      url: 'src/controller/controllerCarrinho.php',
      method: 'POST',
      data: {
        op: 7,
        produto_id: produtoId
      },
      success: function() {
        Swal.fire({
          icon: 'success',
          title: 'Adicionado!',
          text: 'Produto adicionado ao carrinho',
          timer: 2000,
          showConfirmButton: false
        });
      }
    });
  }

  // Limpar favoritos inativos
  function limparInativos() {
    Swal.fire({
      title: 'Limpar produtos inativos?',
      text: 'Produtos que não estão mais disponíveis serão removidos dos favoritos.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#f59e0b',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sim, limpar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: 'src/controller/controllerFavoritos.php',
          method: 'POST',
          data: {
            op: 6
          },
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              Swal.fire({
                icon: 'success',
                title: 'Concluído!',
                text: response.message,
                timer: 2000
              });
              carregarFavoritos();
            }
          }
        });
      }
    });
  }

  // Filtros
  $('#searchFavoritos').on('keyup', aplicarFiltros);
  $('#filterCategoria, #filterDisponibilidade').on('change', aplicarFiltros);

  function aplicarFiltros() {
    const search = $('#searchFavoritos').val().toLowerCase();
    const categoria = $('#filterCategoria').val();
    const disponibilidade = $('#filterDisponibilidade').val();

    const filtrados = favoritosList.filter(fav => {
      const matchSearch = fav.nome.toLowerCase().includes(search);
      const matchCategoria = !categoria || fav.categoria === categoria;
      const disponivel = fav.ativo == 1 && fav.stock > 0;
      const matchDisponibilidade = !disponibilidade ||
        (disponibilidade === 'disponivel' && disponivel) ||
        (disponibilidade === 'indisponivel' && !disponivel);

      return matchSearch && matchCategoria && matchDisponibilidade;
    });

    renderizarFavoritos(filtrados);
  }

  // Limpar filtros
  function limparFiltrosFavoritos() {
    $('#searchFavoritos').val('');
    $('#filterCategoria').val('');
    $('#filterDisponibilidade').val('');
    aplicarFiltros();
  }

  // Carregar ao iniciar
  $(document).ready(function() {
    carregarFavoritos();

    // Dropdown do usuário
    $("#userMenuBtn").on("click", function(e) {
      e.stopPropagation();
      $("#userDropdown").toggleClass("active");
    });

    $(document).on("click", function(e) {
      if (!$(e.target).closest(".navbar-user").length) {
        $("#userDropdown").removeClass("active");
      }
    });

    $("#userDropdown").on("click", function(e) {
      e.stopPropagation();
    });
  });

  function showPasswordModal() {
    Swal.fire({
      title: 'Alterar Senha',
      html: `
                    <input type="password" id="currentPassword" class="swal2-input" placeholder="Senha Atual">
                    <input type="password" id="newPassword" class="swal2-input" placeholder="Nova Senha">
                    <input type="password" id="confirmPassword" class="swal2-input" placeholder="Confirmar Nova Senha">
                `,
      showCancelButton: true,
      confirmButtonText: 'Alterar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#3cb371',
      preConfirm: () => {
        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (!currentPassword || !newPassword || !confirmPassword) {
          Swal.showValidationMessage('Preencha todos os campos');
          return false;
        }

        if (newPassword !== confirmPassword) {
          Swal.showValidationMessage('As senhas não coincidem');
          return false;
        }

        if (newPassword.length < 6) {
          Swal.showValidationMessage('A senha deve ter pelo menos 6 caracteres');
          return false;
        }

        return {
          currentPassword,
          newPassword
        };
      }
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire('Sucesso', 'Senha alterada com sucesso!', 'success');
        $("#userDropdown").removeClass("active");
      }
    });
  }

  function logout() {
    Swal.fire({
      title: 'Terminar Sessão?',
      text: 'Tem a certeza que pretende sair?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sim, sair',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: 'src/controller/controllerPerfil.php?op=2',
          method: 'GET'
        }).always(function() {
          window.location.href = 'index.html';
        });
      }
    });
  }
  </script>
</body>

</html>