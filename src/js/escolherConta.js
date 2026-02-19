function selecionarConta(tipo) {
  $.post(
    "src/controller/controllerPerfil.php",
    {
      op: 15,
      tipoAlvo: tipo,
    },
    function (resp) {
      try {
        const resultado =
          typeof resp === "string" ? JSON.parse(resp.trim()) : resp;

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
    "json",
  ).fail(function (xhr, status, error) {
    let msg = "Erro ao selecionar conta: " + error;
    if (
      xhr &&
      xhr.responseJSON &&
      (xhr.responseJSON.msg || xhr.responseJSON.message)
    ) {
      msg = xhr.responseJSON.msg || xhr.responseJSON.message;
    }
    showModernErrorModal("Erro", msg);
  });
}
