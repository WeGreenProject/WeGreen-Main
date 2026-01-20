/**
 * Sistema de Favoritos/Wishlist
 * Gestão completa da lista de desejos do cliente
 */

// ========================================
// ADICIONAR/REMOVER FAVORITOS
// ========================================

/**
 * Toggle favorito (adicionar ou remover)
 */
function toggleFavorito(produtoId, botaoElement) {
  const isFavorito = $(botaoElement).hasClass("favorito-ativo");

  if (isFavorito) {
    removerFavorito(produtoId, botaoElement);
  } else {
    adicionarFavorito(produtoId, botaoElement);
  }
}

/**
 * Adicionar produto aos favoritos
 */
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
        // Atualizar UI do botão
        $(botaoElement).addClass("favorito-ativo");
        $(botaoElement).find("i").removeClass("far").addClass("fas");

        // Mostrar feedback
        Swal.fire({
          icon: "success",
          title: "Adicionado aos Favoritos!",
          text: response.message,
          timer: 2000,
          showConfirmButton: false,
          toast: true,
          position: "top-end",
        });

        // Atualizar contador se existir
        atualizarContadorFavoritos();
      } else {
        Swal.fire({
          icon: "warning",
          title: "Atenção",
          text: response.message,
          confirmButtonColor: "#f59e0b",
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

/**
 * Remover produto dos favoritos
 */
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
        // Atualizar UI do botão
        $(botaoElement).removeClass("favorito-ativo");
        $(botaoElement).find("i").removeClass("fas").addClass("far");

        // Se estiver na página de favoritos, remover o card
        if (window.location.pathname.includes("meusFavoritos.php")) {
          $(`#produto-${produtoId}`).fadeOut(400, function () {
            $(this).remove();

            // Verificar se ainda há favoritos
            if ($(".produto-favorito-card").length === 0) {
              mostrarMensagemVazio();
            }
          });
        }

        // Mostrar feedback
        Swal.fire({
          icon: "info",
          title: "Removido dos Favoritos",
          text: response.message,
          timer: 2000,
          showConfirmButton: false,
          toast: true,
          position: "top-end",
        });

        // Atualizar contador
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

/**
 * Verificar se produto está nos favoritos
 */
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

/**
 * Atualizar contador de favoritos no header
 */
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

/**
 * Mostrar mensagem de lista vazia
 */
function mostrarMensagemVazio() {
  const mensagemHTML = `
        <div class="empty-state" style="text-align: center; padding: 60px 20px;">
            <i class="fas fa-heart" style="font-size: 80px; color: #e0e0e0; margin-bottom: 20px;"></i>
            <h3 style="color: #666; margin-bottom: 10px;">Sua lista de favoritos está vazia</h3>
            <p style="color: #999; margin-bottom: 30px;">Adicione produtos que você gosta para encontrá-los facilmente depois!</p>
            <a href="index.html" class="btn btn-primary" style="background: #3cb371; border: none; padding: 12px 30px; border-radius: 8px; text-decoration: none; color: white;">
                <i class="fas fa-shopping-bag me-2"></i>Continuar Comprando
            </a>
        </div>
    `;

  $("#favoritosGrid").html(mensagemHTML);
}

// ========================================
// INICIALIZAÇÃO
// ========================================

$(document).ready(function () {
  // Atualizar contador ao carregar
  atualizarContadorFavoritos();

  // Verificar estado dos favoritos em produtos individuais
  $(".btn-favorito").each(function () {
    const produtoId = $(this).data("produto-id");
    if (produtoId) {
      verificarFavorito(produtoId, this);
    }
  });
});
