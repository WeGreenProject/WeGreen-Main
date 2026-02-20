<?php
session_start();

if(!isset($_SESSION['utilizador']) || !isset($_SESSION['tipo'])) {
    header("Location: login.html");
    exit();
}

if(!in_array((int)$_SESSION['tipo'], [1, 2, 3], true)) {
  header("Location: forbiddenerror.html");
  exit();
}

$tipo_utilizador = $_SESSION['tipo'];
$nome_utilizador = $_SESSION['nome'] ?? 'Utilizador';

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
  <script src="src/js/lib/sweatalert.js"></script>
  <script src="src/js/wegreen-modals.js"></script>
  <link rel="stylesheet" href="src/css/notificacoes.css">
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

  <script src="src/js/notificacoes.js"></script>
</body>

</html>
