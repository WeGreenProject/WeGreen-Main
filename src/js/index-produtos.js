$(document).ready(function () {
  carregarProdutosDestaque();
  carregarMaisVendidos();
  carregarNovidades();
});

function carregarMaisVendidos() {
  $.ajax({
    url: "src/controller/controllerMarketplace.php",
    method: "POST",
    data: {
      op: 1,
      ordenacao: "popular",
      limite: 4,
    },
    dataType: "json",
    success: function (data) {
      if (data.success && data.produtos) {
        renderizarProdutos(data.produtos, "#ProdutosVendidos");
      }
    },
    error: function (error) {
      console.error("Erro ao carregar produtos mais vendidos:", error);
    },
  });
}

function carregarProdutosDestaque() {
  $.ajax({
    url: "src/controller/controllerMarketplace.php",
    method: "POST",
    data: {
      op: 1,
      ordenacao: "featured",
      limite: 4,
    },
    dataType: "json",
    success: function (data) {
      if (data.success && data.produtos) {
        renderizarProdutos(data.produtos, "#ProdutosDestaques");
      }
    },
    error: function (error) {
      console.error("Erro ao carregar produtos em destaque:", error);
    },
  });
}

function carregarNovidades() {
  $.ajax({
    url: "src/controller/controllerMarketplace.php",
    method: "POST",
    data: {
      op: 1,
      ordenacao: "newest",
      limite: 4,
    },
    dataType: "json",
    success: function (data) {
      if (data.success && data.produtos) {
        renderizarProdutos(data.produtos, "#ProdutosNovidades");
      }
    },
    error: function (error) {
      console.error("Erro ao carregar produtos recomendados:", error);
    },
  });
}

function renderizarProdutos(produtos, containerId) {
  const container = $(containerId);
  container.empty();

  if (produtos.length === 0) {
    container.html(
      '<div class="col-12 text-center"><p class="text-muted">Nenhum produto encontrado</p></div>',
    );
    return;
  }

  produtos.forEach((produto) => {
    const card = `
            <div class="col-md-3 col-sm-6">
                <a href="produto.php?id=${produto.id}" class="text-decoration-none">
                    <div class="produto-card-homepage" style="background: white; border-radius: 12px; overflow: hidden; transition: transform 0.3s ease, box-shadow 0.3s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.08); height: 100%;">
                        <div style="position: relative; padding-top: 85%; overflow: hidden;">
                            <img src="${produto.foto}" alt="${produto.nome}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div style="padding: 14px;">
                            <h5 style="color: #1a1a1a; font-size: 14px; font-weight: 600; margin-bottom: 6px; min-height: 36px; line-height: 1.3;">${produto.nome}</h5>
                            <p style="color: #64748b; font-size: 12px; margin-bottom: 10px;">
                                ${
                                  produto.tipo_vendedor === "designer"
                                    ? '<i class="fas fa-palette"></i> Designer'
                                    : produto.tipo_vendedor === "artesao"
                                      ? '<i class="fas fa-hands"></i> Artesão'
                                      : produto.tipo_vendedor === "particular"
                                        ? '<i class="fas fa-user"></i> Particular'
                                        : '<i class="fas fa-store"></i> Anunciante'
                                }
                            </p>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: #3cb371; font-size: 18px; font-weight: 700;">€${parseFloat(produto.preco).toFixed(2)}</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        `;
    container.append(card);
  });

  $(".produto-card-homepage").hover(
    function () {
      $(this).css({
        transform: "translateY(-8px)",
        "box-shadow": "0 12px 24px rgba(62, 179, 113, 0.2)",
      });
    },
    function () {
      $(this).css({
        transform: "translateY(0)",
        "box-shadow": "0 2px 8px rgba(0,0,0,0.08)",
      });
    },
  );
}

function carregarRecomendados() {
  $.ajax({
    url: "src/controller/controllerMarketplace.php",
    method: "POST",
    data: {
      op: 1,
      ordenacao: "popular",
      limite: 4,
    },
    dataType: "json",
    success: function (data) {
      if (data.success && data.produtos) {
        renderizarProdutos(data.produtos, "#ProdutosNovidades");
      }
    },
    error: function (error) {
      console.error("Erro ao carregar produtos recomendados:", error);
    },
  });
}
