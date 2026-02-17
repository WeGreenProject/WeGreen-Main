function selecionarConta(tipo) {
  $.post(
    "src/controller/controllerPerfil.php",
    {
      op: 15,
      tipoAlvo: tipo,
    },
    function (resp) {
      try {
        
        const cleanResp = resp.trim();
        const resultado = JSON.parse(cleanResp);

        if (resultado.success) {
          showModernSuccessModal("Conta Selecionada!", "A redirecionar...", {
            timer: 1000,
            onClose: () => (window.location.href = resultado.redirect),
          });
        } else {
          showModernErrorModal("Erro", resultado.msg || "Erro desconhecido");
        }
      } catch (e) {
        showModernErrorModal(
          "Erro",
          "Erro ao processar resposta: " + e.message,
        );
      }
    },
  ).fail(function (xhr, status, error) {
    showModernErrorModal("Erro", "Erro ao selecionar conta: " + error);
  });
}
