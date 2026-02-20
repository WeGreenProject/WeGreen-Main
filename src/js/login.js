function login1() {
  let dados = new FormData();
  dados.append("op", 1);
  dados.append("email", $("#emailLogin").val());
  dados.append("password", $("#passwordLogin").val());

  $.ajax({
    url: "src/controller/controllerLogin.php",
    method: "POST",
    data: dados,
    dataType: "json",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (obj) {
      if (obj.flag) {
        alerta2(obj.msg);

        if (obj.perfil_duplo) {
          setTimeout(function () {
            window.location.href = "escolherConta.php";
          }, 1500);
        } else {
          const urlParams = new URLSearchParams(window.location.search);
          const redirectUrl = urlParams.get("redirect");

          setTimeout(function () {
            if (redirectUrl) {
              window.location.href = redirectUrl;
            } else {
              if (obj.tipo_utilizador == 1) {
                window.location.href = "DashboardAdmin.php";
              } else if (obj.tipo_utilizador == 3) {
                window.location.href = "DashboardAnunciante.php";
              } else if (obj.tipo_utilizador == 2) {
                window.location.href = "DashboardCliente.php";
              } else {
                window.location.href = "index.html";
              }
            }
          }, 2000);
        }
      } else {
        if (obj.email_nao_verificado) {
          showModernConfirmModal("Email Não Verificado", obj.msg, {
            icon: "fa-exclamation",
            iconBg:
              "background: linear-gradient(135deg, #f4c28a 0%, #e2a76f 100%);",
            confirmText: '<i class="fas fa-paper-plane"></i> Reenviar Email',
            cancelText: '<i class="fas fa-times"></i> Fechar',
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href =
                "reenviar_verificacao.html?email=" +
                encodeURIComponent(obj.email);
            }
          });
        } else {
          alerta("Utilizador", obj.msg, "error");
        }
      }
    })

    .fail(function (jqXHR, textStatus, errorThrown) {
      Swal.fire({
        icon: "error",
        title: "Erro ao fazer login",
        text: "Ocorreu um erro ao processar o login. Por favor, tente novamente.",
        confirmButtonColor: "#3cb371",
      });
    });
}
function alerta2(msg) {
  const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    customClass: {
      popup: "custom-toast",
    },
    didOpen: (toast) => {
      toast.onmouseenter = Swal.stopTimer;
      toast.onmouseleave = Swal.resumeTimer;
    },
  });
  Toast.fire({
    icon: "success",
    title: msg,
  });
}
function alerta(titulo, msg, icon) {
  Swal.fire({
    html: `
      <div style="text-align: center;">
        <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);">
          <i class="fas fa-times-circle" style="font-size: 40px; color: white;"></i>
        </div>
        <h2 style="margin: 0 0 10px 0; color: #2d3748; font-size: 24px; font-weight: 700;">${titulo}</h2>
        <p style="color: #64748b; font-size: 15px; margin: 0;">${msg}</p>
      </div>
    `,
    confirmButtonColor: "#dc3545",
    confirmButtonText: '<i class="fas fa-check"></i> OK',
    customClass: {
      confirmButton: "swal2-confirm-modern",
      popup: "swal2-border-radius",
    },
    buttonsStyling: false,
    didOpen: () => {
      const style = document.createElement("style");
      style.textContent = `
        .swal2-confirm-modern {
          padding: 12px 30px !important;
          border-radius: 8px !important;
          font-weight: 600 !important;
          font-size: 14px !important;
          cursor: pointer !important;
          transition: all 0.3s ease !important;
          border: none !important;
          margin: 5px !important;
          background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%) !important;
          color: white !important;
        }
        .swal2-confirm-modern:hover {
          transform: translateY(-2px) !important;
          box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4) !important;
        }
        .swal2-border-radius {
          border-radius: 12px !important;
        }
      `;
      document.head.appendChild(style);
    },
  });
}

document.addEventListener("DOMContentLoaded", function () {
  const togglePassword = document.querySelector(".toggle-password");
  const passwordInput = document.querySelector("#passwordLogin");

  if (togglePassword && passwordInput) {
    togglePassword.addEventListener("click", function () {
      const type =
        passwordInput.getAttribute("type") === "password" ? "text" : "password";
      passwordInput.setAttribute("type", type);

      const icon = this.querySelector("i");
      icon.classList.toggle("fa-eye");
      icon.classList.toggle("fa-eye-slash");
    });
  }
});
