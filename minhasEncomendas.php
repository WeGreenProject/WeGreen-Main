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
  <title>Minhas Encomendas - WeGreen</title>
  <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
  <link rel="stylesheet" href="src/css/DashboardCliente.css">
  <link rel="stylesheet" href="assets/css/notifications-dropdown.css">
  <link rel="stylesheet" href="src/css/lib/datatables.css">
  <link rel="stylesheet" href="src/css/lib/select2.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <script src="src/js/lib/jquery.js"></script>
  <script src="src/js/lib/datatables.js"></script>
  <script src="src/js/lib/select2.js"></script>
  <script src="src/js/lib/sweatalert.js"></script>
  <script src="src/js/notifications.js"></script>
  <!-- jsPDF para gerar PDFs -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
  <!-- Sistema de Devoluções -->
  <script src="assets/js/custom/devolucoes.js"></script>

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
          <a href="minhasEncomendas.php" class="menu-item active">
            <i class="fas fa-shopping-bag"></i>
            <span>Minhas Encomendas</span>
          </a>
          <a href="meusFavoritos.php" class="menu-item">
            <i class="fas fa-heart"></i>
            <span>Meus Favoritos</span>
            <span class="badge" id="favoritosBadge"
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
          <h1 class="page-title"><i class="fas fa-shopping-bag"></i> Minhas Encomendas</h1>
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
        <header class="dashboard-header">
          <div class="header-title">
            <h1>Minhas Encomendas</h1>
            <p>Acompanhe o estado das suas compras</p>
          </div>
          <div class="header-actions">
            <button class="btn-continue-shopping" onclick="window.location.href='index.html'">
              <i class="fas fa-shopping-cart"></i>
              <span>Continuar a Comprar</span>
            </button>
          </div>
        </header>

        <div class="content-area">
          <!-- Barra de Pesquisa e Filtros -->
          <div class="filters-container"
            style="background: #fff; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: 15px; align-items: end;">
              <div>
                <label style="display: block; margin-bottom: 8px; font-size: 14px; color: #666;">
                  <i class="fas fa-search"></i> Pesquisar Produto
                </label>
                <input type="text" id="searchProduct" placeholder="Nome do produto..."
                  style="width: 100%; padding: 10px; border: 1px solid #e0e0e0; border-radius: 8px;">
              </div>
              <div>
                <label style="display: block; margin-bottom: 8px; font-size: 14px; color: #666;">Status</label>
                <select id="filterStatus" class="filter-select"
                  style="width: 100%; padding: 10px; border: 1px solid #e0e0e0; border-radius: 8px;">
                  <option value="">Todos</option>
                  <option value="pendente">Pendente</option>
                  <option value="processando">Processando</option>
                  <option value="enviado">Enviado</option>
                  <option value="entregue">Entregue</option>
                  <option value="devolvido">Devolvido</option>
                  <option value="cancelado">Cancelado</option>
                </select>
              </div>
              <div>
                <label style="display: block; margin-bottom: 8px; font-size: 14px; color: #666;">Período</label>
                <select id="filterPeriod"
                  style="width: 100%; padding: 10px; border: 1px solid #e0e0e0; border-radius: 8px;">
                  <option value="">Todo o período</option>
                  <option value="30">Últimos 30 dias</option>
                  <option value="90">Últimos 3 meses</option>
                  <option value="180">Últimos 6 meses</option>
                  <option value="365">Último ano</option>
                </select>
              </div>
              <div>
                <label style="display: block; margin-bottom: 8px; font-size: 14px; color: #666;">Ordenar</label>
                <select id="sortBy" style="width: 100%; padding: 10px; border: 1px solid #e0e0e0; border-radius: 8px;">
                  <option value="date-desc">Mais recentes</option>
                  <option value="date-asc">Mais antigas</option>
                  <option value="value-desc">Maior valor</option>
                  <option value="value-asc">Menor valor</option>
                </select>
              </div>
              <button class="btn-secondary" onclick="limparFiltros()"
                style="padding: 10px 20px; background: #f5f5f5; border: none; border-radius: 8px; cursor: pointer;">
                <i class="fas fa-redo"></i>
              </button>
            </div>
          </div>

          <!-- Grid de Encomendas (Cards) -->
          <div id="encomendasGrid" style="display: grid; gap: 20px;">
            <!-- Cards gerados dinamicamente -->
          </div>

          <!-- Mensagem vazia -->
          <div id="emptyState"
            style="display: none; text-align: center; padding: 60px; background: #fff; border-radius: 12px;">
            <i class="fas fa-shopping-bag" style="font-size: 64px; color: #e0e0e0; margin-bottom: 20px;"></i>
            <h3 style="color: #999;">Nenhuma encomenda encontrada</h3>
            <p style="color: #999;">Ainda não realizou nenhuma compra ou não há encomendas com os filtros aplicados</p>
            <button class="btn-primary" onclick="window.location.href='index.html'"
              style="margin-top: 20px; padding: 12px 24px; background: #3cb371; color: white; border: none; border-radius: 8px; cursor: pointer;">
              <i class="fas fa-shopping-cart"></i> Começar a Comprar
            </button>
          </div>
        </div>
      </div>

      <!-- Modal Detalhes da Encomenda -->
      <div id="detalhesModal" class="modal" style="display: none;">
        <div class="modal-content" style="max-width: 800px;">
          <div class="modal-header">
            <h3>Detalhes da Encomenda</h3>
            <span class="close" onclick="fecharModal()">&times;</span>
          </div>
          <div class="modal-body" id="detalhesContent">
            <!-- Conteúdo dinâmico -->
          </div>
        </div>
      </div>

    </main>
  </div>

  <script>
  let todasEncomendas = [];
  let encomendasFiltradas = [];

  $(document).ready(function() {
    carregarEncomendas();
    inicializarFiltros();
  });

  function carregarEncomendas() {
    $.ajax({
      url: 'src/controller/controllerEncomendas.php',
      method: 'POST',
      data: {
        op: 'listarEncomendasCliente'
      },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          todasEncomendas = response.data;
          encomendasFiltradas = response.data;
          renderizarEncomendas(response.data);
        } else {
          mostrarEstadoVazio();
        }
      },
      error: function(xhr, status, error) {
        console.error('Erro AJAX:', xhr.responseText);
        Swal.fire('Erro', 'Erro ao comunicar com o servidor.', 'error');
      }
    });
  }

  function renderizarEncomendas(encomendas) {
    const grid = $('#encomendasGrid');
    grid.empty();

    if (!encomendas || encomendas.length === 0) {
      mostrarEstadoVazio();
      return;
    }

    $('#emptyState').hide();
    grid.show();

    encomendas.forEach(enc => {
      const card = criarCardEncomenda(enc);
      grid.append(card);
    });
  }

  function criarCardEncomenda(enc) {
    const statusInfo = getStatusInfo(enc.estado.toLowerCase());
    const timeline = criarTimeline(enc.estado.toLowerCase());

    return $(`
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <div class="order-number">#${enc.codigo_encomenda}</div>
                            <div class="order-date"><i class="far fa-calendar"></i> ${formatarData(enc.data_envio)}</div>
                        </div>
                        <div>
                            <span class="status-badge ${statusInfo.class}">${statusInfo.text}</span>
                        </div>
                    </div>

                    <div class="order-timeline">
                        ${timeline}
                    </div>

                    <div class="order-body">
                        <div class="order-products">
                            <div class="product-image" onclick="previewImage('${enc.foto_produto || 'assets/media/products/default.jpg'}', '${enc.produtos}')">
                                <img src="${enc.foto_produto || 'assets/media/products/default.jpg'}" alt="${enc.produtos}">
                            </div>
                            <div class="product-info">
                                <div class="product-name">${enc.produtos}</div>
                                <div class="product-details">
                                    <span><i class="fas fa-truck"></i> ${enc.transportadora}</span>
                                    ${enc.plano_rastreio ? `<span><i class="fas fa-box"></i> ${enc.plano_rastreio}</span>` : ''}
                                </div>
                            </div>
                        </div>

                        <div class="order-total">
                            <div class="total-label">Total</div>
                            <div class="total-value">€${parseFloat(enc.total).toFixed(2)}</div>
                        </div>
                    </div>

                    <div class="order-actions">
                        <button class="btn-action btn-primary" onclick="verDetalhes('${enc.codigo_encomenda}')">
                            <i class="fas fa-eye"></i> Ver Detalhes
                        </button>
                        ${enc.estado.toLowerCase() === 'entregue' && !enc.devolucao_ativa ? `
                            <button class="btn-action btn-warning" onclick="abrirModalDevolucao('${enc.id}', '${enc.codigo_encomenda}', ${JSON.stringify(enc.produtos).replace(/"/g, '&quot;')})">
                                <i class="fas fa-undo"></i> Solicitar Devolução
                            </button>
                            <button class="btn-action btn-secondary" onclick="comprarNovamente('${enc.codigo_encomenda}')">
                                <i class="fas fa-redo"></i> Comprar Novamente
                            </button>
                            <button class="btn-action btn-secondary" onclick="avaliarProduto('${enc.codigo_encomenda}')">
                                <i class="fas fa-star"></i> Avaliar
                            </button>
                        ` : ''}
                        ${enc.devolucao_ativa && enc.devolucao_estado === 'aprovada' ? `
                            <button class="btn-action btn-success" onclick="mostrarModalConfirmarEnvio(${enc.devolucao_id}, '${enc.devolucao_codigo}')">
                                <i class="fas fa-shipping-fast"></i> Confirmar Envio
                            </button>
                        ` : ''}
                        ${enc.devolucao_ativa && enc.devolucao_estado === 'enviada' ? `
                            <span class="badge-info" style="padding: 8px 16px; background: #3b82f6; color: white; border-radius: 6px; font-size: 13px;">
                                <i class="fas fa-truck"></i> Devolução enviada - aguardando confirmação
                            </span>
                        ` : ''}
                        ${enc.devolucao_ativa && enc.devolucao_estado === 'recebida' ? `
                            <span class="badge-success" style="padding: 8px 16px; background: #10b981; color: white; border-radius: 6px; font-size: 13px;">
                                <i class="fas fa-check-double"></i> Produto recebido - aguardando reembolso
                            </span>
                        ` : ''}
                        <button class="btn-action btn-outline" onclick="descarregarFatura('${enc.codigo_encomenda}')">
                            <i class="fas fa-download"></i> Fatura
                        </button>
                        ${(enc.estado.toLowerCase() === 'pendente' || enc.estado.toLowerCase() === 'processando') ? `
                            <button class="btn-action btn-danger" onclick="cancelarEncomenda('${enc.codigo_encomenda}')">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        ` : ''}
                    </div>
                </div>
            `);
  }

  function criarTimeline(estado) {
    const steps = [{
        key: 'pendente',
        icon: 'clock',
        label: 'Pendente'
      },
      {
        key: 'processando',
        icon: 'cog',
        label: 'Processando'
      },
      {
        key: 'enviado',
        icon: 'truck',
        label: 'Enviado'
      },
      {
        key: 'entregue',
        icon: 'check-circle',
        label: 'Entregue'
      }
    ];

    const estadoIndex = steps.findIndex(s => s.key === estado);

    return `
                <div class="timeline">
                    ${steps.map((step, index) => {
                        const isActive = index <= estadoIndex;
                        const isCurrent = index === estadoIndex;
                        return `
                            <div class="timeline-step ${isActive ? 'active' : ''} ${isCurrent ? 'current' : ''}">
                                <div class="timeline-icon">
                                    <i class="fas fa-${step.icon}"></i>
                                </div>
                                <div class="timeline-label">${step.label}</div>
                            </div>
                            ${index < steps.length - 1 ? '<div class="timeline-line ' + (isActive ? 'active' : '') + '"></div>' : ''}
                        `;
                    }).join('')}
                </div>
            `;
  }

  function getStatusInfo(estado) {
    const statusMap = {
      'pendente': {
        class: 'status-pendente',
        text: 'Pendente'
      },
      'processando': {
        class: 'status-processando',
        text: 'Processando'
      },
      'enviado': {
        class: 'status-enviado',
        text: 'Enviado'
      },
      'entregue': {
        class: 'status-entregue',
        text: 'Entregue'
      },
      'devolvido': {
        class: 'status-devolvido',
        text: 'Devolvido'
      },
      'cancelado': {
        class: 'status-cancelado',
        text: 'Cancelado'
      }
    };
    return statusMap[estado] || {
      class: '',
      text: estado
    };
  }

  function formatarData(data) {
    const d = new Date(data);
    return d.toLocaleDateString('pt-PT', {
      day: '2-digit',
      month: 'long',
      year: 'numeric'
    });
  }

  function mostrarEstadoVazio() {
    $('#encomendasGrid').hide();
    $('#emptyState').show();
  }

  function inicializarFiltros() {
    $('#searchProduct, #filterStatus, #filterPeriod, #sortBy').on('change keyup', aplicarFiltros);
  }

  function aplicarFiltros() {
    let filtradas = [...todasEncomendas];

    // Filtro de pesquisa
    const search = $('#searchProduct').val().toLowerCase();
    if (search) {
      filtradas = filtradas.filter(e => e.produtos.toLowerCase().includes(search));
    }

    // Filtro de status
    const status = $('#filterStatus').val().toLowerCase();
    if (status) {
      filtradas = filtradas.filter(e => e.estado.toLowerCase() === status);
    }

    // Filtro de período
    const period = $('#filterPeriod').val();
    if (period) {
      const hoje = new Date();
      const dataLimite = new Date(hoje.setDate(hoje.getDate() - parseInt(period)));
      filtradas = filtradas.filter(e => new Date(e.data_envio) >= dataLimite);
    }

    // Ordenação
    const sort = $('#sortBy').val();
    filtradas.sort((a, b) => {
      switch (sort) {
        case 'date-desc':
          return new Date(b.data_envio) - new Date(a.data_envio);
        case 'date-asc':
          return new Date(a.data_envio) - new Date(b.data_envio);
        case 'value-desc':
          return parseFloat(b.total) - parseFloat(a.total);
        case 'value-asc':
          return parseFloat(a.total) - parseFloat(b.total);
        default:
          return 0;
      }
    });

    encomendasFiltradas = filtradas;
    renderizarEncomendas(filtradas);
  }

  function limparFiltros() {
    $('#searchProduct').val('');
    $('#filterStatus').val('');
    $('#filterPeriod').val('');
    $('#sortBy').val('date-desc');
    renderizarEncomendas(todasEncomendas);
  }

  function comprarNovamente(codigo) {
    Swal.fire({
      title: 'Comprar Novamente?',
      text: 'Deseja adicionar os produtos desta encomenda ao carrinho?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sim, adicionar!',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire('Sucesso!', 'Produtos adicionados ao carrinho', 'success');
      }
    });
  }

  function avaliarProduto(codigo) {
    Swal.fire({
      title: 'Avaliar Produtos',
      html: `
                    <div style="text-align: left;">
                        <p>Como foi a sua experiência?</p>
                        <div style="font-size: 32px; text-align: center; margin: 20px 0;">
                            <i class="far fa-star rating-star" data-rating="1"></i>
                            <i class="far fa-star rating-star" data-rating="2"></i>
                            <i class="far fa-star rating-star" data-rating="3"></i>
                            <i class="far fa-star rating-star" data-rating="4"></i>
                            <i class="far fa-star rating-star" data-rating="5"></i>
                        </div>
                        <textarea id="comentario" placeholder="Deixe um comentário..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; min-height: 100px;"></textarea>
                    </div>
                `,
      showCancelButton: true,
      confirmButtonText: 'Enviar Avaliação',
      cancelButtonText: 'Cancelar',
      didOpen: () => {
        $('.rating-star').on('click', function() {
          const rating = $(this).data('rating');
          $('.rating-star').each(function(i) {
            $(this).toggleClass('fas far', i < rating);
          });
        });
      }
    });
  }

  function verDetalhes(codigo) {
    $.ajax({
      url: 'src/controller/controllerEncomendas.php',
      method: 'POST',
      data: {
        op: 'detalhesEncomenda',
        codigo: codigo
      },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          mostrarDetalhes(response.data);
        } else {
          Swal.fire('Erro', response.message, 'error');
        }
      },
      error: function(xhr, status, error) {
        Swal.fire('Erro', 'Erro ao carregar detalhes da encomenda', 'error');
      }
    });
  }

  function mostrarDetalhes(encomenda) {
    // Determinar tipo de entrega
    const tipoEntrega = encomenda.tipo_entrega || 'domicilio';
    const tituloMorada = tipoEntrega === 'ponto_recolha' ? '📍 Ponto de Recolha' : '🏠 Morada de Entrega';
    const moradaCompleta = tipoEntrega === 'ponto_recolha' ?
      (encomenda.morada_ponto_recolha || encomenda.morada || 'Morada não disponível') :
      (encomenda.morada_completa || encomenda.morada || 'Morada não disponível');

    // Só mostrar mapa se houver morada válida
    const temMorada = moradaCompleta && moradaCompleta !== 'Morada não disponível' && moradaCompleta !== 'null';

    let mapaHtml = '';
    if (temMorada) {
      // Gerar URL do mapa
      const enderecoEncoded = encodeURIComponent(moradaCompleta);
      const googleMapsUrl = `https://www.google.com/maps/search/?api=1&query=${enderecoEncoded}`;

      // HTML do mapa (iframe do Google Maps)
      mapaHtml = `
                    <div style="margin-top: 15px; border-radius: 8px; overflow: hidden; border: 2px solid #e5e7eb;">
                        <iframe
                            width="100%"
                            height="300"
                            frameborder="0"
                            style="border:0"
                            src="https://maps.google.com/maps?q=${enderecoEncoded}&t=&z=15&ie=UTF8&iwloc=&output=embed"
                            allowfullscreen>
                        </iframe>
                        <div style="padding: 10px; background-color: #f3f4f6; text-align: center;">
                            <a href="${googleMapsUrl}"
                               target="_blank"
                               style="color: #22c55e; text-decoration: none; font-weight: 600; font-size: 12px;">
                                📍 Abrir no Google Maps
                            </a>
                        </div>
                    </div>
                `;
    }

    const html = `
                <div style="padding: 20px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <h4 style="color: #3cb371; margin-bottom: 10px;">Informações da Encomenda</h4>
                            <p><strong>Código:</strong> ${encomenda.codigo_encomenda}</p>
                            <p><strong>Data:</strong> ${encomenda.data_envio}</p>
                            <p><strong>Status:</strong> ${encomenda.estado}</p>
                            <p><strong>Transportadora:</strong> ${encomenda.transportadora}</p>
                        </div>
                        <div>
                            <h4 style="color: #3cb371; margin-bottom: 10px;">${tituloMorada}</h4>
                            ${tipoEntrega === 'ponto_recolha' && encomenda.nome_ponto_recolha ? `
                                <p style="font-weight: 600; margin-bottom: 5px;">${encomenda.nome_ponto_recolha}</p>
                            ` : ''}
                            <p style="white-space: pre-line;">${moradaCompleta}</p>
                            ${mapaHtml}
                        </div>
                    </div>
                    <div>
                        <h4 style="color: #3cb371; margin-bottom: 10px;">Produtos</h4>
                        <div style="background: #f9f9f9; padding: 15px; border-radius: 8px;">
                            ${encomenda.produtos_detalhes || 'Carregando produtos...'}
                        </div>
                    </div>
                    <div style="margin-top: 20px; text-align: right;">
                        <h3 style="color: #3cb371;">Total: €${parseFloat(encomenda.total).toFixed(2)}</h3>
                    </div>
                </div>
            `;

    $('#detalhesContent').html(html);
    $('#detalhesModal').fadeIn();
  }

  function fecharModal() {
    $('#detalhesModal').fadeOut();
  }

  function descarregarFatura(codigo) {
    // Buscar detalhes da encomenda
    $.ajax({
      url: 'src/controller/controllerEncomendas.php',
      type: 'POST',
      data: {
        op: 'detalhesEncomenda',
        codigo: codigo
      },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          gerarPDFFatura(response.data);
        } else {
          Swal.fire('Erro', 'Não foi possível obter os detalhes da encomenda', 'error');
        }
      }
    });
  }

  function gerarPDFFatura(encomenda) {
    const {
      jsPDF
    } = window.jspdf;
    const doc = new jsPDF();

    // Cabeçalho WeGreen
    doc.setFillColor(60, 179, 113);
    doc.rect(0, 0, 210, 35, 'F');

    doc.setTextColor(255, 255, 255);
    doc.setFontSize(24);
    doc.setFont(undefined, 'bold');
    doc.text('WeGreen', 14, 15);

    doc.setFontSize(10);
    doc.setFont(undefined, 'normal');
    doc.text('Marketplace Sustentável', 14, 22);
    doc.text('NIF: 123456789 | Email: info@wegreen.pt', 14, 28);

    // Título Fatura
    doc.setTextColor(0, 0, 0);
    doc.setFontSize(20);
    doc.setFont(undefined, 'bold');
    doc.text('FATURA', 150, 15);

    doc.setFontSize(10);
    doc.setFont(undefined, 'normal');
    doc.text('#' + encomenda.codigo_encomenda, 150, 22);
    doc.text('Data: ' + new Date(encomenda.data_envio).toLocaleDateString('pt-PT'), 150, 28);

    // Informações do Cliente
    doc.setFontSize(12);
    doc.setFont(undefined, 'bold');
    doc.text('Cliente:', 14, 50);

    doc.setFontSize(10);
    doc.setFont(undefined, 'normal');
    doc.text(encomenda.cliente_nome || '<?php echo $_SESSION['nome']; ?>', 14, 57);
    doc.text(encomenda.morada || 'N/A', 14, 63);

    // Linha separadora
    doc.setDrawColor(200, 200, 200);
    doc.line(14, 75, 196, 75);

    // Produtos
    const produtosHTML = $(encomenda.produtos_html);
    const produtos = [];

    produtosHTML.find('tr').each(function() {
      const cols = $(this).find('td');
      if (cols.length > 0) {
        produtos.push([
          cols.eq(0).text().trim(),
          cols.eq(1).text().trim(),
          cols.eq(2).text().trim(),
          cols.eq(3).text().trim()
        ]);
      }
    });

    doc.autoTable({
      startY: 80,
      head: [
        ['Produto', 'Quantidade', 'Preço Unit.', 'Total']
      ],
      body: produtos,
      theme: 'striped',
      headStyles: {
        fillColor: [60, 179, 113],
        textColor: [255, 255, 255],
        fontStyle: 'bold',
        fontSize: 11
      },
      styles: {
        fontSize: 10,
        cellPadding: 5
      },
      columnStyles: {
        0: {
          cellWidth: 80
        },
        1: {
          halign: 'center',
          cellWidth: 30
        },
        2: {
          halign: 'right',
          cellWidth: 35
        },
        3: {
          halign: 'right',
          cellWidth: 35
        }
      }
    });

    // Total
    const finalY = doc.lastAutoTable.finalY + 10;
    doc.setFontSize(12);
    doc.setFont(undefined, 'bold');
    doc.text('Total:', 140, finalY);
    doc.text('€' + parseFloat(encomenda.total).toFixed(2), 180, finalY, {
      align: 'right'
    });

    // Transportadora
    doc.setFontSize(10);
    doc.setFont(undefined, 'normal');
    doc.text('Transportadora: ' + encomenda.transportadora, 14, finalY + 10);
    doc.text('Estado: ' + encomenda.estado, 14, finalY + 16);

    // Rodapé
    doc.setFontSize(8);
    doc.setTextColor(150, 150, 150);
    doc.text('Obrigado pela sua compra! WeGreen - Sustentabilidade em cada produto.', 105, 280, {
      align: 'center'
    });
    doc.text('www.wegreen.pt | suporte@wegreen.pt', 105, 285, {
      align: 'center'
    });

    // Download
    doc.save('fatura_' + encomenda.codigo_encomenda + '.pdf');

    Swal.fire({
      icon: 'success',
      title: 'Fatura Gerada!',
      text: 'O download iniciou automaticamente',
      timer: 2000,
      showConfirmButton: false
    });
  }

  function cancelarEncomenda(codigo) {
    Swal.fire({
      title: 'Cancelar Encomenda?',
      text: 'Esta ação não pode ser revertida!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sim, cancelar!',
      cancelButtonText: 'Não'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: 'src/controller/controllerEncomendas.php',
          method: 'POST',
          data: {
            op: 'cancelarEncomenda',
            codigo: codigo
          },
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              Swal.fire('Cancelada!', 'Encomenda cancelada com sucesso', 'success');
              carregarEncomendas();
            } else {
              Swal.fire('Erro', response.message, 'error');
            }
          }
        });
      }
    });
  }

  function previewImage(imageUrl, productName) {
    Swal.fire({
      imageUrl: imageUrl,
      imageAlt: productName,
      title: productName,
      showConfirmButton: false,
      showCloseButton: true,
      width: '600px',
      customClass: {
        image: 'preview-image-large'
      },
      didOpen: () => {
        const style = document.createElement('style');
        style.innerHTML = `
                        .preview-image-large {
                            max-height: 500px !important;
                            object-fit: contain !important;
                            border-radius: 12px !important;
                        }
                    `;
        document.head.appendChild(style);
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
        window.location.href = 'src/controller/controllerPerfil.php?op=2';
      }
    });
  }

  // Fechar modal ao clicar fora
  window.onclick = function(event) {
    if (event.target.id === 'detalhesModal') {
      fecharModal();
    }
  }
  </script>

  <style>
  /* Order Cards */
  .order-card {
    background: #fff;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s;
    border: 1px solid #f0f0f0;
  }

  .order-card:hover {
    box-shadow: 0 4px 16px rgba(60, 179, 113, 0.15);
    transform: translateY(-2px);
  }

  .order-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 2px solid #f5f5f5;
  }

  .order-number {
    font-size: 18px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 4px;
  }

  .order-date {
    font-size: 13px;
    color: #999;
  }

  /* Timeline */
  .order-timeline {
    margin-bottom: 24px;
  }

  .timeline {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    padding: 20px 0;
  }

  .timeline-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    z-index: 2;
    flex: 1;
  }

  .timeline-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f5f5f5;
    border: 3px solid #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    color: #999;
    transition: all 0.3s;
  }

  .timeline-step.active .timeline-icon {
    background: #e8f5e9;
    border-color: #3cb371;
    color: #3cb371;
  }

  .timeline-step.current .timeline-icon {
    background: #3cb371;
    border-color: #3cb371;
    color: white;
    box-shadow: 0 0 0 4px rgba(60, 179, 113, 0.2);
    animation: pulse 2s infinite;
  }

  @keyframes pulse {

    0%,
    100% {
      box-shadow: 0 0 0 4px rgba(60, 179, 113, 0.2);
    }

    50% {
      box-shadow: 0 0 0 8px rgba(60, 179, 113, 0.1);
    }
  }

  .timeline-label {
    font-size: 11px;
    color: #999;
    margin-top: 8px;
    font-weight: 500;
    text-align: center;
  }

  .timeline-step.active .timeline-label {
    color: #3cb371;
    font-weight: 600;
  }

  .timeline-line {
    position: absolute;
    top: 40px;
    height: 3px;
    background: #e0e0e0;
    z-index: 1;
    transition: all 0.3s;
  }

  .timeline-line.active {
    background: linear-gradient(to right, #3cb371, #2e8b57);
  }

  /* Order Body */
  .order-body {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 16px;
    background: #fafafa;
    border-radius: 8px;
  }

  .order-products {
    display: flex;
    gap: 16px;
    flex: 1;
  }

  .product-image {
    width: 70px;
    height: 70px;
    border-radius: 12px;
    overflow: hidden;
    flex-shrink: 0;
    border: 2px solid #f0f0f0;
    background: #fff;
    cursor: pointer;
    transition: all 0.3s;
  }

  .product-image:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(60, 179, 113, 0.3);
    border-color: #3cb371;
  }

  .product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .product-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #3cb371, #2e8b57);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: white;
  }

  .product-info {
    flex: 1;
  }

  .product-name {
    font-size: 15px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
  }

  .product-details {
    display: flex;
    gap: 16px;
    font-size: 13px;
    color: #666;
  }

  .product-details span {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .order-total {
    text-align: right;
  }

  .total-label {
    font-size: 12px;
    color: #999;
    margin-bottom: 4px;
  }

  .total-value {
    font-size: 24px;
    font-weight: 700;
    color: #3cb371;
  }

  /* Actions */
  .order-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }

  .btn-action {
    padding: 10px 16px;
    border-radius: 8px;
    border: none;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .btn-action.btn-primary {
    background: #3cb371;
    color: white;
  }

  .btn-action.btn-primary:hover {
    background: #2e8b57;
    transform: translateY(-1px);
  }

  .btn-action.btn-secondary {
    background: #f5f5f5;
    color: #666;
  }

  .btn-action.btn-secondary:hover {
    background: #e0e0e0;
  }

  .btn-action.btn-outline {
    background: white;
    color: #3cb371;
    border: 1px solid #3cb371;
  }

  .btn-action.btn-outline:hover {
    background: #3cb371;
    color: white;
  }

  .btn-action.btn-danger {
    background: #fff5f5;
    color: #dc3545;
  }

  .btn-action.btn-danger:hover {
    background: #dc3545;
    color: white;
  }

  /* Status Badges */
  .status-badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    display: inline-block;
  }

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

  .status-pendente {
    background: #fff3cd;
    color: #856404;
  }

  .status-processando {
    background: #cfe2ff;
    color: #084298;
  }

  .status-enviado {
    background: #cff4fc;
    color: #055160;
  }

  .status-entregue {
    background: #d1e7dd;
    color: #0f5132;
  }

  .status-devolvido {
    background: #e2e3e5;
    color: #383d41;
  }

  .status-cancelado {
    background: #f8d7da;
    color: #721c24;
  }

  /* Modal */
  .modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
  }

  .modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 0;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    max-width: 800px;
    animation: slideDown 0.3s;
  }

  @keyframes slideDown {
    from {
      transform: translateY(-50px);
      opacity: 0;
    }

    to {
      transform: translateY(0);
      opacity: 1;
    }
  }

  .modal-header {
    padding: 24px 28px;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .modal-header h3 {
    margin: 0;
    color: #2c3e50;
    font-size: 20px;
  }

  .close {
    font-size: 28px;
    font-weight: bold;
    color: #999;
    cursor: pointer;
    transition: all 0.3s;
  }

  .close:hover {
    color: #dc3545;
    transform: rotate(90deg);
  }

  /* Responsive */
  @media (max-width: 768px) {
    .timeline-label {
      font-size: 9px;
    }

    .timeline-icon {
      width: 32px;
      height: 32px;
      font-size: 14px;
    }

    .order-body {
      flex-direction: column;
      gap: 16px;
    }

    .order-total {
      text-align: left;
      width: 100%;
    }

    .order-actions {
      flex-direction: column;
    }

    .btn-action {
      width: 100%;
      justify-content: center;
    }
  }
  </style>
  <script>
  // Dropdown do usuário
  $(document).ready(function() {
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
  </script>
  <script src="src/js/alternancia.js"></script>
</body>

</html>