<?php
session_start();

// Verificar autenticação
if(!isset($_SESSION['utilizador']) || !isset($_SESSION['tipo'])) {
    header("Location: login.html");
    exit();
}

$tipo_utilizador = $_SESSION['tipo'];
$nome_utilizador = $_SESSION['nome'] ?? 'Utilizador';

// Determinar título baseado no tipo
$titulo_pagina = 'Notificações';
if ($tipo_utilizador == 1) {
    $titulo_pagina = 'Notificações - Admin';
} elseif ($tipo_utilizador == 2) {
    $titulo_pagina = 'Notificações - Cliente';
} elseif ($tipo_utilizador == 3) {
    $titulo_pagina = 'Notificações - Anunciante';
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $titulo_pagina; ?> - WeGreen</title>
  <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="src/js/lib/jquery.js"></script>
  <style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #f5f7fa 0%, #e8f5e9 100%);
    min-height: 100vh;
    padding: 20px;
  }

  .container {
    max-width: 900px;
    margin: 0 auto;
  }

  .page-header {
    background: white;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 30px;
  }

  .page-header h1 {
    color: #2e8b57;
    font-size: 32px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .page-header p {
    color: #64748b;
    font-size: 16px;
  }

  .back-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: #f1f5f9;
    color: #475569;
    text-decoration: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s;
    margin-bottom: 20px;
  }

  .back-button:hover {
    background: #e2e8f0;
    transform: translateX(-3px);
  }

  .filters-bar {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    margin-bottom: 20px;
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    align-items: center;
  }

  .filter-btn {
    padding: 10px 20px;
    border: 2px solid #e2e8f0;
    background: white;
    color: #475569;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
  }

  .filter-btn:hover {
    border-color: #3cb371;
    color: #3cb371;
  }

  .filter-btn.active {
    background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%);
    color: white;
    border-color: #3cb371;
  }

  .mark-all-btn {
    margin-left: auto;
    padding: 10px 20px;
    background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
  }

  .mark-all-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(60, 179, 113, 0.3);
  }

  .notifications-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    overflow: hidden;
  }

  .notification-item-full {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    padding: 24px;
    border-bottom: 1px solid #f3f4f6;
    transition: all 0.2s;
    cursor: pointer;
    position: relative;
  }

  .notification-item-full:hover {
    background: #f7fdf9;
  }

  .notification-item-full.read {
    opacity: 0.6;
  }

  .notification-icon-full {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
  }

  .notification-icon-full.encomenda {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
  }

  .notification-icon-full.devolucao {
    background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
  }

  .notification-icon-full.utilizador {
    background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%);
  }

  .notification-content-full {
    flex: 1;
  }

  .notification-title-full {
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 6px;
  }

  .notification-message-full {
    font-size: 14px;
    color: #64748b;
    margin-bottom: 8px;
    line-height: 1.5;
  }

  .notification-time-full {
    font-size: 13px;
    color: #94a3b8;
    display: flex;
    align-items: center;
    gap: 5px;
  }

  .notification-actions {
    display: flex;
    gap: 10px;
    margin-top: 12px;
  }

  .action-btn {
    padding: 6px 14px;
    border: 1px solid #e2e8f0;
    background: white;
    color: #475569;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
  }

  .action-btn:hover {
    border-color: #3cb371;
    color: #3cb371;
  }

  .unread-badge {
    position: absolute;
    top: 24px;
    right: 24px;
    width: 10px;
    height: 10px;
    background: #3cb371;
    border-radius: 50%;
    box-shadow: 0 0 0 3px rgba(60, 179, 113, 0.2);
  }

  .empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #94a3b8;
  }

  .empty-state i {
    font-size: 64px;
    margin-bottom: 16px;
    opacity: 0.5;
  }

  .empty-state p {
    font-size: 16px;
  }

  .pagination {
    padding: 20px;
    text-align: center;
    display: flex;
    justify-content: center;
    gap: 10px;
  }

  .page-btn {
    padding: 8px 16px;
    border: 1px solid #e2e8f0;
    background: white;
    color: #475569;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
  }

  .page-btn:hover {
    border-color: #3cb371;
    color: #3cb371;
  }

  .page-btn.active {
    background: #3cb371;
    color: white;
    border-color: #3cb371;
  }

  .page-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
  </style>
</head>

<body>
  <div class="container">
    <?php
        $back_url = 'DashboardCliente.php';
        if ($tipo_utilizador == 1) {
            $back_url = 'DashboardAdmin.php';
        } elseif ($tipo_utilizador == 3) {
            $back_url = 'DashboardAnunciante.php';
        }
        ?>
    <a href="<?php echo $back_url; ?>" class="back-button">
      <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
    </a>

    <div class="page-header">
      <h1><i class="fas fa-bell"></i> Todas as Notificações</h1>
      <p>Gerir e visualizar todas as suas notificações</p>
    </div>

    <div class="filters-bar">
      <button class="filter-btn active" data-filter="todas">
        <i class="fas fa-list"></i> Todas
      </button>
      <button class="filter-btn" data-filter="nao-lidas">
        <i class="fas fa-bell"></i> Não Lidas
      </button>
      <button class="filter-btn" data-filter="lidas">
        <i class="fas fa-check-circle"></i> Lidas
      </button>
      <button class="mark-all-btn" onclick="marcarTodasComoLidas()">
        <i class="fas fa-check-double"></i> Marcar Todas como Lidas
      </button>
    </div>

    <div class="notifications-container" id="notificationsContainer">
      <!-- Notificações carregadas via AJAX -->
    </div>

    <div class="pagination" id="pagination" style="display: none;">
      <button class="page-btn" onclick="previousPage()"><i class="fas fa-chevron-left"></i></button>
      <span id="pageInfo" class="page-btn active">Página 1</span>
      <button class="page-btn" onclick="nextPage()"><i class="fas fa-chevron-right"></i></button>
    </div>
  </div>

  <script>
  let currentFilter = 'todas';
  let currentPage = 1;
  const itemsPerPage = 20;
  let allNotifications = [];
  let filteredNotifications = [];

  $(document).ready(function() {
    carregarTodasNotificacoes();
    setupFilterButtons();
  });

  function setupFilterButtons() {
    $('.filter-btn').click(function() {
      $('.filter-btn').removeClass('active');
      $(this).addClass('active');
      currentFilter = $(this).data('filter');
      currentPage = 1;
      aplicarFiltro();
    });
  }

  function carregarTodasNotificacoes() {
    $.ajax({
      url: 'src/controller/controllerNotifications.php',
      method: 'GET',
      data: {
        op: 5
      }, // Nova operação: listar TODAS (lidas + não lidas)
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          allNotifications = response.data || [];
          aplicarFiltro();
        } else {
          mostrarEmpty();
        }
      },
      error: function() {
        mostrarEmpty();
      }
    });
  }

  function aplicarFiltro() {
    if (currentFilter === 'todas') {
      filteredNotifications = allNotifications;
    } else if (currentFilter === 'nao-lidas') {
      filteredNotifications = allNotifications.filter(n => !n.lida);
    } else if (currentFilter === 'lidas') {
      filteredNotifications = allNotifications.filter(n => n.lida);
    }

    renderizarNotificacoes();
  }

  function renderizarNotificacoes() {
    const container = $('#notificationsContainer');

    if (filteredNotifications.length === 0) {
      mostrarEmpty();
      return;
    }

    // Paginação
    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const pageNotifications = filteredNotifications.slice(start, end);

    let html = '';
    pageNotifications.forEach(notif => {
      const timeAgo = calcularTempoDecorrido(notif.data);
      const icone = notif.icone || getIconeByTipo(notif.tipo);
      const readClass = notif.lida ? 'read' : '';

      html += `
                    <div class="notification-item-full ${readClass}" data-tipo="${notif.tipo}" data-id="${notif.id}">
                        ${!notif.lida ? '<div class="unread-badge"></div>' : ''}
                        <div class="notification-icon-full ${notif.tipo}">
                            ${icone}
                        </div>
                        <div class="notification-content-full">
                            <div class="notification-title-full">${notif.titulo}</div>
                            <div class="notification-message-full">${notif.mensagem}</div>
                            <div class="notification-time-full">
                                <i class="far fa-clock"></i> ${timeAgo}
                            </div>
                            <div class="notification-actions">
                                <button class="action-btn" onclick="abrirNotificacao('${notif.tipo}', ${notif.id}, '${notif.link}')">
                                    <i class="fas fa-external-link-alt"></i> Ver Detalhes
                                </button>
                                ${!notif.lida ? `
                                    <button class="action-btn" onclick="marcarComoLida('${notif.tipo}', ${notif.id})">
                                        <i class="fas fa-check"></i> Marcar como Lida
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
    });

    container.html(html);

    // Mostrar paginação se necessário
    const totalPages = Math.ceil(filteredNotifications.length / itemsPerPage);
    if (totalPages > 1) {
      $('#pagination').show();
      $('#pageInfo').text(`Página ${currentPage} de ${totalPages}`);
    } else {
      $('#pagination').hide();
    }
  }

  function mostrarEmpty() {
    $('#notificationsContainer').html(`
                <div class="empty-state">
                    <i class="fas fa-bell-slash"></i>
                    <p>Nenhuma notificação encontrada</p>
                </div>
            `);
    $('#pagination').hide();
  }

  function calcularTempoDecorrido(data) {
    const agora = new Date();
    const dataNotif = new Date(data);
    const diff = Math.floor((agora - dataNotif) / 1000);

    if (diff < 60) return 'Agora mesmo';
    if (diff < 3600) return `${Math.floor(diff / 60)} min atrás`;
    if (diff < 86400) return `${Math.floor(diff / 3600)}h atrás`;
    if (diff < 604800) return `${Math.floor(diff / 86400)}d atrás`;

    return dataNotif.toLocaleDateString('pt-PT');
  }

  function getIconeByTipo(tipo) {
    const icones = {
      encomenda: '📦',
      devolucao: '↩️',
      utilizador: '👤',
      produto: '📦'
    };
    return icones[tipo] || '🔔';
  }

  function abrirNotificacao(tipo, id, link) {
    // Marcar como lida e redirecionar
    $.post('src/controller/controllerNotifications.php', {
      op: 3,
      tipo: tipo,
      id: id
    }).always(function() {
      window.location.href = link;
    });
  }

  function marcarComoLida(tipo, id) {
    $.post('src/controller/controllerNotifications.php', {
      op: 3,
      tipo: tipo,
      id: id
    }).done(function(response) {
      if (response.success) {
        carregarTodasNotificacoes();
      }
    });
  }

  function marcarTodasComoLidas() {
    $.post('src/controller/controllerNotifications.php', {
      op: 4
    }).done(function(response) {
      if (response.success) {
        carregarTodasNotificacoes();
      }
    });
  }

  function nextPage() {
    const totalPages = Math.ceil(filteredNotifications.length / itemsPerPage);
    if (currentPage < totalPages) {
      currentPage++;
      renderizarNotificacoes();
      window.scrollTo(0, 0);
    }
  }

  function previousPage() {
    if (currentPage > 1) {
      currentPage--;
      renderizarNotificacoes();
      window.scrollTo(0, 0);
    }
  }
  </script>
</body>

</html>