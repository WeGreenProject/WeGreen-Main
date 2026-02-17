
document.querySelectorAll(".toggle-password").forEach((button) => {
  button.addEventListener("click", function () {
    const targetId = this.getAttribute("data-target");
    const input = document.getElementById(targetId);
    const icon = this.querySelector("i");

    if (input.type === "password") {
      input.type = "text";
      icon.classList.remove("fa-eye");
      icon.classList.add("fa-eye-slash");
    } else {
      input.type = "password";
      icon.classList.remove("fa-eye-slash");
      icon.classList.add("fa-eye");
    }
  });
});

document
  .getElementById("changePasswordForm")
  .addEventListener("submit", function (e) {
    e.preventDefault();

    const currentPassword = document.getElementById("currentPassword").value;
    const newPassword = document.getElementById("newPassword").value;
    const confirmPassword = document.getElementById("confirmPassword").value;

    
    if (newPassword.length < 6) {
      showModernWarningModal(
        "Senha muito curta",
        "A nova senha deve ter no m�nimo 6 caracteres",
      );
      return;
    }

    if (newPassword !== confirmPassword) {
      showModernWarningModal(
        "Senhas n�o coincidem",
        "A nova senha e a confirma��o devem ser iguais",
      );
      return;
    }

    if (currentPassword === newPassword) {
      showModernWarningModal(
        "Senha igual",
        "A nova senha deve ser diferente da senha atual",
      );
      return;
    }

    
    const formData = new FormData();
    formData.append("op", "alterarSenha");
    formData.append("senhaAtual", currentPassword);
    formData.append("novaSenha", newPassword);

    fetch("src/controller/controllerPerfil.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          showModernSuccessModal(
            "Senha alterada!",
            "Sua senha foi atualizada com sucesso",
            {
              onClose: () => {
                voltarPagina();
              },
            },
          );
        } else {
          showModernErrorModal(
            "Erro",
            data.message ||
              "N�o foi poss�vel alterar a senha. Verifique se a senha atual est� correta.",
          );
        }
      })
      .catch((error) => {
        showModernErrorModal(
          "Erro",
          "Ocorreu um erro ao processar sua solicita��o",
        );
      });
  });

function voltarPagina() {
  
  if (
    document.referrer &&
    document.referrer.includes(window.location.hostname)
  ) {
    window.location.href = document.referrer;
  } else {
    
    const tipo = parseInt(document.body.getAttribute("data-user-tipo")) || 0;

    if (tipo === 1) {
      
      window.location.href = "DashboardAdmin.php";
    } else if (tipo === 2) {
      
      window.location.href = "DashboardCliente.php";
    } else if (tipo === 3) {
      
      window.location.href = "DashboardAnunciante.php";
    } else {
      
      window.location.href = "login.html";
    }
  }
}
