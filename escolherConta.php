<?php
session_start();

if (!isset($_SESSION['email']) || empty($_SESSION['perfil_duplo'])) {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Escolher Conta - WeGreen</title>
  <link rel="icon" type="image/png" href="src/img/WeGreenfav.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <script src="src/js/lib/jquery.js"></script>
  <script src="src/js/lib/sweatalert.js"></script>
    <script src="src/js/wegreen-modals.js"></script>

  <link rel="stylesheet" href="src/css/escolherConta.css">
</head>

<body>
  <div class="container">
    <div class="header">
      <div class="logo">
        <i class="fas fa-leaf"></i>
        <div class="logo-text">
          <h1>WeGreen</h1>
          <p>Moda Sustentável</p>
        </div>
      </div>
      <h2>Escolha o tipo de conta que deseja aceder</h2>
      <div class="user-info">
        <p><strong><?php echo isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Utilizador'; ?></strong> •
          <?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?></p>
      </div>
    </div>

    <div class="accounts-container">
      <!-- Conta Cliente -->
      <div class="account-card" onclick="selecionarConta(2)">
        <div class="account-icon">
          <i class="fas fa-shopping-bag"></i>
        </div>
        <h3 class="account-title">Cliente</h3>
      </div>

      <!-- Conta Anunciante -->
      <div class="account-card" onclick="selecionarConta(3)">
        <div class="account-icon">
          <i class="fas fa-store"></i>
        </div>
        <h3 class="account-title">Anunciante</h3>
      </div>
    </div>

    <div class="logout-link">
      <a href="src/controller/controllerLogin.php?op=2">
        <i class="fas fa-sign-out-alt"></i> Terminar sessão
      </a>
    </div>
  </div>

  <script src="src/js/escolherConta.js"></script>
</body>

</html>
