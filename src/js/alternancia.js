function verificarContaAlternativa() {
  $.post("src/controller/controllerPerfil.php", { op: 12 }, function (resp) {
    try {
      const dados = JSON.parse(resp);
      if (dados.existe) {
        $("#btnAlternarConta").show();
        $("#textoAlternar").text("Alternar para " + dados.nome_tipo);
      }
    } catch (e) {
      console.error("Erro ao verificar conta alternativa:", e);
    }
  }).fail(function () {
    console.error("Erro na requisição de verificação de conta");
  });
}

// Alternar entre contas
function verificarEAlternarConta() {
  Swal.fire({
    title: "Alternar Conta?",
    text: "Deseja mudar para a sua outra conta?",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3cb371",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Sim, alternar!",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      // Descobrir tipo alvo (se é 2, vai para 3 e vice-versa)
      $.post(
        "src/controller/controllerPerfil.php",
        { op: 12 },
        function (resp) {
          try {
            const dados = JSON.parse(resp);
            if (dados.existe) {
              // Fazer alternância
              $.post(
                "src/controller/controllerPerfil.php",
                { op: 15, tipoAlvo: dados.tipo },
                function (resposta) {
                  try {
                    const resultado = JSON.parse(resposta);
                    if (resultado.success) {
                      Swal.fire({
                        icon: "success",
                        title: "Conta Alternada!",
                        text: "A redirecionar...",
                        timer: 1500,
                        showConfirmButton: false,
                      }).then(() => {
                        window.location.href = resultado.redirect;
                      });
                    } else {
                      Swal.fire("Erro", resultado.msg, "error");
                    }
                  } catch (e) {
                    Swal.fire("Erro", "Erro ao processar resposta", "error");
                  }
                }
              ).fail(function () {
                Swal.fire("Erro", "Erro ao alternar conta", "error");
              });
            }
          } catch (e) {
            Swal.fire("Erro", "Erro ao processar dados", "error");
          }
        }
      ).fail(function () {
        Swal.fire("Erro", "Erro ao verificar conta", "error");
      });
    }
  });
}

// Chamar ao carregar a página
$(document).ready(function () {
  // Verificar se existe botão de alternância antes de verificar
  if ($("#btnAlternarConta").length > 0) {
    verificarContaAlternativa();
  }
});
