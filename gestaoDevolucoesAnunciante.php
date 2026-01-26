<?php
session_start();

if($_SESSION['tipo'] == 3 || $_SESSION['tipo'] == 1){
?>

<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestão de Devoluções - WeGreen</title>
  <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
  <link rel="stylesheet" href="src/css/DashboardCliente.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="src/css/DashboardAnunciante.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="src/css/gestaoProdutos.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="src/css/modalProduto.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="src/css/lib/datatables.css">
  <link rel="stylesheet" href="src/css/lib/select2.css">
  <link rel="stylesheet" href="assets/css/notifications-dropdown.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <script src="src/js/lib/jquery.js"></script>
  <script src="src/js/lib/datatables.js"></script>
  <script src="src/js/lib/select2.js"></script>
  <script src="src/js/lib/sweatalert.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
  <script src="src/js/notifications.js"></script>
  <!-- Sistema de Devoluções -->
  <script src="assets/js/custom/devolucoes.js"></script>
  <script src="src/js/Anunciante.js"></script>
</head>

<body>
  <div class="dashboard-container">
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
          <a href="DashboardAnunciante.php" class="menu-item">
            <i class="fas fa-chart-line"></i>
            <span>Dashboard</span>
          </a>
          <a href="gestaoProdutosAnunciante.php" class="menu-item">
            <i class="fas fa-tshirt"></i>
            <span>Produtos</span>
          </a>
          <a href="gestaoEncomendasAnunciante.php" class="menu-item">
            <i class="fas fa-shopping-bag"></i>
            <span>Encomendas</span>
          </a>
          <a href="gestaoDevolucoesAnunciante.php" class="menu-item active">
            <i class="fas fa-undo"></i>
            <span>Devoluções</span>
          </a>
          <a href="ChatAnunciante.php" class="menu-item">
            <i class="fas fa-comments"></i>
            <span>Chat</span>
          </a>
        </div>
      </nav>
    </aside>

    <main class="main-content">
      <nav class="top-navbar">
        <div class="navbar-left">
          <h1 class="page-title"><i class="fas fa-undo"></i> Gestão de Devoluções</h1>
        </div>
        <div class="navbar-right">
          <?php include 'src/views/notifications-widget.php'; ?>
          <button class="btn-upgrade-navbar" id="upgradeBtn" onclick="window.location.href='planos.php'"
            style="display: none;"
            <?php echo (isset($_SESSION['plano']) && $_SESSION['plano'] == 3) ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : ''; ?>>
            <i class="fas fa-crown"></i> Upgrade
          </button>
          <div class="navbar-user" id="userMenuBtn">
            <img
              src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'User'); ?>&background=3cb371&color=fff"
              alt="Usuário" class="user-avatar">
            <div class="user-info">
              <span class="user-name"><?php echo $_SESSION['nome'] ?? 'Usuário'; ?></span>
              <span class="user-role">Anunciante</span>
            </div>
            <i class="fas fa-chevron-down" style="font-size: 12px; color: #64748b;"></i>
          </div>
          <div class="user-dropdown" id="userDropdown">
            <div class="dropdown-header">
              <img
                src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['nome'] ?? 'User'); ?>&background=3cb371&color=fff"
                alt="Usuário" class="dropdown-avatar">
              <div>
                <div class="dropdown-name"><?php echo $_SESSION['nome'] ?? 'Usuário'; ?></div>
                <div class="dropdown-email"><?php echo $_SESSION['email'] ?? 'user@email.com'; ?></div>
              </div>
            </div>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="perfilAnunciante.php">
              <i class="fas fa-user"></i>
              <span>Meu Perfil</span>
            </a>
            <a class="dropdown-item" href="alterarSenha.php">
              <i class="fas fa-key"></i>
              <span>Alterar Senha</span>
            </a>
            <button class="dropdown-item" id="btnAlternarConta" onclick="verificarEAlternarConta()" style="display:none;">
              <i class="fas fa-exchange-alt"></i>
              <span id="textoAlternar">Alternar Conta</span>
            </button>
            <div class="dropdown-divider"></div>
            <button class="dropdown-item dropdown-item-danger" onclick="logout()">
              <i class="fas fa-sign-out-alt"></i>
              <span>Sair</span>
            </button>
          </div>
        </div>
      </nav>

      <div class="content-area">
        <div id="devolucoes" class="page active">
          <div class="page-actions">
            <div class="actions-left">
            </div>
            <div class="actions-right">
              <button id="exportDevolucoesBtn" class="btn-export-pdf">
                <i class="fas fa-file-pdf"></i>
                <span>Exportar PDF</span>
              </button>
            </div>
          </div>

          <div class="stats-grid-compact" id="devolucoesStats">
            <div class="stat-card-compact" id="statPendentes"></div>
            <div class="stat-card-compact" id="statAprovadas"></div>
            <div class="stat-card-compact" id="statRejeitadas"></div>
            <div class="stat-card-compact" id="statReembolsadas"></div>
          </div>

          <div class="filters-container">
            <div class="filters-grid">
              <div class="filter-item">
                <label>
                  <i class="fas fa-search"></i> Pesquisar
                </label>
                <input type="text" id="filterPesquisa" placeholder="Código, cliente ou produto..." class="filter-input"
                  onkeyup="filtrarDevolucoes()">
              </div>
              <div class="filter-item">
                <label>
                  <i class="fas fa-toggle-on"></i> Estado
                </label>
                <select id="filterEstadoDevolucao" onchange="filtrarDevolucoes()" class="filter-select">
                  <option value="">Todos os Estados</option>
                  <option value="solicitada">Pendentes</option>
                  <option value="aprovada">Aprovadas</option>
                  <option value="rejeitada">Rejeitadas</option>
                  <option value="produto_recebido">Produto Recebido</option>
                  <option value="reembolsada">Reembolsadas</option>
                  <option value="cancelada">Canceladas</option>
                </select>
              </div>
              <div class="filter-item">
                <label>
                  <i class="fas fa-comment-alt"></i> Motivo
                </label>
                <select id="filterMotivo" onchange="filtrarDevolucoes()" class="filter-select">
                  <option value="">Todos os Motivos</option>
                  <option value="defeituoso">Defeituoso</option>
                  <option value="tamanho_errado">Tamanho Errado</option>
                  <option value="nao_como_descrito">Não Conforme</option>
                  <option value="arrependimento">Arrependimento</option>
                  <option value="outro">Outro</option>
                </select>
              </div>
              <div class="filter-item">
                <label>
                  <i class="fas fa-calendar-alt"></i> Data Inicial
                </label>
                <input type="date" id="filterDataInicial" class="filter-input" onchange="filtrarDevolucoes()">
              </div>
              <div class="filter-item">
                <label>
                  <i class="fas fa-calendar-alt"></i> Data Final
                </label>
                <input type="date" id="filterDataFinal" class="filter-input" onchange="filtrarDevolucoes()">
              </div>
              <div class="filter-item-button">
                <button class="btn-clear-filters" onclick="limparFiltros()">
                  <i class="fas fa-redo"></i>
                </button>
              </div>
            </div>
          </div>

          <div class="table-container">
            <table id="tabelaDevolucoes" class="display">
              <thead>
                <tr>
                  <th><i class="fas fa-hashtag"></i> Código Devolução</th>
                  <th><i class="fas fa-shopping-bag"></i> Encomenda</th>
                  <th><i class="fas fa-box"></i> Produto</th>
                  <th><i class="fas fa-user"></i> Cliente</th>
                  <th><i class="fas fa-comment-alt"></i> Motivo</th>
                  <th><i class="fas fa-euro-sign"></i> Valor</th>
                  <th><i class="fas fa-calendar-alt"></i> Data</th>
                  <th><i class="fas fa-info-circle"></i> Estado</th>
                  <th><i class="fas fa-cog"></i> Ações</th>
                </tr>
              </thead>
              <tbody>
                <!-- Populated by JavaScript -->
              </tbody>
            </table>
          </div>
        </div>

    </main>
  </div>

  <script>
  let devolucoesTable;

  $(document).ready(function() {
    // Inicializar DataTable
    devolucoesTable = $('#tabelaDevolucoes').DataTable({
      language: {
        url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-PT.json'
      },
      order: [
        [6, 'desc']
      ], // Ordenar por data (coluna 6)
      pageLength: 25,
      responsive: true
    });

    // Carregar dados
    carregarDevolucoesAnunciante();
    carregarEstatisticas();

    // Atualizar a cada 60 segundos
    setInterval(function() {
      carregarDevolucoesAnunciante();
      carregarEstatisticas();
    }, 60000);
  });

  function carregarEstatisticas() {
    $.ajax({
      url: 'src/controller/controllerDevolucoes.php?op=10',
      method: 'GET',
      dataType: 'json',
      success: function(response) {
        console.log('Estatísticas recebidas:', response);
        if (response.success) {
          const stats = response.data;
          $('#statPendentes .stat-value').text(stats.pendentes || 0);
          $('#statAprovadas .stat-value').text(stats.aprovadas || 0);
          $('#statRejeitadas .stat-value').text(stats.rejeitadas || 0);
          $('#statReembolsadas .stat-value').text('€' + parseFloat(stats.valor_total_reembolsado || 0).toFixed(
            2));

          // Atualizar badge de notificação
          const pendentes = stats.pendentes || 0;
          if (pendentes > 0) {
            $('.notification-badge').text(pendentes).show();
          } else {
            $('.notification-badge').hide();
          }
        }
      },
      error: function(xhr, status, error) {
        console.error('Erro ao carregar estatísticas:', error);
        console.error('Response:', xhr.responseText);
      }
    });
  }

  function filtrarDevolucoes() {
    const pesquisa = $('#filterPesquisa').val().toLowerCase();
    const estado = $('#filterEstadoDevolucao').val();
    const motivo = $('#filterMotivo').val();
    const dataInicial = $('#filterDataInicial').val();
    const dataFinal = $('#filterDataFinal').val();

    devolucoesTable.rows().every(function() {
      const data = this.data();
      let mostrar = true;

      // Filtro de pesquisa
      if (pesquisa) {
        const textoCompleto = (
          data[0] + ' ' + // Código
          data[1] + ' ' + // Encomenda
          data[2] + ' ' + // Produto
          data[3] // Cliente
        ).toLowerCase();
        if (!textoCompleto.includes(pesquisa)) {
          mostrar = false;
        }
      }

      // Filtro de estado
      if (estado && mostrar) {
        const estadoCell = $(data[7]).text().toLowerCase();
        if (!estadoCell.includes(estado)) {
          mostrar = false;
        }
      }

      // Filtro de motivo
      if (motivo && mostrar) {
        const motivoCell = data[4].toLowerCase();
        if (!motivoCell.includes(motivo)) {
          mostrar = false;
        }
      }

      // Filtro de data inicial
      if (dataInicial && mostrar) {
        const dataRow = data[6]; // coluna de data
        if (dataRow < dataInicial) {
          mostrar = false;
        }
      }

      // Filtro de data final
      if (dataFinal && mostrar) {
        const dataRow = data[6]; // coluna de data
        if (dataRow > dataFinal) {
          mostrar = false;
        }
      }

      if (mostrar) {
        $(this.node()).show();
      } else {
        $(this.node()).hide();
      }
    });
  }

  function limparFiltros() {
    $('#filterPesquisa').val('');
    $('#filterEstadoDevolucao').val('');
    $('#filterMotivo').val('');
    $('#filterDataInicial').val('');
    $('#filterDataFinal').val('');

    // Apenas limpar os filtros do DataTable, sem recarregar dados
    devolucoesTable.search('').columns().search('').draw();
  }

  function renderizarDevolucoesTabela(devolucoes) {
    console.log('Renderizando devoluções:', devolucoes);
    console.log('Total de devoluções:', devolucoes.length);

    devolucoesTable.clear();

    if (!devolucoes || devolucoes.length === 0) {
      console.warn('Nenhuma devolução para renderizar');
      devolucoesTable.draw();
      return;
    }

    devolucoes.forEach(function(dev) {
      console.log('Processando devolução:', dev);
      console.log('ID da devolução:', dev.id);

      const motivoTexto = {
        'defeituoso': 'Produto Defeituoso',
        'tamanho_errado': 'Tamanho Errado',
        'nao_como_descrito': 'Não como Descrito',
        'arrependimento': 'Arrependimento',
        'outro': 'Outro'
      };

      // Mapear estados para classes CSS e textos amigáveis
      const estadosConfig = {
        'solicitada': {
          class: 'status-solicitada',
          text: 'Solicitada'
        },
        'aprovada': {
          class: 'status-aprovada',
          text: 'Aprovada'
        },
        'enviada': {
          class: 'status-enviada',
          text: 'Enviada'
        },
        'recebida': {
          class: 'status-recebida',
          text: 'Recebida'
        },
        'rejeitada': {
          class: 'status-rejeitada',
          text: 'Rejeitada'
        },
        'reembolsada': {
          class: 'status-reembolsada',
          text: 'Reembolsada'
        },
        'cancelada': {
          class: 'status-cancelada',
          text: 'Cancelada'
        }
      };

      const estadoConfig = estadosConfig[dev.estado] || {
        class: 'status-default',
        text: dev.estado
      };
      const estadoClass = estadoConfig.class;
      const estadoTexto = estadoConfig.text;

      let acoes = `
                    <div class="table-actions">
                        <button class="btn-icon btn-info" onclick="console.log('Clicou ver detalhes'); verDetalhesDevolucao(${dev.id})" title="Ver Detalhes">
                            <i class="fas fa-eye"></i>
                        </button>
                `;

      // Botões para devolução SOLICITADA
      if (dev.estado === 'solicitada') {
        acoes += `
                        <button class="btn-icon btn-success" onclick="console.log('Clicou aprovar'); aprovarDevolucaoAnunciante(${dev.id})" title="Aprovar">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn-icon btn-danger" onclick="console.log('Clicou rejeitar'); rejeitarDevolucaoAnunciante(${dev.id})" title="Rejeitar">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
      }

      // Botão para confirmar RECEBIMENTO (quando produto foi ENVIADO)
      if (dev.estado === 'enviada') {
        acoes += `
                        <button class="btn-icon btn-success" onclick="mostrarModalConfirmarRecebimento(${dev.id}, '${dev.codigo_devolucao}')" title="Confirmar Recebimento">
                            <i class="fas fa-box-open"></i>
                        </button>
                    `;
      }

      // Botão para processar REEMBOLSO (só após confirmar recebimento)
      if (dev.estado === 'recebida' && dev.reembolso_status !== 'succeeded') {
        acoes += `
                        <button class="btn-icon btn-success" onclick="console.log('Clicou reembolso'); processarReembolsoAnunciante(${dev.id})" title="Processar Reembolso">
                            <i class="fas fa-euro-sign"></i>
                        </button>
                    `;
      }

      acoes += `</div>`;

      devolucoesTable.row.add([
        dev.codigo_devolucao,
        dev.codigo_encomenda || 'N/A',
        `<div class="product-info">
                        ${dev.produto_imagem ? `<img src="${dev.produto_imagem}" alt="${dev.produto_nome}" class="product-thumb" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; margin-right: 10px;">` : ''}
                        <span>${dev.produto_nome || 'Produto'}</span>
                    </div>`,
        dev.cliente_nome || 'Cliente',
        motivoTexto[dev.motivo] || dev.motivo,
        '€' + parseFloat(dev.valor_reembolso).toFixed(2),
        new Date(dev.data_solicitacao).toLocaleDateString('pt-PT'),
        `<span class="status-badge ${estadoClass}">${estadoTexto}</span>`,
        acoes
      ]);
    });

    console.log('Desenhando tabela...');
    devolucoesTable.draw();
    console.log('Tabela desenhada com sucesso');
  }

  // Toggle produtos expandidos (para uso futuro se houver múltiplos produtos)
  function toggleProdutosDevolucao(devolucaoId) {
    const expandRow = document.getElementById(`produtos-expand-dev-${devolucaoId}`);
    const arrow = document.getElementById(`arrow-dev-${devolucaoId}`);

    if (expandRow && arrow) {
      if (expandRow.style.display === 'none') {
        expandRow.style.display = 'table-row';
        arrow.style.transform = 'rotate(180deg)';
      } else {
        expandRow.style.display = 'none';
        arrow.style.transform = 'rotate(0deg)';
      }
    }
  }
  </script>
  <script src="src/js/alternancia.js"></script>
</body>

</html>

<?php
}else{
    echo "Sem permissão!";
}
?>
