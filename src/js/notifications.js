

let notificationsDropdownOpen = false;
let notificationsCache = [];

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
  console.log("[Notificações] Atualizando contagem...");
  $.ajax({
    url: "src/controller/controllerNotifications.php?op=1",
    method: "GET",
    dataType: "json",
    success: function (response) {
      console.log("[Notificações] Resposta recebida:", response);
      const ok =
        response && (response.success === true || response.flag === true);
      if (ok) {
        const badge = $(".notification-badge");
        const count = parseInt(response.count);

        console.log("[Notificações] Contagem:", count);
        console.log(
          "[Notificações] Badge encontrado:",
          badge.length,
          "elemento(s)",
        );

        if (count > 0) {
          badge
            .text(count > 99 ? "99+" : count)
            .show()
            .css("display", "inline-block");
          console.log("[Notificações] Badge mostrado com contagem:", count);
        } else {
          // Forçar hide com CSS inline para garantir
          badge.text("").hide().css({ display: "none", visibility: "hidden" });
          console.log("[Notificações] Badge escondido (count = 0)");
        }
      } else {
        console.warn(
          "[Notificações] Resposta sem sucesso:",
          response ? response.message || response.msg : "resposta vazia",
        );
      }
    },
    error: function (xhr, status, error) {
      console.error("[Notificações] Erro ao atualizar:", error);
      console.error("[Notificações] Status:", status);
      console.error("[Notificações] Resposta:", xhr.responseText);
    },
  });
}

/**
 * Carregar lista de notificações
 */
function carregarNotificacoes() {
  console.log("[Notificações] Carregando lista...");
  $.ajax({
    url: "src/controller/controllerNotifications.php?op=2",
    method: "GET",
    dataType: "json",
    success: function (response) {
      console.log("[Notificações] Resposta bruta:", JSON.stringify(response));
      console.log("[Notificações] Lista recebida:", response);
      const ok =
        response && (response.success === true || response.flag === true);
      const lista = response
        ? response.data || response.notificacoes || []
        : [];
      if (ok) {
        notificationsCache = lista;
        console.log("[Notificações] Total de itens:", lista ? lista.length : 0);
        console.log("[Notificações] Dados:", lista);
        renderizarNotificacoes(lista);
      } else {
        console.warn(
          "[Notificações] Erro ao listar:",
          response ? response.message || response.msg : "resposta vazia",
        );
        renderizarNotificacoes([]);
      }
    },
    error: function (xhr, status, error) {
      console.error("[Notificações] Erro ao carregar:", error);
      console.error("[Notificações] Status:", status);
      console.error("[Notificações] Resposta:", xhr.responseText);
      renderizarNotificacoes([]);
    },
  });
}

/**
 * Renderizar notificações no dropdown
 */
function renderizarNotificacoes(notificacoes) {
  const container = $("#notificationsList");
  console.log(
    "[Notificações] Renderizando",
    notificacoes ? notificacoes.length : 0,
    "notificações",
  );

  if (!notificacoes || notificacoes.length === 0) {
    console.log("[Notificações] Mostrando mensagem 'sem notificações'");
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

/**
 * Abrir notificação (redirecionar e marcar como lida)
 */
function abrirNotificacao(tipo, notifId, link) {
  console.log("[Notificações] Abrindo notificação:", { tipo, notifId, link });
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
      console.log("[Notificações] Marcada como lida:", response);
    })
    .fail(function (xhr, status, error) {
      console.error("[Notificações] Erro ao marcar como lida:", error);
      console.error("[Notificações] Resposta:", xhr.responseText);
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
  console.log("[Notificações] Marcando todas como lidas...");
  console.log(
    "[Notificações] Notificações em cache:",
    notificationsCache.length,
  );

  $.ajax({
    url: "src/controller/controllerNotifications.php",
    method: "POST",
    data: { op: 4 },
    dataType: "json",
    success: function (response) {
      console.log(
        "[Notificações] Resposta marcar todas (raw):",
        JSON.stringify(response),
      );
      console.log("[Notificações] Resposta marcar todas:", response);
      const ok =
        response && (response.success === true || response.flag === true);
      if (ok) {
        console.log("[Notificações] Todas marcadas com sucesso!");
        // Limpar cache local
        notificationsCache = [];
        // Atualizar interface
        atualizarNotificacoes();
        carregarNotificacoes();
        // Fechar dropdown após marcar
        setTimeout(fecharNotificationsDropdown, 500);
      } else {
        console.error(
          "[Notificações] Erro ao marcar todas:",
          response ? response.message || response.msg : "resposta vazia",
        );
        alert(
          "Erro ao marcar notificações como lidas: " +
            (response ? response.message || response.msg : "Resposta inválida"),
        );
      }
    },
    error: function (xhr, status, error) {
      console.error("[Notificações] Erro AJAX ao marcar todas:", error);
      console.error("[Notificações] Status:", status);
      console.error("[Notificações] Resposta:", xhr.responseText);
      alert("Erro ao marcar notificações: " + error);
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

  // Atualização automática
  atualizarNotificacoes(); // Primeira chamada
  setInterval(atualizarNotificacoes, 30000); // A cada 30s
});
