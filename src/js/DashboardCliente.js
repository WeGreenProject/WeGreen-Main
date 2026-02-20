document.querySelectorAll(".menu-item").forEach((item) => {
  item.addEventListener("click", function (e) {
    if (this.getAttribute("href") && this.getAttribute("href") !== "#") {
      return;
    }
    e.preventDefault();
    document
      .querySelectorAll(".menu-item")
      .forEach((mi) => mi.classList.remove("active"));
    this.classList.add("active");
  });
});

$(document).ready(function () {
  carregarDadosDashboard();
  atualizarContadorFavoritos();
});

function carregarDadosDashboard() {
  $.ajax({
    url: "src/controller/controllerDashboardCliente.php",
    method: "GET",
    data: { op: 2 },
    dataType: "json",
    success: function (response) {
      if (response.success && response.data.length > 0) {
        renderEncomendasRecentes(response.data);
      } else {
        emptyStateEncomendas();
      }
    },
    error: function (xhr, status, error) {
      emptyStateEncomendas();
    },
  });

  $.ajax({
    url: "src/controller/controllerDashboardCliente.php",
    method: "GET",
    data: { op: 3 },
    dataType: "json",
    success: function (response) {
      if (response.success && response.data && response.data.length > 0) {
        renderRecomendacoes(response.data);
      } else {
        emptyStateRecomendacoes();
      }
    },
    error: function (xhr, status, error) {
      emptyStateRecomendacoes();
    },
  });
}

function renderEncomendasRecentes(encomendas) {
  let html = "";

  encomendas.slice(0, 5).forEach(function (enc) {
    const statusInfo = getStatusInfo(enc.estado);
    const data = formatarData(enc.data_envio);
    const codigoEncomenda = encodeURIComponent(enc.codigo_encomenda || enc.id);

    const foto =
      enc.foto_produto ||
      'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="%23cbd5e0" stroke-width="2"%3E%3Crect x="3" y="3" width="18" height="18" rx="2" ry="2"%3E%3C/rect%3E%3Ccircle cx="8.5" cy="8.5" r="1.5"%3E%3C/circle%3E%3Cpolyline points="21 15 16 10 5 21"%3E%3C/polyline%3E%3C/svg%3E';

    const produtos = enc.nomes_produtos || "Produto";
    const produtosTexto =
      produtos.length > 50 ? produtos.substring(0, 50) + "..." : produtos;
    const transportadora = enc.transportadora || "N/A";

    html += `
                    <div class="encomenda-card">
                        <div class="encomenda-header">
                            <div class="encomenda-info">
                                <div class="encomenda-numero">#${enc.codigo_encomenda || enc.id}</div>
                                <div class="encomenda-data"><i class="far fa-calendar"></i> ${data}</div>
                            </div>
                            <span class="status-badge ${statusInfo.class}">${statusInfo.text}</span>
                        </div>
                        <div class="encomenda-body">
                            <div class="encomenda-produto-info">
                                <img src="${foto}" alt="Produto" class="produto-thumbnail" style="background: #f7fafc;">
                                <div class="encomenda-produto-detalhes">
                                    <div class="encomenda-produto-nome" title="${produtos}">${produtosTexto}</div>
                                    <div class="encomenda-produto-meta">
                                        <span><i class="fas fa-box"></i> ${enc.total_produtos || 0} item(s)</span>
                                        <span><i class="fas fa-truck"></i> ${transportadora}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="encomenda-valor">
                                <div class="valor-label">Total</div>
                                <div class="valor-amount">€${parseFloat(enc.valor_total || 0).toFixed(2)}</div>
                            </div>
                        </div>
                        <div class="encomenda-actions">
                          <button class="btn-action btn-primary" onclick="window.location.href='minhasEncomendas.php?encomenda=${codigoEncomenda}'">
                                <i class="fas fa-eye"></i> Ver Detalhes
                            </button>
                        </div>
                    </div>
                `;
  });

  $("#encomendasContainer").html(html);
}

function renderRecomendacoes(produtos) {
  let itemsHtml = "";

  produtos.forEach(function (produto) {
    const imagem =
      produto.foto || produto.Imagem1 || "assets/media/products/default.jpg";
    const preco = parseFloat(produto.preco || produto.Preco || 0).toFixed(2);
    const nome = produto.nome || produto.Nome || "Produto";
    const id = produto.Produto_id || produto.produto_id;

    itemsHtml += `
                    <div class="produto-item">
                        <div class="produto-image">
                            <img src="${imagem}" alt="${nome}">
                            <div class="produto-overlay">
                                <a href="produto.php?id=${id}" class="btn-ver-produto">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                        <div class="produto-info">
                            <h4 class="produto-nome">${nome}</h4>
                            <div class="produto-preco">€${preco}</div>
                        </div>
                    </div>
                `;
  });

  const html = `
    <div class="produtos-carousel">
      <button class="produtos-carousel-btn prev" type="button" aria-label="Produto anterior">
        <i class="fas fa-chevron-left"></i>
      </button>
      <div class="produtos-grid">${itemsHtml}</div>
      <button class="produtos-carousel-btn next" type="button" aria-label="Produto seguinte">
        <i class="fas fa-chevron-right"></i>
      </button>
    </div>
  `;

  $("#recomendacoesContainer").html(html);

  inicializarCarouselRecomendacoes();
}

function inicializarCarouselRecomendacoes() {
  const $carousel = $("#recomendacoesContainer .produtos-carousel");
  const $track = $carousel.find(".produtos-grid");
  const $btnPrev = $carousel.find(".produtos-carousel-btn.prev");
  const $btnNext = $carousel.find(".produtos-carousel-btn.next");

  if (!$track.length) return;

  const atualizarBotoes = () => {
    const el = $track.get(0);
    if (!el) return;

    const maxScrollLeft = el.scrollWidth - el.clientWidth;
    const scrollLeft = el.scrollLeft;

    $btnPrev.prop("disabled", scrollLeft <= 0);
    $btnNext.prop("disabled", scrollLeft >= maxScrollLeft - 2);
  };

  $btnPrev.on("click", function () {
    const el = $track.get(0);
    if (!el) return;
    $track.animate({ scrollLeft: Math.max(0, el.scrollLeft - 260) }, 220);
  });

  $btnNext.on("click", function () {
    const el = $track.get(0);
    if (!el) return;
    const maxScrollLeft = el.scrollWidth - el.clientWidth;
    $track.animate(
      { scrollLeft: Math.min(maxScrollLeft, el.scrollLeft + 260) },
      220,
    );
  });

  $track.on("scroll", atualizarBotoes);
  $(window)
    .off("resize.recomendacoesCarousel")
    .on("resize.recomendacoesCarousel", atualizarBotoes);

  atualizarBotoes();
}

function emptyStateEncomendas() {
  const html = `
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h4>Ainda não tens encomendas</h4>
                    <p>Explora os nossos produtos sustentáveis e faz a tua primeira compra!</p>
                    <a href="ecommerce.html" class="btn-primary">
                        <i class="fas fa-shopping-bag"></i> Explorar Produtos
                    </a>
                </div>
            `;
  $("#encomendasContainer").html(html);
}

function emptyStateRecomendacoes() {
  const html = `
                <div class="empty-state">
                    <i class="fas fa-shopping-bag"></i>
                    <h4>Ainda não compraste nada</h4>
                    <p>Explora a nossa coleção de moda sustentável e faz a tua primeira compra!</p>
                    <a href="marketplace.html" class="btn-primary">
                        <i class="fas fa-search"></i> Explorar Produtos
                    </a>
                </div>
            `;
  $("#recomendacoesContainer").html(html);
}

function getStatusClass(estado) {
  return estado?.toLowerCase() || "pendente";
}

function getStatusLabel(estado) {
  const labelMap = {
    pendente: "Pendente",
    processando: "Processando",
    enviado: "Enviado",
    entregue: "Entregue",
    cancelado: "Cancelado",
  };
  return labelMap[estado?.toLowerCase()] || estado;
}

function getStatusInfo(estado) {
  const statusMap = {
    pendente: { class: "status-pendente", text: "Pendente" },
    processando: { class: "status-processando", text: "Processando" },
    enviado: { class: "status-enviado", text: "Enviado" },
    entregue: { class: "status-entregue", text: "Entregue" },
    devolvido: { class: "status-devolvido", text: "Devolvido" },
    cancelado: { class: "status-cancelado", text: "Cancelado" },
  };
  return (
    statusMap[estado?.toLowerCase()] || {
      class: "status-pendente",
      text: estado,
    }
  );
}

function formatarData(data) {
  const d = new Date(data);
  return d.toLocaleDateString("pt-PT", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  });
}

function atualizarContadorFavoritos() {
  $.ajax({
    url: "src/controller/controllerFavoritos.php",
    method: "GET",
    data: { op: 5 },
    dataType: "json",
    success: function (response) {
      if (response.success && response.total > 0) {
        $("#favoritosBadge").text(response.total).show();
      } else {
        $("#favoritosBadge").hide();
      }
    },
  });
}

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
    confirmButtonColor: "#3cb371",
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

      return { currentPassword, newPassword };
    },
  }).then((result) => {
    if (result.isConfirmed) {
      showModernSuccessModal("Sucesso!", "Senha alterada com sucesso!");
      $("#userDropdown").removeClass("active");
    }
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
