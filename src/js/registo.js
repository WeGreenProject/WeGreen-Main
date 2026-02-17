function registaUser() {
  
  const nome = $("#nome").val().trim();
  const apelido = $("#apelido").val().trim();
  const email = $("#email").val().trim();
  const nif = $("#nif").val().trim();
  const morada = $("#morada").val().trim();
  const password = $("#password").val();
  const tipoUtilizador = $("#tipoUtilizador").val();

  if (!nome || !apelido || !email || !password || !morada) {
    alerta(
      "Atenção",
      "Por favor, preencha todos os campos obrigatórios",
      "warning",
    );
    return;
  }

  if (morada.length < 10) {
    alerta(
      "Atenção",
      "A morada deve ter pelo menos 10 caracteres (Rua, Número, Código Postal, Cidade)",
      "warning",
    );
    return;
  }

  if (!tipoUtilizador) {
    alerta("Atenção", "Por favor, selecione o tipo de conta", "warning");
    return;
  }

  if (password.length < 6) {
    alerta("Atenção", "A password deve ter pelo menos 6 caracteres", "warning");
    return;
  }

  
  if (nif && !/^\d{9}$/.test(nif)) {
    alerta(
      "Atenção",
      "NIF deve conter exatamente 9 dígitos numéricos",
      "warning",
    );
    return;
  }

  let dados = new FormData();
  dados.append("op", 1);
  dados.append("nome", nome);
  dados.append("apelido", apelido);
  dados.append("email", email);
  dados.append("nif", nif);
  dados.append("morada", morada);
  dados.append("password", password);
  dados.append("tipoUtilizador", tipoUtilizador);

  $.ajax({
    url: "src/controller/controllerRegisto.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      let obj = JSON.parse(msg);
      console.log(msg);
      if (obj.flag) {
        Swal.fire({
          position: "center",
          icon: "success",
          title: "Conta Criada!",
          text: obj.msg,
          showConfirmButton: true,
          confirmButtonText: "Iniciar Sessão",
          confirmButtonColor: "#3cb371",
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = "login.html";
          }
        });
      } else {
        alerta("Erro no Registo", obj.msg, "error");
      }
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
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
    confirmButtonColor: "#3cb371",
  });
}

function togglePassword(fieldId) {
  const field = document.getElementById(fieldId);
  const button = field.parentElement.querySelector(".toggle-password");
  const icon = button.querySelector("i");

  if (field.type === "password") {
    field.type = "text";
    icon.classList.remove("fa-eye");
    icon.classList.add("fa-eye-slash");
  } else {
    field.type = "password";
    icon.classList.remove("fa-eye-slash");
    icon.classList.add("fa-eye");
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const selectElement = document.querySelector("#tipoUtilizador");

  if (selectElement) {
    selectElement.addEventListener("change", function () {
      const inputGroup = this.closest(".input-group");
      if (this.value !== "") {
        inputGroup.classList.add("focused");
      } else {
        inputGroup.classList.remove("focused");
      }
    });
  }
});
