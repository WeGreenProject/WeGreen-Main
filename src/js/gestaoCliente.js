
function initializeDropdownEvents() {
  const userMenuBtn = document.getElementById("userMenuBtn");
  const userDropdown = document.getElementById("userDropdown");

  if (userMenuBtn) {
    userMenuBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      userDropdown.classList.toggle("active");
    });
  }

  
  document.addEventListener("click", function (e) {
    if (!userMenuBtn?.contains(e.target) && !userDropdown?.contains(e.target)) {
      userDropdown?.classList.remove("active");
    }
  });
}

function logout() {
  showModernConfirmModal(
    "Terminar Sessão",
    "Tem a certeza que deseja sair da plataforma?",
    {
      confirmText: '<i class="fas fa-check"></i> Sim, sair',
      icon: "fa-sign-out-alt",
      iconBg:
        "background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%); box-shadow: 0 8px 20px rgba(60, 179, 113, 0.3);",
    },
  ).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        html: '<div style="padding: 20px;"><i class="fas fa-spinner fa-spin" style="font-size: 48px; color: #3cb371;"></i><p style="margin-top: 20px; font-size: 16px; color: #64748b;">A terminar sessão...</p></div>',
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
      });

      let dados = new FormData();
      dados.append("op", 10);

      $.ajax({
        url: "src/controller/controllerDashboardAdmin.php",
        method: "POST",
        data: dados,
        dataType: "json",
        cache: false,
        contentType: false,
        processData: false,
      })
        .done(function (response) {
          if (response.success) {
            window.location.href = "index.html";
          }
        })
        .fail(function () {
          window.location.href = "index.html";
        });
    }
  });
}

$(document).ready(function () {
  initializeDropdownEvents();
  getCardUtilizadores();
  getClientes();
});
