$(document).ready(function () {
  // Carregar produtos ao iniciar
  loadProducts();

  // Event listeners para filtros
  $('input[type="checkbox"], input[type="radio"]').on("change", function () {
    loadProducts();
  });

  $("#sortSelect").on("change", function () {
    loadProducts();
  });

  $("#priceMin, #priceMax").on(
    "input",
    debounce(function () {
      loadProducts();
    }, 500),
  );

  $("#searchInput").on(
    "input",
    debounce(function () {
      loadProducts();
    }, 500),
  );
});

// Função debounce para evitar múltiplas chamadas
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Função para carregar produtos
function loadProducts() {
  // Obter filtros selecionados
  const categoria = $('input[name="category"]:checked').val();

  // Tipo de vendedor
  const tipoVendedor = [];
  $('input[id^="seller-"]:checked').each(function () {
    tipoVendedor.push($(this).val());
  });

  // Tipo de produto
  const tipoProduto = [];
  $('input[id^="prod-"]:checked').each(function () {
    tipoProduto.push($(this).val());
  });

  // Marcas
  const marcas = [];
  $('input[id^="brand-"]:checked').each(function () {
    marcas.push($(this).val());
  });

  // Tamanhos
  const tamanhos = [];
  $('input[id^="size-"]:checked').each(function () {
    tamanhos.push($(this).val());
  });

  // Estados
  const estados = [];
  $('input[id^="cond-"]:checked').each(function () {
    estados.push($(this).val());
  });

  // Preço
  const precoMin = $("#priceMin").val();
  const precoMax = $("#priceMax").val();

  // Pesquisa
  const pesquisa = $("#searchInput").val();

  // Ordenação
  const ordenacao = $("#sortSelect").val();

  // Fazer requisição AJAX
  $.ajax({
    url: "src/controller/controllerMarketplace.php",
    method: "POST",
    data: {
      op: 1,
      categoria: categoria,
      tipoVendedor: JSON.stringify(tipoVendedor),
      tipoProduto: JSON.stringify(tipoProduto),
      marca: JSON.stringify(marcas),
      precoMin: precoMin,
      precoMax: precoMax,
      tamanho: JSON.stringify(tamanhos),
      estado: JSON.stringify(estados),
      pesquisa: pesquisa,
      ordenacao: ordenacao,
    },
    dataType: "json",
    success: function (response) {
      console.log("Resposta recebida:", response);
      if (response.success) {
        displayProducts(response.produtos);
        updateResultsCount(response.total);
      } else {
        console.error("Erro no response:", response.error || response);
        const errorMsg = response.error || "Erro ao carregar produtos";
        $("#productsGrid").html(`
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                        <h4 class="mt-3">Erro ao Carregar</h4>
                        <p class="text-muted">${errorMsg}</p>
                    </div>
                `);
        $("#resultsCount").text("Erro ao carregar produtos");
      }
    },
    error: function (xhr, status, error) {
      console.error("Erro AJAX:", { xhr, status, error });
      console.error("Response Text:", xhr.responseText);
      $("#productsGrid").html(`
                <div class="col-12 text-center py-5">
                    <i class="bi bi-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">Erro de Conexão</h4>
                    <p class="text-muted">Não foi possível conectar ao servidor</p>
                    <small class="text-muted d-block mt-2">Detalhes: ${error}</small>
                </div>
            `);
      $("#resultsCount").text("Erro de conexão");
    },
  });
}

// Função para exibir produtos
function displayProducts(produtos) {
  const grid = $("#productsGrid");

  if (produtos.length === 0) {
    grid.html(`
            <div class="col-12 text-center py-5">
                <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                <h4 class="mt-3">Nenhum produto encontrado</h4>
                <p class="text-muted">Tenta ajustar os filtros para ver mais resultados</p>
            </div>
        `);
    return;
  }

  let html = "";
  produtos.forEach((produto) => {
    const badgeClass = produto.tipo_vendedor === "designer" ? "designer" : "";
    const badgeText = produto.tipo_vendedor === "designer" ? "DESIGNER" : "";

    const imageUrl = produto.foto
      ? produto.foto
      : "assets/media/products/placeholder.jpg";

    html += `
            <div class="product-card" onclick="window.location.href='produto.php?id=${produto.id}'">
                <button class="btn-favorite" onclick="event.stopPropagation(); addToFavorites(${produto.id})">
                    <i class="far fa-heart"></i>
                </button>
                ${badgeText ? `<span class="product-badge ${badgeClass}">${badgeText}</span>` : ""}
                <img src="${imageUrl}" alt="${produto.nome}" class="product-image"
                     onerror="this.src='assets/media/products/placeholder.jpg'">
                <div class="product-info">
                    <div class="product-name">${produto.nome}</div>
                    <div class="product-seller">
                        <i class="bi bi-person me-1"></i>${produto.nome_vendedor}
                    </div>
                    <div class="product-price">€${produto.preco.toFixed(2)}</div>
                </div>
            </div>
        `;
  });

  grid.html(html);
}

// Função para atualizar contador de resultados
function updateResultsCount(total) {
  const text = total === 1 ? "produto encontrado" : "produtos encontrados";
  $("#resultsCount").text(`${total} ${text}`);
}

// Função para limpar todos os filtros
function clearAllFilters() {
  // Limpar categorias
  $("#cat-all").prop("checked", true);

  // Limpar checkboxes
  $('input[type="checkbox"]').prop("checked", false);

  // Limpar preços
  $("#priceMin").val("");
  $("#priceMax").val("");

  // Limpar pesquisa
  $("#searchInput").val("");

  // Recarregar produtos
  loadProducts();

  Swal.fire({
    icon: "success",
    title: "Filtros Limpos",
    text: "Todos os filtros foram removidos",
    timer: 1500,
    showConfirmButton: false,
  });
}

// Função para adicionar aos favoritos
function addToFavorites(productId) {
  $.ajax({
    url: "src/controller/controllerFavoritos.php",
    method: "POST",
    data: {
      op: 1,
      produto_id: productId,
    },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        Swal.fire({
          icon: "success",
          title: "Adicionado aos Favoritos!",
          timer: 1500,
          showConfirmButton: false,
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Erro",
          text: response.message || "Não foi possível adicionar aos favoritos",
        });
      }
    },
    error: function () {
      Swal.fire({
        icon: "info",
        title: "Login Necessário",
        text: "Precisas de fazer login para adicionar favoritos",
        confirmButtonText: "Ir para Login",
        showCancelButton: true,
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = "login.html";
        }
      });
    },
  });
}
