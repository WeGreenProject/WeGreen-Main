function getDadosTipoPerfil() {
  let dados = new FormData();
  dados.append("op", 1);

  $.ajax({
    url: "src/controller/controllerPerfil.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#PerfilTipo").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function PerfilDoUtilizador() {
  let dados = new FormData();
  dados.append("op", 10);

  $.ajax({
    url: "src/controller/controllerPerfil.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#FotoPerfil").attr("src", msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function logout() {
  Swal.fire({
    html: `
      <div style="padding: 20px 0;">
        <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
          <i class="fas fa-sign-out-alt" style="font-size: 32px; color: #dc2626;"></i>
        </div>
        <h2 style="margin: 0 0 12px 0; color: #1e293b; font-size: 24px; font-weight: 700;">Terminar Sessao?</h2>
        <p style="margin: 0; color: #64748b; font-size: 15px; line-height: 1.6;">Tem a certeza que pretende sair da sua conta?</p>
      </div>
    `,
    showCancelButton: true,
    confirmButtonText: '<i class="fas fa-sign-out-alt"></i> Sim, sair',
    cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
    confirmButtonColor: "#dc2626",
    cancelButtonColor: "#64748b",
    customClass: {
      confirmButton: "swal2-confirm-modern",
      cancelButton: "swal2-cancel-modern",
      popup: "swal2-logout-popup",
    },
    buttonsStyling: false,
    reverseButtons: true,
    didOpen: () => {
      const popup = Swal.getPopup();
      popup.style.borderRadius = "16px";
      popup.style.padding = "25px";

      const confirmBtn = popup.querySelector(".swal2-confirm");
      const cancelBtn = popup.querySelector(".swal2-cancel");

      if (confirmBtn) {
        confirmBtn.style.padding = "12px 28px";
        confirmBtn.style.borderRadius = "10px";
        confirmBtn.style.fontSize = "15px";
        confirmBtn.style.fontWeight = "600";
        confirmBtn.style.border = "none";
        confirmBtn.style.cursor = "pointer";
        confirmBtn.style.transition = "all 0.3s ease";
        confirmBtn.style.backgroundColor = "#dc2626";
        confirmBtn.style.color = "#ffffff";
        confirmBtn.style.marginLeft = "10px";
      }

      if (cancelBtn) {
        cancelBtn.style.padding = "12px 28px";
        cancelBtn.style.borderRadius = "10px";
        cancelBtn.style.fontSize = "15px";
        cancelBtn.style.fontWeight = "600";
        cancelBtn.style.border = "2px solid #e2e8f0";
        cancelBtn.style.cursor = "pointer";
        cancelBtn.style.transition = "all 0.3s ease";
        cancelBtn.style.backgroundColor = "#ffffff";
        cancelBtn.style.color = "#64748b";
      }
    },
  }).then((result) => {
    if (result.isConfirmed) {
<<<<<<< Updated upstream
      window.location.href = "src/controller/controllerPerfil.php?op=2";
=======
      Swal.fire({
        html: `
          <div style="padding: 20px;">
            <div class="swal2-loading-spinner" style="margin: 0 auto 20px;">
              <div style="width: 50px; height: 50px; border: 4px solid #f3f4f6; border-top: 4px solid #3cb371; border-radius: 50%; animation: spin 1s linear infinite;"></div>
            </div>
            <p style="margin: 0; color: #64748b; font-size: 15px;">A terminar sessao...</p>
          </div>
          <style>
            @keyframes spin {
              0% { transform: rotate(0deg); }
              100% { transform: rotate(360deg); }
            }
          </style>
        `,
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
          const popup = Swal.getPopup();
          popup.style.borderRadius = "16px";
        },
      });

      $.ajax({
        url: "src/controller/controllerPerfil.php?op=2",
        method: "GET",
      }).always(function () {
        window.location.href = "index.html";
      });
>>>>>>> Stashed changes
    }
  });
}
function getDadosPlanos() {
  let dados = new FormData();
  dados.append("op", 3);

  $.ajax({
    url: "src/controller/controllerPerfil.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      console.log(msg);
      $("#PlanosComprados").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
function getContactForm() {
  let dados = new FormData();
  dados.append("op", 13);

  $.ajax({
    url: "src/controller/controllerPerfil.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      $("#contactForm").html(msg);
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
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
function ErrorNoSession() {
  alerta("Inicie Sessão", "Não tem sessao iniciada", "error");
}
function AdicionarMensagemContacto() {
  let dados = new FormData();
  dados.append("op", 14);
  dados.append("mensagemUser", $("#mensagemUser").val());

  $.ajax({
    url: "src/controller/controllerPerfil.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })

    .done(function (msg) {
      console.log(msg);
      let obj = JSON.parse(msg);
      alerta("Mensagem", obj.msg, "success");
    })

    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}
$(function () {
  getContactForm();
  PerfilDoUtilizador();
  getDadosTipoPerfil();
  getDadosPlanos();
  // getDadosProdutos();
});
