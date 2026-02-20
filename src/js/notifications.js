let notificationsDropdownOpen = false;
let notificationsCache = [];

function mostrarModalSucesso(titulo, mensagem, opcoes) {
  if (typeof showModernSuccessModal === "function") {
    return showModernSuccessModal(titulo, mensagem, opcoes || {});
  }
  return Swal.fire({ icon: "success", title: titulo, text: mensagem });
}

function mostrarModalErro(titulo, mensagem) {
  if (typeof showModernErrorModal === "function") {
    return showModernErrorModal(titulo, mensagem);
  }
  return Swal.fire({ icon: "error", title: titulo, text: mensagem });
}

function escapeHtml(value) {
  if (value === null || value === undefined) return "";
  return String(value)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/\"/g, "&quot;")
    .replace(/'/g, "&#39;");
}

function normalizeIconMarkup(iconValue, tipo) {
  const fallback = getIconeByTipo(tipo);
  const value = (iconValue || "").toString().trim();

  if (!value) return fallback;

  if (value.startsWith("<i") || value.startsWith("<svg")) {
    return value;
  }

  if (value.startsWith("fa-")) {
    return `<i class="fas ${escapeHtml(value)}"></i>`;
  }

  if (value.includes(" ") && value.includes("fa-")) {
    return `<i class="${escapeHtml(value)}"></i>`;
  }

  return value.length <= 3 ? value : fallback;
}

/**
 * Atualizar contagem de notificações
 */
function atualizarNotificacoes() {
  $.ajax({
    url: "src/controller/controllerNotifications.php?op=1",
    method: "GET",
    dataType: "json",
    success: function (response) {
      const ok =
        response && (response.success === true || response.flag === true);
      if (ok) {
        const badge = $(".notification-badge");
        const count = parseInt(response.count);

        if (count > 0) {
          badge
            .text(count > 99 ? "99+" : count)
            .show()
            .css("display", "inline-block");
        } else {
          // Forçar hide com CSS inline para garantir
          badge.text("").hide().css({ display: "none", visibility: "hidden" });
        }
      } else {
      }
    },
    error: function (xhr, status, error) {
    },
  });
}

/**
 * Carregar lista de notificações
 */
function carregarNotificacoes() {
  $.ajax({
    url: "src/controller/controllerNotifications.php?op=2",
    method: "GET",
    dataType: "json",
    success: function (response) {
      const ok =
        response && (response.success === true || response.flag === true);
      const lista = response
        ? response.data || response.notificacoes || []
        : [];
      if (ok) {
        notificationsCache = lista;
        renderizarNotificacoes(lista);
      } else {
        renderizarNotificacoes([]);
      }
    },
    error: function (xhr, status, error) {
      renderizarNotificacoes([]);
    },
  });
}

/**
 * Renderizar notificações no dropdown
 */
function renderizarNotificacoes(notificacoes) {
  const container = $("#notificationsList");
  if (!notificacoes || notificacoes.length === 0) {
    container.html(`
            <div class="notifications-empty">
                <i class="fas fa-bell-slash"></i>
                <p>Sem notificações no momento</p>
            </div>
        `);
    return;
  }

  let html = "";

  notificacoes.forEach((notif) => {
    const timeAgo = calcularTempoDecorrido(notif.data);
    const tipo = (notif.tipo || "").toString();
    const icone = normalizeIconMarkup(notif.icone, tipo);
    const titulo = escapeHtml(notif.titulo || "Notificação");
    const mensagem = escapeHtml(notif.mensagem || "");
    const link = escapeHtml(notif.link || "#");
    const id = escapeHtml(notif.id || "");

    html += `
        <div class="notification-item" data-tipo="${escapeHtml(tipo)}" data-id="${id}" data-link="${link}" onclick="abrirNotificacao(this.dataset.tipo, this.dataset.id, this.dataset.link)">
          <div class="notification-icon ${escapeHtml(tipo)}">
                    ${icone}
                </div>
                <div class="notification-content">
            <div class="notification-title">${titulo}</div>
            <div class="notification-message">${mensagem}</div>
                    <div class="notification-time">${timeAgo}</div>
                </div>
            </div>
        `;
  });

  container.html(html);
}

/**
 * Obter ícone por tipo de notificação
 */
function getIconeByTipo(tipo) {
  const icones = {
    encomenda: "📦",
    devolucao: "↩️",
    utilizador: "👤",
    produto: "📦",
    suporte: "🎧",
    chat: "💬",
  };
  return icones[tipo] || "🔔";
}

/**
 * Calcular tempo decorrido
 */
function calcularTempoDecorrido(data) {
  const agora = new Date();
  const dataNotif = new Date(data);
  const diff = Math.floor((agora - dataNotif) / 1000); // segundos

  if (diff < 60) return "Agora mesmo";
  if (diff < 3600) return `${Math.floor(diff / 60)}min atrás`;
  if (diff < 86400) return `${Math.floor(diff / 3600)}h atrás`;
  if (diff < 604800) return `${Math.floor(diff / 86400)}d atrás`;

  return dataNotif.toLocaleDateString("pt-PT");
}

/**
 * Toggle dropdown de notificações
 */
function toggleNotificationsDropdown() {
  const dropdown = $("#notificationsDropdown");

  if (!notificationsDropdownOpen) {
    // Abrir
    carregarNotificacoes();
    dropdown.addClass("active");
    notificationsDropdownOpen = true;

    // Fechar ao clicar fora
    setTimeout(() => {
      $(document).on("click.notifications", function (e) {
        if (
          !$(e.target).closest("#notificationBtn, #notificationsDropdown")
            .length
        ) {
          fecharNotificationsDropdown();
        }
      });
    }, 100);
  } else {
    fecharNotificationsDropdown();
  }
}

/**
 * Fechar dropdown
 */
function fecharNotificationsDropdown() {
  $("#notificationsDropdown").removeClass("active");
  notificationsDropdownOpen = false;
  $(document).off("click.notifications");
}

function executarLogoutGlobal() {
  $.ajax({
    url: "src/controller/controllerPerfil.php?op=2",
    method: "GET",
    timeout: 5000,
  }).always(function () {
    window.location.href = "index.html";
  });
}

if (typeof window.logout !== "function") {
  window.logout = function () {
    if (typeof showModernConfirmModal === "function") {
      showModernConfirmModal(
        "Terminar Sessão?",
        "Tem a certeza que pretende sair?",
        {
          confirmText: '<i class="fas fa-check"></i> Sim, sair',
          icon: "fa-sign-out-alt",
          iconBg:
            "background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%); box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);",
        },
      ).then(function (result) {
        if (result && result.isConfirmed) {
          executarLogoutGlobal();
        }
      });
      return;
    }

    if (typeof Swal !== "undefined") {
      Swal.fire({
        icon: "warning",
        title: "Terminar Sessão?",
        text: "Tem a certeza que pretende sair?",
        showCancelButton: true,
        confirmButtonText: "Sim, sair",
        cancelButtonText: "Cancelar",
      }).then(function (result) {
        if (result && result.isConfirmed) {
          executarLogoutGlobal();
        }
      });
      return;
    }

    executarLogoutGlobal();
  };
}

function inicializarDropdownUtilizador() {
  const $btn = $("#userMenuBtn");
  const $dropdown = $("#userDropdown");

  if (!$btn.length || !$dropdown.length) {
    return;
  }

  $btn.off("click.userDropdown").on("click.userDropdown", function (e) {
    e.preventDefault();
    e.stopPropagation();
    if (typeof e.stopImmediatePropagation === "function") {
      e.stopImmediatePropagation();
    }

    const estaAberto = $dropdown.hasClass("active");
    if (estaAberto) {
      $dropdown.removeClass("active");
    } else {
      $dropdown.addClass("active");
    }
  });

  $dropdown.off("click.userDropdown").on("click.userDropdown", function (e) {
    e.stopPropagation();
    if (typeof e.stopImmediatePropagation === "function") {
      e.stopImmediatePropagation();
    }
  });

  $(document)
    .off("click.userDropdown")
    .on("click.userDropdown", function (e) {
      if (!$(e.target).closest(".navbar-user, #userDropdown").length) {
        $dropdown.removeClass("active");
      }
    });
}

/**
 * Abrir notificação (redirecionar e marcar como lida)
 */
function abrirNotificacao(tipo, notifId, link) {
  const tipoNotificacao = (tipo || "").toString().trim();
  const referenciaId = parseInt(notifId, 10);
  const destino = (link || "").toString().trim();

  let redirecionou = false;
  const redirecionar = () => {
    if (redirecionou) return;
    redirecionou = true;

    if (destino && destino !== "#") {
      window.location.href = destino;
    } else {
      atualizarNotificacoes();
      carregarNotificacoes();
    }
  };

  if (!tipoNotificacao || !Number.isFinite(referenciaId) || referenciaId <= 0) {
    redirecionar();
    return;
  }

  const fallbackRedirect = setTimeout(redirecionar, 1500);

  $.ajax({
    url: "src/controller/controllerNotifications.php",
    method: "POST",
    dataType: "json",
    timeout: 5000,
    data: {
      op: 3,
      tipo: tipoNotificacao,
      id: referenciaId,
    },
  })
    .done(function (response) {
    })
    .fail(function (xhr, status, error) {
    })
    .always(function () {
      clearTimeout(fallbackRedirect);
      redirecionar();
    });
}

/**
 * Marcar todas como lidas
 */
function marcarTodasComoLidas() {
  $.ajax({
    url: "src/controller/controllerNotifications.php",
    method: "POST",
    data: { op: 4 },
    dataType: "json",
    success: function (response) {
      const ok =
        response && (response.success === true || response.flag === true);
      if (ok) {
        // Limpar cache local
        notificationsCache = [];
        // Atualizar interface
        atualizarNotificacoes();
        carregarNotificacoes();
        // Fechar dropdown após marcar
        setTimeout(fecharNotificationsDropdown, 500);
      } else {
        mostrarModalErro(
          "Erro",
          "Erro ao marcar notificações como lidas: " +
            (response ? response.message || response.msg : "Resposta inválida"),
        );
      }
    },
    error: function (xhr, status, error) {
      mostrarModalErro("Erro", "Erro ao marcar notificações: " + error);
    },
  });
}

/**
 * Inicialização
 */
$(document).ready(function () {
  // Criar estrutura HTML do dropdown (se não existir)
  if ($("#notificationsDropdown").length === 0) {
    $("body").append(`
            <div id="notificationsDropdown" class="notifications-dropdown">
                <div class="notifications-header">
                    <h3><i class="fas fa-bell"></i> Notificações</h3>
                    <button class="mark-all-read" onclick="marcarTodasComoLidas()">
                        <i class="fas fa-check-double"></i> Marcar como lidas
                    </button>
                </div>
                <div class="notifications-list" id="notificationsList">
                    <div class="notifications-empty">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>A carregar...</p>
                    </div>
                </div>
            </div>
        `);
  }

  // Botão de notificações
  $("#notificationBtn").on("click", function (e) {
    e.stopPropagation();
    toggleNotificationsDropdown();
  });

  inicializarDropdownUtilizador();

  // Atualização automática
  atualizarNotificacoes(); // Primeira chamada
  setInterval(atualizarNotificacoes, 30000); // A cada 30s
});
