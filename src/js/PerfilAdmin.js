
function getInfoUserDropdown() {
  
}

$("#userMenuBtn").click(function (e) {
  e.stopPropagation();
  $("#userDropdown").toggleClass("active");
});

$(document).click(function (e) {
  if (!$(e.target).closest(".navbar-user").length) {
    $("#userDropdown").removeClass("active");
  }
});

function closePasswordModal() {
  $("#passwordModal").removeClass("active");
}

function openPasswordModal() {
  $("#passwordModal").addClass("active");
}

$(document).ready(function () {
  carregarPerfilAdmin();
});

function carregarPerfilAdmin() {

  $.ajax({
    url: "src/controller/controllerAdminPerfil.php",
    type: "POST",
    data: { op: 3 },
    success: function (response) {

      try {
        const dados =
          typeof response === "string" ? JSON.parse(response) : response;

        if (dados.error) {
          showModernErrorModal("Erro", dados.error);
          return;
        }

        
        if (dados.nome) $("#nome").val(dados.nome);
        if (dados.email) $("#email").val(dados.email);
        if (dados.nif) $("#nif").val(dados.nif);
        if (dados.telefone) $("#telefone").val(dados.telefone);

      } catch (e) {
        showModernErrorModal(
          "Erro",
          "Não foi possível carregar os dados do perfil",
        );
      }
    },
    error: function (xhr, status, error) {
      showModernErrorModal("Erro", "Não foi possível carregar o perfil");
    },
  });
}

$("#profileForm").submit(function (e) {
  e.preventDefault();

  const dados = {
    op: 11,
    nomeAdminEdit: $("#nome").val(),
    emailAdminEdit: $("#email").val(),
    nifAdminEdit: $("#nif").val(),
    telefoneAdminEdit: $("#telefone").val(),
  };

  
  if (!dados.nomeAdminEdit || dados.nomeAdminEdit.trim().length < 3) {
    return showModernWarningModal(
      "Atenção",
      "Nome completo é obrigatório (mínimo 3 caracteres)",
    );
  }

  if (!dados.emailAdminEdit || !dados.emailAdminEdit.includes("@")) {
    return showModernWarningModal("Atenção", "Email inválido");
  }

  Swal.close();
  showModernConfirmModal(
    "Guardar alterações?",
    "As suas informações de perfil serão atualizadas",
    {
      confirmText: '<i class="fas fa-save"></i> Sim, guardar!',
      icon: "fa-save",
      iconBg: "background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%);",
    },
  ).then((result) => {
    if (result.isConfirmed) {
      $.post(
        "src/controller/controllerAdminPerfil.php",
        dados,
        function (resp) {
          try {
            const resultado =
              typeof resp === "string" ? JSON.parse(resp) : resp;
            if (resultado.flag) {
              showModernSuccessModal(
                "Perfil atualizado!",
                resultado.msg || "Dados atualizados com sucesso",
                {
                  timer: 2000,
                  onClose: () => {
                    carregarPerfilAdmin();
                  },
                },
              );
            } else {
              showModernErrorModal(
                "Erro",
                resultado.msg || "Não foi possível atualizar o perfil",
              );
            }
          } catch (e) {
            showModernErrorModal("Erro", "Resposta inválida do servidor");
          }
        },
      ).fail(function () {
        showModernErrorModal("Erro", "Não foi possível atualizar o perfil");
      });
    }
  });
});

function logout() {
  $.ajax({
    url: "src/controller/controllerAdminPerfil.php?op=logout",
    method: "GET",
  }).always(function () {
    window.location.href = "index.html";
  });
}
