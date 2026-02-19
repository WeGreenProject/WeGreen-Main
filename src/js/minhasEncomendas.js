let todasEncomendas = [];
let encomendasFiltradas = [];
let encomendaAlvoUrl = null;
let detalhesAbertosPorUrl = false;

$(document).ready(function () {
  const params = new URLSearchParams(window.location.search);
  encomendaAlvoUrl = params.get("encomenda");
  carregarEncomendas();
  inicializarFiltros();
});

function carregarEncomendas() {
  $.ajax({
    url: "src/controller/controllerEncomendas.php",
    method: "POST",
    data: {
      op: "listarEncomendasCliente",
    },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        let lista = response.data;
        if (typeof lista === "string") {
          try {
            lista = JSON.parse(lista);
          } catch (error) {
            lista = [];
          }
        }
        if (!Array.isArray(lista)) {
          lista = [];
        }

        todasEncomendas = lista;
        encomendasFiltradas = lista;
        renderizarEncomendas(lista);
        abrirDetalhesPorParametroUrl();
      } else {
        mostrarEstadoVazio();
      }
    },
    error: function (xhr, status, error) {
      showModernErrorModal("Erro", "Erro ao comunicar com o servidor.");
    },
  });
}

function abrirDetalhesPorParametroUrl() {
  if (!encomendaAlvoUrl || detalhesAbertosPorUrl) {
    return;
  }

  detalhesAbertosPorUrl = true;
  verDetalhes(encomendaAlvoUrl);

  const urlLimpa = window.location.pathname;
  window.history.replaceState({}, document.title, urlLimpa);
}

function renderizarEncomendas(encomendas) {
  const grid = $("#encomendasGrid");
  grid.empty();

  if (!Array.isArray(encomendas)) {
    mostrarEstadoVazio();
    return;
  }

  if (!encomendas || encomendas.length === 0) {
    mostrarEstadoVazio();
    return;
  }

  $("#emptyState").hide();
  grid.show();

  encomendas.forEach((enc) => {
    const card = criarCardEncomenda(enc);
    grid.append(card);
  });
}

function normalizarEstadoEncomenda(estado) {
  const valor = (estado || "").toString().trim().toLowerCase();

  if (valor === "pendente") return "pendente";
  if (
    valor === "processando" ||
    valor === "em processamento" ||
    valor === "processamento"
  ) {
    return "processando";
  }
  if (valor === "enviado" || valor === "enviada") return "enviado";
  if (valor === "entregue" || valor === "entregada") return "entregue";
  if (valor === "cancelado" || valor === "cancelada") return "cancelado";
  if (valor === "devolvido" || valor === "devolvida") return "devolvido";

  return valor || "pendente";
}

function criarCardEncomenda(enc) {
  const estado = normalizarEstadoEncomenda(enc.estado);
  const encomendaTotalmenteAvaliada =
    Number(enc.encomenda_totalmente_avaliada || 0) === 1;
  const statusInfo = getStatusInfo(estado);
  const timeline = criarTimeline(estado);
  const devolucaoAtiva = Number(enc.devolucao_ativa || 0) > 0;
  const devolucaoInfo = getDevolucaoStatusInfo(enc.devolucao_estado);
  const devolucaoProdutos = (enc.devolucao_produtos_nomes || "").toString();
  const devolucaoQtdProdutos = Number(enc.devolucao_num_produtos || 0);
  const devolucaoQtdAprovada = Number(enc.devolucao_aprovada_qtd || 0);
  const devolucaoQtdSolicitada = Number(enc.devolucao_solicitada_qtd || 0);
  const devolucaoAprovadaId = Number(enc.devolucao_aprovada_id || 0);
  const devolucaoAprovadaCodigo = (enc.devolucao_aprovada_codigo || "")
    .toString()
    .trim();
  const podeConfirmarEnvio = devolucaoAtiva && devolucaoQtdAprovada > 0;
  const devolucaoUltimaEstado = (enc.devolucao_ultima_estado || "")
    .toString()
    .trim()
    .toLowerCase();
  const devolucaoUltimaInfo = getDevolucaoStatusInfo(devolucaoUltimaEstado);
  const devolucaoTemHistorico = Number(enc.devolucao_existe || 0) > 0;
  const respostaVendedor = (enc.devolucao_ultima_notas_anunciante || "")
    .toString()
    .trim();

  let produtosHTML = "";
  let fotoProduto = enc.foto_produto || "assets/media/products/default.jpg";
  let nomeProdutos = enc.produtos || "";

  if (
    enc.produtos_lista &&
    Array.isArray(enc.produtos_lista) &&
    enc.produtos_lista.length > 0
  ) {
    if (enc.produtos_lista.length === 1) {
      const p = enc.produtos_lista[0];
      fotoProduto = p.foto || fotoProduto;
      produtosHTML = `
          <div class="product-image" onclick="previewImage('${fotoProduto}', '${p.nome}')">
            <img src="${fotoProduto}" alt="${p.nome}">
          </div>
          <div class="product-info">
            <div class="product-name">${p.nome}</div>
            <div class="product-details">
              <span><i class="fas fa-box"></i> Qtd: ${p.quantidade}</span>
              <span><i class="fas fa-truck"></i> ${enc.transportadora}</span>
              ${enc.plano_rastreio ? `<span><i class="fas fa-barcode"></i> ${enc.plano_rastreio}</span>` : ""}
            </div>
          </div>
        `;
    } else {
      fotoProduto = enc.produtos_lista[0].foto || fotoProduto;
      const produtosText = enc.produtos_lista.map((p) => p.nome).join(", ");
      const qtdTotal = enc.produtos_lista.reduce(
        (sum, p) => sum + parseInt(p.quantidade),
        0,
      );

      produtosHTML = `
          <div class="product-image" onclick="mostrarTodosProdutos(${JSON.stringify(enc.produtos_lista).replace(/"/g, "&quot;")})">
            <img src="${fotoProduto}" alt="Produtos">
            <div style="position: absolute; top: 5px; right: 5px; background: #3cb371; color: white; border-radius: 50%; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.3);">
              ${enc.produtos_lista.length}
            </div>
          </div>
          <div class="product-info">
            <div class="product-name" style="cursor: pointer;" onclick="mostrarTodosProdutos(${JSON.stringify(enc.produtos_lista).replace(/"/g, "&quot;")})">
              <i class="fas fa-cubes" style="margin-right: 6px; color: #3cb371;"></i>
              ${enc.produtos_lista.length} produtos diferentes
              <i class="fas fa-chevron-right" style="margin-left: 6px; font-size: 11px; color: #999;"></i>
            </div>
            <div class="product-details">
              <span><i class="fas fa-box"></i> Qtd total: ${qtdTotal}</span>
              <span><i class="fas fa-truck"></i> ${enc.transportadora}</span>
              ${enc.plano_rastreio ? `<span><i class="fas fa-barcode"></i> ${enc.plano_rastreio}</span>` : ""}
            </div>
          </div>
        `;
    }
  } else {
    produtosHTML = `
        <div class="product-image" onclick="previewImage('${fotoProduto}', '${nomeProdutos}')">
          <img src="${fotoProduto}" alt="${nomeProdutos}">
        </div>
        <div class="product-info">
          <div class="product-name">${nomeProdutos}</div>
          <div class="product-details">
            <span><i class="fas fa-truck"></i> ${enc.transportadora}</span>
            ${enc.plano_rastreio ? `<span><i class="fas fa-barcode"></i> ${enc.plano_rastreio}</span>` : ""}
          </div>
        </div>
      `;
  }

  return $(`
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <div class="order-number">#${enc.codigo_encomenda}</div>
                            <div class="order-date"><i class="far fa-calendar"></i> ${formatarData(enc.data_envio)}</div>
                        </div>
                        <div>
                            <span class="status-badge ${statusInfo.class}">${statusInfo.text}</span>
                        </div>
                    </div>

                    <div class="order-timeline">
                        ${timeline}
                    </div>

                    <div class="order-body">
                        <div class="order-products">
                            ${produtosHTML}
                        </div>

                        <div class="order-total">
                            <div class="total-label">Total</div>
                            <div class="total-value">€${parseFloat(enc.total).toFixed(2)}</div>
                        </div>
                    </div>

                      ${
                        devolucaoAtiva
                          ? `
                      <div style="margin: 0 24px 14px 24px; border: 1px solid ${devolucaoInfo.border}; background: ${devolucaoInfo.bg}; border-radius: 10px; padding: 10px 12px;">
                        <div style="display: flex; align-items: center; gap: 8px; color: ${devolucaoInfo.color}; font-weight: 700; font-size: 13px; margin-bottom: 4px;">
                          <i class="fas ${devolucaoInfo.icon}"></i>
                          ${devolucaoInfo.text}
                        </div>
                        <div style="color: #334155; font-size: 12px; line-height: 1.45;">
                          ${devolucaoQtdProdutos > 0 ? `<strong>${devolucaoQtdProdutos}</strong> produto(s) em devolução` : "Produtos em devolução"}
                          ${devolucaoProdutos ? `: ${devolucaoProdutos}` : ""}
                        </div>
                        ${
                          podeConfirmarEnvio && devolucaoQtdSolicitada > 0
                            ? `<div style="margin-top: 6px; color: #1d4ed8; font-size: 12px; line-height: 1.45;"><i class="fas fa-info-circle" style="margin-right: 4px;"></i><strong>${devolucaoQtdAprovada}</strong> item(ns) já aprovado(s) para envio e <strong>${devolucaoQtdSolicitada}</strong> ainda em análise.</div>`
                            : ""
                        }
                        ${
                          respostaVendedor
                            ? `<div style="margin-top: 8px; color: #334155; font-size: 12px; line-height: 1.45;"><strong>Resposta do vendedor:</strong> ${respostaVendedor}</div>`
                            : ""
                        }
                      </div>
                      `
                          : ""
                      }

                      ${
                        !devolucaoAtiva &&
                        devolucaoTemHistorico &&
                        ["rejeitada", "reembolsada", "cancelada"].includes(
                          devolucaoUltimaEstado,
                        )
                          ? `
                      <div style="margin: 0 24px 14px 24px; border: 1px solid ${devolucaoUltimaInfo.border}; background: ${devolucaoUltimaInfo.bg}; border-radius: 10px; padding: 10px 12px;">
                        <div style="display: flex; align-items: center; gap: 8px; color: ${devolucaoUltimaInfo.color}; font-weight: 700; font-size: 13px; margin-bottom: 4px;">
                          <i class="fas ${devolucaoUltimaInfo.icon}"></i>
                          ${devolucaoUltimaInfo.text}
                        </div>
                        ${
                          respostaVendedor
                            ? `<div style="color: #334155; font-size: 12px; line-height: 1.45;"><strong>Resposta do vendedor:</strong> ${respostaVendedor}</div>`
                            : ""
                        }
                      </div>
                      `
                          : ""
                      }

                    <div class="order-actions">
                        <button class="btn-action btn-primary" onclick="verDetalhes('${enc.codigo_encomenda}')">
                            <i class="fas fa-eye"></i> Ver Detalhes
                        </button>
                        ${
                          estado === "enviado"
                            ? `
                            <button class="btn-action btn-success" onclick="confirmarEntrega('${enc.codigo_confirmacao_recepcao || ""}')" style="font-weight: bold;">
                                <i class="fas fa-check-circle"></i> Confirmar Receção
                            </button>
                        `
                            : ""
                        }
                        ${
                          estado === "entregue" && !devolucaoAtiva
                            ? `
                            <button class="btn-action btn-warning" onclick="abrirModalDevolucao('${enc.id}', '${enc.codigo_encomenda}', ${JSON.stringify(enc.produtos_lista || []).replace(/"/g, "&quot;")})">
                                <i class="fas fa-undo"></i> Solicitar Devolução
                            </button>
                            <button class="btn-action btn-secondary" onclick="comprarNovamente('${enc.codigo_encomenda}')">
                                <i class="fas fa-redo"></i> Comprar Novamente
                            </button>
                            ${
                              encomendaTotalmenteAvaliada
                                ? ``
                                : `
                            <button class="btn-action btn-secondary" onclick="avaliarProduto('${enc.codigo_encomenda}')">
                              <i class="fas fa-star"></i> Avaliar
                            </button>
                            `
                            }
                        `
                            : ""
                        }
                        ${
                          podeConfirmarEnvio
                            ? `
                            <button class="btn-action btn-success" onclick="mostrarModalConfirmarEnvio(${devolucaoAprovadaId || enc.devolucao_id}, '${devolucaoAprovadaCodigo || enc.devolucao_codigo || ""}')">
                                <i class="fas fa-shipping-fast"></i> Confirmar Envio
                            </button>
                        `
                            : ""
                        }
                        ${
                          devolucaoAtiva &&
                          enc.devolucao_estado === "produto_enviado"
                            ? `
                            <span class="badge-info" style="padding: 8px 16px; background: #3b82f6; color: white; border-radius: 6px; font-size: 13px;">
                                <i class="fas fa-truck"></i> Devolução enviada - aguardando confirmação
                            </span>
                        `
                            : ""
                        }
                        ${
                          devolucaoAtiva &&
                          enc.devolucao_estado === "produto_recebido"
                            ? `
                            <span class="badge-success" style="padding: 8px 16px; background: #10b981; color: white; border-radius: 6px; font-size: 13px;">
                                <i class="fas fa-check-double"></i> Produto recebido - aguardando reembolso
                            </span>
                        `
                            : ""
                        }
                        <button class="btn-action btn-outline" onclick="descarregarFatura('${enc.codigo_encomenda}')">
                            <i class="fas fa-download"></i> Fatura
                        </button>
                        ${
                          estado === "pendente" || estado === "processando"
                            ? `
                            <button class="btn-action btn-danger" onclick="cancelarEncomenda('${enc.codigo_encomenda}')">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        `
                            : ""
                        }
                    </div>
                </div>
            `);
}

function criarTimeline(estado) {
  const steps = [
    {
      key: "pendente",
      icon: "clock",
      label: "Pendente",
    },
    {
      key: "processando",
      icon: "cog",
      label: "Processando",
    },
    {
      key: "enviado",
      icon: "truck",
      label: "Enviado",
    },
    {
      key: "entregue",
      icon: "check-circle",
      label: "Entregue",
    },
  ];

  const estadoIndex = steps.findIndex((s) => s.key === estado);

  return `
                <div class="timeline">
                    ${steps
                      .map((step, index) => {
                        const isActive = index <= estadoIndex;
                        const isCurrent = index === estadoIndex;
                        return `
                            <div class="timeline-step ${isActive ? "active" : ""} ${isCurrent ? "current" : ""}">
                                <div class="timeline-icon">
                                    <i class="fas fa-${step.icon}"></i>
                                </div>
                                <div class="timeline-label">${step.label}</div>
                            </div>
                            ${index < steps.length - 1 ? '<div class="timeline-line ' + (isActive ? "active" : "") + '"></div>' : ""}
                        `;
                      })
                      .join("")}
                </div>
            `;
}

function getStatusInfo(estado) {
  const statusMap = {
    pendente: {
      class: "status-pendente",
      text: "Pendente",
    },
    processando: {
      class: "status-processando",
      text: "Processando",
    },
    enviado: {
      class: "status-enviado",
      text: "Enviado",
    },
    entregue: {
      class: "status-entregue",
      text: "Entregue",
    },
    devolvido: {
      class: "status-devolvido",
      text: "Devolvido",
    },
    cancelado: {
      class: "status-cancelado",
      text: "Cancelado",
    },
  };
  return (
    statusMap[estado] || {
      class: "",
      text: estado,
    }
  );
}

function getDevolucaoStatusInfo(estadoDevolucao) {
  const estado = (estadoDevolucao || "").toString().trim().toLowerCase();

  const map = {
    solicitada: {
      text: "Devolução solicitada - a aguardar resposta do vendedor",
      color: "#92400e",
      bg: "#fffbeb",
      border: "#fcd34d",
      icon: "fa-hourglass-half",
    },
    aprovada: {
      text: "Devolução aprovada - falta confirmar envio",
      color: "#1d4ed8",
      bg: "#eff6ff",
      border: "#93c5fd",
      icon: "fa-check-circle",
    },
    produto_enviado: {
      text: "Devolução enviada - a aguardar receção",
      color: "#1e3a8a",
      bg: "#dbeafe",
      border: "#60a5fa",
      icon: "fa-truck",
    },
    produto_recebido: {
      text: "Produto recebido - a aguardar reembolso",
      color: "#065f46",
      bg: "#ecfdf5",
      border: "#6ee7b7",
      icon: "fa-money-bill-wave",
    },
    rejeitada: {
      text: "Devolução rejeitada pelo vendedor",
      color: "#991b1b",
      bg: "#fef2f2",
      border: "#fca5a5",
      icon: "fa-times-circle",
    },
    reembolsada: {
      text: "Devolução concluída - reembolso processado",
      color: "#065f46",
      bg: "#ecfdf5",
      border: "#6ee7b7",
      icon: "fa-euro-sign",
    },
    cancelada: {
      text: "Devolução cancelada",
      color: "#475569",
      bg: "#f8fafc",
      border: "#cbd5e1",
      icon: "fa-ban",
    },
  };

  return (
    map[estado] || {
      text: "Devolução em processamento",
      color: "#334155",
      bg: "#f8fafc",
      border: "#cbd5e1",
      icon: "fa-undo",
    }
  );
}

function formatarData(data) {
  const d = new Date(data);
  return d.toLocaleDateString("pt-PT", {
    day: "2-digit",
    month: "long",
    year: "numeric",
  });
}

function mostrarEstadoVazio() {
  $("#encomendasGrid").hide();
  $("#emptyState").show();
}

function inicializarFiltros() {
  $("#searchProduct, #filterStatus, #filterPeriod, #sortBy").on(
    "change keyup",
    aplicarFiltros,
  );
}

function aplicarFiltros() {
  let filtradas = [...todasEncomendas];

  // Filtro de pesquisa
  const search = $("#searchProduct").val().toLowerCase();
  if (search) {
    filtradas = filtradas.filter((e) => {
      const produtos = (e.produtos || "").toLowerCase();
      const codigoEncomenda = (e.codigo_encomenda || "").toLowerCase();
      const idEncomenda = String(e.id || "").toLowerCase();

      return (
        produtos.includes(search) ||
        codigoEncomenda.includes(search) ||
        idEncomenda.includes(search)
      );
    });
  }

  // Filtro de status
  const status = $("#filterStatus").val().toLowerCase();
  if (status) {
    filtradas = filtradas.filter(
      (e) => normalizarEstadoEncomenda(e.estado) === status,
    );
  }

  // Filtro de per�odo
  const period = $("#filterPeriod").val();
  if (period) {
    const hoje = new Date();
    const dataLimite = new Date(
      hoje.setDate(hoje.getDate() - parseInt(period)),
    );
    filtradas = filtradas.filter((e) => new Date(e.data_envio) >= dataLimite);
  }

  // Ordena��o
  const sort = $("#sortBy").val();
  filtradas.sort((a, b) => {
    switch (sort) {
      case "date-desc":
        return new Date(b.data_envio) - new Date(a.data_envio);
      case "date-asc":
        return new Date(a.data_envio) - new Date(b.data_envio);
      case "value-desc":
        return parseFloat(b.total) - parseFloat(a.total);
      case "value-asc":
        return parseFloat(a.total) - parseFloat(b.total);
      default:
        return 0;
    }
  });

  encomendasFiltradas = filtradas;
  renderizarEncomendas(filtradas);
}

function limparFiltros() {
  $("#searchProduct").val("");
  $("#filterStatus").val("");
  $("#filterPeriod").val("");
  $("#sortBy").val("date-desc");
  renderizarEncomendas(todasEncomendas);
}

function garantirEstilosModalCodigoRececao() {
  if (document.getElementById("wegreen-rececao-codigo-style")) return;

  const style = document.createElement("style");
  style.id = "wegreen-rececao-codigo-style";
  style.textContent = `
    .swal2-popup.rececao-codigo-modal {
      width: min(560px, 94vw) !important;
      border-radius: 16px !important;
      padding: 0 !important;
      overflow: hidden !important;
    }

    .swal2-popup.rececao-codigo-modal .swal2-title {
      margin: 0 !important;
      padding: 16px 20px !important;
      background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%);
      color: #ffffff !important;
      font-size: 24px !important;
      font-weight: 700 !important;
      text-align: center !important;
    }

    .swal2-popup.rececao-codigo-modal .swal2-html-container {
      margin: 14px 22px 8px !important;
      padding: 0 !important;
      color: #4b5563 !important;
      font-size: 16px !important;
      text-align: center !important;
    }

    .swal2-popup.rececao-codigo-modal .swal2-input.rececao-codigo-input {
      width: calc(100% - 44px) !important;
      margin: 0 22px 10px !important;
      border: 1px solid #d1d5db !important;
      border-radius: 12px !important;
      padding: 13px 14px !important;
      font-size: 18px !important;
      letter-spacing: 0.8px !important;
      font-weight: 600 !important;
      color: #1f2937 !important;
      text-transform: uppercase;
      box-sizing: border-box !important;
    }

    .swal2-popup.rececao-codigo-modal .swal2-input.rececao-codigo-input:focus {
      border-color: #3cb371 !important;
      box-shadow: 0 0 0 3px rgba(60, 179, 113, 0.2) !important;
    }

    .swal2-popup.rececao-codigo-modal .swal2-actions {
      width: calc(100% - 44px) !important;
      margin: 8px 22px 18px !important;
      padding: 0 !important;
      display: grid !important;
      grid-template-columns: 1fr 1fr;
      gap: 10px !important;
    }

    .swal2-popup.rececao-codigo-modal .swal2-actions button {
      width: 100% !important;
      margin: 0 !important;
      border-radius: 10px !important;
      font-weight: 700 !important;
      padding: 10px 12px !important;
    }

    .swal2-popup.rececao-codigo-modal .swal2-validation-message {
      margin: 0 22px 8px !important;
      border-radius: 10px !important;
      font-weight: 600 !important;
    }
  `;

  document.head.appendChild(style);
}

function confirmarEntrega(codigo) {
  garantirEstilosModalCodigoRececao();

  const codigoInicial = (codigo || "").toString().trim();
  const codigoExibicao = codigoInicial
    ? codigoInicial
    : "Insira manualmente no próximo passo";

  Swal.fire({
    html: `
        <div style="text-align: center;">
          <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(60, 179, 113, 0.3);">
            <i class="fas fa-box-open" style="font-size: 36px; color: white;"></i>
          </div>
          <h2 style="margin: 0 0 15px 0; color: #2d3748; font-size: 24px; font-weight: 700;">Confirmar Receção</h2>
        </div>
        <div style="text-align: left; padding: 0;">
          <p style="margin-bottom: 15px; color: #4b5563;">Confirme que recebeu a sua encomenda usando o código:</p>
          <div style="background: #fef3c7; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 20px;">
            <p style="margin: 0 0 8px 0; color: #92400e; font-weight: bold; font-size: 13px;">Código de Confirmação:</p>
            <p style="margin: 0; font-size: 20px; font-weight: bold; color: #92400e; letter-spacing: 2px; font-family: monospace;">
              ${codigoExibicao}
            </p>
          </div>
          <p style="color: #6b7280; font-size: 14px; margin-bottom: 10px;">
            <strong>O que acontece ao confirmar?</strong>
          </p>
          <ul style="color: #6b7280; font-size: 13px; margin-left: 20px;">
            <li>Encomenda marcada como "Entregue"</li>
            <li>Pagamento liberado para o vendedor</li>
            <li>Poderá avaliar o produto</li>
          </ul>
          <p style="color: #dc2626; font-size: 13px; margin-top: 15px;">
            <i class="fas fa-exclamation-triangle"></i> <strong>Só confirme se recebeu o produto em boas condições!</strong>
          </p>
        </div>
      `,
    showCancelButton: true,
    confirmButtonText: '<i class="fas fa-check"></i> Sim, recebi!',
    cancelButtonText: '<i class="fas fa-clock"></i> Ainda não recebi',
    customClass: {
      confirmButton: "swal2-confirm-modern",
      cancelButton: "swal2-cancel-modern",
      popup: "swal2-border-radius",
    },
    buttonsStyling: false,
    width: 550,
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        title: "Inserir Código",
        text: "Introduza o código de confirmação da receção:",
        input: "text",
        inputPlaceholder: "Ex: CONF-ABC123",
        inputValue: codigoInicial,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-check"></i> Confirmar',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
        customClass: {
          confirmButton: "swal2-confirm-modern",
          cancelButton: "swal2-cancel-modern",
          popup: "rececao-codigo-modal",
          input: "rececao-codigo-input",
        },
        buttonsStyling: false,
        inputValidator: (value) => {
          if (!value || !value.trim()) {
            return "O código é obrigatório";
          }
        },
      }).then((codigoResult) => {
        if (!codigoResult.isConfirmed) {
          return;
        }

        $.ajax({
          url: "src/controller/controllerEncomendas.php",
          method: "POST",
          dataType: "json",
          data: {
            op: "confirmarRececao",
            codigo_confirmacao: codigoResult.value,
          },
          success: function (response) {
            if (response && response.success) {
              showModernSuccessModal(
                "Receção confirmada",
                response.message ||
                  "Encomenda marcada como entregue com sucesso.",
              ).then(() => {
                carregarEncomendas();
              });
            } else {
              showModernErrorModal(
                "Não foi possível confirmar",
                response?.message || "Código inválido ou já utilizado.",
              );
            }
          },
          error: function () {
            showModernErrorModal(
              "Erro",
              "Erro ao confirmar receção. Tente novamente.",
            );
          },
        });
      });
    } else if (result.dismiss === Swal.DismissReason.cancel) {
      showModernInfoModal(
        "Sem problema",
        "Pode confirmar a receção mais tarde, quando tiver recebido a encomenda.",
      );
    }
  });
}

function comprarNovamente(codigo) {
  showModernConfirmModal(
    "Comprar Novamente?",
    "Deseja adicionar os produtos desta encomenda ao carrinho?",
    {
      confirmText: '<i class="fas fa-cart-plus"></i> Sim, adicionar!',
      icon: "fa-cart-plus",
      iconBg: "background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%);",
    },
  ).then((result) => {
    if (result.isConfirmed) {
      showModernSuccessModal("Sucesso!", "Produtos adicionados ao carrinho");
    }
  });
}

function avaliarProduto(codigo) {
  const encomenda = (todasEncomendas || []).find(
    (item) => item.codigo_encomenda === codigo,
  );
  if (encomenda && Number(encomenda.encomenda_totalmente_avaliada || 0) === 1) {
    showModernInfoModal(
      "Informação",
      "Todos os produtos desta encomenda já foram avaliados.",
    );
    return;
  }

  // Primeiro, obter os produtos da encomenda
  $.ajax({
    url: "src/controller/controllerAvaliacoes.php",
    method: "POST",
    data: {
      op: "obterProdutosParaAvaliar",
      encomenda_codigo: codigo,
    },
    dataType: "json",
    success: function (response) {
      const produtos = Array.isArray(response?.produtos)
        ? response.produtos.filter(
            (produto) => Number(produto.avaliado || 0) !== 1,
          )
        : [];

      if (response.success && produtos.length > 0) {
        mostrarModalAvaliacao(produtos, codigo);
      } else {
        showModernWarningModal(
          "Informação",
          "Não há produtos para avaliar nesta encomenda",
        );
      }
    },
    error: function () {
      showModernErrorModal("Erro", "Erro ao carregar produtos");
    },
  });
}

function garantirEstilosModalAvaliacao() {
  if (document.getElementById("wegreen-avaliacao-modal-style")) return;

  const style = document.createElement("style");
  style.id = "wegreen-avaliacao-modal-style";
  style.textContent = `
    .swal2-popup.avaliacao-modal-popup {
      width: min(620px, 94vw) !important;
      border-radius: 16px !important;
      padding: 0 !important;
      overflow: hidden !important;
    }

    .swal2-popup.avaliacao-modal-popup .swal2-title {
      display: block !important;
      width: 100% !important;
      box-sizing: border-box !important;
      margin: 0 !important;
      padding: 16px 20px !important;
      background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%);
      color: #ffffff !important;
      font-size: 18px !important;
      font-weight: 700;
      text-align: center;
      line-height: 1.2;
      border-radius: 16px 16px 0 0;
      box-shadow: 0 4px 14px rgba(60, 179, 113, 0.25);
    }

    .swal2-popup.avaliacao-modal-popup .swal2-html-container {
      margin: 14px 1.25rem 0 !important;
      padding: 0 !important;
    }

    .avaliacao-modal-wrap {
      text-align: left;
      color: #2d3748;
    }

    .avaliacao-modal-top {
      display: flex;
      gap: 16px;
      align-items: center;
      margin-bottom: 14px;
      background: linear-gradient(135deg, #f7fafc 0%, #ffffff 100%);
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      padding: 12px;
    }

    .avaliacao-modal-img {
      width: 110px;
      height: 110px;
      border-radius: 10px;
      object-fit: cover;
      border: 2px solid #e2e8f0;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      flex-shrink: 0;
    }

    .avaliacao-modal-produto {
      min-width: 0;
    }

    .avaliacao-modal-produto h4 {
      margin: 0 0 8px 0;
      font-size: 21px;
      font-weight: 700;
      color: #1a202c;
      line-height: 1.25;
      word-break: break-word;
    }

    .avaliacao-modal-sub {
      margin: 0;
      font-size: 14px;
      color: #64748b;
      font-weight: 600;
    }

    .avaliacao-stars {
      display: flex;
      justify-content: center;
      gap: 8px;
      margin: 14px 0;
      font-size: 34px;
      line-height: 1;
    }

    .avaliacao-stars .rating-star {
      color: #cbd5e0;
      cursor: pointer;
      transition: transform 0.2s ease, color 0.2s ease;
    }

    .avaliacao-stars .rating-star.fas {
      color: #f59e0b;
      text-shadow: 0 2px 8px rgba(245, 158, 11, 0.35);
    }

    .avaliacao-stars .rating-star:hover {
      transform: translateY(-2px) scale(1.04);
    }

    .avaliacao-stars.readonly .rating-star {
      cursor: default;
    }

    .avaliacao-comentario {
      width: 100%;
      padding: 12px 14px;
      border: 1px solid #d1d5db;
      border-radius: 10px;
      min-height: 96px;
      resize: vertical;
      font-size: 14px;
      color: #1f2937;
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
      background: #ffffff;
    }

    .avaliacao-comentario:focus {
      outline: none;
      border-color: #3cb371;
      box-shadow: 0 0 0 3px rgba(60, 179, 113, 0.16);
    }

    .avaliacao-modal-footer {
      margin-top: 10px;
      text-align: center;
      color: #94a3b8;
      font-size: 13px;
      font-weight: 600;
    }

    .swal2-popup.avaliacao-modal-popup .swal2-actions {
      width: calc(100% - 2.5rem) !important;
      max-width: calc(100% - 2.5rem) !important;
      box-sizing: border-box !important;
      display: grid !important;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 10px !important;
      margin: 16px 1.25rem 1rem !important;
      padding: 0 !important;
      justify-content: stretch !important;
    }

    .swal2-popup.avaliacao-modal-popup .swal2-actions button {
      width: 100% !important;
      min-width: 0 !important;
      box-sizing: border-box !important;
      margin: 0 !important;
      padding: 10px 12px !important;
      border-radius: 10px !important;
      font-size: 14px !important;
      font-weight: 700 !important;
    }

    .swal2-popup.avaliacao-modal-popup .swal2-actions .swal2-cancel {
      order: 1;
    }

    .swal2-popup.avaliacao-modal-popup .swal2-actions .swal2-deny {
      order: 2;
    }

    .swal2-popup.avaliacao-modal-popup .swal2-actions .swal2-confirm {
      order: 3;
    }

    .swal2-popup.avaliacao-modal-popup .swal2-actions .swal2-deny.swal2-styled {
      background: #e5e7eb !important;
      color: #111827 !important;
      border: none !important;
    }

    .swal2-popup.avaliacao-modal-popup .swal2-actions .swal2-deny.swal2-styled:hover {
      background: #d1d5db !important;
      transform: translateY(-1px) !important;
      box-shadow: 0 4px 12px rgba(100, 116, 139, 0.2) !important;
    }

    .swal2-popup.avaliacao-modal-popup .swal2-actions .swal2-deny.swal2-styled:disabled {
      background: #f3f4f6 !important;
      color: #9ca3af !important;
      cursor: not-allowed !important;
      box-shadow: none !important;
      transform: none !important;
    }

    .swal2-popup.avaliacao-modal-popup .swal2-close {
      color: #ffffff !important;
      top: 8px !important;
      right: 10px !important;
    }

    .swal2-popup.avaliacao-modal-popup .swal2-close:hover {
      color: #e5f9ed !important;
      background: rgba(255, 255, 255, 0.16) !important;
    }
  `;

  document.head.appendChild(style);
}

function mostrarModalAvaliacao(produtos, encomenda_codigo) {
  garantirEstilosModalAvaliacao();

  let currentIndex = 0;
  let selectedRating = 0;

  function showProduct(index) {
    const produto = produtos[index];
    selectedRating = produto.avaliado ? produto.avaliacao_dada : 0;

    const imagem = produto.foto || "assets/media/products/placeholder.jpg";
    const temAnterior = index > 0;
    const temProximo = index < produtos.length - 1;
    const mostrarAnteriorSeparado = !temProximo && temAnterior;
    const html = `
      <div class="avaliacao-modal-wrap">
        <div class="avaliacao-modal-top">
          <img src="${imagem}" alt="${produto.nome}" class="avaliacao-modal-img" onerror="this.src='assets/media/products/placeholder.jpg'">
          <div class="avaliacao-modal-produto">
            <h4>${produto.nome}</h4>
            <p class="avaliacao-modal-sub">${produto.avaliado ? "Avaliação já registada para este produto" : "Como foi a sua experiência com este produto?"}</p>
          </div>
        </div>

        <div class="avaliacao-stars ${produto.avaliado ? "readonly" : ""}">
          <i class="far fa-star rating-star" data-rating="1"></i>
          <i class="far fa-star rating-star" data-rating="2"></i>
          <i class="far fa-star rating-star" data-rating="3"></i>
          <i class="far fa-star rating-star" data-rating="4"></i>
          <i class="far fa-star rating-star" data-rating="5"></i>
        </div>

        <textarea id="comentario-avaliacao" class="avaliacao-comentario" placeholder="Deixe um comentário (opcional)..." ${produto.avaliado ? "disabled" : ""}>${produto.avaliado ? produto.comentario_dado || "" : ""}</textarea>

        <div class="avaliacao-modal-footer">Produto ${index + 1} de ${produtos.length}</div>
      </div>
    `;

    Swal.fire({
      title: produto.avaliado ? "Avaliação Registada" : "Avaliar Produto",
      html: html,
      showCancelButton: true,
      showDenyButton: temProximo || mostrarAnteriorSeparado,
      confirmButtonText: produto.avaliado ? "Fechar" : "Enviar Avaliação",
      denyButtonText: temProximo ? "Próximo" : "Anterior",
      cancelButtonText: mostrarAnteriorSeparado
        ? "Cancelar"
        : temAnterior
          ? "Anterior"
          : "Cancelar",
      allowOutsideClick: false,
      buttonsStyling: false,
      customClass: {
        confirmButton: "swal2-confirm-modern-success",
        denyButton: "swal2-confirm-modern",
        cancelButton: "swal2-cancel-modern",
        popup: "swal2-border-radius avaliacao-modal-popup",
      },
      preConfirm: () => {
        if (produto.avaliado) return true;

        if (selectedRating === 0) {
          Swal.showValidationMessage("Por favor, selecione uma avaliação");
          return false;
        }
        return {
          produto_id: produto.Produto_id,
          avaliacao: selectedRating,
          comentario: document.getElementById("comentario-avaliacao").value,
        };
      },
      didOpen: () => {
        const pintarEstrelas = (rating) => {
          $(".rating-star").each(function (i) {
            if (i < rating) {
              $(this).removeClass("far").addClass("fas");
            } else {
              $(this).removeClass("fas").addClass("far");
            }
          });
        };

        if (produto.avaliado) {
          pintarEstrelas(produto.avaliacao_dada || 0);
        } else if (selectedRating > 0) {
          pintarEstrelas(selectedRating);
        }

        $(".rating-star")
          .on("mouseenter", function () {
            if (produto.avaliado) return;
            const hoverRating = $(this).data("rating");
            pintarEstrelas(hoverRating);
          })
          .on("mouseleave", function () {
            if (produto.avaliado) return;
            pintarEstrelas(selectedRating);
          });

        $(".rating-star").on("click", function () {
          if (produto.avaliado) return;

          selectedRating = $(this).data("rating");
          pintarEstrelas(selectedRating);
        });
      },
    }).then((result) => {
      if (result.isConfirmed && !produto.avaliado) {
        enviarAvaliacao(result.value, encomenda_codigo, index, produtos, () => {
          if (temProximo) {
            showProduct(index + 1);
          } else {
            carregarEncomendas();
          }
        });
      } else if (result.isDenied) {
        if (temProximo) {
          showProduct(index + 1);
        } else if (temAnterior) {
          showProduct(index - 1);
        }
      } else if (result.dismiss === Swal.DismissReason.cancel && temAnterior) {
        showProduct(index - 1);
      } else if (
        result.isConfirmed ||
        result.dismiss === Swal.DismissReason.cancel
      ) {
        carregarEncomendas();
      }
    });
  }

  showProduct(currentIndex);
}

function enviarAvaliacao(dados, encomenda_codigo, index, produtos, onSucesso) {
  $.ajax({
    url: "src/controller/controllerAvaliacoes.php",
    method: "POST",
    data: {
      op: "criarAvaliacao",
      produto_id: dados.produto_id,
      encomenda_codigo: encomenda_codigo,
      avaliacao: dados.avaliacao,
      comentario: dados.comentario,
    },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        showModernSuccessModal("Avaliação Registada!", response.message, {
          timer: 1500,
          onClose: () => {
            if (typeof onSucesso === "function") {
              onSucesso();
              return;
            }

            if (index < produtos.length - 1) {
              mostrarModalAvaliacao(
                produtos.slice(index + 1),
                encomenda_codigo,
              );
            } else {
              carregarEncomendas();
            }
          },
        });
      } else {
        showModernErrorModal("Erro", response.message);
      }
    },
    error: function () {
      showModernErrorModal("Erro", "Erro ao enviar avaliação");
    },
  });
}

function verDetalhes(codigo) {
  $.ajax({
    url: "src/controller/controllerEncomendas.php",
    method: "POST",
    data: {
      op: "detalhesEncomenda",
      codigo: codigo,
    },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        mostrarDetalhes(response.data);
      } else {
        showModernErrorModal("Erro", response.message);
      }
    },
    error: function (xhr, status, error) {
      showModernErrorModal("Erro", "Erro ao carregar detalhes da encomenda");
    },
  });
}

function mostrarDetalhes(encomenda) {
  // Determinar tipo de entrega
  const tipoEntrega = encomenda.tipo_entrega || "domicilio";
  const tituloMorada =
    tipoEntrega === "ponto_recolha" ? "Ponto de Recolha" : "Morada de Entrega";
  const moradaCompleta =
    tipoEntrega === "ponto_recolha"
      ? encomenda.morada_ponto_recolha ||
        encomenda.morada ||
        "Morada não disponível"
      : encomenda.morada_completa ||
        encomenda.morada ||
        "Morada não disponível";

  // Só mostrar mapa se houver morada válida
  const temMorada =
    moradaCompleta &&
    moradaCompleta !== "Morada não disponível" &&
    moradaCompleta !== "null";

  // Calcular dias desde encomenda
  const dataEncomenda = new Date(encomenda.data_envio);
  const hoje = new Date();
  const diasDesdeEncomenda = Math.floor(
    (hoje - dataEncomenda) / (1000 * 60 * 60 * 24),
  );

  const statusInfo = getStatusInfo(normalizarEstadoEncomenda(encomenda.estado));

  const html = `
      <div style="padding: 25px;">

        <!-- Grid: Informações à esquerda, Mapa à direita -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">

          <!-- COLUNA ESQUERDA: Informações -->
          <div>
            <!-- Encomenda -->
            <div style="background: linear-gradient(135deg, #f7fafc 0%, #ffffff 100%); padding: 18px; border-radius: 10px; border-left: 4px solid #3cb371; margin-bottom: 16px; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
              <h4 style="margin: 0 0 12px 0; color: #2d3748; font-size: 15px; font-weight: 700;">
                <i class="fas fa-box" style="margin-right: 8px; color: #3cb371;"></i>
                Encomenda
              </h4>
              <p style="margin: 6px 0; font-size: 13px; color: #4a5568;"><strong style="color: #2d3748;">Código:</strong> ${encomenda.codigo_encomenda}</p>
              <p style="margin: 6px 0; font-size: 13px; color: #4a5568;"><strong style="color: #2d3748;">Data:</strong> ${encomenda.data_envio}</p>
              <p style="margin: 6px 0; font-size: 13px; color: #4a5568;"><strong style="color: #2d3748;">Valor:</strong> <span style="color: #3cb371; font-weight: bold; font-size: 15px;">€${parseFloat(encomenda.total).toFixed(2)}</span></p>
              <p style="margin: 6px 0; font-size: 13px; color: #4a5568;"><strong style="color: #2d3748;">Status:</strong> <span class="badge ${statusInfo.class}" style="font-size: 12px; padding: 4px 10px; border-radius: 6px;">${statusInfo.text}</span></p>
            </div>

            <!-- Envio -->
            <div style="background: linear-gradient(135deg, #f7fafc 0%, #ffffff 100%); padding: 18px; border-radius: 10px; border-left: 4px solid #3cb371; margin-bottom: 16px; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
              <h4 style="margin: 0 0 12px 0; color: #2d3748; font-size: 15px; font-weight: 700;">
                <i class="fas fa-shipping-fast" style="margin-right: 8px; color: #3cb371;"></i>
                Envio
              </h4>
              <p style="margin: 6px 0; font-size: 13px; color: #4a5568;"><strong style="color: #2d3748;">Transportadora:</strong> ${encomenda.transportadora || "N/A"}</p>
              <p style="margin: 6px 0; font-size: 13px; color: #4a5568;"><strong style="color: #2d3748;">Rastreio:</strong> ${encomenda.plano_rastreio || "N/A"}</p>
              <p style="margin: 6px 0; font-size: 13px; color: #4a5568;"><strong style="color: #2d3748;">Decorrido:</strong> ${diasDesdeEncomenda} dia(s)</p>
            </div>

            <!-- Morada -->
            <div style="background: linear-gradient(135deg, #f7fafc 0%, #ffffff 100%); padding: 18px; border-radius: 10px; border-left: 4px solid #3cb371; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
              <h4 style="margin: 0 0 12px 0; color: #2d3748; font-size: 15px; font-weight: 700;">
                <i class="fas fa-map-marker-alt" style="margin-right: 8px; color: #3cb371;"></i>
                ${tituloMorada}
              </h4>
              ${
                tipoEntrega === "ponto_recolha" && encomenda.nome_ponto_recolha
                  ? `<p style="font-weight: 600; margin: 0 0 6px 0; font-size: 14px; color: #2d3748;">${encomenda.nome_ponto_recolha}</p>`
                  : ""
              }
              <p style="margin: 0; font-size: 13px; color: #4a5568; line-height: 1.6;">${moradaCompleta}</p>
              ${temMorada ? `<button onclick="navigator.clipboard.writeText('${moradaCompleta.replace(/'/g, "\\'")}').then(() => showModernSuccessModal('Copiado!', 'Morada copiada', {timer: 1500}))" style="margin-top: 10px; padding: 7px 14px; background: #3cb371; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600;" onmouseover="this.style.background='#2e8b57'" onmouseout="this.style.background='#3cb371'"><i class="fas fa-copy" style="margin-right: 5px;"></i>Copiar Morada</button>` : ""}
            </div>
          </div>

          <!-- COLUNA DIREITA: Mapa -->
          ${
            temMorada
              ? `
            <div style="background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); height: fit-content;">
              <h4 style="margin: 0 0 12px 0; color: #2d3748; font-size: 15px; font-weight: 700;">
                <i class="fas fa-map" style="margin-right: 8px; color: #3cb371;"></i>
                Localização de Entrega
              </h4>
              <div style="border-radius: 8px; overflow: hidden; border: 2px solid #e5e7eb;">
                <iframe width="100%" height="400" frameborder="0" style="border:0"
                  src="https://maps.google.com/maps?q=${encodeURIComponent(moradaCompleta)}&t=&z=15&ie=UTF8&iwloc=&output=embed" allowfullscreen>
                </iframe>
              </div>
              <div style="text-align: center; margin-top: 12px;">
                <a href="https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(moradaCompleta)}" target="_blank"
                  style="display: inline-block; padding: 10px 18px; background: #3cb371; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 13px; border-radius: 8px; box-shadow: 0 2px 8px rgba(60, 179, 113, 0.3); transition: all 0.3s ease;"
                  onmouseover="this.style.background='#2e8b57'; this.style.transform='translateY(-2px)'"
                  onmouseout="this.style.background='#3cb371'; this.style.transform='translateY(0)'">
                  <i class="fas fa-external-link-alt" style="margin-right: 6px;"></i>
                  Abrir no Google Maps
                </a>
              </div>
            </div>
          `
              : "<div></div>"
          }

        </div>
      </div>
    `;

  $("#detalhesContent").html(html);
  $("#detalhesModal").fadeIn();
}

function mostrarTodosProdutos(produtos) {
  let produtosHTML = produtos
    .map(
      (p, index) => `
      <div onclick="window.location.href='produto.php?id=${p.Produto_id}'"
           style="display: flex; align-items: center; gap: 15px; padding: 15px; background: ${index % 2 === 0 ? "#f9fafb" : "#ffffff"}; border-radius: 8px; margin-bottom: 10px; cursor: pointer; transition: all 0.3s ease;"
           onmouseover="this.style.background='#e8f5e9'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(60, 179, 113, 0.2)';"
           onmouseout="this.style.background='${index % 2 === 0 ? "#f9fafb" : "#ffffff"}'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
        <img src="${p.foto || "assets/media/products/default.jpg"}" alt="${p.nome}"
             style="width: 70px; height: 70px; object-fit: cover; border-radius: 8px; border: 2px solid #e5e7eb;">
        <div style="flex: 1;">
          <div style="font-weight: 600; color: #2d3748; font-size: 15px; margin-bottom: 4px;">${p.nome}</div>
          <div style="color: #64748b; font-size: 13px;">
            <i class="fas fa-box" style="color: #3cb371; margin-right: 4px;"></i>
            Quantidade: ${p.quantidade}
            <span style="margin-left: 15px;">
              <i class="fas fa-tag" style="color: #3cb371; margin-right: 4px;"></i>
              Preço: €${parseFloat(p.valor).toFixed(2)}
            </span>
          </div>
        </div>
        <i class="fas fa-chevron-right" style="color: #3cb371; font-size: 14px;"></i>
      </div>
    `,
    )
    .join("");

  Swal.fire({
    html: `
        <div style="margin: -20px -20px 0 -20px; overflow-x: hidden;">
          <div style="background: linear-gradient(135deg, #3cb371 0%, #2d8659 100%); padding: 25px 20px; border-radius: 8px 8px 0 0;">
            <h2 style="margin: 0; color: white; font-size: 22px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 10px;">
              <i class="fas fa-cubes" style="font-size: 24px;"></i>
              Produtos da Encomenda
            </h2>
          </div>
          <div style="text-align: center; max-height: 500px; overflow-y: auto; overflow-x: hidden; padding: 30px 25px;">
            <div style="background: #e8f5e9; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center; border-left: 4px solid #3cb371; max-width: 400px; margin-left: auto; margin-right: auto;">
              <span style="font-weight: 600; color: #2d3748;">
                <i class="fas fa-shopping-bag" style="color: #3cb371; margin-right: 6px;"></i>
                Total de ${produtos.length} produto${produtos.length > 1 ? "s" : ""} nesta encomenda
              </span>
            </div>
            <div style="max-width: 550px; margin: 0 auto;">
              ${produtosHTML}
            </div>
          </div>
        </div>
      `,
    width: "650px",
    confirmButtonText: "Fechar",
    buttonsStyling: false,
    showClass: {
      popup: "swal2-show",
      backdrop: "swal2-backdrop-show",
    },
    customClass: {
      confirmButton: "swal2-confirm-modern-success",
      popup: "swal2-border-radius",
      htmlContainer: "swal2-html-container-no-padding",
    },
    didOpen: () => {
      // Adicionar estilo customizado para remover padding do container HTML
      const style = document.createElement("style");
      style.textContent = `
          .swal2-html-container-no-padding {
            padding: 0 !important;
            margin: 0 !important;
            overflow-x: hidden !important;
          }
          .swal2-border-radius {
            border-radius: 12px !important;
            overflow: hidden;
          }
        `;
      document.head.appendChild(style);
    },
  });
}

function fecharModal() {
  $("#detalhesModal").fadeOut();
}

function descarregarFatura(codigo) {
  Swal.fire({
    html: `
      <div style="text-align: center; padding: 6px 0;">
        <div style="width: 76px; height: 76px; margin: 0 auto 18px; border-radius: 50%; background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(60, 179, 113, 0.25);">
          <i class="fas fa-file-invoice" style="font-size: 34px; color: #2d8a5a;"></i>
        </div>
        <h2 style="margin: 0 0 8px 0; color: #1f2937; font-size: 24px; font-weight: 700;">A gerar fatura</h2>
        <p style="margin: 0; color: #64748b; font-size: 15px;">A preparar o seu PDF...</p>
      </div>
    `,
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false,
    customClass: {
      popup: "swal2-border-radius",
    },
    didOpen: () => {
      Swal.showLoading();

      if (!document.getElementById("wegreen-swal-fatura-style")) {
        const style = document.createElement("style");
        style.id = "wegreen-swal-fatura-style";
        style.textContent = `
          .swal2-loader {
            border-color: #d1fae5 transparent #d1fae5 transparent !important;
            width: 2.4em !important;
            height: 2.4em !important;
            margin-top: 14px !important;
          }
        `;
        document.head.appendChild(style);
      }
    },
  });

  // Buscar detalhes da encomenda
  $.ajax({
    url: "src/controller/controllerEncomendas.php",
    type: "POST",
    data: {
      op: "detalhesEncomenda",
      codigo: codigo,
    },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        gerarPDFFatura(response.data);
      } else {
        Swal.close();
        showModernErrorModal(
          "Erro",
          "Não foi possível obter os detalhes da encomenda",
        );
      }
    },
    error: function (xhr, status, error) {
      Swal.close();
      showModernErrorModal(
        "Erro",
        "Erro ao comunicar com o servidor: " + error,
      );
    },
  });
}

function gerarPDFFatura(encomenda) {
  try {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Cabeçalho WeGreen
    doc.setFillColor(60, 179, 113);
    doc.rect(0, 0, 210, 35, "F");

    doc.setTextColor(255, 255, 255);
    doc.setFontSize(24);
    doc.setFont(undefined, "bold");
    doc.text("WeGreen", 14, 15);

    doc.setFontSize(10);
    doc.setFont(undefined, "normal");
    doc.text("Marketplace Sustentável", 14, 22);
    doc.text("NIF: 123456789 | Email: info@wegreen.pt", 14, 28);

    // Título Fatura
    doc.setTextColor(0, 0, 0);
    doc.setFontSize(20);
    doc.setFont(undefined, "bold");
    doc.text("FATURA", 150, 15);

    doc.setFontSize(10);
    doc.setFont(undefined, "normal");
    doc.text("#" + encomenda.codigo_encomenda, 150, 22);
    doc.text(
      "Data: " + new Date(encomenda.data_envio).toLocaleDateString("pt-PT"),
      150,
      28,
    );

    // Informações do Cliente
    doc.setFontSize(12);
    doc.setFont(undefined, "bold");
    doc.text("Cliente:", 14, 50);

    doc.setFontSize(10);
    doc.setFont(undefined, "normal");
    doc.text(
      encomenda.cliente_nome ||
        document.body.getAttribute("data-user-nome") ||
        "",
      14,
      57,
    );
    doc.text(encomenda.morada || "N/A", 14, 63);

    // Linha separadora
    doc.setDrawColor(200, 200, 200);
    doc.line(14, 75, 196, 75);

    // Processar produtos
    let produtos = [];

    // Verificar se temos produtos_lista (novo formato)
    if (encomenda.produtos_lista && Array.isArray(encomenda.produtos_lista)) {
      produtos = encomenda.produtos_lista.map((p) => [
        p.nome,
        p.quantidade.toString(),
        "€" + parseFloat(p.valor / p.quantidade).toFixed(2),
        "€" + parseFloat(p.valor).toFixed(2),
      ]);
    }
    // Se não, tentar processar produtos_detalhes (HTML)
    else if (encomenda.produtos_detalhes) {
      const produtosHTML = $(encomenda.produtos_detalhes);
      produtosHTML.find("tr").each(function () {
        const cols = $(this).find("td");
        if (cols.length >= 3) {
          produtos.push([
            cols.eq(0).text().trim(),
            cols.eq(1).text().trim(),
            cols.eq(2).text().trim(),
            cols.eq(2).text().trim(), // Total = Preço (já calculado no HTML)
          ]);
        }
      });
    }
    // Fallback: se não houver produtos, criar linha genérica
    else if (encomenda.produtos) {
      produtos.push([
        encomenda.produtos,
        "1",
        "€" + parseFloat(encomenda.total).toFixed(2),
        "€" + parseFloat(encomenda.total).toFixed(2),
      ]);
    }

    doc.autoTable({
      startY: 80,
      head: [["Produto", "Quantidade", "Preço Unit.", "Total"]],
      body: produtos,
      theme: "striped",
      headStyles: {
        fillColor: [60, 179, 113],
        textColor: [255, 255, 255],
        fontStyle: "bold",
        fontSize: 11,
      },
      styles: {
        fontSize: 10,
        cellPadding: 5,
      },
      columnStyles: {
        0: {
          cellWidth: 80,
        },
        1: {
          halign: "center",
          cellWidth: 30,
        },
        2: {
          halign: "right",
          cellWidth: 35,
        },
        3: {
          halign: "right",
          cellWidth: 35,
        },
      },
    });

    // Total
    const finalY = doc.lastAutoTable.finalY + 10;
    doc.setFontSize(12);
    doc.setFont(undefined, "bold");
    doc.text("Total:", 140, finalY);
    doc.text("€" + parseFloat(encomenda.total).toFixed(2), 180, finalY, {
      align: "right",
    });

    // Transportadora
    doc.setFontSize(10);
    doc.setFont(undefined, "normal");
    doc.text(
      "Transportadora: " + (encomenda.transportadora || "N/A"),
      14,
      finalY + 10,
    );
    doc.text("Estado: " + (encomenda.estado || "N/A"), 14, finalY + 16);

    // Rodapé
    doc.setFontSize(8);
    doc.setTextColor(150, 150, 150);
    doc.text(
      "Obrigado pela sua compra! WeGreen - Sustentabilidade em cada produto.",
      105,
      280,
      {
        align: "center",
      },
    );
    doc.text("www.wegreen.pt | suporte@wegreen.pt", 105, 285, {
      align: "center",
    });

    // Download
    doc.save("fatura_" + encomenda.codigo_encomenda + ".pdf");

    Swal.close();
    showModernSuccessModal(
      "Fatura gerada!",
      "O download iniciou automaticamente.",
      { timer: 2000 },
    );
  } catch (error) {
    Swal.close();
    showModernErrorModal("Erro", "Erro ao gerar a fatura: " + error.message);
  }
}

function cancelarEncomenda(codigo) {
  showModernConfirmModal(
    "Cancelar Encomenda?",
    "Esta ação não pode ser revertida! A encomenda será permanentemente cancelada.",
    {
      confirmText: '<i class="fas fa-times-circle"></i> Sim, cancelar!',
      icon: "fa-exclamation-triangle",
      iconBg:
        "background: linear-gradient(135deg, #ff6b6b 0%, #c92a2a 100%); box-shadow: 0 8px 20px rgba(201, 42, 42, 0.3);",
    },
  ).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "src/controller/controllerEncomendas.php",
        method: "POST",
        data: {
          op: "cancelarEncomenda",
          codigo: codigo,
        },
        dataType: "json",
        success: function (response) {
          if (response.success) {
            showModernSuccessModal(
              "Encomenda cancelada",
              "A encomenda foi cancelada com sucesso.",
              { timer: 2000 },
            );
            carregarEncomendas();
          } else {
            // Determinar motivo específico
            let motivoHTML = "";
            if (response.message.includes("não pode ser cancelada")) {
              motivoHTML = `
                  <div style="background: #fff5f5; border-left: 4px solid #ff6b6b; padding: 15px; border-radius: 8px; margin-top: 15px; text-align: left;">
                    <p style="margin: 0 0 10px 0; color: #c92a2a; font-weight: 600; font-size: 14px;">
                      <i class="fas fa-ban" style="margin-right: 8px;"></i>Motivo:
                    </p>
                    <p style="margin: 0; color: #64748b; font-size: 13px;">
                      Apenas encomendas com estado <strong>Pendente</strong> ou <strong>Em Processamento</strong> podem ser canceladas.
                    </p>
                    <p style="margin: 10px 0 0 0; color: #64748b; font-size: 13px;">
                      Esta encomenda já está <strong>Enviada</strong> ou <strong>Entregue</strong> e não pode mais ser cancelada.
                    </p>
                  </div>
                `;
            } else if (response.message.includes("não encontrada")) {
              motivoHTML = `
                  <div style="background: #fff5f5; border-left: 4px solid #ff6b6b; padding: 15px; border-radius: 8px; margin-top: 15px;">
                    <p style="margin: 0; color: #c92a2a; font-size: 13px;">
                      <i class="fas fa-search" style="margin-right: 5px;"></i>
                      A encomenda não foi encontrada no sistema.
                    </p>
                  </div>
                `;
            }

            Swal.fire({
              html: `
                  <div style="text-align: center;">
                    <div style="width: 70px; height: 70px; margin: 0 auto 20px; background: linear-gradient(135deg, #ff6b6b 0%, #c92a2a 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                      <i class="fas fa-times" style="font-size: 35px; color: white;"></i>
                    </div>
                    <h3 style="margin: 0 0 10px 0; color: #2d3748; font-size: 20px; font-weight: 700;">Erro</h3>
                    <p style="color: #64748b; font-size: 14px; margin: 0;">Encomenda não pode ser cancelada</p>
                    ${motivoHTML}
                  </div>
                `,
              confirmButtonText: "OK",
              buttonsStyling: false,
              customClass: {
                confirmButton: "swal2-confirm-modern-error",
                popup: "swal2-border-radius",
              },
            });
          }
        },
      });
    }
  });
}

function previewImage(imageUrl, productName) {
  Swal.fire({
    imageUrl: imageUrl,
    imageAlt: productName,
    title: productName,
    showConfirmButton: false,
    showCloseButton: true,
    width: "600px",
    customClass: {
      image: "preview-image-large",
      popup: "swal2-border-radius",
    },
    didOpen: () => {
      const style = document.createElement("style");
      style.innerHTML = `
                        .preview-image-large {
                            max-height: 500px !important;
                            object-fit: contain !important;
                            border-radius: 12px !important;
                        }
                    `;
      document.head.appendChild(style);
    },
  });
}

function logout() {
  showModernConfirmModal(
    "Terminar Sessão?",
    "Tem a certeza que pretende sair?",
    {
      confirmText: '<i class="fas fa-check"></i> Sim, sair',
      icon: "fa-sign-out-alt",
      iconBg:
        "background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%); box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);",
    },
  ).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "src/controller/controllerPerfil.php?op=2",
        method: "GET",
      }).always(function () {
        window.location.href = "index.html";
      });
    }
  });
}

// Fechar modal ao clicar fora
window.onclick = function (event) {
  if (event.target.id === "detalhesModal") {
    fecharModal();
  }
};

// Dropdown do usuário
$(document).ready(function () {
  $("#userMenuBtn").on("click", function (e) {
    e.stopPropagation();
    $("#userDropdown").toggleClass("active");
  });

  $(document).on("click", function (e) {
    if (!$(e.target).closest(".navbar-user").length) {
      $("#userDropdown").removeClass("active");
    }
  });

  $("#userDropdown").on("click", function (e) {
    e.stopPropagation();
  });
});

function showPasswordModal() {
  Swal.fire({
    title: "Alterar Senha",
    html: `
                    <input type="password" id="currentPassword" class="swal2-input" placeholder="Senha Atual">
                    <input type="password" id="newPassword" class="swal2-input" placeholder="Nova Senha">
                    <input type="password" id="confirmPassword" class="swal2-input" placeholder="Confirmar Nova Senha">
                `,
    showCancelButton: true,
    confirmButtonText: "Alterar",
    cancelButtonText: "Cancelar",
    buttonsStyling: false,
    customClass: {
      confirmButton: "swal2-confirm-modern-success",
      cancelButton: "swal2-cancel-modern",
      popup: "swal2-border-radius",
    },
    preConfirm: () => {
      const currentPassword = document.getElementById("currentPassword").value;
      const newPassword = document.getElementById("newPassword").value;
      const confirmPassword = document.getElementById("confirmPassword").value;

      if (!currentPassword || !newPassword || !confirmPassword) {
        Swal.showValidationMessage("Preencha todos os campos");
        return false;
      }

      if (newPassword !== confirmPassword) {
        Swal.showValidationMessage("As senhas não coincidem");
        return false;
      }

      if (newPassword.length < 6) {
        Swal.showValidationMessage("A senha deve ter pelo menos 6 caracteres");
        return false;
      }

      return {
        currentPassword,
        newPassword,
      };
    },
  }).then((result) => {
    if (result.isConfirmed) {
      showModernSuccessModal("Sucesso!", "Senha alterada com sucesso!");
      $("#userDropdown").removeClass("active");
    }
  });
}
