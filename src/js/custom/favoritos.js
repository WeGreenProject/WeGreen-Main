let favoritosAcessoCliente = null;

function obterTipoUtilizadorPagina() {
  const tipoAttr =
    document.body?.getAttribute("data-user-tipo") ||
    document.body?.dataset?.userTipo ||
    "";
  const tipo = parseInt(tipoAttr, 10);
  return Number.isNaN(tipo) ? 0 : tipo;
}

function mostrarAcessoNegadoFavoritos() {
  Swal.fire({
    icon: "warning",
    title: "Acesso restrito",
    text: "Só clientes autenticados podem usar favoritos.",
    confirmButtonColor: "#f59e0b",
  });
}

function bloquearBotoesFavorito() {
  $(".btn-favorito").each(function () {
    $(this)
      .addClass("favorito-bloqueado")
      .attr("title", "Disponível apenas para clientes autenticados")
      .css({ opacity: "0.5", cursor: "not-allowed", pointerEvents: "none" });
  });
}

function verificarAcessoFavoritos(callback, opcoes = {}) {
  const mostrarModal = opcoes.mostrarModal !== false;

  if (favoritosAcessoCliente === true) {
    callback();
    return;
  }

  if (favoritosAcessoCliente === false) {
    if (mostrarModal) {
      mostrarAcessoNegadoFavoritos();
    }
    return;
  }

  const tipo = obterTipoUtilizadorPagina();
  if (tipo > 0 && tipo !== 2) {
    favoritosAcessoCliente = false;
    bloquearBotoesFavorito();
    if (mostrarModal) {
      mostrarAcessoNegadoFavoritos();
    }
    return;
  }

  $.ajax({
    url: "src/controller/controllerFavoritos.php",
    method: "GET",
    data: { op: 5 },
    dataType: "json",
    success: function (response) {
      if (response && response.success) {
        favoritosAcessoCliente = true;
        callback();
      } else {
        favoritosAcessoCliente = false;
        bloquearBotoesFavorito();
        if (mostrarModal) {
          mostrarAcessoNegadoFavoritos();
        }
      }
    },
    error: function () {
      favoritosAcessoCliente = false;
      bloquearBotoesFavorito();
      if (mostrarModal) {
        mostrarAcessoNegadoFavoritos();
      }
    },
  });
}

function toggleFavorito(produtoId, botaoElement) {
  verificarAcessoFavoritos(function () {
    const isFavorito = $(botaoElement).hasClass("favorito-ativo");

    if (isFavorito) {
      removerFavorito(produtoId, botaoElement);
    } else {
      adicionarFavorito(produtoId, botaoElement);
    }
  });
}

function mostrarModalSucessoFavoritoAdicionado(texto) {
  Swal.fire({
    html: `
      <div style="text-align: center;">
        <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: linear-gradient(135deg, #3cb371 0%, #2d8a5a 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(60, 179, 113, 0.3);">
          <i class="fas fa-check" style="font-size: 40px; color: white;"></i>
        </div>
        <h2 style="margin: 0 0 10px 0; color: #2d3748; font-size: 24px; font-weight: 700;">Sucesso!</h2>
        <p style="color: #64748b; font-size: 15px; margin: 0;">${texto}</p>
      </div>
    `,
    timer: 2000,
    timerProgressBar: true,
    showConfirmButton: true,
    confirmButtonText: "OK",
    confirmButtonColor: "#3cb371",
    customClass: {
      confirmButton: "swal2-confirm-modern-success",
      popup: "swal2-border-radius",
    },
    buttonsStyling: false,
    didOpen: () => {
      if (!document.getElementById("wegreen-favoritos-success-style")) {
        const style = document.createElement("style");
        style.id = "wegreen-favoritos-success-style";
        style.textContent = `
          .swal2-confirm-modern-success {
            padding: 12px 30px !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
            border: none !important;
            background: linear-gradient(135deg, #3cb371 0%, #2d8a5a 100%) !important;
            color: white !important;
          }
          .swal2-confirm-modern-success:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(60, 179, 113, 0.4) !important;
          }
          .swal2-border-radius {
            border-radius: 12px !important;
          }
        `;
        document.head.appendChild(style);
      }
    },
  });
}

function mostrarModalSucessoFavoritoRemovido(texto) {
  Swal.fire({
    html: `
      <div style="text-align: center;">
        <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: linear-gradient(135deg, #3cb371 0%, #2d8a5a 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(60, 179, 113, 0.3);">
          <i class="fas fa-check" style="font-size: 40px; color: white;"></i>
        </div>
        <h2 style="margin: 0 0 10px 0; color: #2d3748; font-size: 24px; font-weight: 700;">Removido!</h2>
        <p style="color: #64748b; font-size: 15px; margin: 0;">${texto}</p>
      </div>
    `,
    timer: 2000,
    timerProgressBar: true,
    showConfirmButton: true,
    confirmButtonText: "OK",
    confirmButtonColor: "#3cb371",
    customClass: {
      confirmButton: "swal2-confirm-modern-success",
      popup: "swal2-border-radius",
    },
    buttonsStyling: false,
    didOpen: () => {
      if (!document.getElementById("wegreen-favoritos-success-style")) {
        const style = document.createElement("style");
        style.id = "wegreen-favoritos-success-style";
        style.textContent = `
          .swal2-confirm-modern-success {
            padding: 12px 30px !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
            border: none !important;
            background: linear-gradient(135deg, #3cb371 0%, #2d8a5a 100%) !important;
            color: white !important;
          }
          .swal2-confirm-modern-success:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(60, 179, 113, 0.4) !important;
          }
          .swal2-border-radius {
            border-radius: 12px !important;
          }
        `;
        document.head.appendChild(style);
      }
    },
  });
}

function adicionarFavorito(produtoId, botaoElement) {
  $.ajax({
    url: "src/controller/controllerFavoritos.php",
    method: "POST",
    data: {
      op: 1,
      produto_id: produtoId,
    },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        $(botaoElement).addClass("favorito-ativo");
        $(botaoElement).find("i").removeClass("far").addClass("fas");

        mostrarModalSucessoFavoritoAdicionado(
          response.message || "Produto adicionado aos favoritos.",
        );

        atualizarContadorFavoritos();
      } else {
        Swal.fire({
          html: `
            <div style="text-align: center;">
              <div style="width: 80px; height: 80px; margin: 0 auto 20px; background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(245, 158, 11, 0.25);">
                <i class="fas fa-exclamation" style="font-size: 40px; color: #f59e0b;"></i>
              </div>
              <h2 style="margin: 0 0 10px 0; color: #2d3748; font-size: 24px; font-weight: 700;">Atenção</h2>
              <p style="color: #64748b; font-size: 15px; margin: 0;">${response.message || "Este produto já está nos seus favoritos."}</p>
            </div>
          `,
          confirmButtonText: "OK",
          customClass: {
            confirmButton: "swal2-confirm-modern-warning",
            popup: "swal2-border-radius",
          },
          buttonsStyling: false,
          didOpen: () => {
            if (!document.getElementById("wegreen-favoritos-modal-style")) {
              const style = document.createElement("style");
              style.id = "wegreen-favoritos-modal-style";
              style.textContent = `
                .swal2-confirm-modern-warning {
                  padding: 12px 30px !important;
                  border-radius: 8px !important;
                  font-weight: 600 !important;
                  font-size: 14px !important;
                  cursor: pointer !important;
                  transition: all 0.3s ease !important;
                  border: none !important;
                  margin: 5px !important;
                  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
                  color: white !important;
                }
                .swal2-confirm-modern-warning:hover {
                  transform: translateY(-2px) !important;
                  box-shadow: 0 6px 20px rgba(245, 158, 11, 0.35) !important;
                }
                .swal2-border-radius {
                  border-radius: 12px !important;
                }
              `;
              document.head.appendChild(style);
            }
          },
        });
      }
    },
    error: function () {
      Swal.fire({
        icon: "error",
        title: "Erro",
        text: "Erro ao adicionar aos favoritos. Tente novamente.",
        confirmButtonColor: "#ef4444",
      });
    },
  });
}

function removerFavorito(produtoId, botaoElement) {
  $.ajax({
    url: "src/controller/controllerFavoritos.php",
    method: "POST",
    data: {
      op: 2,
      produto_id: produtoId,
    },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        $(botaoElement).removeClass("favorito-ativo");
        $(botaoElement).find("i").removeClass("fas").addClass("far");

        if (window.location.pathname.includes("meusFavoritos.php")) {
          $(`#produto-${produtoId}`).fadeOut(400, function () {
            $(this).remove();

            if ($(".produto-favorito-card").length === 0) {
              mostrarMensagemVazio();
            }
          });
        }

        mostrarModalSucessoFavoritoRemovido(
          response.message || "Produto removido dos favoritos.",
        );

        atualizarContadorFavoritos();
      } else {
        Swal.fire({
          icon: "error",
          title: "Erro",
          text: response.message,
          confirmButtonColor: "#ef4444",
        });
      }
    },
    error: function () {
      Swal.fire({
        icon: "error",
        title: "Erro",
        text: "Erro ao remover dos favoritos. Tente novamente.",
        confirmButtonColor: "#ef4444",
      });
    },
  });
}

function verificarFavorito(produtoId, botaoElement) {
  $.ajax({
    url: "src/controller/controllerFavoritos.php",
    method: "GET",
    data: {
      op: 4,
      produto_id: produtoId,
    },
    dataType: "json",
    success: function (response) {
      if (response.success && response.isFavorito) {
        $(botaoElement).addClass("favorito-ativo");
        $(botaoElement).find("i").removeClass("far").addClass("fas");
      }
    },
  });
}

function atualizarContadorFavoritos() {
  $.ajax({
    url: "src/controller/controllerFavoritos.php",
    method: "GET",
    data: { op: 5 },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        const total = response.total;
        $("#favoritosCount").text(total);

        if (total > 0) {
          $("#favoritosCount").show();
        } else {
          $("#favoritosCount").hide();
        }
      }
    },
  });
}

function mostrarMensagemVazio() {
  const mensagemHTML = `
        <div class="empty-state" style="text-align: center; padding: 60px 20px;">
            <i class="fas fa-heart" style="font-size: 80px; color: #e0e0e0; margin-bottom: 20px;"></i>
            <h3 style="color: #666; margin-bottom: 10px;">Sua lista de favoritos está vazia</h3>
            <p style="color: #999; margin-bottom: 30px;">Adicione produtos que você gosta para encontrá-los facilmente depois!</p>
            <a href="marketplace.html" class="btn btn-primary" style="background: #3cb371; border: none; padding: 12px 30px; border-radius: 8px; text-decoration: none; color: white;">
                <i class="fas fa-shopping-bag me-2"></i>Continuar Comprando
            </a>
        </div>
    `;

  $("#favoritosGrid").html(mensagemHTML);
}

$(document).ready(function () {
  const tipo = obterTipoUtilizadorPagina();
  if (tipo > 0 && tipo !== 2) {
    favoritosAcessoCliente = false;
    bloquearBotoesFavorito();
    return;
  }

  verificarAcessoFavoritos(
    function () {
      atualizarContadorFavoritos();

      $(".btn-favorito").each(function () {
        const produtoId = $(this).data("produto-id");
        if (produtoId) {
          verificarFavorito(produtoId, this);
        }
      });
    },
    { mostrarModal: false },
  );
});
