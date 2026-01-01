// ============ FUNÇÕES DE CARRINHO (CHECKOUT) ============

// Funções legacy removidas - não são usadas nesta versão do checkout

// Remover produto do carrinho
function removerDoCarrinho(produto_id) {
  Swal.fire({
    title: "Tem a certeza?",
    text: "Pretende remover este produto do carrinho?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sim, remover!",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      let dados = new FormData();
      dados.append("op", 4);
      dados.append("produto_id", produto_id);

      $.ajax({
        url: "src/controller/controllerCarrinho.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false,
      })
        .done(function (msg) {
          Swal.fire(
            "Removido!",
            "O produto foi removido do carrinho.",
            "success"
          );
          loadCarrinho(); // Recarregar carrinho
        })
        .fail(function (jqXHR, textStatus) {
          Swal.fire({
            icon: "error",
            title: "Erro",
            text: "Não foi possível remover o produto.",
          });
        });
    }
  });
}

function limparCarrinho() {
  Swal.fire({
    title: "Limpar carrinho?",
    text: "Todos os produtos serão removidos!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sim, limpar tudo!",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      let dados = new FormData();
      dados.append("op", 5);

      $.ajax({
        url: "src/controller/controllerCarrinho.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false,
      })
        .done(function (msg) {
          Swal.fire("Limpo!", "O carrinho foi limpo com sucesso.", "success");
          loadCarrinho(); // Recarregar carrinho
        })
        .fail(function (jqXHR, textStatus) {
          Swal.fire({
            icon: "error",
            title: "Erro",
            text: "Não foi possível limpar o carrinho.",
          });
        });
    }
  });
}

function aplicarCupao() {
  let codigo = $("#couponCode").val();

  if (codigo.trim() === "") {
    Swal.fire({
      icon: "error",
      title: "Oops...",
      text: "Por favor, insira um código de cupão!",
    });
    return;
  }

  let dados = new FormData();
  dados.append("op", 6);
  dados.append("codigo", codigo);

  $.ajax({
    url: "src/controller/controllerCarrinho.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      if (msg.includes("sucesso")) {
        Swal.fire({
          icon: "success",
          title: "Cupão aplicado!",
          text: "Desconto de 10% aplicado ao seu carrinho",
          showConfirmButton: true,
          timer: 2000,
        });
        getResumoPedido();
      } else {
        Swal.fire({
          icon: "error",
          title: "Cupão inválido",
          text: "O código inserido não é válido ou já expirou.",
        });
      }
    })
    .fail(function (jqXHR, textStatus) {
      alert("Request failed: " + textStatus);
    });
}

function removerCupao() {
  Swal.fire({
    title: "Remover cupão?",
    text: "O desconto será removido do carrinho.",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#ffd700",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sim, remover",
    cancelButtonText: "Cancelar",
  }).then((result) => {
    if (result.isConfirmed) {
      let dados = new FormData();
      dados.append("op", 8);

      $.ajax({
        url: "src/controller/controllerCarrinho.php",
        method: "POST",
        data: dados,
        dataType: "html",
        cache: false,
        contentType: false,
        processData: false,
      })
        .done(function (msg) {
          Swal.fire({
            icon: "success",
            title: "Removido!",
            text: "Cupão removido com sucesso.",
            timer: 1500,
            showConfirmButton: false,
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
      if (response.tem_produtos) {
        window.location.href = "checkout_stripe.php";
      } else {
        Swal.fire({
          icon: "warning",
          title: "Carrinho Vazio",
          text: "Adicione produtos ao carrinho antes de finalizar a compra!",
        });
      }
    })
    .fail(function (jqXHR, textStatus) {
      Swal.fire({
        icon: "error",
        title: "Erro",
        text: "Ocorreu um erro. Tente novamente.",
      });
    });
}

// ============ FUNÇÕES DOS STEPS ============
let currentStep = 1;
const totalSteps = 4;
let isUserLoggedIn = false; // Estado de login do utilizador

// Funções de perfil (compatível com index.html)
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
      // Verificar se está logado baseado no conteúdo retornado
      if (msg.includes("Olá")) {
        isUserLoggedIn = true;
      } else {
        isUserLoggedIn = false;
      }
    })
    .fail(function (jqXHR, textStatus) {
      console.error("Request failed: " + textStatus);
    });
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
    .fail(function (jqXHR, textStatus) {
      console.error("Request failed: " + textStatus);
    });
}

// Carregar nome do utilizador (chama as funções acima)
function loadUserName() {
  PerfilDoUtilizador();
  getDadosTipoPerfil();
}

// Carregar carrinho (versão JSON)
function loadCarrinho() {
  $.ajax({
    url: "src/controller/controllerCarrinho.php",
    method: "POST",
    data: { op: 10 },
    dataType: "json",
  })
    .done(function (data) {
      if (data.produtos && data.produtos.length > 0) {
        displayCarrinho(data.produtos, data.total);
      } else {
        $("#cartItemsStep1").html(
          '<p class="text-center text-muted py-4">O carrinho está vazio.</p>'
        );
      }
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
      console.error("ERRO loadCarrinho:", textStatus, errorThrown);
      console.error("Resposta do servidor:", jqXHR.responseText);
      Swal.fire({
        icon: "error",
        title: "Erro ao Carregar Carrinho",
        text: "Não foi possível carregar os produtos. Tente novamente.",
      });
    });
}

// Atualizar quantidade (incremento +1 ou -1)
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
      if (response.success) {
        // Atualizar quantidade no input
        const newQty = parseInt($("#qty-" + produtoId).val()) + mudanca;
        if (newQty > 0) {
          $("#qty-" + produtoId).val(newQty);
          // Recarregar carrinho para atualizar totais
          loadCarrinho();
          updateOrderSummary();
        }
      } else {
        Swal.fire({
          icon: "error",
          title: "Erro",
          text: response.message || "Não foi possível atualizar a quantidade.",
        });
      }
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
      console.error("Erro AJAX:", textStatus, errorThrown);
      console.error("Resposta:", jqXHR.responseText);
      Swal.fire({
        icon: "error",
        title: "Erro",
        text:
          "Erro ao atualizar quantidade: " + (jqXHR.responseText || textStatus),
      });
    });
}

// Definir quantidade exata (via input)
function setQuantidade(produtoId, novaQuantidade) {
  const qty = parseInt(novaQuantidade);
  if (isNaN(qty) || qty < 1) {
    $("#qty-" + produtoId).val(1);
    return;
  }

  const currentQty = parseInt(
    $("#qty-" + produtoId).data("original-qty") || $("#qty-" + produtoId).val()
  );
  const mudanca = qty - currentQty;

  if (mudanca !== 0) {
    // Atualizar via AJAX multiple vezes se necessário
    updateQuantidadeDireta(produtoId, qty);
  }
}

// Atualizar quantidade direta (chamadas múltiplas se necessário)
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
      if (response.success) {
        completed++;
        updateStep();
      }
    });
  }

  updateStep();
}

// Remover produto do carrinho
function removeFromCart(produtoId) {
  Swal.fire({
    title: "Remover Produto?",
    text: "Tem certeza que deseja remover este produto do carrinho?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3cb371",
    cancelButtonColor: "#dc3545",
    confirmButtonText: "Sim, remover",
    cancelButtonText: "Cancelar",
  }).then((result) => {
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
          if (response.success) {
            // Animação de remoção
            $("#cart-item-" + produtoId).fadeOut(300, function () {
              $(this).remove();
              loadCarrinho();
              updateOrderSummary();
            });

            Swal.fire({
              icon: "success",
              title: "Removido!",
              text: "Produto removido do carrinho.",
              timer: 1500,
              showConfirmButton: false,
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Erro",
              text: response.message || "Não foi possível remover o produto.",
            });
          }
        })
        .fail(function () {
          Swal.fire({
            icon: "error",
            title: "Erro",
            text: "Erro ao remover produto. Tente novamente.",
          });
        });
    }
  });
}

// Carregar resumo do pedido
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

// Exibir itens do carrinho
function displayCarrinho(items, total) {
  let html = "";
  if (items && items.length > 0) {
    items.forEach((item) => {
      const reachedStock = item.quantidade >= item.stock;
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
                          item.preco
                        )
                          .toFixed(2)
                          .replace(".", ",")} €</p>
                        ${
                          item.stock
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
                                   max="${item.stock || 99}"
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
                  total
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

// Mostrar step - Atualizar indicadores horizontais
function showStep(step) {
  currentStep = step;

  // Atualizar steps
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

  // Atualizar indicadores horizontais no topo
  $(".step-progress-item").each(function (index) {
    const stepNum = index + 1;
    $(this).removeClass("active completed");

    if (stepNum < step) {
      $(this).addClass("completed");
    } else if (stepNum === step) {
      $(this).addClass("active");
    }
  });

  // Scroll suave para o topo do step
  setTimeout(() => {
    $(".steps-progress-container")[0]?.scrollIntoView({
      behavior: "smooth",
      block: "start",
    });
  }, 100);
}

// Ir para próximo step
function nextStep() {
  if (currentStep < totalSteps) {
    if (validateStep(currentStep)) {
      showStep(currentStep + 1);
    }
  }
}

// Voltar step anterior
function previousStep() {
  if (currentStep > 1) {
    showStep(currentStep - 1);
  }
}

// Validar step atual
function validateStep(step) {
  if (step === 1) {
    const hasItems = $("#cartItemsStep1 .cart-item").length > 0;
    if (!hasItems) {
      Swal.fire({
        icon: "warning",
        title: "Carrinho Vazio",
        text: "Adicione produtos ao carrinho antes de continuar.",
      });
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

    // Validação específica por campo
    if (!firstName) {
      $("#firstName").focus();
      Swal.fire({
        icon: "warning",
        title: "Nome Obrigatório",
        text: "Por favor, preencha o seu primeiro nome.",
      });
      return false;
    }

    if (!lastName) {
      $("#lastName").focus();
      Swal.fire({
        icon: "warning",
        title: "Apelido Obrigatório",
        text: "Por favor, preencha o seu apelido.",
      });
      return false;
    }

    if (!address1) {
      $("#address1").focus();
      Swal.fire({
        icon: "warning",
        title: "Morada Obrigatória",
        text: "Por favor, preencha a sua morada.",
      });
      return false;
    }

    if (!city) {
      $("#city").focus();
      Swal.fire({
        icon: "warning",
        title: "Cidade Obrigatória",
        text: "Por favor, preencha a sua cidade.",
      });
      return false;
    }

    if (!zipCode) {
      $("#zipCode").focus();
      Swal.fire({
        icon: "warning",
        title: "Código Postal Obrigatório",
        text: "Por favor, preencha o código postal no formato XXXX-XXX.",
      });
      return false;
    }

    // Validação formato postal code PT
    const zipCodePattern = /^[0-9]{4}-[0-9]{3}$/;
    if (!zipCodePattern.test(zipCode)) {
      $("#zipCode").focus();
      Swal.fire({
        icon: "warning",
        title: "Código Postal Inválido",
        text: "Use o formato português: XXXX-XXX (ex: 1000-001)",
      });
      return false;
    }

    if (!state) {
      $("#state").focus();
      Swal.fire({
        icon: "warning",
        title: "Localidade Obrigatória",
        text: "Por favor, selecione a localidade/distrito.",
      });
      return false;
    }

    saveShippingInfo();
    return true;
  }

  if (step === 3) {
    const selectedTransportadora = localStorage.getItem("transportadora_id");
    if (!selectedTransportadora) {
      Swal.fire({
        icon: "warning",
        title: "Transportadora Não Selecionada",
        text: "Selecione uma transportadora antes de continuar.",
      });
      return false;
    }

    // Se for pickup point (ID 2 ou 4), verificar se selecionou um ponto
    if (selectedTransportadora === "2" || selectedTransportadora === "4") {
      const pickupPointId = localStorage.getItem("pickup_point_id");
      if (!pickupPointId) {
        Swal.fire({
          icon: "warning",
          title: "Ponto de Recolha Não Selecionado",
          text: "Selecione um ponto de recolha no mapa.",
        });
        return false;
      }
    }

    // Verificar se utilizador está logado ANTES de ir para pagamento
    if (!isUserLoggedIn) {
      Swal.fire({
        icon: "info",
        title: "Login Necessário para Pagamento",
        html: "Para finalizar a compra, precisa estar logado.<br><small class='text-muted'>Os seus dados de envio serão guardados.</small>",
        showCancelButton: true,
        confirmButtonText:
          '<i class="bi bi-box-arrow-in-right"></i> Fazer Login',
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#3cb371",
        cancelButtonColor: "#6c757d",
      }).then((result) => {
        if (result.isConfirmed) {
          // Dados já estão guardados em localStorage
          window.location.href = "login.html?redirect=Carrinho.html";
        }
      });
      return false;
    }

    return true;
  }

  return true;
}

// Guardar informações de entrega
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
  updateOrderSummary(); // Atualizar resumo sidebar
}

// Selecionar transportadora
function selectTransportadora(id, price) {
  $(".transportadora-card")
    .removeClass("selected")
    .attr("aria-checked", "false");
  $(`#transportadora${id}`).addClass("selected").attr("aria-checked", "true");

  localStorage.setItem("transportadora_id", id.toString());
  localStorage.setItem("transportadora_price", price.toString());

  updateOrderSummary(); // Atualizar resumo sidebar

  // Se for pickup point (IDs 2 ou 4), mostrar mapa
  if (id === 2 || id === 4) {
    const transportadoraType = id === 2 ? "ctt_pickup" : "dpd_pickup";
    showPickupMap(transportadoraType);
  } else {
    hidePickupMap();
  }
}

// Mostrar revisão de dados de entrega
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

// Mostrar revisão da transportadora
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
              transportadoraPrice || 0
            ).toFixed(2)}€</p>
            ${pickupInfo}
        </div>
    `;

  $("#transportadoraReview").html(html);
}

// Mostrar revisão do carrinho
function displayCartReview() {
  $.ajax({
    url: "src/controller/controllerCarrinho.php",
    method: "POST",
    data: { op: 10 },
    dataType: "json",
  }).done(function (data) {
    if (data.success) {
      let html = "";
      data.items.forEach((item) => {
        html += `
                    <div class="cart-item d-flex align-items-center mb-3 p-3 bg-light rounded">
                        <img src="${item.imagem}" alt="${
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
                              2
                            )}€</strong>
                        </div>
                    </div>
                `;
      });

      const transportadoraPrice = parseFloat(
        localStorage.getItem("transportadora_price") || 0
      );
      const subtotal = parseFloat(data.total);
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
                          2
                        )}€</strong>
                    </div>
                </div>
            `;

      $("#cartReview").html(html);
    }
  });
}

// Finalizar pedido
function finalizarPedido() {
  // Validar checkbox GDPR
  if (!$("#gdprConsent").is(":checked")) {
    Swal.fire({
      icon: "warning",
      title: "Termos Não Aceites",
      text: "Por favor, aceite os Termos e Condições e a Política de Privacidade para continuar.",
    });
    return;
  }

  const shippingInfo = JSON.parse(localStorage.getItem("shippingInfo") || "{}");
  const transportadoraId = localStorage.getItem("transportadora_id");
  const pickupPointId = localStorage.getItem("pickup_point_id");
  const pickupPointName = localStorage.getItem("pickup_point_name");
  const pickupPointAddress = localStorage.getItem("pickup_point_address");

  if (!transportadoraId) {
    Swal.fire({
      icon: "error",
      title: "Erro",
      text: "Selecione uma transportadora.",
    });
    return;
  }

  // Mostrar loading
  Swal.fire({
    title: "A processar...",
    text: "Por favor aguarde enquanto processamos o seu pedido.",
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    },
  });

  const form = document.createElement("form");
  form.method = "POST";
  form.action = "checkout_stripe.php";

  const fields = {
    ...shippingInfo,
    transportadora_id: transportadoraId,
  };

  // Adicionar dados do pickup point se aplicável
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

// Logout
function logout() {
  let dados = new FormData();
  dados.append("op", 2);

  $.ajax({
    url: "src/controller/controllerPerfil.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  }).done(function (msg) {
    sessionStorage.clear();
    localStorage.clear();
    Swal.fire({
      icon: "success",
      title: "Sessão Encerrada",
      text: msg,
      timer: 2000,
      showConfirmButton: false,
    }).then(() => {
      window.location.href = "index.html";
    });
  });
}

// Atualizar resumo do pedido no sidebar
function updateOrderSummary() {
  const shippingInfo = JSON.parse(localStorage.getItem("shippingInfo") || "{}");
  const transportadoraId = localStorage.getItem("transportadora_id");
  const transportadoraPrice = parseFloat(
    localStorage.getItem("transportadora_price") || 0
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

  // Produtos do carrinho
  $.ajax({
    url: "src/controller/controllerCarrinho.php",
    method: "POST",
    data: { op: 10 },
    dataType: "json",
    async: false, // Síncrono para popular imediatamente
  }).done(function (data) {
    if (data && data.produtos && data.produtos.length > 0) {
      html += `<div class="mb-3"><strong>Produtos (${data.produtos.length})</strong></div>`;

      data.produtos.forEach((item) => {
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
          <strong>${parseFloat(data.total)
            .toFixed(2)
            .replace(".", ",")} €</strong>
        </div>
      `;

      // Shipping info se disponível
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

      // Transportadora se selecionada
      if (transportadoraId) {
        html += `
          <div class="mt-3 pt-3 border-top">
            <strong class="d-block mb-2"><i class="bi bi-truck"></i> Envio</strong>
            <small class="text-muted">${
              transportadoraNomes[transportadoraId] || "N/A"
            }</small>
            ${
              pickupPointName
                ? `<br><small class="text-muted">📍 ${pickupPointName}</small>`
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

        // Total final
        const total = parseFloat(data.total) + transportadoraPrice;
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

// Pre-preencher formulário com dados salvos em localStorage
function loadShippingInfo() {
  const shippingInfo = JSON.parse(localStorage.getItem("shippingInfo") || "{}");

  if (shippingInfo.firstName) {
    $("#firstName").val(shippingInfo.firstName);
    $("#lastName").val(shippingInfo.lastName);
    $("#address1").val(shippingInfo.address1);
    $("#address2").val(shippingInfo.address2 || "");
    $("#city").val(shippingInfo.city);
    $("#zipCode").val(shippingInfo.zipCode);
    $("#state").val(shippingInfo.state);
  }
}

$(function () {
  // Inicializar steps
  showStep(1);
  loadUserName();
  loadCarrinho();
  loadShippingInfo(); // Pre-preencher formulário se já existem dados
  updateOrderSummary(); // Popular sidebar desde o início

  // Event listener para o formulário de entrega
  $("#shippingForm").on("submit", function (e) {
    e.preventDefault();
    if (validateStep(2)) {
      nextStep();
    }
  });

  // Adicionar navegação por teclado nas transportadoras
  $(".transportadora-card").on("keypress", function (e) {
    if (e.which === 13 || e.which === 32) {
      // Enter ou Space
      e.preventDefault();
      $(this).click();
    }
  });
});

// ==================== LEAFLET + OPENSTREETMAP PICKUP POINT FUNCTIONALITY ====================

let pickupMap = null;
let pickupMarkers = [];
let userLocation = null;

// Dados dos pontos de recolha CTT e DPD em Portugal
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
      name: "CTT Saldanha",
      address: "Av. Fontes Pereira de Melo, 6, 1050-121 Lisboa",
      lat: 38.7332,
      lng: -9.1448,
      hours: "Seg-Sex: 09:00-20:00, Sáb: 09:00-14:00",
      phone: "+351 21 345 6789",
    },
    {
      id: 4,
      name: "CTT Cascais",
      address: "Rua Frederico Arouca, 77, 2750-357 Cascais",
      lat: 38.6979,
      lng: -9.4213,
      hours: "Seg-Sex: 09:00-19:00, Sáb: 09:00-13:00",
      phone: "+351 21 456 7890",
    },
  ],
  dpd_pickup: [
    {
      id: 5,
      name: "DPD Lisboa Parque",
      address: "Av. Eng. Duarte Pacheco, Amoreiras, 1070-103 Lisboa",
      lat: 38.7253,
      lng: -9.1534,
      hours: "Seg-Dom: 08:00-20:00",
      phone: "+351 21 567 8901",
    },
    {
      id: 6,
      name: "DPD Centro Colombo",
      address: "Av. Lusíada, Centro Colombo, 1500-392 Lisboa",
      lat: 38.7568,
      lng: -9.1952,
      hours: "Seg-Dom: 10:00-22:00",
      phone: "+351 21 678 9012",
    },
    {
      id: 7,
      name: "DPD Alameda",
      address: "Av. Almirante Reis, 59, 1150-011 Lisboa",
      lat: 38.7358,
      lng: -9.1378,
      hours: "Seg-Sex: 09:00-19:00, Sáb: 09:00-14:00",
      phone: "+351 21 789 0123",
    },
    {
      id: 8,
      name: "DPD Sintra",
      address: "Rua Dr. Alfredo Costa, 15, 2710-524 Sintra",
      lat: 38.7989,
      lng: -9.3878,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 21 890 1234",
    },
  ],
};

function showPickupMap(transportadoraId) {
  // Criar container do mapa se não existir
  let mapContainer = $("#pickupMapContainer");
  if (mapContainer.length === 0) {
    const mapHTML = `
      <div id="pickupMapContainer" class="pickup-map-container">
        <div class="pickup-header">
          <h4><i class="bi bi-geo-alt-fill"></i> Escolha o Ponto de Recolha</h4>
          <p class="pickup-subtitle">Selecione o ponto mais próximo de si</p>
        </div>
        <div id="pickupMap" class="pickup-map"></div>
        <div id="pickupPointsList" class="pickup-points-list"></div>
      </div>
    `;
    $(".transportadora-options").after(mapHTML);
  } else {
    mapContainer.show();
  }

  // Inicializar mapa
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
  const points = pickupPoints[transportadoraId] || [];

  if (points.length === 0) {
    console.error("Nenhum ponto de recolha encontrado para:", transportadoraId);
    return;
  }

  // Verificar se Leaflet está disponível
  if (typeof L === "undefined") {
    console.error("Leaflet não carregado");
    Swal.fire({
      icon: "warning",
      title: "Mapa Indisponível",
      text: "Use a lista abaixo para selecionar um ponto de recolha",
      timer: 3000,
    });
    renderPickupPointsList(points);
    return;
  }

  // Calcular centro do mapa
  const centerLat = points.reduce((sum, p) => sum + p.lat, 0) / points.length;
  const centerLng = points.reduce((sum, p) => sum + p.lng, 0) / points.length;

  // Remover mapa anterior se existir
  if (pickupMap) {
    pickupMap.remove();
  }

  // Inicializar Leaflet map
  pickupMap = L.map("pickupMap").setView([centerLat, centerLng], 12);

  // Adicionar tile layer do OpenStreetMap
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution:
      '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    maxZoom: 19,
  }).addTo(pickupMap);

  // Limpar marcadores anteriores
  pickupMarkers.forEach((marker) => marker.remove());
  pickupMarkers = [];

  // Ícone customizado verde
  const greenIcon = L.divIcon({
    className: "custom-leaflet-marker",
    html: '<div class="marker-pin-green"><i class="bi bi-geo-alt-fill"></i></div>',
    iconSize: [30, 42],
    iconAnchor: [15, 42],
    popupAnchor: [0, -42],
  });

  // Adicionar marcadores
  points.forEach((point) => {
    const marker = L.marker([point.lat, point.lng], { icon: greenIcon }).addTo(
      pickupMap
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
    });
    pickupMarkers.push(marker);
  });

  // Renderizar lista de pontos
  renderPickupPointsList(points);

  // Tentar obter localização do usuário
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        userLocation = {
          lat: position.coords.latitude,
          lng: position.coords.longitude,
        };

        // Ícone azul para o usuário
        const userIcon = L.divIcon({
          className: "custom-user-marker",
          html: '<div class="marker-pin-blue"><i class="bi bi-person-fill"></i></div>',
          iconSize: [25, 35],
          iconAnchor: [12, 35],
        });

        // Adicionar marcador do usuário
        L.marker([userLocation.lat, userLocation.lng], { icon: userIcon })
          .addTo(pickupMap)
          .bindPopup("<strong>A sua localização</strong>");

        // Ordenar pontos por distância
        const pointsWithDistance = calculateDistances(points, userLocation);
        renderPickupPointsList(pointsWithDistance);

        // Ajustar bounds do mapa
        const allLats = [...points.map((p) => p.lat), userLocation.lat];
        const allLngs = [...points.map((p) => p.lng), userLocation.lng];
        const bounds = L.latLngBounds(
          [Math.min(...allLats), Math.min(...allLngs)],
          [Math.max(...allLats), Math.max(...allLngs)]
        );
        pickupMap.fitBounds(bounds, { padding: [50, 50] });
      },
      (error) => {
        console.log("Geolocalização não disponível:", error);
        renderPickupPointsList(points);
      }
    );
  } else {
    renderPickupPointsList(points);
  }
}

function calculateDistances(points, userLoc) {
  return points
    .map((point) => {
      const distance = getDistanceInKm(
        userLoc.lat,
        userLoc.lng,
        point.lat,
        point.lng
      );
      return { ...point, distance };
    })
    .sort((a, b) => a.distance - b.distance);
}

function getDistanceInKm(lat1, lon1, lat2, lon2) {
  const R = 6371; // Raio da Terra em km
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
    <div class="pickup-point-item" onclick="focusPickupPoint(${point.lat}, ${
        point.lng
      })">
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
                1
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
  `
    )
    .join("");

  $("#pickupPointsList").html(html);
}

function focusPickupPoint(lat, lng) {
  if (pickupMap) {
    pickupMap.setView([lat, lng], 15);

    // Abrir popup do marcador correspondente
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

  updateOrderSummary(); // Atualizar resumo com pickup point

  Swal.fire({
    icon: "success",
    title: "Ponto de Recolha Selecionado",
    html: `<strong>${name}</strong><br><small>${address}</small>`,
    timer: 2000,
    showConfirmButton: false,
  });

  // Destacar ponto selecionado
  $(".pickup-point-item").removeClass("selected");
  $(".pickup-point-item").each(function () {
    if ($(this).find("h5").text() === name) {
      $(this).addClass("selected");
    }
  });
}
