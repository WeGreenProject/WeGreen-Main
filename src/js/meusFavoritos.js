let favoritosList = [];

function carregarFavoritos() {
  $.ajax({
    url: "src/controller/controllerFavoritos.php",
    method: "GET",
    data: {
      op: 3,
    },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        favoritosList = response.data;
        renderizarFavoritos(favoritosList);
        $("#totalFavoritos").text(
          `${response.total} produto${response.total !== 1 ? "s" : ""}`,
        );
        $("#sidebarFavCount")
          .text(response.total)
          .toggle(response.total > 0);
      }
    },
    error: function () {
      showModernErrorModal("Erro", "Erro ao carregar favoritos.");
    },
  });
}

function renderizarFavoritos(favoritos) {
  if (favoritos.length === 0) {
    mostrarMensagemVazio();
    return;
  }

  let html = "";
  favoritos.forEach((fav) => {
    const disponivel = fav.ativo == 1 && fav.stock > 0;
    const dataFormatada = new Date(fav.data_adicao).toLocaleDateString("pt-PT");

    html += `
                    <div class="produto-favorito-card" id="produto-${fav.produto_id}">
                        <a href="produto.php?id=${fav.produto_id}" style="text-decoration: none; color: inherit;">
                            <img src="${fav.foto}" alt="${fav.nome}" class="produto-foto" onerror="this.src='src/img/placeholder-produto.jpg'">
                            <div class="produto-info">
                                <h3 class="produto-nome">${fav.nome}</h3>
                                <div class="produto-preco">€${parseFloat(fav.preco).toFixed(2)}</div>
                                <div class="produto-detalhes">
                                    <i class="fas fa-tag"></i> ${fav.marca || "Sem marca"} |
                                    <i class="fas fa-tshirt"></i> ${fav.tamanho || "Único"}
                                </div>
                                <div class="produto-detalhes">
                                    <i class="fas fa-store"></i> ${fav.anunciante_nome}
                                </div>
                                <span class="produto-status ${disponivel ? "status-disponivel" : "status-indisponivel"}">
                                    ${disponivel ? "✓ Disponível" : "✗ Indisponível"}
                                </span>
                            </div>
                        </a>
                        <div class="produto-info" style="padding-top: 0;">
                            <div class="produto-acoes">
                                ${
                                  disponivel
                                    ? `
                                    <button class="btn-action btn-carrinho" onclick="event.stopPropagation(); adicionarCarrinho(${fav.produto_id})">
                                        <i class="fas fa-shopping-cart"></i> Comprar
                                    </button>
                                `
                                    : ""
                                }
                                <button class="btn-action btn-remover" onclick="event.stopPropagation(); removerFavorito(${fav.produto_id}, this)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="data-adicao">Adicionado em ${dataFormatada}</div>
                        </div>
                    </div>
                `;
  });

  $("#favoritosGrid").html(html);
}

function mostrarModalSucessoCarrinho(texto) {
  showModernSuccessModal("Sucesso!", texto, { timer: 2000 });
}

function adicionarCarrinho(produtoId) {
  $.ajax({
    url: "src/controller/controllerCarrinho.php",
    method: "POST",
    data: {
      op: 7,
      produto_id: produtoId,
    },
    success: function (response) {
      let obj = response;
      if (typeof response === "string") {
        try {
          obj = JSON.parse(response);
        } catch (e) {
          obj = null;
        }
      }
      if (obj && obj.flag === false) {
        showModernErrorModal(
          "Erro!",
          obj.msg || "Não foi possível adicionar o produto ao carrinho",
        );
      } else {
        mostrarModalSucessoCarrinho("Produto adicionado ao carrinho");
      }
    },
    error: function () {
      showModernErrorModal(
        "Erro",
        "Não foi possível adicionar o produto ao carrinho.",
      );
    },
  });
}

function limparInativos() {
  showModernConfirmModal(
    "Limpar produtos inativos?",
    "Produtos que não estão mais disponíveis serão removidos dos favoritos.",
    {
      confirmText: '<i class="fas fa-broom"></i> Sim, limpar',
      icon: "fa-broom",
      iconBg: "background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);",
    },
  ).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: "src/controller/controllerFavoritos.php",
        method: "POST",
        data: {
          op: 6,
        },
        dataType: "json",
        success: function (response) {
          if (response.success) {
            showModernSuccessModal("Concluído!", response.message, {
              timer: 2000,
            });
            carregarFavoritos();
          }
        },
      });
    }
  });
}

$("#searchFavoritos").on("keyup", aplicarFiltros);
$("#filterCategoria, #filterDisponibilidade").on("change", aplicarFiltros);

function aplicarFiltros() {
  const search = $("#searchFavoritos").val().toLowerCase();
  const categoria = $("#filterCategoria").val();
  const disponibilidade = $("#filterDisponibilidade").val();

  const filtrados = favoritosList.filter((fav) => {
    const matchSearch = fav.nome.toLowerCase().includes(search);
    const matchCategoria = !categoria || fav.categoria === categoria;
    const disponivel = fav.ativo == 1 && fav.stock > 0;
    const matchDisponibilidade =
      !disponibilidade ||
      (disponibilidade === "disponivel" && disponivel) ||
      (disponibilidade === "indisponivel" && !disponivel);

    return matchSearch && matchCategoria && matchDisponibilidade;
  });

  renderizarFavoritos(filtrados);
}

function limparFiltrosFavoritos() {
  $("#searchFavoritos").val("");
  $("#filterCategoria").val("");
  $("#filterDisponibilidade").val("");
  aplicarFiltros();
}

$(document).ready(function () {
  carregarFavoritos();

  
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
