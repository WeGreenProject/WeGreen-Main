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

        // Verificar se tem perfil duplo
        if (obj.perfil_duplo) {
          setTimeout(function () {
            window.location.href = "escolherConta.php";
          }, 1500);
        } else {
          // Verificar se existe parâmetro redirect na URL
          const urlParams = new URLSearchParams(window.location.search);
          const redirectUrl = urlParams.get("redirect");

          setTimeout(function () {
            if (redirectUrl) {
              window.location.href = redirectUrl;
            } else {
              window.location.href = "index.html";
            }
          }, 2000);
        }
      } else {
        // Verificar se é erro de email não verificado
        if (obj.email_nao_verificado) {
          Swal.fire({
            icon: "warning",
            title: "Email Não Verificado",
            text: obj.msg,
            showCancelButton: true,
            confirmButtonText: "Reenviar Email",
            cancelButtonText: "Fechar",
            confirmButtonColor: "#3cb371",
            cancelButtonColor: "#6b7280",
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
      console.error("Erro completo:", jqXHR.responseText);
      console.error("Status:", textStatus);
      console.error("Error:", errorThrown);
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
    position: "center",
    icon: icon,
    title: titulo,
    text: msg,
    showConfirmButton: true,
  });
}

// Toggle password visibility
document.addEventListener("DOMContentLoaded", function () {
  const togglePassword = document.querySelector(".toggle-password");
  const passwordInput = document.querySelector("#passwordLogin");

  if (togglePassword && passwordInput) {
    togglePassword.addEventListener("click", function () {
      const type =
        passwordInput.getAttribute("type") === "password" ? "text" : "password";
      passwordInput.setAttribute("type", type);

      // Toggle icon
      const icon = this.querySelector("i");
      icon.classList.toggle("fa-eye");
      icon.classList.toggle("fa-eye-slash");
    });
  }
});
