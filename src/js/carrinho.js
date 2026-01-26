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
            "success",
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
          '<p class="text-center text-muted py-4">O carrinho está vazio.</p>',
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
    $("#qty-" + produtoId).data("original-qty") || $("#qty-" + produtoId).val(),
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
                          item.preco,
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

  // Scroll suave para o topo do step - DESATIVADO para evitar scroll no carregamento inicial
  // setTimeout(() => {
  //   $(".steps-progress-container")[0]?.scrollIntoView({
  //     behavior: "smooth",
  //     block: "start",
  //   });
}

// Ir para próximo step
function nextStep() {
  if (currentStep < totalSteps) {
    if (validateStep(currentStep)) {
      // Se indo para step 2 (entrega), preencher dados do utilizador
      if (currentStep === 1) {
        preencherDadosUtilizador();
      }
      showStep(currentStep + 1, true);
    }
  }
}

// Preencher dados do utilizador automaticamente
function preencherDadosUtilizador() {
  $.ajax({
    url: "src/controller/controllerPerfil.php",
    method: "POST",
    data: { op: 12 },
    dataType: "json",
    success: function (response) {
      if (response.success && response.data) {
        const dados = response.data;

        // Preencher nome e apelido
        if (dados.nome) {
          const nomeCompleto = dados.nome.split(" ");
          $("#firstName").val(nomeCompleto[0] || "");
          $("#lastName").val(nomeCompleto.slice(1).join(" ") || "");
        }

        // Preencher morada se disponível
        if (dados.morada && dados.morada !== "Morada não cadastrada") {
          // Tentar extrair partes da morada
          const moradaParts = dados.morada.split(",");
          if (moradaParts.length > 0) {
            $("#address1").val(moradaParts[0].trim());
          }
          if (moradaParts.length > 1) {
            $("#address2").val(moradaParts[1].trim());
          }

          // Tentar extrair código postal (formato XXXX-XXX)
          const zipMatch = dados.morada.match(/\d{4}-\d{3}/);
          if (zipMatch) {
            $("#zipCode").val(zipMatch[0]);
          }

          // Tentar extrair cidade
          const cidadeMatch = dados.morada.match(
            /[A-ZÀ-Ú][a-zà-ú]+(?:\s+[A-ZÀ-Ú][a-zà-ú]+)*/,
          );
          if (cidadeMatch) {
            $("#city").val(cidadeMatch[0]);
            $("#state").val(cidadeMatch[0]);
          }
        }
      }
    },
    error: function (xhr, status, error) {
      console.log("Erro ao carregar dados do utilizador:", error);
    },
  });
}

// Voltar step anterior
function previousStep() {
  if (currentStep > 1) {
    showStep(currentStep - 1, true);
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
              transportadoraPrice || 0,
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
                              2,
                            )}€</strong>
                        </div>
                    </div>
                `;
      });

      const transportadoraPrice = parseFloat(
        localStorage.getItem("transportadora_price") || 0,
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
                          2,
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
  Swal.fire({
    html: `
      <div style="padding: 20px 0;">
        <div style="width: 70px; height: 70px; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
          <i class="fas fa-sign-out-alt" style="font-size: 32px; color: #dc2626;"></i>
        </div>
        <h2 style="margin: 0 0 12px 0; color: #1e293b; font-size: 24px; font-weight: 700;">Terminar Sessao?</h2>
        <p style="margin: 0; color: #64748b; font-size: 15px; line-height: 1.6;">Tem a certeza que pretende sair da sua conta?</p>
      </div>
    `,
    showCancelButton: true,
    confirmButtonText: '<i class="fas fa-sign-out-alt"></i> Sim, sair',
    cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
    confirmButtonColor: "#dc2626",
    cancelButtonColor: "#64748b",
    customClass: {
      confirmButton: "swal2-confirm-modern",
      cancelButton: "swal2-cancel-modern",
      popup: "swal2-logout-popup",
    },
    buttonsStyling: false,
    reverseButtons: true,
    didOpen: () => {
      const popup = Swal.getPopup();
      popup.style.borderRadius = "16px";
      popup.style.padding = "25px";

      const confirmBtn = popup.querySelector(".swal2-confirm");
      const cancelBtn = popup.querySelector(".swal2-cancel");

      if (confirmBtn) {
        confirmBtn.style.padding = "12px 28px";
        confirmBtn.style.borderRadius = "10px";
        confirmBtn.style.fontSize = "15px";
        confirmBtn.style.fontWeight = "600";
        confirmBtn.style.border = "none";
        confirmBtn.style.cursor = "pointer";
        confirmBtn.style.transition = "all 0.3s ease";
        confirmBtn.style.backgroundColor = "#dc2626";
        confirmBtn.style.color = "#ffffff";
        confirmBtn.style.marginLeft = "10px";
      }

      if (cancelBtn) {
        cancelBtn.style.padding = "12px 28px";
        cancelBtn.style.borderRadius = "10px";
        cancelBtn.style.fontSize = "15px";
        cancelBtn.style.fontWeight = "600";
        cancelBtn.style.border = "2px solid #e2e8f0";
        cancelBtn.style.cursor = "pointer";
        cancelBtn.style.transition = "all 0.3s ease";
        cancelBtn.style.backgroundColor = "#ffffff";
        cancelBtn.style.color = "#64748b";
      }
    },
  }).then((result) => {
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
      window.location.href = "src/controller/controllerPerfil.php?op=2";
    }
  });
}

// Atualizar resumo do pedido no sidebar
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
  console.log("Carrinho.js iniciado");
  console.log("Bootstrap disponível:", typeof bootstrap !== "undefined");

  // Inicializar Bootstrap Dropdowns manualmente
  if (typeof bootstrap !== "undefined") {
    var dropdownElementList = [].slice.call(
      document.querySelectorAll('[data-bs-toggle="dropdown"]'),
    );
    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
      return new bootstrap.Dropdown(dropdownToggleEl);
    });
    console.log("Dropdowns Bootstrap inicializados:", dropdownList.length);
  }

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

// Função para extrair cidade do ponto de recolha
function getCityFromPickupPoint(point) {
  if (point.city) return point.city;

  // Mapa de cidades baseado em nomes e endereços
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

// Dados dos pontos de recolha CTT e DPD em Portugal (principais cidades)
const pickupPoints = {
  ctt_pickup: [
    // LISBOA E ARREDORES
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
    // PORTO E ARREDORES
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
    // BRAGA
    {
      id: 8,
      name: "CTT Braga",
      address: "Av. da Liberdade, 658, 4710-251 Braga",
      lat: 41.5454,
      lng: -8.4265,
      hours: "Seg-Sex: 09:00-19:00, Sáb: 09:00-13:00",
      phone: "+351 253 203 500",
    },
    // COIMBRA
    {
      id: 9,
      name: "CTT Coimbra",
      address: "Av. Fernão de Magalhães, 223, 3000-175 Coimbra",
      lat: 40.2111,
      lng: -8.4291,
      hours: "Seg-Sex: 09:00-19:00, Sáb: 09:00-13:00",
      phone: "+351 239 851 700",
    },
    // AVEIRO
    {
      id: 10,
      name: "CTT Aveiro",
      address: "Praça Marquês de Pombal, 11, 3800-176 Aveiro",
      lat: 40.6412,
      lng: -8.6537,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 234 377 800",
    },
    // VISEU
    {
      id: 11,
      name: "CTT Viseu",
      address: "Rua Formosa, 83, 3500-139 Viseu",
      lat: 40.6573,
      lng: -7.9138,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 232 480 900",
    },
    // SETÚBAL
    {
      id: 12,
      name: "CTT Setúbal",
      address: "Av. Luísa Todi, 390, 2900-456 Setúbal",
      lat: 38.5244,
      lng: -8.8926,
      hours: "Seg-Sex: 09:00-19:00, Sáb: 09:00-13:00",
      phone: "+351 265 541 100",
    },
    // ÉVORA
    {
      id: 13,
      name: "CTT Évora",
      address: "Rua de Olivença, 82, 7000-849 Évora",
      lat: 38.5713,
      lng: -7.9067,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 266 748 200",
    },
    // FARO E ALGARVE
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
    // LEIRIA
    {
      id: 17,
      name: "CTT Leiria",
      address: "Av. Heróis de Angola, 2400-185 Leiria",
      lat: 39.7437,
      lng: -8.8071,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 244 839 600",
    },
    // SANTARÉM
    {
      id: 18,
      name: "CTT Santarém",
      address: "Rua Pedro de Santarém, 71, 2005-247 Santarém",
      lat: 39.2369,
      lng: -8.6867,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 243 309 700",
    },
    // GUIMARÃES
    {
      id: 19,
      name: "CTT Guimarães",
      address: "Alameda de São Dâmaso, 4810-286 Guimarães",
      lat: 41.4416,
      lng: -8.2918,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 253 540 100",
    },
    // VIANA DO CASTELO
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
    // VILA REAL
    {
      id: 21,
      name: "CTT Vila Real",
      address: "Av. Carvalho Araújo, 5000-657 Vila Real",
      lat: 41.3007,
      lng: -7.7437,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 259 309 300",
    },
    // BRAGANÇA
    {
      id: 22,
      name: "CTT Bragança",
      address: "Av. Sá Carneiro, 5300-252 Bragança",
      lat: 41.806,
      lng: -6.757,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 273 309 400",
    },
    // GUARDA
    {
      id: 23,
      name: "CTT Guarda",
      address: "Rua Batalha Reis, 6300-738 Guarda",
      lat: 40.5375,
      lng: -7.2681,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 271 205 500",
    },
    // CASTELO BRANCO
    {
      id: 24,
      name: "CTT Castelo Branco",
      address: "Rua da Piscina, 6000-459 Castelo Branco",
      lat: 39.8208,
      lng: -7.4916,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 272 348 600",
    },
    // PORTALEGRE
    {
      id: 25,
      name: "CTT Portalegre",
      address: "Rua 19 de Junho, 7300-211 Portalegre",
      lat: 39.2967,
      lng: -7.4281,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 245 307 700",
    },
    // BEJA
    {
      id: 26,
      name: "CTT Beja",
      address: "Rua de Évora, 7800-451 Beja",
      lat: 38.015,
      lng: -7.8632,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 284 311 800",
    },
    // FUNCHAL (MADEIRA)
    {
      id: 27,
      name: "CTT Funchal",
      address: "Av. Zarco, 9000-069 Funchal",
      lat: 32.6492,
      lng: -16.9093,
      hours: "Seg-Sex: 09:00-19:00, Sáb: 09:00-13:00",
      phone: "+351 291 214 900",
    },
    // PONTA DELGADA (AÇORES)
    {
      id: 28,
      name: "CTT Ponta Delgada",
      address: "Rua Ernesto do Canto, 9500-318 Ponta Delgada",
      lat: 37.7412,
      lng: -25.6756,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 296 209 100",
    },
    // FIGUEIRA DA FOZ
    {
      id: 29,
      name: "CTT Figueira da Foz",
      address: "Rua 5 de Outubro, 3080-169 Figueira da Foz",
      lat: 40.1508,
      lng: -8.8618,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 233 407 200",
    },
    // CALDAS DA RAINHA
    {
      id: 30,
      name: "CTT Caldas da Rainha",
      address: "Praça 25 de Abril, 2500-116 Caldas da Rainha",
      lat: 39.4034,
      lng: -9.1372,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 262 839 300",
    },
    // TORRES VEDRAS
    {
      id: 31,
      name: "CTT Torres Vedras",
      address: "Praça 25 de Abril, 2560-280 Torres Vedras",
      lat: 39.0908,
      lng: -9.2589,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 261 310 400",
    },
    // SINTRA
    {
      id: 32,
      name: "CTT Sintra",
      address: "Praça da República, 2710-616 Sintra",
      lat: 38.7989,
      lng: -9.3878,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 219 247 500",
    },
    // OEIRAS
    {
      id: 33,
      name: "CTT Oeiras",
      address: "Rua Cândido dos Reis, 2780-229 Oeiras",
      lat: 38.6913,
      lng: -9.3106,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 214 408 600",
    },
    // PÓVOA DE VARZIM
    {
      id: 34,
      name: "CTT Póvoa de Varzim",
      address: "Praça do Almada, 4490-438 Póvoa de Varzim",
      lat: 41.3833,
      lng: -8.7606,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 252 615 700",
    },
    // BARCELOS
    {
      id: 35,
      name: "CTT Barcelos",
      address: "Av. Dr. Sidónio Pais, 4750-333 Barcelos",
      lat: 41.5311,
      lng: -8.6157,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 253 809 800",
    },
    // ESPINHO
    {
      id: 36,
      name: "CTT Espinho",
      address: "Rua 19, 4500-258 Espinho",
      lat: 41.0078,
      lng: -8.6411,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 227 331 900",
    },
    // OLHÃO
    {
      id: 37,
      name: "CTT Olhão",
      address: "Av. da República, 8700-307 Olhão",
      lat: 37.0263,
      lng: -7.8408,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 289 702 100",
    },
    // TAVIRA
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
    // LISBOA E ARREDORES
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
    // PORTO E ARREDORES
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
    // BRAGA
    {
      id: 46,
      name: "DPD Braga Parque",
      address: "Quinta dos Congregados, 4710-427 Braga",
      lat: 41.562,
      lng: -8.3963,
      hours: "Seg-Dom: 10:00-23:00",
      phone: "+351 253 680 700",
    },
    // COIMBRA
    {
      id: 47,
      name: "DPD Coimbra Shopping",
      address: "Av. Dr. Mendes Silva, 3030-290 Coimbra",
      lat: 40.2034,
      lng: -8.4122,
      hours: "Seg-Dom: 09:00-23:00",
      phone: "+351 239 497 800",
    },
    // AVEIRO
    {
      id: 48,
      name: "DPD Aveiro Centro",
      address: "Rua Batalhão de Caçadores, 3810-064 Aveiro",
      lat: 40.6443,
      lng: -8.6455,
      hours: "Seg-Sex: 09:00-19:00, Sáb: 09:00-14:00",
      phone: "+351 234 098 900",
    },
    // VISEU
    {
      id: 49,
      name: "DPD Viseu",
      address: "Estrada Nacional 2, 3510-021 Viseu",
      lat: 40.6651,
      lng: -7.9201,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 232 419 100",
    },
    // SETÚBAL
    {
      id: 50,
      name: "DPD Setúbal",
      address: "Av. Bento Gonçalves, 2910-414 Setúbal",
      lat: 38.5317,
      lng: -8.8843,
      hours: "Seg-Sex: 09:00-19:00, Sáb: 09:00-14:00",
      phone: "+351 265 709 200",
    },
    // ÉVORA
    {
      id: 51,
      name: "DPD Évora",
      address: "Rua Vasco da Gama, 7005-841 Évora",
      lat: 38.5658,
      lng: -7.9091,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 266 760 300",
    },
    // FARO E ALGARVE
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
    // LEIRIA
    {
      id: 55,
      name: "DPD Leiria Shopping",
      address: "Rua de Tomar, 2400-441 Leiria",
      lat: 39.7392,
      lng: -8.8159,
      hours: "Seg-Dom: 09:00-23:00",
      phone: "+351 244 870 700",
    },
    // SANTARÉM
    {
      id: 56,
      name: "DPD Santarém",
      address: "Av. Bernardo Santareno, 2005-177 Santarém",
      lat: 39.2414,
      lng: -8.679,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 243 330 800",
    },
    // GUIMARÃES
    {
      id: 57,
      name: "DPD Guimarães Shopping",
      address: "Rua 25 de Abril, 4835-400 Creixomil",
      lat: 41.4498,
      lng: -8.2868,
      hours: "Seg-Dom: 10:00-23:00",
      phone: "+351 253 510 200",
    },
    // VIANA DO CASTELO
    {
      id: 58,
      name: "DPD Viana Shopping",
      address: "Meadela, 4900-727 Viana do Castelo",
      lat: 41.708,
      lng: -8.8165,
      hours: "Seg-Dom: 10:00-23:00",
      phone: "+351 258 820 300",
    },
    // VILA REAL
    {
      id: 59,
      name: "DPD Vila Real",
      address: "Av. 1º de Maio, 5000-651 Vila Real",
      lat: 41.2976,
      lng: -7.7456,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 259 320 400",
    },
    // BRAGANÇA
    {
      id: 60,
      name: "DPD Bragança",
      address: "Av. Cidade de León, 5300-123 Bragança",
      lat: 41.8089,
      lng: -6.7503,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 273 320 500",
    },
    // GUARDA
    {
      id: 61,
      name: "DPD Guarda",
      address: "Rua Coronel Orlindo de Carvalho, 6300-532 Guarda",
      lat: 40.5401,
      lng: -7.2712,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 271 220 600",
    },
    // CASTELO BRANCO
    {
      id: 62,
      name: "DPD Castelo Branco",
      address: "Av. Nuno Álvares, 6000-083 Castelo Branco",
      lat: 39.8183,
      lng: -7.4936,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 272 320 700",
    },
    // PORTALEGRE
    {
      id: 63,
      name: "DPD Portalegre",
      address: "Rossio, 7300-110 Portalegre",
      lat: 39.2942,
      lng: -7.4298,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 245 330 800",
    },
    // BEJA
    {
      id: 64,
      name: "DPD Beja",
      address: "Rua Luís de Camões, 7800-440 Beja",
      lat: 38.0172,
      lng: -7.8651,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 284 320 900",
    },
    // FUNCHAL (MADEIRA)
    {
      id: 65,
      name: "DPD Funchal Madeira Shopping",
      address: "Caminho de Santa Quitéria, 9020-069 Funchal",
      lat: 32.6595,
      lng: -16.9242,
      hours: "Seg-Dom: 10:00-23:00",
      phone: "+351 291 230 100",
    },
    // PONTA DELGADA (AÇORES)
    {
      id: 66,
      name: "DPD Ponta Delgada",
      address: "Av. Infante D. Henrique, 9500-764 Ponta Delgada",
      lat: 37.7394,
      lng: -25.665,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 296 280 200",
    },
    // FIGUEIRA DA FOZ
    {
      id: 67,
      name: "DPD Figueira da Foz",
      address: "Rua Dr. Calado, 3080-161 Figueira da Foz",
      lat: 40.1476,
      lng: -8.8639,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 233 430 300",
    },
    // CALDAS DA RAINHA
    {
      id: 68,
      name: "DPD Caldas da Rainha",
      address: "Rua Almirante Cândido dos Reis, 2500-152 Caldas da Rainha",
      lat: 39.4018,
      lng: -9.1352,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 262 840 400",
    },
    // TORRES VEDRAS
    {
      id: 69,
      name: "DPD Torres Vedras",
      address: "Rua Paiva de Andrade, 2560-337 Torres Vedras",
      lat: 39.0915,
      lng: -9.2577,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 261 320 500",
    },
    // SINTRA
    {
      id: 70,
      name: "DPD Sintra",
      address: "Rua Dr. Alfredo Costa, 15, 2710-524 Sintra",
      lat: 38.7989,
      lng: -9.3878,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-12:00",
      phone: "+351 21 890 1234",
    },
    // OEIRAS
    {
      id: 71,
      name: "DPD Oeiras Parque",
      address: "Av. António Bernardo Cabral de Macedo, 2770-219 Paço de Arcos",
      lat: 38.6921,
      lng: -9.2944,
      hours: "Seg-Dom: 10:00-23:00",
      phone: "+351 214 405 600",
    },
    // PÓVOA DE VARZIM
    {
      id: 72,
      name: "DPD Póvoa de Varzim",
      address: "Rua Gomes de Amorim, 4490-665 Póvoa de Varzim",
      lat: 41.3872,
      lng: -8.7638,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 252 690 700",
    },
    // BARCELOS
    {
      id: 73,
      name: "DPD Barcelos",
      address: "Av. da Liberdade, 4750-287 Barcelos",
      lat: 41.5336,
      lng: -8.6183,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 253 820 800",
    },
    // ESPINHO
    {
      id: 74,
      name: "DPD Espinho",
      address: "Rua 37, 4500-904 Espinho",
      lat: 41.0089,
      lng: -8.6392,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 227 340 900",
    },
    // OLHÃO
    {
      id: 75,
      name: "DPD Olhão",
      address: "Av. 5 de Outubro, 8700-304 Olhão",
      lat: 37.0281,
      lng: -7.8392,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 289 710 100",
    },
    // TAVIRA
    {
      id: 76,
      name: "DPD Tavira",
      address: "Rua Almirante Cândido dos Reis, 8800-355 Tavira",
      lat: 37.1289,
      lng: -7.6512,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 281 325 200",
    },
    // VILA REAL DE SANTO ANTÓNIO
    {
      id: 77,
      name: "DPD Vila Real de Santo António",
      address: "Av. da República, 8900-209 Vila Real de Santo António",
      lat: 37.1934,
      lng: -7.4155,
      hours: "Seg-Sex: 09:00-18:00, Sáb: 09:00-13:00",
      phone: "+351 281 510 300",
    },
    // LOULÉ
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
  // Criar container do mapa se não existir
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
    // Atualizar subtítulo com cidade filtrada
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
  let points = pickupPoints[transportadoraId] || [];

  if (points.length === 0) {
    console.error("Nenhum ponto de recolha encontrado para:", transportadoraId);
    return;
  }

  // Filtrar por cidade selecionada
  const shippingInfo = JSON.parse(localStorage.getItem("shippingInfo") || "{}");
  const selectedCity = shippingInfo.state; // state contém a cidade selecionada

  if (selectedCity) {
    // Filtrar pontos pela cidade usando a função getCityFromPickupPoint
    const filteredPoints = points.filter((p) => {
      const pointCity = getCityFromPickupPoint(p);
      return pointCity === selectedCity;
    });

    if (filteredPoints.length > 0) {
      points = filteredPoints;
      console.log(
        `Filtrados ${filteredPoints.length} pontos para ${selectedCity}`,
      );
    } else {
      // Se não encontrar pontos para a cidade, mostrar aviso
      Swal.fire({
        icon: "info",
        title: "Pontos de Recolha",
        html: `Não há pontos de recolha em <strong>${selectedCity}</strong>.<br><small>A mostrar pontos de todas as localidades disponíveis.</small>`,
        timer: 3000,
        showConfirmButton: false,
      });
      console.log(
        `Nenhum ponto encontrado para ${selectedCity}. Mostrando todos.`,
      );
    }
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
          [Math.max(...allLats), Math.max(...allLngs)],
        );
        pickupMap.fitBounds(bounds, { padding: [50, 50] });
      },
      (error) => {
        console.log("Geolocalização não disponível:", error);
        renderPickupPointsList(points);
      },
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
        point.lng,
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
