<?php
session_start();

// Verifica se está logado
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
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
  </head>
  <body>
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
            <img
              src="src/img/2-removebg-preview.png"
              alt="Wegreen"
              class="logo-img"
              style="width: auto; height: 40px"
            />
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
            <input
              type="password"
              id="currentPassword"
              placeholder="Senha atual"
              required
            />
            <label>Senha Atual</label>
            <button type="button" class="toggle-password" data-target="currentPassword">
              <i class="fas fa-eye"></i>
            </button>
          </div>

          <div class="input-group">
            <div class="input-icon">
              <i class="fas fa-lock"></i>
            </div>
            <input
              type="password"
              id="newPassword"
              placeholder="Nova senha"
              required
              minlength="6"
            />
            <label>Nova Senha</label>
            <button type="button" class="toggle-password" data-target="newPassword">
              <i class="fas fa-eye"></i>
            </button>
          </div>

          <div class="input-group">
            <div class="input-icon">
              <i class="fas fa-lock"></i>
            </div>
            <input
              type="password"
              id="confirmPassword"
              placeholder="Confirme a nova senha"
              required
              minlength="6"
            />
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

          <button
            type="submit"
            id="btnChangePassword"
            class="btn-primary"
          >
            <span>Alterar Senha</span>
            <i class="fas fa-check"></i>
          </button>

          <div style="text-align: center; margin-top: 15px;">
            <button type="button" class="link-primary" onclick="voltarPagina()" style="background: none; border: none; cursor: pointer; font-size: 14px;">
              <i class="fas fa-arrow-left"></i> Voltar
            </button>
          </div>
        </form>
      </div>
    </div>

    <script>
      // Toggle password visibility
      document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
          const targetId = this.getAttribute('data-target');
          const input = document.getElementById(targetId);
          const icon = this.querySelector('i');

          if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
          } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
          }
        });
      });

      // Form submission
      document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        // Validações
        if (newPassword.length < 6) {
          Swal.fire({
            icon: 'warning',
            title: 'Senha muito curta',
            text: 'A nova senha deve ter no mínimo 6 caracteres',
            confirmButtonColor: '#3cb371'
          });
          return;
        }

        if (newPassword !== confirmPassword) {
          Swal.fire({
            icon: 'warning',
            title: 'Senhas não coincidem',
            text: 'A nova senha e a confirmação devem ser iguais',
            confirmButtonColor: '#3cb371'
          });
          return;
        }

        if (currentPassword === newPassword) {
          Swal.fire({
            icon: 'warning',
            title: 'Senha igual',
            text: 'A nova senha deve ser diferente da senha atual',
            confirmButtonColor: '#3cb371'
          });
          return;
        }

        // Enviar requisição
        const formData = new FormData();
        formData.append('op', 'alterarSenha');
        formData.append('senhaAtual', currentPassword);
        formData.append('novaSenha', newPassword);

        fetch('src/controller/controllerPerfil.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'Senha alterada!',
              text: 'Sua senha foi atualizada com sucesso',
              confirmButtonColor: '#3cb371'
            }).then(() => {
              voltarPagina();
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Erro',
              text: data.message || 'Não foi possível alterar a senha. Verifique se a senha atual está correta.',
              confirmButtonColor: '#3cb371'
            });
          }
        })
        .catch(error => {
          console.error('Erro:', error);
          Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: 'Ocorreu um erro ao processar sua solicitação',
            confirmButtonColor: '#3cb371'
          });
        });
      });

      function voltarPagina() {
        // Volta para a página anterior ou dashboard
        const tipo = <?php echo $_SESSION['tipo'] ?? 0; ?>;

        if (tipo === 1 || tipo === 3) {
          // Anunciante
          window.location.href = 'DashboardAnunciante.php';
        } else if (tipo === 2) {
          // Cliente
          window.location.href = 'DashboardCliente.php';
        } else if (tipo === 4) {
          // Admin
          window.location.href = 'DashboardAdmin.php';
        } else {
          // Fallback - voltar para login
          window.location.href = 'login.html';
        }
      }
    </script>
  </body>
</html>
