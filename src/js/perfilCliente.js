
$("#userMenuBtn").click(function (e) {
  e.stopPropagation();
  $("#userDropdown").toggleClass("active");
});

$(document).click(function (e) {
  if (!$(e.target).closest(".navbar-user").length) {
    $("#userDropdown").removeClass("active");
  }
});

$(document).ready(function () {
  carregarPerfilCliente();
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

function carregarPerfilCliente() {
  $.ajax({
    url: "src/controller/controllerPerfil.php",
    type: "POST",
    data: { op: 12 },
    success: function (response) {
      try {
        
        const dados =
          typeof response === "string" ? JSON.parse(response) : response;

        if (dados.error) {
          showModernErrorModal("Erro", dados.error);
          return;
        }

        
        if (dados.nome_completo || dados.nome) {
          $("#nome").val(dados.nome_completo || dados.nome);
        }
        if (dados.email) {
          $("#email").val(dados.email);
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
      } catch (e) {
        showModernErrorModal(
          "Erro",
          "Não foi possível carregar os dados do perfil",
        );
      }
    },
    error: function (xhr, status, error) {
      showModernErrorModal("Erro", "Erro ao comunicar com o servidor");
    },
  });
}

$("#profileForm").submit(function (e) {
  e.preventDefault();

  const dados = {
    op: 16,
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
      $.post("src/controller/controllerPerfil.php", dados, function (resp) {
        const resultado = JSON.parse(resp);
        if (resultado.success) {
          showModernSuccessModal(
            "Perfil atualizado!",
            resultado.message || "Perfil atualizado com sucesso",
            { timer: 2000, onClose: () => carregarPerfilCliente() },
          );
        } else {
          showModernErrorModal("Erro", resultado.message);
        }
      }).fail(function () {
        showModernErrorModal("Erro", "Não foi possível atualizar o perfil");
      });
    }
  });
});

function logout() {
  $.ajax({
    url: "src/controller/controllerPerfil.php?op=2",
    method: "GET",
  }).always(function () {
    window.location.href = "index.html";
  });
}
