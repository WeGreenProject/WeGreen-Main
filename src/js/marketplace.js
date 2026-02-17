$(document).ready(function () {
  
  loadProducts();

  
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

let marketplaceIsCliente = false;

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

function loadProducts() {
  
  const categoria = $('input[name="category"]:checked').val();

  
  const tipoVendedor = [];
  $('input[id^="seller-"]:checked').each(function () {
    tipoVendedor.push($(this).val());
  });

  
  const tipoProduto = [];
  $('input[id^="prod-"]:checked').each(function () {
    tipoProduto.push($(this).val());
  });

  
  const marcas = [];
  $('input[id^="brand-"]:checked').each(function () {
    marcas.push($(this).val());
  });

  
  const tamanhos = [];
  $('input[id^="size-"]:checked').each(function () {
    tamanhos.push($(this).val());
  });

  
  const estados = [];
  $('input[id^="cond-"]:checked').each(function () {
    estados.push($(this).val());
  });

  
  const precoMin = $("#priceMin").val();
  const precoMax = $("#priceMax").val();

  
  const pesquisa = $("#searchInput").val();

  
  const ordenacao = $("#sortSelect").val();

  
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
        marketplaceIsCliente = response.isCliente === true;
        displayProducts(response.produtos, response.isCliente);
        updateResultsCount(response.total);
      } else {
        marketplaceIsCliente = false;
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
      marketplaceIsCliente = false;
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

function displayProducts(produtos, isCliente = false) {
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
    let badgeClass = produto.tipo_vendedor === "designer" ? "designer" : "";
    let badgeText = produto.tipo_vendedor === "designer" ? "DESIGNER" : "";

    
    if (Number(produto.plano_id) === 3) {
      badgeClass = "premium";
      badgeText = "\u2B50 PREMIUM";
    }

    const imageUrl = produto.foto
      ? produto.foto
      : "assets/media/products/placeholder.jpg";

    
    const favoritoBtn = isCliente
      ? `<button class="btn-favorite" onclick="event.stopPropagation(); addToFavorites(${produto.id})">
                    <i class="far fa-heart"></i>
                </button>`
      : "";

    html += `
            <div class="product-card" onclick="window.location.href='produto.php?id=${produto.id}'">
                ${favoritoBtn}
                ${badgeText ? `<span class="product-badge ${badgeClass}">${badgeText}</span>` : ""}
                <img src="${imageUrl}" alt="${produto.nome}" class="product-image"
                     onerror="this.src='assets/media/products/placeholder.jpg'">
                <div class="product-info">
                    <div class="product-name">${produto.nome}</div>
                    <div class="product-seller">
                        <i class="bi bi-person me-1"></i>${produto.nome_vendedor}
                    </div>
                    <div class="product-price">€${produto.preco.toFixed(2)}</div>
            <button class="btn-add-cart" onclick="event.stopPropagation(); addToCart(${produto.id})">
              <i class="bi bi-bag-plus-fill me-1"></i>Adicionar ao Carrinho
            </button>
                </div>
            </div>
        `;
  });

  grid.html(html);
}

function addToCart(productId) {
  $.ajax({
    url: "src/controller/controllerCarrinho.php",
    method: "POST",
    data: {
      op: 7,
      produto_id: productId,
    },
    dataType: "json",
    success: function (response) {
      if (!response || response.flag !== true) {
        const msg =
          (response && response.msg) ||
          "Não foi possível adicionar o produto ao carrinho";

        if (typeof showModernErrorModal === "function") {
          showModernErrorModal("Erro", msg);
        } else {
          Swal.fire("Erro", msg, "error");
        }
        return;
      }

      if (typeof showModernSuccessModal === "function") {
        showModernSuccessModal("Sucesso!", "Produto adicionado ao carrinho");
      } else {
        Swal.fire("Sucesso!", "Produto adicionado ao carrinho", "success");
      }
    },
    error: function (jqXHR) {
      let msg = "Não foi possível adicionar o produto ao carrinho";
      if (jqXHR && jqXHR.responseJSON && jqXHR.responseJSON.msg) {
        msg = jqXHR.responseJSON.msg;
      }

      if (typeof showModernErrorModal === "function") {
        showModernErrorModal("Erro", msg);
      } else {
        Swal.fire("Erro", msg, "error");
      }
    },
  });
}

function updateResultsCount(total) {
  const text = total === 1 ? "produto encontrado" : "produtos encontrados";
  $("#resultsCount").text(`${total} ${text}`);
}

function clearAllFilters() {
  
  $("#cat-all").prop("checked", true);

  
  $('input[type="checkbox"]').prop("checked", false);

  
  $("#priceMin").val("");
  $("#priceMax").val("");

  
  $("#searchInput").val("");

  
  loadProducts();

  if (typeof showModernSuccessModal === "function") {
    showModernSuccessModal(
      "Filtros Limpos",
      "Todos os filtros foram removidos",
      {
        timer: 1500,
      },
    );
  } else {
    Swal.fire({
      icon: "success",
      title: "Filtros Limpos",
      text: "Todos os filtros foram removidos",
      timer: 1500,
      showConfirmButton: false,
    });
  }
}

function addToFavorites(productId) {
  if (!marketplaceIsCliente) {
    const modalFn =
      typeof showModernConfirmModal === "function"
        ? showModernConfirmModal(
            "Login Necessário",
            "Apenas clientes autenticados podem adicionar favoritos.",
            {
              confirmText: '<i class="fas fa-sign-in-alt"></i> Ir para Login',
              icon: "fa-user-lock",
              iconBg:
                "background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);",
            },
          )
        : Swal.fire({
            icon: "info",
            title: "Login Necessário",
            text: "Apenas clientes autenticados podem adicionar favoritos.",
            confirmButtonText: "Ir para Login",
            showCancelButton: true,
            cancelButtonText: "Cancelar",
          });

    modalFn.then((result) => {
      if (result.isConfirmed) {
        window.location.href = "login.html";
      }
    });
    return;
  }

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
        if (typeof showModernSuccessModal === "function") {
          showModernSuccessModal(
            "Adicionado aos Favoritos!",
            "Produto guardado com sucesso.",
            {
              timer: 1500,
            },
          );
        } else {
          Swal.fire({
            icon: "success",
            title: "Adicionado aos Favoritos!",
            timer: 1500,
            showConfirmButton: false,
          });
        }
      } else {
        const msg =
          response.message || "Não foi possível adicionar aos favoritos";
        if (typeof showModernErrorModal === "function") {
          showModernErrorModal("Erro", msg);
        } else {
          Swal.fire({
            icon: "error",
            title: "Erro",
            text: msg,
          });
        }
      }
    },
    error: function () {
      const modalFn =
        typeof showModernConfirmModal === "function"
          ? showModernConfirmModal(
              "Login Necessário",
              "Precisas de fazer login para adicionar favoritos.",
              {
                confirmText: '<i class="fas fa-sign-in-alt"></i> Ir para Login',
                icon: "fa-user-lock",
                iconBg:
                  "background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);",
              },
            )
          : Swal.fire({
              icon: "info",
              title: "Login Necessário",
              text: "Precisas de fazer login para adicionar favoritos",
              confirmButtonText: "Ir para Login",
              showCancelButton: true,
            });

      modalFn.then((result) => {
        if (result.isConfirmed) {
          window.location.href = "login.html";
        }
      });
    },
  });
}
