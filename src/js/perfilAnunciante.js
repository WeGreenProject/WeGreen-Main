
$("#userMenuBtn").click(function (e) {
  e.stopPropagation();
  $("#userDropdown").toggleClass("active");
});

$(document).click(function (e) {
  if (!$(e.target).closest(".navbar-user").length) {
    $("#userDropdown").removeClass("active");
  }
});

function normalizarTextoComparacao(valor) {
  return (valor || "")
    .toString()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .toLowerCase()
    .trim();
}

function extrairDadosMorada(morada) {
  const resultado = { distrito: "", localidade: "" };
  if (!morada) {
    return resultado;
  }

  const moradaTexto = morada.toString();
  const partes = moradaTexto
    .split(",")
    .map((parte) => parte.trim())
    .filter(Boolean);

  if (partes.length > 1) {
    resultado.localidade = partes[partes.length - 1];
  }

  const moradaNormalizada = normalizarTextoComparacao(moradaTexto);
  $("#distrito option").each(function () {
    const valor = ($(this).val() || "").trim();
    if (!valor || resultado.distrito) {
      return;
    }

    const valorNormalizado = normalizarTextoComparacao(valor);
    if (valorNormalizado && moradaNormalizada.includes(valorNormalizado)) {
      resultado.distrito = valor;
    }
  });

  return resultado;
}

function switchTab(tabName) {
  $(".profile-tab").removeClass("active");
  $(".tab-pane").removeClass("active");

  event.target.closest(".profile-tab").classList.add("active");
  $(`#tab-${tabName}`).addClass("active");
}

$(document).ready(function () {
  carregarPerfilAnunciante();
  verificarPlanoUpgrade();
  carregarInfoExpiracaoPlano();
});

function carregarInfoExpiracaoPlano() {
  $.ajax({
    url: "src/controller/controllerDashboardAnunciante.php",
    type: "POST",
    data: { op: 38 },
    success: function (response) {
      try {
        const data =
          typeof response === "string" ? JSON.parse(response) : response;

        if (data.success) {
          const container = $("#planExpirationContainer");
          const diasSpan = $("#diasRestantes");

          if (data.status_plano === "revertido") {
            
            
            $("#planoNome").text("Essencial Verde");
            
            container.hide();

            
            carregarPerfilAnunciante();
          } else if (
            data.status_plano === "ativo" &&
            data.dias_restantes !== null
          ) {
            const dias = data.dias_restantes;

            if (dias <= 3) {
              diasSpan.text(
                `${dias} dia${dias !== 1 ? "s" : ""} restante${dias !== 1 ? "s" : ""} - Renove agora!`,
              );
              container.removeClass("warning").addClass("critical");
              container.show();
            } else if (dias <= 7) {
              diasSpan.text(`${dias} dias restantes`);
              container.addClass("warning").removeClass("critical");
              container.show();
            } else {
              diasSpan.text(`${dias} dias restantes`);
              container.removeClass("warning critical");
              container.show();
            }
          } else {
            
            container.hide();
          }
        }
      } catch (e) {}
    },
    error: function () {},
  });
}

function carregarPerfilAnunciante() {
  $.ajax({
    url: "src/controller/controllerDashboardAnunciante.php",
    type: "POST",
    data: { op: 27 },
    success: function (response) {
      try {
        const dados =
          typeof response === "string" ? JSON.parse(response) : response;

        if (dados.error) {
          return;
        }

        
        if (dados.nome_completo || dados.nome) {
          $("#nome").val(dados.nome_completo || dados.nome);
          $("#profileName").text(dados.nome_completo || dados.nome);
        }
        if (dados.email) {
          $("#email").val(dados.email);
          $("#emailRecuperacao").text(dados.email);
        }
        if (dados.telefone) {
          $("#telefone").val(dados.telefone);
        }
        if (dados.nif) {
          $("#nif").val(dados.nif);
        }
        if (dados.morada) {
          $("#morada").val(dados.morada);
        }

        const fallbackMorada = extrairDadosMorada(dados.morada);
        const distritoFinal = dados.distrito || fallbackMorada.distrito;
        const localidadeFinal = dados.localidade || fallbackMorada.localidade;
        const codigoPostalFinal = dados.codigo_postal || "";

        if (distritoFinal) {
          $("#distrito").val(distritoFinal);
        }
        if (localidadeFinal) {
          $("#localidade").val(localidadeFinal);
        }
        if (codigoPostalFinal) {
          $("#codigo_postal").val(codigoPostalFinal);
        }

        
        if (dados.pontos_conf !== undefined) {
          $("#pontosConfianca").text(dados.pontos_conf);
        }

        
        if (dados.plano_nome) {
          $("#planoNome").text(dados.plano_nome);
        }

        
        if (dados.ranking_nome) {
          const rankingBadge = $("#rankingBadge");
          const rankingNome = dados.ranking_nome;
          $("#rankingNome").text(rankingNome);

          
          rankingBadge.removeClass(
            "rank-sem-classificacao rank-bronze rank-prata rank-ouro rank-platina",
          );
          const rankClass = rankingNome
            .toLowerCase()
            .replace(/\s+/g, "-")
            .replace(/[íìîï]/g, "i")
            .replace(/[áàâãä]/g, "a");
          if (rankClass.includes("bronze")) {
            rankingBadge.addClass("rank-bronze");
          } else if (rankClass.includes("prata")) {
            rankingBadge.addClass("rank-prata");
          } else if (rankClass.includes("ouro")) {
            rankingBadge.addClass("rank-ouro");
          } else if (rankClass.includes("platina")) {
            rankingBadge.addClass("rank-platina");
          } else {
            rankingBadge.addClass("rank-sem-classificacao");
          }

          rankingBadge.show();
        }

        
        if (dados.foto && dados.foto !== "src/img/default_user.png") {
          $("#avatarImg").attr("src", dados.foto);
        }
      } catch (e) {}
    },
    error: function (xhr, status, error) {},
  });
}

function verificarPlanoUpgrade() {
  $.ajax({
    url: "src/controller/controllerDashboardAnunciante.php",
    type: "POST",
    data: { op: 27 },
    success: function (resp) {
      try {
        const dados = typeof resp === "string" ? JSON.parse(resp) : resp;
        if (dados && dados.plano_nome !== "Profissional Eco+") {
          $("#upgradeBtn").show();
        } else {
          $("#upgradeBtn").hide();
        }
      } catch (e) {
        
        $("#upgradeBtn").show();
      }
    },
    error: function () {
      $("#upgradeBtn").show();
    },
  });
}

$("#profileForm").submit(function (e) {
  e.preventDefault();

  const dados = {
    op: 28, 
    nome: $("#nome").val().trim(),
    email: $("#email").val().trim(),
    telefone: $("#telefone").val().trim(),
    nif: $("#nif").val().trim(),
    morada: $("#morada").val().trim(),
    distrito: $("#distrito").val().trim(),
    localidade: $("#localidade").val().trim(),
    codigo_postal: $("#codigo_postal").val().trim(),
  };

  
  if (!dados.nome || dados.nome.length < 3) {
    return showModernWarningModal(
      "Atenção",
      "Nome deve ter no mínimo 3 caracteres",
    );
  }

  if (!dados.email || !dados.email.includes("@")) {
    return showModernWarningModal("Atenção", "Email inválido");
  }

  if (!dados.morada || dados.morada.trim().length < 10) {
    return showModernWarningModal(
      "Atenção",
      "Morada completa é obrigatória (mínimo 10 caracteres)",
    );
  }

  if (
    !dados.codigo_postal ||
    !/^[0-9]{4}-[0-9]{3}$/.test(dados.codigo_postal)
  ) {
    return showModernWarningModal(
      "Atenção",
      "Código postal inválido (use XXXX-XXX)",
    );
  }

  showModernConfirmModal(
    "Guardar alterações?",
    "As suas informações de perfil serão atualizadas",
    {
      confirmText: '<i class="fas fa-check"></i> Sim, guardar',
      icon: "fa-save",
      iconBg:
        "background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%); box-shadow: 0 8px 20px rgba(60, 179, 113, 0.3);",
    },
  ).then((result) => {
    if (result.isConfirmed) {
      $.post(
        "src/controller/controllerDashboardAnunciante.php",
        dados,
        function (resp) {
          const resultado = typeof resp === "string" ? JSON.parse(resp) : resp;
          if (resultado.success) {
            showModernSuccessModal("Perfil atualizado!", resultado.message, {
              timer: 2000,
              onClose: () => carregarPerfilAnunciante(),
            });
          } else {
            showModernErrorModal("Erro", resultado.message);
          }
        },
      ).fail(function () {
        showModernErrorModal("Erro", "Não foi possível atualizar o perfil");
      });
    }
  });
});

function uploadAvatar(input) {
  if (input.files && input.files[0]) {
    const file = input.files[0];

    
    if (!file.type.match("image.*")) {
      return showModernErrorModal(
        "Erro",
        "Por favor selecione uma imagem válida",
      );
    }

    
    if (file.size > 5 * 1024 * 1024) {
      return showModernErrorModal("Erro", "A imagem deve ter no máximo 5MB");
    }

    const formData = new FormData();
    formData.append("avatar", file);
    formData.append("op", 30); 

    $.ajax({
      url: "src/controller/controllerDashboardAnunciante.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (resp) {
        const resultado = JSON.parse(resp);
        if (resultado.success) {
          $("#avatarImg").attr(
            "src",
            resultado.url + "?" + new Date().getTime(),
          );
          showModernSuccessModal("Avatar atualizado!", "", { timer: 1500 });
        } else {
          showModernErrorModal("Erro", resultado.message);
        }
      },
      error: function () {
        showModernErrorModal("Erro", "Não foi possível fazer upload da imagem");
      },
    });
  }
}

function logout() {
  $.ajax({
    url: "src/controller/controllerPerfil.php?op=2",
    method: "GET",
  }).always(function () {
    window.location.href = "index.html";
  });
}
