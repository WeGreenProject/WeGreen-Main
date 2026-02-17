
function mostrarModalSucessoRemocao(texto) {
  showModernSuccessModal("Removido!", texto, { timer: 2000 });
}

function mostrarModalAviso(titulo, texto) {
  showModernWarningModal(titulo, texto);
}

function removerDoCarrinho(produto_id) {
  showModernConfirmModal(
    "Remover Produto?",
    "Pretende remover este produto do carrinho?",
    {
      confirmText: '<i class="fas fa-trash-alt"></i> Sim, remover',
      icon: "fa-trash-alt",
      iconBg:
        "background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%); box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);",
    },
  ).then((result) => {
    if (result.isConfirmed) {
      let dados = new FormData();
      dados.append("op", 4);
      dados.append("produto_id", produto_id);

      $.ajax({
        url: "src/controller/controllerCarrinho.php",
        method: "POST",
        data: dados,
        dataType: "json",
        cache: false,
        contentType: false,
        processData: false,
      })
        .done(function (msg) {
          mostrarModalSucessoRemocao("O produto foi removido do carrinho.");
          loadCarrinho(); 
        })
        .fail(function (jqXHR, textStatus) {
          showModernErrorModal("Erro", "Não foi possível remover o produto.");
        });
    }
  });
}

function limparCarrinho() {
  showModernConfirmModal(
    "Limpar carrinho?",
    "Todos os produtos serão removidos!",
    {
      confirmText: '<i class="fas fa-trash-alt"></i> Sim, limpar tudo!',
      icon: "fa-trash-alt",
      iconBg: "background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);",
    },
  ).then((result) => {
    if (result.isConfirmed) {
      let dados = new FormData();
      dados.append("op", 5);

      $.ajax({
        url: "src/controller/controllerCarrinho.php",
        method: "POST",
        data: dados,
        dataType: "json",
        cache: false,
        contentType: false,
        processData: false,
      })
        .done(function (msg) {
          showModernSuccessModal("Limpo!", "O carrinho foi limpo com sucesso.");
          loadCarrinho(); 
        })
        .fail(function (jqXHR, textStatus) {
          showModernErrorModal("Erro", "Não foi possível limpar o carrinho.");
        });
    }
  });
}

function aplicarCupao() {
  let codigo = $("#couponCode").val();

  if (codigo.trim() === "") {
    showModernErrorModal("Oops...", "Por favor, insira um código de cupão!");
    return;
  }

  let dados = new FormData();
  dados.append("op", 6);
  dados.append("codigo", codigo);

  $.ajax({
    url: "src/controller/controllerCarrinho.php",
    method: "POST",
    data: dados,
    dataType: "json",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (resp) {
      if (resp.flag) {
        showModernSuccessModal(
          "Cupão aplicado!",
          "Desconto de 10% aplicado ao seu carrinho",
          { timer: 2000 },
        );
        getResumoPedido();
      } else {
        showModernErrorModal(
          "Cupão inválido",
          resp.msg || "O código inserido não é válido ou já expirou.",
        );
      }
    })
    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}

function removerCupao() {
  showModernConfirmModal(
    "Remover cupão?",
    "O desconto será removido do carrinho.",
    {
      confirmText: '<i class="fas fa-tag"></i> Sim, remover',
      icon: "fa-tag",
      iconBg: "background: linear-gradient(135deg, #ffd700 0%, #f5a623 100%);",
    },
  ).then((result) => {
    if (result.isConfirmed) {
      let dados = new FormData();
      dados.append("op", 8);

      $.ajax({
        url: "src/controller/controllerCarrinho.php",
        method: "POST",
        data: dados,
        dataType: "json",
        cache: false,
        contentType: false,
        processData: false,
      })
        .done(function (msg) {
          showModernSuccessModal("Removido!", "Cupão removido com sucesso.", {
            timer: 1500,
          });
          getResumoPedido();
        })
        .fail(function (jqXHR, textStatus) {
          alert("Request failed: " + textStatus);
        });
    }
  });
}

function irParaCheckout() {
  let dados = new FormData();
  dados.append("op", 9);

  $.ajax({
    url: "src/controller/controllerCarrinho.php",
    method: "POST",
    data: dados,
    dataType: "json",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (response) {
      if (response.tem_produtos == 1) {
        window.location.href = "checkout_stripe.php";
      } else {
        showModernWarningModal(
          "Carrinho Vazio",
          "Adicione produtos ao carrinho antes de finalizar a compra!",
        );
      }
    })
    .fail(function (jqXHR, textStatus) {
      showModernErrorModal("Erro", "Ocorreu um erro. Tente novamente.");
    });
}

let currentStep = 1;
const totalSteps = 4;
let isUserLoggedIn = false; 

function getDadosTipoPerfil() {
  let dados = new FormData();
  dados.append("op", 1);

  $.ajax({
    url: "src/controller/controllerPerfil.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      $("#PerfilTipo").html(msg);
      
      if (msg.includes("Olá")) {
        isUserLoggedIn = true;
      } else {
        isUserLoggedIn = false;
      }
    })
    .fail(function (jqXHR, textStatus) {});
}

function PerfilDoUtilizador() {
  let dados = new FormData();
  dados.append("op", 10);

  $.ajax({
    url: "src/controller/controllerPerfil.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      $("#FotoPerfil").attr("src", msg);
    })
    .fail(function (jqXHR, textStatus) {});
}

function loadUserName() {
  PerfilDoUtilizador();
  getDadosTipoPerfil();
}

function normalizarRespostaCarrinho(response) {
  const produtosRaw = Array.isArray(response?.items)
    ? response.items
    : Array.isArray(response?.produtos)
      ? response.produtos
      : [];

  const produtos = produtosRaw.map((item) => {
    const produtoId =
      item.id !== undefined
        ? item.id
        : item.Produto_id !== undefined
          ? item.Produto_id
          : item.produto_id;

    return {
      ...item,
      id: parseInt(produtoId, 10),
      produto_id: parseInt(produtoId, 10),
      stock: item.stock !== undefined ? parseInt(item.stock, 10) : null,
    };
  });

  return {
    flag: !!response?.flag,
    items: produtos,
    total: parseFloat(response?.total || 0),
  };
}

function loadCarrinho() {
  $.ajax({
    url: "src/controller/controllerCarrinho.php",
    method: "POST",
    data: { op: 10 },
    dataType: "json",
  })
    .done(function (response) {
      const carrinho = normalizarRespostaCarrinho(response);

      if (carrinho.items.length > 0) {
        displayCarrinho(carrinho.items, carrinho.total);
      } else {
        $("#cartItemsStep1").html(
          '<p class="text-center text-muted py-4">O carrinho está vazio.</p>',
        );
      }
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
      showModernErrorModal(
        "Erro ao Carregar Carrinho",
        "Não foi possível carregar os produtos. Tente novamente.",
      );
    });
}

function updateQuantidade(produtoId, mudanca) {
  $.ajax({
    url: "src/controller/controllerCarrinho.php",
    method: "POST",
    data: {
      op: 3,
      produto_id: produtoId,
      mudanca: mudanca,
    },
    dataType: "json",
  })
    .done(function (response) {
      if (response.flag) {
        
        const newQty = parseInt($("#qty-" + produtoId).val()) + mudanca;
        if (newQty > 0) {
          $("#qty-" + produtoId).val(newQty);
          
          loadCarrinho();
          updateOrderSummary();
        }
      } else {
        showModernErrorModal(
          "Erro",
          response.msg || "Não foi possível atualizar a quantidade.",
        );
      }
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
      showModernErrorModal("Erro", "Erro ao atualizar quantidade.");
    });
}

function setQuantidade(produtoId, novaQuantidade) {
  const qty = parseInt(novaQuantidade);
  if (isNaN(qty) || qty < 1) {
    $("#qty-" + produtoId).val(1);
    return;
  }

  const currentQty = parseInt(
    $("#qty-" + produtoId).data("original-qty") || $("#qty-" + produtoId).val(),
  );
  const mudanca = qty - currentQty;

  if (mudanca !== 0) {
    
    updateQuantidadeDireta(produtoId, qty);
  }
}

function updateQuantidadeDireta(produtoId, targetQty) {
  const currentQty = parseInt($("#qty-" + produtoId).val());
  const mudanca = targetQty > currentQty ? 1 : -1;
  const steps = Math.abs(targetQty - currentQty);

  let completed = 0;

  function updateStep() {
    if (completed >= steps) {
      loadCarrinho();
      updateOrderSummary();
      return;
    }

    $.ajax({
      url: "src/controller/controllerCarrinho.php",
      method: "POST",
      data: {
        op: 3,
        produto_id: produtoId,
        mudanca: mudanca,
      },
      dataType: "json",
    }).done(function (response) {
      if (response.flag) {
        completed++;
        updateStep();
      }
    });
  }

  updateStep();
}

function removeFromCart(produtoId) {
  showModernConfirmModal(
    "Remover Produto?",
    "Tem certeza que deseja remover este produto do carrinho?",
    {
      confirmText: '<i class="fas fa-trash-alt"></i> Sim, remover',
      icon: "fa-trash-alt",
      iconBg:
        "background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%); box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);",
    },
  ).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "src/controller/controllerCarrinho.php",
        method: "POST",
        data: {
          op: 4,
          produto_id: produtoId,
        },
        dataType: "json",
      })
        .done(function (response) {
          if (response.flag) {
            
            $("#cart-item-" + produtoId).fadeOut(300, function () {
              $(this).remove();
              loadCarrinho();
              updateOrderSummary();
            });

            mostrarModalSucessoRemocao("Produto removido do carrinho.");
          } else {
            showModernErrorModal(
              "Erro",
              response.msg || "Não foi possível remover o produto.",
            );
          }
        })
        .fail(function () {
          showModernErrorModal(
            "Erro",
            "Erro ao remover produto. Tente novamente.",
          );
        });
    }
  });
}

function loadResumoPedido() {
  $.ajax({
    url: "src/controller/controllerCarrinho.php",
    method: "POST",
    data: { op: 2 },
    dataType: "html",
  }).done(function (html) {
    $("#resumoPedido").html(html);
  });
}

function displayCarrinho(items, total) {
  let html = "";
  if (items && items.length > 0) {
    items.forEach((item) => {
      const stockLimitado = Number.isFinite(item.stock) && item.stock > 0;
      const reachedStock = stockLimitado && item.quantidade >= item.stock;
      const minQuantity = item.quantidade <= 1;
      html += `
                <div class="cart-item d-flex align-items-center mb-3 p-3 bg-light rounded" id="cart-item-${
                  item.id
                }">
                    <img src="${item.foto}" alt="${
                      item.nome
                    }" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;" />
                    <div class="ms-3 flex-grow-1">
                        <h6 class="mb-1">${item.nome}</h6>
                        <p class="text-muted mb-1">Preço unitário: ${parseFloat(
                          item.preco,
                        )
                          .toFixed(2)
                          .replace(".", ",")} €</p>
                        ${
                          stockLimitado
                            ? `<small class="text-muted d-block">Stock disponível: ${item.stock}</small>`
                            : ""
                        }
                        <div class="quantity-controls d-flex align-items-center gap-2 mt-2">
                            <button class="btn btn-sm btn-outline-secondary"
                                    onclick="updateQuantidade(${item.id}, -1)"
                                    title="Diminuir quantidade"
                                    ${minQuantity ? "disabled" : ""}>
                                <i class="bi bi-dash"></i>
                            </button>
                            <input type="number" class="form-control form-control-sm text-center"
                                   id="qty-${item.id}"
                                   value="${item.quantidade}"
                                   min="1"
                                   max="${stockLimitado ? item.stock : 99}"
                                   style="width: 60px;"
                                   onchange="setQuantidade(${
                                     item.id
                                   }, this.value)" readonly>
                            <button class="btn btn-sm btn-outline-secondary"
                                    onclick="updateQuantidade(${item.id}, 1)"
                                    title="Aumentar quantidade"
                                    ${reachedStock ? "disabled" : ""}>
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="text-end d-flex flex-column align-items-end gap-2">
                        <strong class="item-total" id="total-${item.id}">${(
                          item.preco * item.quantidade
                        )
                          .toFixed(2)
                          .replace(".", ",")} €</strong>
                        <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${
                          item.id
                        })" title="Remover produto">
                            <i class="bi bi-trash"></i> Remover
                        </button>
                    </div>
                </div>
            `;
    });
    html += `
            <div class="cart-total mt-3 p-3 bg-success text-white rounded">
                <h5 class="mb-0">Total: <span id="cart-grand-total">${parseFloat(
                  total,
                )
                  .toFixed(2)
                  .replace(".", ",")} €</span></h5>
            </div>
        `;
  } else {
    html = '<p class="text-center text-muted py-4">O carrinho está vazio.</p>';
  }
  $("#cartItemsStep1").html(html);
}

function showStep(step) {
  currentStep = step;

  
  for (let i = 1; i <= totalSteps; i++) {
    $(`#step-${i}`).removeClass("active-step completed-step upcoming-step");

    if (i < step) {
      $(`#step-${i}`).addClass("completed-step");
    } else if (i === step) {
      $(`#step-${i}`).addClass("active-step");
    } else {
      $(`#step-${i}`).addClass("upcoming-step");
    }
  }

  
  $(".step-progress-item").each(function (index) {
    const stepNum = index + 1;
    $(this).removeClass("active completed");

    if (stepNum < step) {
      $(this).addClass("completed");
    } else if (stepNum === step) {
      $(this).addClass("active");
    }
  });

  
  
  
  
  
  
}

function nextStep() {
  if (currentStep < totalSteps) {
    if (validateStep(currentStep)) {
      showStep(currentStep + 1, true);
    }
  }
}

function previousStep() {
  if (currentStep > 1) {
    showStep(currentStep - 1, true);
  }
}

function validateStep(step) {
  if (step === 1) {
    const hasItems = $("#cartItemsStep1 .cart-item").length > 0;
    if (!hasItems) {
      mostrarModalAviso(
        "Carrinho Vazio",
        "Adicione produtos ao carrinho antes de continuar.",
      );
      return false;
    }
    return true;
  }

  if (step === 2) {
    const firstName = $("#firstName").val().trim();
    const lastName = $("#lastName").val().trim();
    const address1 = $("#address1").val().trim();
    const city = $("#city").val().trim();
    const zipCode = $("#zipCode").val().trim();
    const state = $("#state").val();

    
    if (!firstName) {
      $("#firstName").focus();
      mostrarModalAviso(
        "Nome Obrigatório",
        "Por favor, preencha o seu primeiro nome.",
      );
      return false;
    }

    if (!lastName) {
      $("#lastName").focus();
      mostrarModalAviso(
        "Apelido Obrigatório",
        "Por favor, preencha o seu apelido.",
      );
      return false;
    }

    if (!address1) {
      $("#address1").focus();
      mostrarModalAviso(
        "Morada Obrigatória",
        "Por favor, preencha a sua morada.",
      );
      return false;
    }

    if (!city) {
      $("#city").focus();
      mostrarModalAviso(
        "Cidade Obrigatória",
        "Por favor, preencha a sua cidade.",
      );
      return false;
    }

    if (!zipCode) {
      $("#zipCode").focus();
      mostrarModalAviso(
        "Código Postal Obrigatório",
        "Por favor, preencha o código postal no formato XXXX-XXX.",
      );
      return false;
    }

    
    const zipCodePattern = /^[0-9]{4}-[0-9]{3}$/;
    if (!zipCodePattern.test(zipCode)) {
      $("#zipCode").focus();
      mostrarModalAviso(
        "Código Postal Inválido",
        "Use o formato português: XXXX-XXX (ex: 1000-001)",
      );
      return false;
    }

    if (!state) {
      $("#state").focus();
      mostrarModalAviso(
        "Localidade Obrigatória",
        "Por favor, selecione a localidade/distrito.",
      );
      return false;
    }

    saveShippingInfo();
    return true;
  }

  if (step === 3) {
    const selectedTransportadora = localStorage.getItem("transportadora_id");
    if (!selectedTransportadora) {
      mostrarModalAviso(
        "Transportadora Não Selecionada",
        "Selecione uma transportadora antes de continuar.",
      );
      return false;
    }

    
    if (selectedTransportadora === "2" || selectedTransportadora === "4") {
      const pickupPointId = localStorage.getItem("pickup_point_id");
      if (!pickupPointId) {
        mostrarModalAviso(
          "Ponto de Recolha Não Selecionado",
          "Selecione um ponto de recolha no mapa.",
        );
        return false;
      }
    }

    
    if (!isUserLoggedIn) {
      showModernConfirmModal(
        "Login Necessário para Pagamento",
        "Para finalizar a compra, precisa estar logado.",
        {
          confirmText: '<i class="bi bi-box-arrow-in-right"></i> Fazer Login',
          icon: "fa-sign-in-alt",
          iconBg:
            "background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); box-shadow: 0 8px 20px rgba(23, 162, 184, 0.3);",
        },
      ).then((result) => {
        if (result.isConfirmed) {
          window.location.href = "login.html?redirect=Carrinho.html";
        }
      });
      return false;
    }

    return true;
  }

  return true;
}

function saveShippingInfo() {
  const shippingData = {
    firstName: $("#firstName").val().trim(),
    lastName: $("#lastName").val().trim(),
    address1: $("#address1").val().trim(),
    address2: $("#address2").val().trim(),
    city: $("#city").val().trim(),
    zipCode: $("#zipCode").val().trim(),
    state: $("#state").val(),
  };

  localStorage.setItem("shippingInfo", JSON.stringify(shippingData));
  updateOrderSummary(); 
}

function selectTransportadora(id, price) {
  $(".transportadora-card")
    .removeClass("selected")
    .attr("aria-checked", "false");
  $(`#transportadora${id}`).addClass("selected").attr("aria-checked", "true");

  localStorage.setItem("transportadora_id", id.toString());
  localStorage.setItem("transportadora_price", price.toString());

  updateOrderSummary(); 

  
  if (id === 2 || id === 4) {
    
    localStorage.removeItem("pickup_point_id");
    localStorage.removeItem("pickup_point_name");
    localStorage.removeItem("pickup_point_address");

    
    $("button[onclick*='nextStep']")
      .prop("disabled", true)
      .addClass("disabled-btn");

    const transportadoraType = id === 2 ? "ctt_pickup" : "dpd_pickup";
    showPickupMap(transportadoraType);

    
    setTimeout(() => {
      const mapContainer = document.getElementById("pickupMapContainer");
      if (mapContainer) {
        mapContainer.scrollIntoView({ behavior: "smooth", block: "start" });
      }
    }, 300);
  } else {
    hidePickupMap();
    
    $("button[onclick*='nextStep']")
      .prop("disabled", false)
      .removeClass("disabled-btn");
  }
}

function displayShippingReview() {
  const shippingInfo = JSON.parse(localStorage.getItem("shippingInfo") || "{}");

  const address2Line = shippingInfo.address2
    ? `<p><strong>Morada (Linha 2):</strong> ${shippingInfo.address2}</p>`
    : "";

  const html = `
        <div class="shipping-details">
            <p><strong>Nome:</strong> ${shippingInfo.firstName || "N/A"} ${
              shippingInfo.lastName || "N/A"
            }</p>
            <p><strong>Morada:</strong> ${shippingInfo.address1 || "N/A"}</p>
            ${address2Line}
            <p><strong>Cidade:</strong> ${shippingInfo.city || "N/A"}</p>
            <p><strong>Código Postal:</strong> ${
              shippingInfo.zipCode || "N/A"
            }</p>
            <p><strong>Localidade/Distrito:</strong> ${
              shippingInfo.state || "N/A"
            }</p>
        </div>
    `;

  $("#shippingReview").html(html);
}

function displayTransportadoraReview() {
  const transportadoraId = localStorage.getItem("transportadora_id");
  const transportadoraPrice = localStorage.getItem("transportadora_price");
  const pickupPointId = localStorage.getItem("pickup_point_id");
  const pickupPointName = localStorage.getItem("pickup_point_name");
  const pickupPointAddress = localStorage.getItem("pickup_point_address");

  const transportadoraNomes = {
    1: "CTT - Correios de Portugal (2-4 dias)",
    2: "CTT - Ponto de Recolha",
    3: "DPD - Entrega Rápida (1-2 dias)",
    4: "DPD - Ponto de Recolha",
    5: "Entrega em Casa (1-3 dias)",
  };

  let pickupInfo = "";
  if ((transportadoraId === "2" || transportadoraId === "4") && pickupPointId) {
    pickupInfo = `
      <div class="mt-3 p-3 bg-light rounded">
        <p class="mb-2"><strong><i class="bi bi-geo-alt-fill text-primary"></i> Ponto de Recolha:</strong></p>
        <p class="mb-1 ms-3"><strong>Nome:</strong> ${
          pickupPointName || "N/A"
        }</p>
        <p class="mb-0 ms-3"><strong>Morada:</strong> ${
          pickupPointAddress || "N/A"
        }</p>
      </div>
    `;
  }

  const html = `
        <div class="shipping-details">
            <p><strong>Transportadora:</strong> ${
              transportadoraNomes[transportadoraId] || "N/A"
            }</p>
            <p><strong>Custo de Envio:</strong> ${parseFloat(
              transportadoraPrice || 0,
            ).toFixed(2)}€</p>
            ${pickupInfo}
        </div>
    `;

  $("#transportadoraReview").html(html);
}

function displayCartReview() {
  $.ajax({
    url: "src/controller/controllerCarrinho.php",
    method: "POST",
    data: { op: 10 },
    dataType: "json",
  }).done(function (response) {
    const carrinho = normalizarRespostaCarrinho(response);

    if (carrinho.flag && carrinho.items.length) {
      let html = "";
      carrinho.items.forEach((item) => {
        html += `
                    <div class="cart-item d-flex align-items-center mb-3 p-3 bg-light rounded">
                        <img src="${item.foto}" alt="${
                          item.nome
                        }" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;" />
                        <div class="ms-3 flex-grow-1">
                            <h6 class="mb-1">${item.nome}</h6>
                            <p class="text-muted mb-0">Qtd: ${
                              item.quantidade
                            }</p>
                        </div>
                        <div class="text-end">
                            <strong>${(item.preco * item.quantidade).toFixed(
                              2,
                            )}€</strong>
                        </div>
                    </div>
                `;
      });

      const transportadoraPrice = parseFloat(
        localStorage.getItem("transportadora_price") || 0,
      );
      const subtotal = carrinho.total;
      const total = subtotal + transportadoraPrice;

      html += `
                <div class="mt-3 p-3 bg-light rounded">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong>${subtotal.toFixed(2)}€</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Envio:</span>
                        <strong>${transportadoraPrice.toFixed(2)}€</strong>
                    </div>
                    <hr />
                    <div class="d-flex justify-content-between">
                        <span class="h5 mb-0">Total:</span>
                        <strong class="h5 mb-0 text-success">${total.toFixed(
                          2,
                        )}€</strong>
                    </div>
                </div>
            `;

      $("#cartReview").html(html);
    }
  });
}

function finalizarPedido() {
  const shippingInfo = JSON.parse(localStorage.getItem("shippingInfo") || "{}");
  const transportadoraId = localStorage.getItem("transportadora_id");
  const pickupPointId = localStorage.getItem("pickup_point_id");
  const pickupPointName = localStorage.getItem("pickup_point_name");
  const pickupPointAddress = localStorage.getItem("pickup_point_address");

  if (!transportadoraId) {
    showModernErrorModal("Erro", "Selecione uma transportadora.");
    return;
  }

  
  Swal.fire({
    html: `
      <div style="text-align: center; padding: 6px 0;">
        <div style="width: 76px; height: 76px; margin: 0 auto 18px; border-radius: 50%; background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(60, 179, 113, 0.25);">
          <i class="fas fa-lock" style="font-size: 34px; color: #2d8a5a;"></i>
        </div>
        <h2 style="margin: 0 0 8px 0; color: #1f2937; font-size: 24px; font-weight: 700;">A processar pagamento</h2>
        <p style="margin: 0; color: #64748b; font-size: 15px;">A encaminhar para o Stripe em segurança...</p>
      </div>
    `,
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false,
    customClass: {
      popup: "swal2-border-radius",
    },
    didOpen: () => {
      const popup = Swal.getPopup();
      if (popup) {
        popup.style.borderRadius = "14px";
        popup.style.padding = "24px";
      }
      Swal.showLoading();

      if (!document.getElementById("wegreen-swal-loading-style")) {
        const style = document.createElement("style");
        style.id = "wegreen-swal-loading-style";
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

  const form = document.createElement("form");
  form.method = "POST";
  form.action = "checkout_stripe.php";

  const fields = {
    ...shippingInfo,
    transportadora_id: transportadoraId,
  };

  
  if ((transportadoraId === "2" || transportadoraId === "4") && pickupPointId) {
    fields.pickup_point_id = pickupPointId;
    fields.pickup_point_name = pickupPointName;
    fields.pickup_point_address = pickupPointAddress;
  }

  for (let key in fields) {
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = key;
    input.value = fields[key];
    form.appendChild(input);
  }

  document.body.appendChild(form);
  form.submit();
}

function logout() {
  showModernConfirmModal(
    "Terminar Sessão?",
    "Tem a certeza que pretende sair da sua conta?",
    {
      confirmText: '<i class="fas fa-sign-out-alt"></i> Sim, sair',
      icon: "fa-sign-out-alt",
      iconBg:
        "background: linear-gradient(135deg, #dc3545 0%, #c92a2a 100%); box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);",
    },
  ).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        html: `
          <div style="padding: 20px;">
            <div class="swal2-loading-spinner" style="margin: 0 auto 20px;">
              <div style="width: 50px; height: 50px; border: 4px solid #f3f4f6; border-top: 4px solid #3cb371; border-radius: 50%; animation: spin 1s linear infinite;"></div>
            </div>
            <p style="margin: 0; color: #64748b; font-size: 15px;">A terminar sessao...</p>
          </div>
          <style>
            @keyframes spin {
              0% { transform: rotate(0deg); }
              100% { transform: rotate(360deg); }
            }
          </style>
        `,
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
          const popup = Swal.getPopup();
          popup.style.borderRadius = "16px";
        },
      });

      sessionStorage.clear();
      localStorage.clear();
      $.ajax({
        url: "src/controller/controllerPerfil.php?op=2",
        method: "GET",
      }).always(function () {
        window.location.href = "index.html";
      });
    }
  });
}

function updateOrderSummary() {
  const shippingInfo = JSON.parse(localStorage.getItem("shippingInfo") || "{}");
  const transportadoraId = localStorage.getItem("transportadora_id");
  const transportadoraPrice = parseFloat(
    localStorage.getItem("transportadora_price") || 0,
  );
  const pickupPointName = localStorage.getItem("pickup_point_name");

  const transportadoraNomes = {
    1: "CTT (2-4 dias)",
    2: "CTT Pickup",
    3: "DPD (1-2 dias)",
    4: "DPD Pickup",
    5: "Entrega em Casa (1-3 dias)",
  };

  let html = `
    <div class="summary-card">
      <h3><i class="bi bi-receipt"></i> Resumo do Pedido</h3>
  `;

  
  $.ajax({
    url: "src/controller/controllerCarrinho.php",
    method: "POST",
    data: { op: 10 },
    dataType: "json",
    async: false, 
  }).done(function (response) {
    const carrinho = normalizarRespostaCarrinho(response);

    if (carrinho.items.length > 0) {
      html += `<div class="mb-3"><strong>Produtos (${carrinho.items.length})</strong></div>`;

      carrinho.items.forEach((item) => {
        html += `
          <div class="summary-row">
            <div>
              <small>${item.nome}</small><br>
              <small class="text-muted">Qtd: ${item.quantidade}</small>
            </div>
            <strong>${(item.preco * item.quantidade)
              .toFixed(2)
              .replace(".", ",")} €</strong>
          </div>
        `;
      });

      html += `
        <div class="summary-row">
          <span>Subtotal</span>
          <strong>${parseFloat(carrinho.total)
            .toFixed(2)
            .replace(".", ",")} €</strong>
        </div>
      `;

      
      if (shippingInfo.firstName) {
        html += `
          <div class="mt-3 pt-3 border-top">
            <strong class="d-block mb-2"><i class="bi bi-geo-alt"></i> Entrega</strong>
            <small class="text-muted">
              ${shippingInfo.firstName} ${shippingInfo.lastName}<br>
              ${shippingInfo.address1}<br>
              ${shippingInfo.zipCode} ${shippingInfo.city}
            </small>
          </div>
        `;
      }

      
      if (transportadoraId) {
        html += `
          <div class="mt-3 pt-3 border-top">
            <strong class="d-block mb-2"><i class="bi bi-truck"></i> Envio</strong>
            <small class="text-muted">${
              transportadoraNomes[transportadoraId] || "N/A"
            }</small>
            ${
              pickupPointName
                ? `<br><small class="text-muted">ðŸ“ ${pickupPointName}</small>`
                : ""
            }
          </div>
          <div class="summary-row">
            <span>Custo de Envio</span>
            <strong>${transportadoraPrice
              .toFixed(2)
              .replace(".", ",")} €</strong>
          </div>
        `;

        
        const total = parseFloat(response.total) + transportadoraPrice;
        html += `
          <div class="summary-row mt-3 pt-3 border-top">
            <span class="h5 mb-0">Total</span>
            <strong class="h5 mb-0 text-success">${total
              .toFixed(2)
              .replace(".", ",")} €</strong>
          </div>
        `;
      }
    } else {
      html += `<p class="text-muted text-center py-3">Carrinho vazio</p>`;
    }
  });

  html += `</div>`;
  $("#ResumoPedido").html(html);
}

$(function () {
  
  if (typeof bootstrap !== "undefined") {
    var dropdownElementList = [].slice.call(
      document.querySelectorAll('[data-bs-toggle="dropdown"]'),
    );
    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
      return new bootstrap.Dropdown(dropdownToggleEl);
    });
  }

  
  showStep(1);
  loadUserName();
  loadCarrinho();
  updateOrderSummary(); 

  
  $("#shippingForm").on("submit", function (e) {
    e.preventDefault();
    if (validateStep(2)) {
      nextStep();
    }
  });

  
  $(".transportadora-card").on("keypress", function (e) {
    if (e.which === 13 || e.which === 32) {
      
      e.preventDefault();
      $(this).click();
    }
  });
});

let pickupMap = null;
let pickupMarkers = [];
let userLocation = null;

function getCityFromPickupPoint(point) {
  if (point.city) return point.city;

  
  const cityMap = {
    Lisboa: [
      "Lisboa Centro",
      "Benfica",
      "Saldanha",
      "Alameda",
      "Amoreiras",
      "Colombo",
    ],
    Cascais: ["Cascais"],
    Amadora: ["Amadora"],
    Sintra: ["Sintra"],
    Oeiras: ["Oeiras"],
    Almada: ["Almada"],
    "Torres Vedras": ["Torres Vedras"],
    Porto: ["Porto", "Norte Shopping"],
    "Vila Nova de Gaia": ["Gaia"],
    Matosinhos: ["Matosinhos"],
    Maia: ["Maia"],
    "Póvoa de Varzim": ["Póvoa"],
    Espinho: ["Espinho"],
    Barcelos: ["Barcelos"],
    Braga: ["Braga"],
    Guimarães: ["Guimarães"],
    "Viana do Castelo": ["Viana"],
    "Vila Real": ["Vila Real"],
    Bragança: ["Bragança"],
    Coimbra: ["Coimbra"],
    "Figueira da Foz": ["Figueira"],
    Aveiro: ["Aveiro"],
    Viseu: ["Viseu"],
    Guarda: ["Guarda"],
    "Castelo Branco": ["Castelo Branco"],
    Leiria: ["Leiria"],
    "Caldas da Rainha": ["Caldas"],
    Santarém: ["Santarém"],
    Setúbal: ["Setúbal"],
    Portalegre: ["Portalegre"],
    Évora: ["Évora"],
    Beja: ["Beja"],
    Faro: ["Faro", "Forum Algarve"],
    Albufeira: ["Albufeira"],
    Portimão: ["Portimão"],
    Lagos: ["Lagos"],
    Loulé: ["Loulé"],
    Olhão: ["Olhão"],
    Tavira: ["Tavira"],
    "Vila Real de Santo António": ["Vila Real de Santo António"],
    Funchal: ["Funchal", "Madeira"],
    "Ponta Delgada": ["Ponta Delgada", "Açores"],
  };

  const searchText = `${point.name} ${point.address}`;

  for (const [city, keywords] of Object.entries(cityMap)) {
    if (keywords.some((keyword) => searchText.includes(keyword))) {
      return city;
    }
  }

  return null;
}

const pickupPoints = {
  ctt_pickup: [
    
    {
      id: 1,
      name: "CTT Lisboa Centro",
      address: "Rua da Prata, 26, 1100-420 Lisboa",
      lat: 38.7131,
      lng: -9.1367,
      hours: "Seg-Sex: 09:00-19:00, Sáb: 09:00-13:00",
      phone: "+351 21 123 4567",
    },
    {
      id: 2,
      name: "CTT Benfica",
      address: "Estrada de Benfica, 505, 1500-078 Lisboa",
      lat: 38.7436,
      lng: -9.1936,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 21 234 5678",
    },
    {
      id: 3,
      name: "CTT Cascais",
      address: "Rua Frederico Arouca, 77, 2750-357 Cascais",
      lat: 38.6979,
      lng: -9.4213,
      hours: "Seg-Sex: 09:00-19:00, Sáb: 09:00-13:00",
      phone: "+351 21 456 7890",
    },
    {
      id: 4,
      name: "CTT Amadora",
      address: "Rua Elias Garcia, 1, 2700-312 Amadora",
      lat: 38.7538,
      lng: -9.2343,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 21 498 0100",
    },
    
    {
      id: 5,
      name: "CTT Porto Centro",
      address: "Praça General Humberto Delgado, 4000-281 Porto",
      lat: 41.1496,
      lng: -8.6109,
      hours: "Seg-Sex: 09:00-19:00, Sáb: 09:00-13:00",
      phone: "+351 22 340 0200",
    },
    {
      id: 6,
      name: "CTT Gaia",
      address: "Av. da República, 1371, 4430-201 Vila Nova de Gaia",
      lat: 41.1239,
      lng: -8.6113,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 22 375 0300",
    },
    {
      id: 7,
      name: "CTT Matosinhos",
      address: "Rua Brito Capelo, 241, 4450-079 Matosinhos",
      lat: 41.182,
      lng: -8.689,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 22 937 0400",
    },
    
    {
      id: 8,
      name: "CTT Braga",
      address: "Av. da Liberdade, 658, 4710-251 Braga",
      lat: 41.5454,
      lng: -8.4265,
      hours: "Seg-Sex: 09:00-19:00, Sáb: 09:00-13:00",
      phone: "+351 253 203 500",
    },
    
    {
      id: 9,
      name: "CTT Coimbra",
      address: "Av. Fernão de Magalhães, 223, 3000-175 Coimbra",
      lat: 40.2111,
      lng: -8.4291,
      hours: "Seg-Sex: 09:00-19:00, Sáb: 09:00-13:00",
      phone: "+351 239 851 700",
    },
    
    {
      id: 10,
      name: "CTT Aveiro",
      address: "Praça Marquês de Pombal, 11, 3800-176 Aveiro",
      lat: 40.6412,
      lng: -8.6537,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 234 377 800",
    },
    
    {
      id: 11,
      name: "CTT Viseu",
      address: "Rua Formosa, 83, 3500-139 Viseu",
      lat: 40.6573,
      lng: -7.9138,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 232 480 900",
    },
    
    {
      id: 12,
      name: "CTT Setúbal",
      address: "Av. Luísa Todi, 390, 2900-456 Setúbal",
      lat: 38.5244,
      lng: -8.8926,
      hours: "Seg-Sex: 09:00-19:00, Sáb: 09:00-13:00",
      phone: "+351 265 541 100",
    },
    
    {
      id: 13,
      name: "CTT Évora",
      address: "Rua de Olivença, 82, 7000-849 Évora",
      lat: 38.5713,
      lng: -7.9067,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 266 748 200",
    },
    
    {
      id: 14,
      name: "CTT Faro",
      address: "Largo do Carmo, 1, 8000-148 Faro",
      lat: 37.0194,
      lng: -7.9304,
      hours: "Seg-Sex: 09:00-19:00, Sáb: 09:00-13:00",
      phone: "+351 289 860 300",
    },
    {
      id: 15,
      name: "CTT Portimão",
      address: "Largo do Dique, 8500-533 Portimão",
      lat: 37.1364,
      lng: -8.5372,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 282 405 400",
    },
    {
      id: 16,
      name: "CTT Albufeira",
      address: "Rua 5 de Outubro, 8200-080 Albufeira",
      lat: 37.0886,
      lng: -8.2503,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 289 585 500",
    },
    
    {
      id: 17,
      name: "CTT Leiria",
      address: "Av. Heróis de Angola, 2400-185 Leiria",
      lat: 39.7437,
      lng: -8.8071,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 244 839 600",
    },
    
    {
      id: 18,
      name: "CTT Santarém",
      address: "Rua Pedro de Santarém, 71, 2005-247 Santarém",
      lat: 39.2369,
      lng: -8.6867,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 243 309 700",
    },
    
    {
      id: 19,
      name: "CTT Guimarães",
      address: "Alameda de São Dâmaso, 4810-286 Guimarães",
      lat: 41.4416,
      lng: -8.2918,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 253 540 100",
    },
    
    {
      id: 20,
      name: "CTT Viana do Castelo",
      address:
        "Av. dos Combatentes da Grande Guerra, 4900-343 Viana do Castelo",
      lat: 41.6938,
      lng: -8.8344,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 258 809 200",
    },
    
    {
      id: 21,
      name: "CTT Vila Real",
      address: "Av. Carvalho Araújo, 5000-657 Vila Real",
      lat: 41.3007,
      lng: -7.7437,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 259 309 300",
    },
    
    {
      id: 22,
      name: "CTT Bragança",
      address: "Av. Sá Carneiro, 5300-252 Bragança",
      lat: 41.806,
      lng: -6.757,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 273 309 400",
    },
    
    {
      id: 23,
      name: "CTT Guarda",
      address: "Rua Batalha Reis, 6300-738 Guarda",
      lat: 40.5375,
      lng: -7.2681,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 271 205 500",
    },
    
    {
      id: 24,
      name: "CTT Castelo Branco",
      address: "Rua da Piscina, 6000-459 Castelo Branco",
      lat: 39.8208,
      lng: -7.4916,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 272 348 600",
    },
    
    {
      id: 25,
      name: "CTT Portalegre",
      address: "Rua 19 de Junho, 7300-211 Portalegre",
      lat: 39.2967,
      lng: -7.4281,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 245 307 700",
    },
    
    {
      id: 26,
      name: "CTT Beja",
      address: "Rua de Évora, 7800-451 Beja",
      lat: 38.015,
      lng: -7.8632,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 284 311 800",
    },
    
    {
      id: 27,
      name: "CTT Funchal",
      address: "Av. Zarco, 9000-069 Funchal",
      lat: 32.6492,
      lng: -16.9093,
      hours: "Seg-Sex: 09:00-19:00, Sáb: 09:00-13:00",
      phone: "+351 291 214 900",
    },
    
    {
      id: 28,
      name: "CTT Ponta Delgada",
      address: "Rua Ernesto do Canto, 9500-318 Ponta Delgada",
      lat: 37.7412,
      lng: -25.6756,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 296 209 100",
    },
    
    {
      id: 29,
      name: "CTT Figueira da Foz",
      address: "Rua 5 de Outubro, 3080-169 Figueira da Foz",
      lat: 40.1508,
      lng: -8.8618,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 233 407 200",
    },
    
    {
      id: 30,
      name: "CTT Caldas da Rainha",
      address: "Praça 25 de Abril, 2500-116 Caldas da Rainha",
      lat: 39.4034,
      lng: -9.1372,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 262 839 300",
    },
    
    {
      id: 31,
      name: "CTT Torres Vedras",
      address: "Praça 25 de Abril, 2560-280 Torres Vedras",
      lat: 39.0908,
      lng: -9.2589,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 261 310 400",
    },
    
    {
      id: 32,
      name: "CTT Sintra",
      address: "Praça da República, 2710-616 Sintra",
      lat: 38.7989,
      lng: -9.3878,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 219 247 500",
    },
    
    {
      id: 33,
      name: "CTT Oeiras",
      address: "Rua Cândido dos Reis, 2780-229 Oeiras",
      lat: 38.6913,
      lng: -9.3106,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 214 408 600",
    },
    
    {
      id: 34,
      name: "CTT Póvoa de Varzim",
      address: "Praça do Almada, 4490-438 Póvoa de Varzim",
      lat: 41.3833,
      lng: -8.7606,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 252 615 700",
    },
    
    {
      id: 35,
      name: "CTT Barcelos",
      address: "Av. Dr. Sidónio Pais, 4750-333 Barcelos",
      lat: 41.5311,
      lng: -8.6157,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 253 809 800",
    },
    
    {
      id: 36,
      name: "CTT Espinho",
      address: "Rua 19, 4500-258 Espinho",
      lat: 41.0078,
      lng: -8.6411,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 227 331 900",
    },
    
    {
      id: 37,
      name: "CTT Olhão",
      address: "Av. da República, 8700-307 Olhão",
      lat: 37.0263,
      lng: -7.8408,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 289 702 100",
    },
    
    {
      id: 38,
      name: "CTT Tavira",
      address: "Rua da Liberdade, 8800-371 Tavira",
      lat: 37.127,
      lng: -7.6484,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 281 320 200",
    },
  ],
  dpd_pickup: [
    
    {
      id: 39,
      name: "DPD Lisboa Amoreiras",
      address: "Av. Eng. Duarte Pacheco, Amoreiras, 1070-103 Lisboa",
      lat: 38.7253,
      lng: -9.1534,
      hours: "Seg-Dom: 08:00-20:00",
      phone: "+351 21 567 8901",
    },
    {
      id: 20,
      name: "DPD Centro Colombo",
      address: "Av. Lusíada, Centro Colombo, 1500-392 Lisboa",
      lat: 38.7568,
      lng: -9.1952,
      hours: "Seg-Dom: 10:00-22:00",
      phone: "+351 21 678 9012",
    },
    {
      id: 41,
      name: "DPD Cascais",
      address: "Av. 25 de Abril, 2750-512 Cascais",
      lat: 38.6968,
      lng: -9.4215,
      hours: "Seg-Sex: 09:00-19:00, Sáb: 09:00-14:00",
      phone: "+351 21 460 2000",
    },
    {
      id: 42,
      name: "DPD Almada Forum",
      address: "Rua Sérgio Malpique, 2800-278 Almada",
      lat: 38.6785,
      lng: -9.157,
      hours: "Seg-Dom: 10:00-22:00",
      phone: "+351 21 272 3000",
    },
    
    {
      id: 43,
      name: "DPD Porto Norte Shopping",
      address: "Rua Sara Afonso, 105, 4460-841 Senhora da Hora",
      lat: 41.1893,
      lng: -8.6537,
      hours: "Seg-Dom: 10:00-22:00",
      phone: "+351 22 950 4000",
    },
    {
      id: 44,
      name: "DPD Gaia Shopping",
      address: "Av. dos Descobrimentos, 4400-103 Vila Nova de Gaia",
      lat: 41.1311,
      lng: -8.6314,
      hours: "Seg-Dom: 10:00-23:00",
      phone: "+351 22 371 5000",
    },
    {
      id: 45,
      name: "DPD Maia",
      address: "Rua do Lidador, 4470-343 Maia",
      lat: 41.2358,
      lng: -8.6209,
      hours: "Seg-Sex: 09:00-19:00, Sáb: 09:00-14:00",
      phone: "+351 22 948 6000",
    },
    
    {
      id: 46,
      name: "DPD Braga Parque",
      address: "Quinta dos Congregados, 4710-427 Braga",
      lat: 41.562,
      lng: -8.3963,
      hours: "Seg-Dom: 10:00-23:00",
      phone: "+351 253 680 700",
    },
    
    {
      id: 47,
      name: "DPD Coimbra Shopping",
      address: "Av. Dr. Mendes Silva, 3030-290 Coimbra",
      lat: 40.2034,
      lng: -8.4122,
      hours: "Seg-Dom: 09:00-23:00",
      phone: "+351 239 497 800",
    },
    
    {
      id: 48,
      name: "DPD Aveiro Centro",
      address: "Rua Batalhão de Caçadores, 3810-064 Aveiro",
      lat: 40.6443,
      lng: -8.6455,
      hours: "Seg-Sex: 09:00-19:00, Sáb: 09:00-14:00",
      phone: "+351 234 098 900",
    },
    
    {
      id: 49,
      name: "DPD Viseu",
      address: "Estrada Nacional 2, 3510-021 Viseu",
      lat: 40.6651,
      lng: -7.9201,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 232 419 100",
    },
    
    {
      id: 50,
      name: "DPD Setúbal",
      address: "Av. Bento Gonçalves, 2910-414 Setúbal",
      lat: 38.5317,
      lng: -8.8843,
      hours: "Seg-Sex: 09:00-19:00, Sáb: 09:00-14:00",
      phone: "+351 265 709 200",
    },
    
    {
      id: 51,
      name: "DPD Évora",
      address: "Rua Vasco da Gama, 7005-841 Évora",
      lat: 38.5658,
      lng: -7.9091,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 266 760 300",
    },
    
    {
      id: 52,
      name: "DPD Faro Forum Algarve",
      address: "EN 125, Sitio das Figuras, 8005-518 Faro",
      lat: 37.0332,
      lng: -7.9528,
      hours: "Seg-Dom: 10:00-23:00",
      phone: "+351 289 889 400",
    },
    {
      id: 53,
      name: "DPD Portimão",
      address: "Rua de São Pedro, 8500-801 Portimão",
      lat: 37.1391,
      lng: -8.5378,
      hours: "Seg-Sex: 09:00-19:00, Sáb: 09:00-14:00",
      phone: "+351 282 498 500",
    },
    {
      id: 54,
      name: "DPD Lagos",
      address: "Av. dos Descobrimentos, 8600-645 Lagos",
      lat: 37.1028,
      lng: -8.6735,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 282 769 600",
    },
    
    {
      id: 55,
      name: "DPD Leiria Shopping",
      address: "Rua de Tomar, 2400-441 Leiria",
      lat: 39.7392,
      lng: -8.8159,
      hours: "Seg-Dom: 09:00-23:00",
      phone: "+351 244 870 700",
    },
    
    {
      id: 56,
      name: "DPD Santarém",
      address: "Av. Bernardo Santareno, 2005-177 Santarém",
      lat: 39.2414,
      lng: -8.679,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 243 330 800",
    },
    
    {
      id: 57,
      name: "DPD Guimarães Shopping",
      address: "Rua 25 de Abril, 4835-400 Creixomil",
      lat: 41.4498,
      lng: -8.2868,
      hours: "Seg-Dom: 10:00-23:00",
      phone: "+351 253 510 200",
    },
    
    {
      id: 58,
      name: "DPD Viana Shopping",
      address: "Meadela, 4900-727 Viana do Castelo",
      lat: 41.708,
      lng: -8.8165,
      hours: "Seg-Dom: 10:00-23:00",
      phone: "+351 258 820 300",
    },
    
    {
      id: 59,
      name: "DPD Vila Real",
      address: "Av. 1º de Maio, 5000-651 Vila Real",
      lat: 41.2976,
      lng: -7.7456,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 259 320 400",
    },
    
    {
      id: 60,
      name: "DPD Bragança",
      address: "Av. Cidade de León, 5300-123 Bragança",
      lat: 41.8089,
      lng: -6.7503,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 273 320 500",
    },
    
    {
      id: 61,
      name: "DPD Guarda",
      address: "Rua Coronel Orlindo de Carvalho, 6300-532 Guarda",
      lat: 40.5401,
      lng: -7.2712,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 271 220 600",
    },
    
    {
      id: 62,
      name: "DPD Castelo Branco",
      address: "Av. Nuno Álvares, 6000-083 Castelo Branco",
      lat: 39.8183,
      lng: -7.4936,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 272 320 700",
    },
    
    {
      id: 63,
      name: "DPD Portalegre",
      address: "Rossio, 7300-110 Portalegre",
      lat: 39.2942,
      lng: -7.4298,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 245 330 800",
    },
    
    {
      id: 64,
      name: "DPD Beja",
      address: "Rua Luís de Camões, 7800-440 Beja",
      lat: 38.0172,
      lng: -7.8651,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 284 320 900",
    },
    
    {
      id: 65,
      name: "DPD Funchal Madeira Shopping",
      address: "Caminho de Santa Quitéria, 9020-069 Funchal",
      lat: 32.6595,
      lng: -16.9242,
      hours: "Seg-Dom: 10:00-23:00",
      phone: "+351 291 230 100",
    },
    
    {
      id: 66,
      name: "DPD Ponta Delgada",
      address: "Av. Infante D. Henrique, 9500-764 Ponta Delgada",
      lat: 37.7394,
      lng: -25.665,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 296 280 200",
    },
    
    {
      id: 67,
      name: "DPD Figueira da Foz",
      address: "Rua Dr. Calado, 3080-161 Figueira da Foz",
      lat: 40.1476,
      lng: -8.8639,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 233 430 300",
    },
    
    {
      id: 68,
      name: "DPD Caldas da Rainha",
      address: "Rua Almirante Cândido dos Reis, 2500-152 Caldas da Rainha",
      lat: 39.4018,
      lng: -9.1352,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 262 840 400",
    },
    
    {
      id: 69,
      name: "DPD Torres Vedras",
      address: "Rua Paiva de Andrade, 2560-337 Torres Vedras",
      lat: 39.0915,
      lng: -9.2577,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 261 320 500",
    },
    
    {
      id: 70,
      name: "DPD Sintra",
      address: "Rua Dr. Alfredo Costa, 15, 2710-524 Sintra",
      lat: 38.7989,
      lng: -9.3878,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 21 890 1234",
    },
    
    {
      id: 71,
      name: "DPD Oeiras Parque",
      address: "Av. António Bernardo Cabral de Macedo, 2770-219 Paço de Arcos",
      lat: 38.6921,
      lng: -9.2944,
      hours: "Seg-Dom: 10:00-23:00",
      phone: "+351 214 405 600",
    },
    
    {
      id: 72,
      name: "DPD Póvoa de Varzim",
      address: "Rua Gomes de Amorim, 4490-665 Póvoa de Varzim",
      lat: 41.3872,
      lng: -8.7638,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 252 690 700",
    },
    
    {
      id: 73,
      name: "DPD Barcelos",
      address: "Av. da Liberdade, 4750-287 Barcelos",
      lat: 41.5336,
      lng: -8.6183,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 253 820 800",
    },
    
    {
      id: 74,
      name: "DPD Espinho",
      address: "Rua 37, 4500-904 Espinho",
      lat: 41.0089,
      lng: -8.6392,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 227 340 900",
    },
    
    {
      id: 75,
      name: "DPD Olhão",
      address: "Av. 5 de Outubro, 8700-304 Olhão",
      lat: 37.0281,
      lng: -7.8392,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 289 710 100",
    },
    
    {
      id: 76,
      name: "DPD Tavira",
      address: "Rua Almirante Cândido dos Reis, 8800-355 Tavira",
      lat: 37.1289,
      lng: -7.6512,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 281 325 200",
    },
    
    {
      id: 77,
      name: "DPD Vila Real de Santo António",
      address: "Av. da República, 8900-209 Vila Real de Santo António",
      lat: 37.1934,
      lng: -7.4155,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 281 510 300",
    },
    
    {
      id: 78,
      name: "DPD Loulé",
      address: "Av. José da Costa Mealha, 8100-500 Loulé",
      lat: 37.1399,
      lng: -8.0229,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 289 410 400",
    },
  ],
};

function showPickupMap(transportadoraId) {
  
  let mapContainer = $("#pickupMapContainer");
  if (mapContainer.length === 0) {
    const shippingInfo = JSON.parse(
      localStorage.getItem("shippingInfo") || "{}",
    );
    const cityFilter = shippingInfo.state
      ? `<small class="text-muted">Mostrando pontos em <strong>${shippingInfo.state}</strong></small>`
      : "";

    const mapHTML = `
      <div id="pickupMapContainer" class="pickup-map-container">
        <div class="pickup-header">
          <h4><i class="bi bi-geo-alt-fill"></i> Escolha o Ponto de Recolha</h4>
          <p class="pickup-subtitle">Selecione o ponto mais próximo de si ${cityFilter}</p>
        </div>
        <div id="pickupMap" class="pickup-map"></div>
        <div id="pickupPointsList" class="pickup-points-list"></div>
      </div>
    `;
    $(".transportadora-options").after(mapHTML);
  } else {
    
    const shippingInfo = JSON.parse(
      localStorage.getItem("shippingInfo") || "{}",
    );
    const cityFilter = shippingInfo.state
      ? `<small class="text-muted">Mostrando pontos em <strong>${shippingInfo.state}</strong></small>`
      : "";
    $(".pickup-subtitle").html(
      `Selecione o ponto mais próximo de si ${cityFilter}`,
    );
    mapContainer.show();
  }

  
  setTimeout(() => {
    initPickupMap(transportadoraId);
  }, 100);
}

function hidePickupMap() {
  $("#pickupMapContainer").hide();
  localStorage.removeItem("pickup_point_id");
  localStorage.removeItem("pickup_point_name");
  localStorage.removeItem("pickup_point_address");
}

function initPickupMap(transportadoraId) {
  let points = pickupPoints[transportadoraId] || [];

  if (points.length === 0) {
    return;
  }

  
  const shippingInfo = JSON.parse(localStorage.getItem("shippingInfo") || "{}");
  const selectedCity = shippingInfo.state; 

  if (selectedCity) {
    
    const filteredPoints = points.filter((p) => {
      const pointCity = getCityFromPickupPoint(p);
      return pointCity === selectedCity;
    });

    if (filteredPoints.length > 0) {
      points = filteredPoints;
    } else {
      
      const regionPoints = points.filter((p) => {
        const pointCity = getCityFromPickupPoint(p);
        
        return (
          pointCity &&
          pointCity
            .toLowerCase()
            .includes(selectedCity.toLowerCase().substring(0, 3))
        );
      });
      if (regionPoints.length > 0) {
        points = regionPoints;
      }
    }
  }

  
  if (typeof L === "undefined") {
    showModernWarningModal(
      "Mapa Indisponível",
      "Use a lista abaixo para selecionar um ponto de recolha",
    );
    renderPickupPointsList(points);
    return;
  }

  
  if (pickupMap) {
    pickupMap.remove();
  }

  
  pickupMap = L.map("pickupMap").setView([38.7223, -9.1393], 7);

  
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution:
      '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    maxZoom: 19,
  }).addTo(pickupMap);

  
  pickupMarkers.forEach((marker) => marker.remove());
  pickupMarkers = [];

  
  const greenIcon = L.divIcon({
    className: "custom-leaflet-marker",
    html: '<div class="marker-pin-green"><i class="bi bi-geo-alt-fill"></i></div>',
    iconSize: [30, 42],
    iconAnchor: [15, 42],
    popupAnchor: [0, -42],
  });

  
  points.forEach((point) => {
    const marker = L.marker([point.lat, point.lng], { icon: greenIcon }).addTo(
      pickupMap,
    );

    const popupContent = `
      <div class="leaflet-pickup-popup">
        <h5>${point.name}</h5>
        <div class="popup-details">
          <p><i class="bi bi-geo-alt"></i> ${point.address}</p>
          <p><i class="bi bi-clock"></i> ${point.hours}</p>
          <p><i class="bi bi-telephone"></i> ${point.phone}</p>
        </div>
        <button onclick="selectPickupPoint(${point.id}, '${point.name}', '${point.address}')"
                class="btn-select-pickup">
          <i class="bi bi-check-circle"></i> Selecionar Este Ponto
        </button>
      </div>
    `;

    marker.bindPopup(popupContent, {
      maxWidth: 300,
      className: "custom-leaflet-popup",
      autoPan: true,
      autoPanPadding: [50, 50],
      keepInView: true,
      closeButton: true,
    });
    pickupMarkers.push(marker);
  });

  
  renderPickupPointsList(points);

  
  if (points.length > 0) {
    const initialBounds = L.latLngBounds(points.map((p) => [p.lat, p.lng]));
    setTimeout(() => {
      pickupMap.fitBounds(initialBounds, {
        padding: [80, 80],
        maxZoom: points.length === 1 ? 14 : 12,
      });
    }, 500);
  }

  
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        userLocation = {
          lat: position.coords.latitude,
          lng: position.coords.longitude,
        };

        
        const userIcon = L.divIcon({
          className: "custom-user-marker",
          html: '<div class="marker-pin-blue"><i class="bi bi-person-fill"></i></div>',
          iconSize: [25, 35],
          iconAnchor: [12, 35],
        });

        
        L.marker([userLocation.lat, userLocation.lng], { icon: userIcon })
          .addTo(pickupMap)
          .bindPopup("<strong>A sua localização</strong>");

        
        const pointsWithDistance = calculateDistances(points, userLocation);
        renderPickupPointsList(pointsWithDistance);

        
        const allLats = [...points.map((p) => p.lat), userLocation.lat];
        const allLngs = [...points.map((p) => p.lng), userLocation.lng];
        const boundsWithUser = L.latLngBounds(
          [Math.min(...allLats), Math.min(...allLngs)],
          [Math.max(...allLats), Math.max(...allLngs)],
        );
        setTimeout(() => {
          pickupMap.fitBounds(boundsWithUser, {
            padding: [80, 80],
            maxZoom: points.length === 1 ? 14 : 12,
          });
        }, 500);

        
        if (pointsWithDistance.length > 0 && pickupMarkers.length > 0) {
          const closestPoint = pointsWithDistance[0];
          const closestMarker = pickupMarkers.find((m) => {
            const pos = m.getLatLng();
            return (
              Math.abs(pos.lat - closestPoint.lat) < 0.0001 &&
              Math.abs(pos.lng - closestPoint.lng) < 0.0001
            );
          });
          if (closestMarker) {
            setTimeout(() => closestMarker.openPopup(), 500);
          }
        }
      },
      (error) => {
        renderPickupPointsList(points);
        
        if (points.length > 0 && pickupMarkers.length > 0) {
          setTimeout(() => pickupMarkers[0].openPopup(), 500);
        }
      },
    );
  } else {
    renderPickupPointsList(points);
    
    if (points.length > 0 && pickupMarkers.length > 0) {
      setTimeout(() => pickupMarkers[0].openPopup(), 500);
    }
  }
}

function calculateDistances(points, userLoc) {
  return points
    .map((point) => {
      const distance = getDistanceInKm(
        userLoc.lat,
        userLoc.lng,
        point.lat,
        point.lng,
      );
      return { ...point, distance };
    })
    .sort((a, b) => a.distance - b.distance);
}

function getDistanceInKm(lat1, lon1, lat2, lon2) {
  const R = 6371; 
  const dLat = ((lat2 - lat1) * Math.PI) / 180;
  const dLon = ((lon2 - lon1) * Math.PI) / 180;
  const a =
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos((lat1 * Math.PI) / 180) *
      Math.cos((lat2 * Math.PI) / 180) *
      Math.sin(dLon / 2) *
      Math.sin(dLon / 2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  return R * c;
}

function renderPickupPointsList(points) {
  const html = points
    .map(
      (point) => `
    <div class="pickup-point-item" onclick="selectPickupPoint(${point.id}, '${point.name}', '${point.address}')">
      <div class="pickup-point-icon">
        <i class="bi bi-geo-alt-fill"></i>
      </div>
      <div class="pickup-point-info">
        <h5>${point.name}</h5>
        <p class="address"><i class="bi bi-geo-alt"></i> ${point.address}</p>
        <p class="hours"><i class="bi bi-clock"></i> ${point.hours}</p>
        ${
          point.phone
            ? `<p class="phone"><i class="bi bi-telephone"></i> ${point.phone}</p>`
            : ""
        }
        ${
          point.distance
            ? `<p class="distance"><i class="bi bi-pin-map"></i> ${point.distance.toFixed(
                1,
              )} km de si</p>`
            : ""
        }
      </div>
      <button onclick="event.stopPropagation(); selectPickupPoint(${
        point.id
      }, '${point.name}', '${point.address}')" class="btn-select-point">
        Selecionar
      </button>
    </div>
  `,
    )
    .join("");

  $("#pickupPointsList").html(html);
}

function focusPickupPoint(lat, lng) {
  if (pickupMap) {
    pickupMap.setView([lat, lng], 15);

    
    pickupMarkers.forEach((marker) => {
      const pos = marker.getLatLng();
      if (
        Math.abs(pos.lat - lat) < 0.0001 &&
        Math.abs(pos.lng - lng) < 0.0001
      ) {
        marker.openPopup();
      }
    });
  }
}

function selectPickupPoint(id, name, address) {
  localStorage.setItem("pickup_point_id", id);
  localStorage.setItem("pickup_point_name", name);
  localStorage.setItem("pickup_point_address", address);

  updateOrderSummary(); 

  
  $("button[onclick*='nextStep']")
    .prop("disabled", false)
    .removeClass("disabled-btn");

  
  $(".pickup-point-item").removeClass("selected");
  $(".pickup-point-item").each(function () {
    if ($(this).find("h5").text() === name) {
      $(this).addClass("selected");
    }
  });

  
  setTimeout(() => {
    const continueBtn = $("button[onclick*='nextStep']")[0];
    if (continueBtn) {
      continueBtn.scrollIntoView({ behavior: "smooth", block: "center" });
    }
  }, 500);
}
