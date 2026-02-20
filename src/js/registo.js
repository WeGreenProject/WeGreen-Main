function registaUser() {
  const nome = $("#nome").val().trim();
  const apelido = $("#apelido").val().trim();
  const email = $("#email").val().trim();
  const nif = $("#nif").val().trim();
  const morada = $("#morada").val().trim();
  const codigoPostal = $("#codigoPostal").val().trim();
  const distrito = $("#distrito").val();
  const localidade = $("#localidade").val().trim();
  const password = $("#password").val();
  const tipoUtilizador = $("#tipoUtilizador").val();

  if (
    !nome ||
    !apelido ||
    !email ||
    !password ||
    !morada ||
    !codigoPostal ||
    !distrito ||
    !localidade
  ) {
    showModernWarningModal(
      "Atenção",
      "Por favor, preencha todos os campos obrigatórios",
    );
    return;
  }

  if (morada.length < 10) {
    showModernWarningModal(
      "Atenção",
      "A morada deve ter pelo menos 10 caracteres (Rua, Número, Código Postal, Cidade)",
    );
    return;
  }

  if (!/^\d{4}-\d{3}$/.test(codigoPostal)) {
    showModernWarningModal("Atenção", "Código postal inválido (use XXXX-XXX)");
    return;
  }

  if (!tipoUtilizador) {
    showModernWarningModal("Atenção", "Por favor, selecione o tipo de conta");
    return;
  }

  if (password.length < 6) {
    showModernWarningModal(
      "Atenção",
      "A password deve ter pelo menos 6 caracteres",
    );
    return;
  }

  if (nif && !/^\d{9}$/.test(nif)) {
    showModernWarningModal(
      "Atenção",
      "NIF deve conter exatamente 9 dígitos numéricos",
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
  dados.append("codigo_postal", codigoPostal);
  dados.append("distrito", distrito);
  dados.append("localidade", localidade);

  $.ajax({
    url: "src/controller/controllerRegisto.php",
    method: "POST",
    data: dados,
    dataType: "json",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (obj) {
      const sucesso = !!(obj && (obj.flag === true || obj.success === true));
      const mensagem =
        (obj && (obj.msg || obj.message)) ||
        "Não foi possível concluir o registo.";

      if (sucesso) {
        showModernSuccessModal("Conta Criada!", mensagem, {
          onClose: function (result) {
            if (result && result.isConfirmed) {
              window.location.href = "login.html";
            }
          },
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = "login.html";
          }
        });
      } else {
        if (!sucesso) {
          showModernErrorModal("Erro no Registo", mensagem);
        }
      }
    })

    .fail(function (jqXHR, textStatus) {
      let serverMessage =
        (jqXHR &&
          jqXHR.responseJSON &&
          (jqXHR.responseJSON.msg || jqXHR.responseJSON.message)) ||
        "Erro de comunicação com o servidor. Tente novamente.";

      if (
        (!jqXHR.responseJSON || !serverMessage) &&
        jqXHR &&
        typeof jqXHR.responseText === "string"
      ) {
        try {
          const parsed = JSON.parse(jqXHR.responseText);
          serverMessage = parsed.msg || parsed.message || serverMessage;
        } catch (e) {}
      }

      showModernErrorModal("Erro no Registo", serverMessage);
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
  if (icon === "warning") {
    showModernWarningModal(titulo, msg);
    return;
  }

  if (icon === "error") {
    showModernErrorModal(titulo, msg);
    return;
  }

  if (icon === "success") {
    showModernSuccessModal(titulo, msg);
    return;
  }

  showModernInfoModal(titulo, msg);
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
