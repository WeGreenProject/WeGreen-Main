let currentFilter = "todas";
let currentPage = 1;
const itemsPerPage = 20;
const autoRefreshIntervalMs = 15000;
let allNotifications = [];
let filteredNotifications = [];
let notificationsRefreshTimer = null;

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

function respostaOk(response) {
  return Boolean(
    response && (response.success === true || response.flag === true),
  );
}

function extrairNotificacoes(response) {
  if (!response || typeof response !== "object") {
    return [];
  }

  if (Array.isArray(response.data)) {
    return response.data;
  }

  if (Array.isArray(response.notificacoes)) {
    return response.notificacoes;
  }

  return [];
}

function normalizarNotificacoes(lista) {
  if (!Array.isArray(lista)) return [];

  return lista.map((notif) => {
    const lida =
      notif && (notif.lida === true || notif.lida === 1 || notif.lida === "1");

    return {
      ...notif,
      lida,
      id: notif && notif.id !== undefined ? Number(notif.id) : notif.id,
      tipo: notif && notif.tipo ? String(notif.tipo) : "",
      titulo: notif && notif.titulo ? String(notif.titulo) : "Notificação",
      mensagem: notif && notif.mensagem ? String(notif.mensagem) : "",
      link: notif && notif.link ? String(notif.link) : "#",
    };
  });
}

$(document).ready(function () {
  carregarTodasNotificacoes();
  setupFilterButtons();
  iniciarAutoAtualizacaoNotificacoes();

  $(document).on("visibilitychange", function () {
    if (document.visibilityState === "visible") {
      carregarTodasNotificacoes();
      iniciarAutoAtualizacaoNotificacoes();
    } else {
      pararAutoAtualizacaoNotificacoes();
    }
  });

  $(window).on("focus", function () {
    carregarTodasNotificacoes();
  });

  $(window).on("beforeunload", function () {
    pararAutoAtualizacaoNotificacoes();
  });
});

function iniciarAutoAtualizacaoNotificacoes() {
  if (notificationsRefreshTimer) {
    return;
  }

  notificationsRefreshTimer = setInterval(function () {
    carregarTodasNotificacoes();
  }, autoRefreshIntervalMs);
}

function pararAutoAtualizacaoNotificacoes() {
  if (!notificationsRefreshTimer) {
    return;
  }

  clearInterval(notificationsRefreshTimer);
  notificationsRefreshTimer = null;
}

function setupFilterButtons() {
  $(".filter-btn").click(function () {
    $(".filter-btn").removeClass("active");
    $(this).addClass("active");
    currentFilter = $(this).data("filter");
    currentPage = 1;
    aplicarFiltro();
  });
}

function carregarTodasNotificacoes() {
  $.ajax({
    url: "src/controller/controllerNotifications.php",
    method: "GET",
    data: {
      op: 5,
      _ts: Date.now(),
    },
    cache: false,
    dataType: "json",
    success: function (response) {
      if (respostaOk(response)) {
        allNotifications = normalizarNotificacoes(
          extrairNotificacoes(response),
        );
        aplicarFiltro();
      } else {
        mostrarEmpty();
      }
    },
    error: function () {
      mostrarEmpty();
    },
  });
}

function aplicarFiltro() {
  if (currentFilter === "todas") {
    filteredNotifications = allNotifications;
  } else if (currentFilter === "nao-lidas") {
    filteredNotifications = allNotifications.filter((n) => !n.lida);
  } else if (currentFilter === "lidas") {
    filteredNotifications = allNotifications.filter((n) => n.lida);
  }

  const totalPages = Math.max(
    1,
    Math.ceil(filteredNotifications.length / itemsPerPage),
  );
  if (currentPage > totalPages) {
    currentPage = totalPages;
  }

  renderizarNotificacoes();
}

function renderizarNotificacoes() {
  const container = $("#notificationsContainer");

  if (filteredNotifications.length === 0) {
    mostrarEmpty();
    return;
  }

  const start = (currentPage - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  const pageNotifications = filteredNotifications.slice(start, end);

  let html = "";
  pageNotifications.forEach((notif) => {
    const timeAgo = calcularTempoDecorrido(notif.data);
    const icone = renderIconeNotificacao(notif);
    const readClass = notif.lida ? "read" : "";

    html += `
                    <div class="notification-item-full ${readClass}" data-tipo="${notif.tipo}" data-id="${notif.id}">
                        ${!notif.lida ? '<div class="unread-badge"></div>' : ""}
                        <div class="notification-icon-full ${notif.tipo}">
                            ${icone}
                        </div>
                        <div class="notification-content-full">
                            <div class="notification-title-full">${notif.titulo}</div>
                            <div class="notification-message-full">${notif.mensagem}</div>
                            <div class="notification-time-full">
                                <i class="far fa-clock"></i> ${timeAgo}
                            </div>
                            <div class="notification-actions">
                                <button class="action-btn" onclick="abrirNotificacao('${notif.tipo}', ${notif.id}, '${notif.link}')">
                                    <i class="fas fa-external-link-alt"></i> Ver Detalhes
                                </button>
                                ${
                                  !notif.lida
                                    ? `
                                    <button class="action-btn" onclick="marcarComoLida('${notif.tipo}', ${notif.id})">
                                        <i class="fas fa-check"></i> Marcar como Lida
                                    </button>
                                `
                                    : ""
                                }
                            </div>
                        </div>
                    </div>
                `;
  });

  container.html(html);

  const totalPages = Math.ceil(filteredNotifications.length / itemsPerPage);
  if (totalPages > 1) {
    $("#pagination").show();
    $("#pageInfo").text(`Página ${currentPage} de ${totalPages}`);
  } else {
    $("#pagination").hide();
  }
}

function mostrarEmpty() {
  $("#notificationsContainer").html(`
                <div class="empty-state">
                    <i class="fas fa-bell-slash"></i>
                    <p>Nenhuma notificação encontrada</p>
                </div>
            `);
  $("#pagination").hide();
}

function calcularTempoDecorrido(data) {
  const agora = new Date();
  const dataNotif = new Date(data);
  const diff = Math.floor((agora - dataNotif) / 1000);

  if (diff < 60) return "Agora mesmo";
  if (diff < 3600) return `${Math.floor(diff / 60)} min atrás`;
  if (diff < 86400) return `${Math.floor(diff / 3600)}h atrás`;
  if (diff < 604800) return `${Math.floor(diff / 86400)}d atrás`;

  return dataNotif.toLocaleDateString("pt-PT");
}

function getIconeByTipo(tipo) {
  const icones = {
    encomenda: "fa-shopping-bag",
    devolucao: "fa-undo",
    utilizador: "fa-user",
    produto: "fa-box",
    stock_baixo: "fa-exclamation-triangle",
    stock_esgotado: "fa-times-circle",
    produto_rejeitado: "fa-times-circle",
  };
  return icones[tipo] || "fa-bell";
}

function detectarEstadoDevolucao(notif) {
  const titulo = notif && notif.titulo ? notif.titulo : "";
  const mensagem = notif && notif.mensagem ? notif.mensagem : "";
  const texto = `${titulo} ${mensagem}`.toLowerCase();

  if (texto.includes("reembolso")) return "reembolsada";
  if (texto.includes("rejeitad")) return "rejeitada";
  if (texto.includes("recebid")) return "produto_recebido";
  if (texto.includes("enviad")) return "produto_enviado";
  if (texto.includes("aprovad")) return "aprovada";
  if (texto.includes("cancelad")) return "cancelada";
  if (texto.includes("solicitad")) return "solicitada";

  return "";
}

function getIconeByEstadoDevolucao(estado) {
  const icones = {
    solicitada: "fa-undo",
    aprovada: "fa-check-circle",
    produto_enviado: "fa-shipping-fast",
    produto_recebido: "fa-box-open",
    rejeitada: "fa-times-circle",
    reembolsada: "fa-euro-sign",
    cancelada: "fa-ban",
  };

  return icones[estado] || "fa-undo";
}

function normalizarIcone(iconeRaw) {
  const valor = String(iconeRaw || "").trim();
  if (!valor) return "";

  if (valor.includes("<i")) return valor;
  if (/^fa[srlbd]?\s+fa-/.test(valor)) return `<i class="${valor}"></i>`;
  if (/^fa-/.test(valor)) return `<i class="fas ${valor}"></i>`;

  const emojiMap = {
    "??": "fa-box",
    "??": "fa-undo",
    "??": "fa-user",
    "??": "fa-bell",
    "?": "fa-check-circle",
    "??": "fa-shipping-fast",
    "?": "fa-times-circle",
    "??": "fa-euro-sign",
  };

  if (emojiMap[valor]) {
    return `<i class="fas ${emojiMap[valor]}"></i>`;
  }

  return "";
}

function renderIconeNotificacao(notif) {
  const iconeNormalizado = normalizarIcone(
    notif && notif.icone ? notif.icone : "",
  );
  if (iconeNormalizado) return iconeNormalizado;

  if (notif && notif.tipo === "devolucao") {
    const estado = detectarEstadoDevolucao(notif);
    return `<i class="fas ${getIconeByEstadoDevolucao(estado)}"></i>`;
  }

  return `<i class="fas ${getIconeByTipo(notif ? notif.tipo : "")}"></i>`;
}

function abrirNotificacao(tipo, id, link) {
  $.post("src/controller/controllerNotifications.php", {
    op: 3,
    tipo: tipo,
    id: id,
  }).always(function () {
    window.location.href = link;
  });
}

function marcarComoLida(tipo, id) {
  $.ajax({
    url: "src/controller/controllerNotifications.php",
    method: "POST",
    data: {
      op: 3,
      tipo: tipo,
      id: id,
    },
    dataType: "json",
    success: function (response) {
      if (respostaOk(response)) {
        carregarTodasNotificacoes();
      } else {
      }
    },
    error: function (xhr, status, error) {},
  });
}

function marcarTodasComoLidas() {
  $.ajax({
    url: "src/controller/controllerNotifications.php",
    method: "POST",
    data: { op: 4 },
    dataType: "json",
    success: function (response) {
      if (respostaOk(response)) {
        carregarTodasNotificacoes();

        mostrarModalSucesso(
          "Sucesso",
          "Todas as notificações foram marcadas como lidas!",
          { timer: 1800 },
        );
      } else {
        mostrarModalErro(
          "Erro",
          "Erro ao marcar notificações: " +
            (response ? response.message || response.msg : "Resposta inválida"),
        );
      }
    },
    error: function (xhr, status, error) {
      mostrarModalErro("Erro", "Erro ao marcar notificações. Tente novamente.");
    },
  });
}

function nextPage() {
  const totalPages = Math.ceil(filteredNotifications.length / itemsPerPage);
  if (currentPage < totalPages) {
    currentPage++;
    renderizarNotificacoes();
    window.scrollTo(0, 0);
  }
}

function previousPage() {
  if (currentPage > 1) {
    currentPage--;
    renderizarNotificacoes();
    window.scrollTo(0, 0);
  }
}
