<?php
session_start();

if(!isset($_SESSION['utilizador'])){
    header("Location: login.html");
    exit();
}

$nome = $_SESSION['nome'] ?? 'Utilizador';
$email = $_SESSION['email'] ?? '';
?>
<!doctype html>
<html lang="pt">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Alterar Senha - WeGreen</title>
  <link rel="stylesheet" href="src/css/login.css" />
  <link rel="icon" type="image/png" href="src/img/WeGreenfav.png" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="src/js/wegreen-modals.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body data-user-tipo="<?php echo $_SESSION['tipo'] ?? 0; ?>">
  <div class="auth-container">
    <div class="auth-background">
      <div class="gradient-overlay"></div>
      <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
      </div>
    </div>
    <div class="auth-card">
      <div class="auth-header">
        <div class="logo">
          <img src="src/img/2-removebg-preview.png" alt="Wegreen" class="logo-img" style="width: auto; height: 40px" />
        </div>
        <h1>Alterar Senha</h1>
        <p>Bem-vindo(a), <?php echo htmlspecialchars($nome); ?></p>
        <small style="color: #64748b; display: block; margin-top: 5px;">
          <?php echo htmlspecialchars($email); ?>
        </small>
      </div>

      <form class="auth-form" id="changePasswordForm">
        <div class="input-group">
          <div class="input-icon">
            <i class="fas fa-lock"></i>
          </div>
          <input type="password" id="currentPassword" placeholder="Senha atual" required />
          <label>Senha Atual</label>
          <button type="button" class="toggle-password" data-target="currentPassword">
            <i class="fas fa-eye"></i>
          </button>
        </div>

        <div class="input-group">
          <div class="input-icon">
            <i class="fas fa-lock"></i>
          </div>
          <input type="password" id="newPassword" placeholder="Nova senha" required minlength="6" />
          <label>Nova Senha</label>
          <button type="button" class="toggle-password" data-target="newPassword">
            <i class="fas fa-eye"></i>
          </button>
        </div>

        <div class="input-group">
          <div class="input-icon">
            <i class="fas fa-lock"></i>
          </div>
          <input type="password" id="confirmPassword" placeholder="Confirme a nova senha" required minlength="6" />
          <label>Confirmar Nova Senha</label>
          <button type="button" class="toggle-password" data-target="confirmPassword">
            <i class="fas fa-eye"></i>
          </button>
        </div>

        <div style="margin-bottom: 15px;">
          <small style="color: #64748b; font-size: 13px;">
            <i class="fas fa-info-circle"></i> A senha deve ter no mínimo 6 caracteres
          </small>
        </div>

        <button type="submit" id="btnChangePassword" class="btn-primary">
          <span>Alterar Senha</span>
          <i class="fas fa-check"></i>
        </button>

        <div style="text-align: center; margin-top: 15px;">
          <button type="button" class="link-primary" onclick="voltarPagina()"
            style="background: none; border: none; cursor: pointer; font-size: 14px;">
            <i class="fas fa-arrow-left"></i> Voltar
          </button>
        </div>
      </form>
    </div>
  </div>

  <script src="src/js/alterarSenha.js"></script>
</body>

</html>
