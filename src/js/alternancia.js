function parseAlternanciaResponse(payload) {
  if (typeof payload === "string") {
    try {
      return JSON.parse(payload);
    } catch (e) {
      console.error("Resposta inválida em alternância:", payload, e);
      return null;
    }
  }
  return payload;
}

function verificarContaAlternativa() {
  $.post("src/controller/controllerPerfil.php", { op: 12 }, function (resp) {
    try {
      const dados = parseAlternanciaResponse(resp);
      if (!dados) return;
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
      
      $.post(
        "src/controller/controllerPerfil.php",
        { op: 12 },
        function (resp) {
          try {
            const dados = parseAlternanciaResponse(resp);
            if (!dados) return;
            if (dados.existe) {
              
              $.post(
                "src/controller/controllerPerfil.php",
                { op: 15, tipoAlvo: dados.tipo },
                function (resposta) {
                  try {
                    const resultado = parseAlternanciaResponse(resposta);
                    if (!resultado) {
                      Swal.fire("Erro", "Erro ao processar resposta", "error");
                      return;
                    }
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
                },
              ).fail(function () {
                Swal.fire("Erro", "Erro ao alternar conta", "error");
              });
            }
          } catch (e) {
            Swal.fire("Erro", "Erro ao processar dados", "error");
          }
        },
      ).fail(function () {
        Swal.fire("Erro", "Erro ao verificar conta", "error");
      });
    }
  });
}

$(document).ready(function () {
  
  if ($("#btnAlternarConta").length > 0) {
    verificarContaAlternativa();
  }
});
