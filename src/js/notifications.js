/**
 * Sistema de Notificações Unificado - WeGreen
 * Suporta: Cliente, Anunciante e Admin
 */

let notificationsDropdownOpen = false;
let notificationsCache = [];

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
      if (response.success) {
        const badge = $(".notification-badge");
        const count = parseInt(response.count);

        console.log("[Notificações] Contagem:", count);
        if (count > 0) {
          badge.text(count > 99 ? "99+" : count).show();
        } else {
          badge.hide();
        }
      } else {
        console.warn("[Notificações] Resposta sem sucesso:", response.message);
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
      console.log("[Notificações] Lista recebida:", response);
      if (response.success) {
        notificationsCache = response.data;
        console.log("[Notificações] Total de itens:", response.data.length);
        renderizarNotificacoes(response.data);
      } else {
        console.warn("[Notificações] Erro ao listar:", response.message);
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
    const icone = notif.icone || getIconeByTipo(notif.tipo);

    html += `
            <div class="notification-item" data-tipo="${notif.tipo}" data-id="${notif.id}" data-link="${notif.link}" onclick="abrirNotificacao('${notif.tipo}', ${notif.id}, '${notif.link}')">
                <div class="notification-icon ${notif.tipo}">
                    ${icone}
                </div>
                <div class="notification-content">
                    <div class="notification-title">${notif.titulo}</div>
                    <div class="notification-message">${notif.mensagem}</div>
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

  // Marcar como lida
  $.post("src/controller/controllerNotifications.php", {
    op: 3,
    tipo: tipo,
    id: notifId,
  })
    .done(function (response) {
      console.log("[Notificações] Marcada como lida:", response);
    })
    .fail(function (xhr, status, error) {
      console.error("[Notificações] Erro ao marcar como lida:", error);
    });

  // Redirecionar
  window.location.href = link;
}

/**
 * Marcar todas como lidas
 */
function marcarTodasComoLidas() {
  $.post("src/controller/controllerNotifications.php", {
    op: 4,
  }).done(function (response) {
    if (response.success) {
      atualizarNotificacoes();
      carregarNotificacoes();
    }
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
