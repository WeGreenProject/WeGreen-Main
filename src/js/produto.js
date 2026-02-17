function getProdutoMostrar() {
  const params = new URLSearchParams(window.location.search);
  const produtoID = params.get("id");

  if (!produtoID) {
    window.location.href = "index.html";
    return;
  }

  let dados = new FormData();
  dados.append("op", 1);
  dados.append("id", produtoID);

  $.ajax({
    url: "src/controller/controllerProduto.php",
    method: "POST",
    data: dados,
    dataType: "html",
    cache: false,
    contentType: false,
    processData: false,
  })
    .done(function (msg) {
      console.log(msg);
      $("#ProdutoInfo").html(msg);

      $(".btnComprarAgora").on("click", function () {
        const produtoId = $(this).data("id");
        comprarAgora(produtoId);
      });

      setTimeout(function () {
        console.log(
          "✅ HTML do produto carregado, iniciando carregamento de avaliações...",
        );
        carregarAvaliacoes(produtoID);
      }, 100);
    })
    .fail(function (jqXHR, textStatus) {
      alert("Erro ao carregar o produto: " + textStatus);
      window.location.href = "index.html";
    });
}

function ErrorSession() {
  const modal =
    typeof showModernConfirmModal === "function"
      ? showModernConfirmModal(
          "Inicie Sessão",
          "É necessário iniciar sessão para conversar com o vendedor!",
          {
            confirmText: '<i class="fas fa-sign-in-alt"></i> Ir para Login',
            icon: "fa-sign-in-alt",
            iconBg:
              "background: linear-gradient(135deg, #3cb371 0%, #2e8b57 100%);",
          },
        )
      : Swal.fire({
          icon: "warning",
          title: "Inicie Sessão",
          text: "É necessário iniciar sessão para conversar com o vendedor!",
          showCancelButton: true,
          confirmButtonText: "Ir para Login",
          cancelButtonText: "Cancelar",
          confirmButtonColor: "#3cb371",
          cancelButtonColor: "#6c757d",
          reverseButtons: true,
        });

  modal.then((result) => {
    if (result.isConfirmed) {
      window.location.href = "login.html";
    }
  });
}

function ErrorSession2() {
  if (typeof showModernInfoModal === "function") {
    showModernInfoModal(
      "Ação Inválida",
      "Não pode iniciar uma conversa consigo mesmo!",
    );
  } else {
    Swal.fire(
      "Ação Inválida",
      "Não pode iniciar uma conversa consigo mesmo!",
      "info",
    );
  }
}

function comprarAgora(produtoId) {
  let dados = new FormData();
  dados.append("op", 7);
  dados.append("produto_id", produtoId);

  $.ajax({
    url: "src/controller/controllerCarrinho.php",
    method: "POST",
    data: dados,
    dataType: "json",
    contentType: false,
    processData: false,
  })
    .done(function (response) {
      console.log("Resposta do servidor:", response);

      if (!response || response.flag !== true) {
        const msg =
          (response && response.msg) ||
          "Não foi possível adicionar o produto ao carrinho";

        if (typeof showModernErrorModal === "function") {
          showModernErrorModal("Erro", msg);
        } else {
          Swal.fire("Erro", msg, "error");
        }
      } else {
        if (typeof showModernSuccessModal === "function") {
          showModernSuccessModal("Sucesso!", "Produto adicionado ao carrinho");
        } else {
          Swal.fire("Sucesso!", "Produto adicionado ao carrinho", "success");
        }
      }
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
      console.error("Erro AJAX:", textStatus, errorThrown);

      let msg = "Não foi possível adicionar o produto ao carrinho";
      if (jqXHR && jqXHR.responseJSON && jqXHR.responseJSON.msg) {
        msg = jqXHR.responseJSON.msg;
      }

      if (typeof showModernErrorModal === "function") {
        showModernErrorModal("Erro", msg);
      } else {
        Swal.fire("Erro", msg, "error");
      }
    });
}

function alerta(titulo, msg, icon) {
  Swal.fire({
    position: "center",
    icon: icon,
    title: titulo,
    text: msg,
    showConfirmButton: true,
  });
}

$(function () {
  getProdutoMostrar();

  
  
});

function carregarAvaliacoes(produtoId) {
  console.log("🔍 Carregando avaliações para produto ID:", produtoId);

  $.ajax({
    url: "src/controller/controllerAvaliacoes.php",
    method: "POST",
    data: {
      op: "obterAvaliacoes",
      produto_id: produtoId,
    },
    dataType: "json",
    cache: false, 
    success: function (response) {
      console.log("✅ Resposta recebida:", response);

      if (response && response.success) {
        console.log("📊 Avaliações:", response.avaliacoes);
        console.log("📈 Estatísticas:", response.estatisticas);

        
        if (!response.avaliacoes || !response.estatisticas) {
          console.error("❌ Dados de avaliações inválidos na resposta");
          $("#ListaAvaliacoes").html(
            '<div class="text-center py-2" style="color: #888;"><small>Erro ao carregar avaliações</small></div>',
          );
          return;
        }

        renderizarAvaliacoes(response.avaliacoes, response.estatisticas);
      } else {
        console.error("❌ Resposta sem sucesso:", response);
        $("#ListaAvaliacoes").html(
          '<div class="text-center py-2" style="color: #888;"><small>Erro ao carregar avaliações</small></div>',
        );
      }
    },
    error: function (xhr, status, error) {
      console.error("❌ Erro AJAX:", { xhr, status, error });
      console.error("Resposta do servidor:", xhr.responseText);

      $("#ListaAvaliacoes").html(
        '<div class="text-center py-2" style="color: #888;"><small>Erro ao carregar avaliações</small></div>',
      );
    },
  });
}

let avaliacoesGlobal = [];
let paginaAtual = 1;
const avaliacoesPorPagina = 3;

function renderizarAvaliacoes(avaliacoes, estatisticas) {
  console.log("🎨 Iniciando renderização de avaliações...");

  
  if (!$("#MediaAvaliacoes").length) {
    console.error("❌ Elemento #MediaAvaliacoes não encontrado no DOM");
    return;
  }
  if (!$("#barrasEstrelas").length) {
    console.error("❌ Elemento #barrasEstrelas não encontrado no DOM");
    return;
  }
  if (!$("#ListaAvaliacoes").length) {
    console.error("❌ Elemento #ListaAvaliacoes não encontrado no DOM");
    return;
  }

  
  avaliacoesGlobal = avaliacoes;

  
  const starsHtml = gerarEstrelasHtml(estatisticas.media, "small");
  $("#MediaAvaliacoes .stars-display").html(starsHtml);
  $("#MediaAvaliacoes .rating-text").text(estatisticas.media.toFixed(1));
  $("#MediaAvaliacoes .total-reviews").text(`(${estatisticas.total})`);
  console.log("✅ Média atualizada:", estatisticas.media);

  
  renderizarBarrasEstatisticas(estatisticas);
  console.log("✅ Barras de estatísticas renderizadas");

  
  if (avaliacoes.length === 0) {
    $("#ListaAvaliacoes").html(`
      <div class="text-center py-3" style="color: #888;">
        <i class="fas fa-comments" style="font-size: 24px; margin-bottom: 8px;"></i>
        <p class="mb-0" style="font-size: 13px;">Sem avaliações</p>
      </div>
    `);
    $("#PaginacaoAvaliacoes").html("");
    console.log("ℹ️ Nenhuma avaliação para exibir");
    return;
  }

  
  console.log(`✅ Renderizando ${avaliacoes.length} avaliação(ões)...`);
  renderizarPagina(1);
}

function renderizarPagina(numeroPagina) {
  paginaAtual = numeroPagina;

  const inicio = (numeroPagina - 1) * avaliacoesPorPagina;
  const fim = inicio + avaliacoesPorPagina;
  const avaliacoesPagina = avaliacoesGlobal.slice(inicio, fim);

  let html = '<div class="d-flex flex-column gap-3">';

  avaliacoesPagina.forEach((avaliacao) => {
    const dataFormatada = new Date(avaliacao.data_criacao).toLocaleDateString(
      "pt-PT",
      {
        day: "numeric",
        month: "short",
        year: "numeric",
      },
    );

    const nomeCompleto =
      `${avaliacao.utilizador_nome || "Anónimo"} ${avaliacao.utilizador_apelido || ""}`.trim();

    html += `
      <div class="avaliacao-item p-3" style="border-left: 4px solid #3cb371; background: white; border-radius: 10px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
        <div class="d-flex align-items-center justify-content-between mb-1">
          <div class="d-flex align-items-center gap-2">
            <strong style="color: #1a1a1a; font-size: 15px;">${nomeCompleto}</strong>
            <div>${gerarEstrelasHtml(avaliacao.avaliacao, "small")}</div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <small style="color: #888; font-size: 11px;">${dataFormatada}</small>
            <button onclick="abrirModalReporte(${avaliacao.id})"
                    class="btn btn-sm"
                    title="Reportar avaliação"
                    style="color: #ef4444; background: transparent; border: none; padding: 4px 8px; transition: all 0.3s ease;"
                    onmouseover="this.style.background='#fee2e2'; this.style.borderRadius='6px';"
                    onmouseout="this.style.background='transparent';">
              <i class="fas fa-flag" style="font-size: 12px;"></i>
            </button>
          </div>
        </div>
        ${
          avaliacao.comentario
            ? `
          <div class="mt-1" style="color: #555; font-size: 14px; line-height: 1.5;">
            ${escapeHtml(avaliacao.comentario)}
          </div>
        `
            : ""
        }
      </div>
    `;
  });

  html += "</div>";
  $("#ListaAvaliacoes").html(html);

  
  renderizarPaginacao();
}

function renderizarPaginacao() {
  const totalPaginas = Math.ceil(avaliacoesGlobal.length / avaliacoesPorPagina);

  if (totalPaginas <= 1) {
    $("#PaginacaoAvaliacoes").html("");
    return;
  }

  let html =
    '<div class="d-flex align-items-center justify-content-center gap-2">';

  
  const anteriorDisabled = paginaAtual === 1;
  html += `
    <button
      class="btn btn-sm d-flex align-items-center gap-1"
      onclick="renderizarPagina(${paginaAtual - 1}); return false;"
      ${anteriorDisabled ? "disabled" : ""}
      style="
        background: ${anteriorDisabled ? "#f0f0f0" : "white"};
        color: ${anteriorDisabled ? "#ccc" : "#3cb371"};
        border: 2px solid ${anteriorDisabled ? "#e0e0e0" : "#3cb371"};
        border-radius: 8px;
        padding: 8px 16px;
        font-weight: 600;
        transition: all 0.3s ease;
        ${!anteriorDisabled ? "cursor: pointer;" : "cursor: not-allowed;"}
      "
      ${!anteriorDisabled ? "onmouseover=\"this.style.background='#3cb371'; this.style.color='white';\" onmouseout=\"this.style.background='white'; this.style.color='#3cb371';\"" : ""}
    >
      <i class="fas fa-chevron-left"></i>
      <span>Anterior</span>
    </button>
  `;

  
  html += '<div class="d-flex gap-2">';
  for (let i = 1; i <= totalPaginas; i++) {
    const ativo = i === paginaAtual;

    html += `
      <button
        class="btn btn-sm"
        onclick="renderizarPagina(${i}); return false;"
        style="
          background: ${ativo ? "linear-gradient(135deg, #3cb371, #2e8b57)" : "white"};
          color: ${ativo ? "white" : "#3cb371"};
          border: 2px solid ${ativo ? "#2e8b57" : "#E8F5E9"};
          border-radius: 8px;
          width: 40px;
          height: 40px;
          font-weight: ${ativo ? "700" : "600"};
          font-size: 15px;
          transition: all 0.3s ease;
          box-shadow: ${ativo ? "0 4px 12px rgba(62,179,113,0.3)" : "none"};
          cursor: pointer;
        "
        ${!ativo ? "onmouseover=\"this.style.background='#E8F5E9'; this.style.borderColor='#3cb371';\" onmouseout=\"this.style.background='white'; this.style.borderColor='#E8F5E9';\"" : ""}
      >
        ${i}
      </button>
    `;
  }
  html += "</div>";

  
  const proximoDisabled = paginaAtual === totalPaginas;
  html += `
    <button
      class="btn btn-sm d-flex align-items-center gap-1"
      onclick="renderizarPagina(${paginaAtual + 1}); return false;"
      ${proximoDisabled ? "disabled" : ""}
      style="
        background: ${proximoDisabled ? "#f0f0f0" : "white"};
        color: ${proximoDisabled ? "#ccc" : "#3cb371"};
        border: 2px solid ${proximoDisabled ? "#e0e0e0" : "#3cb371"};
        border-radius: 8px;
        padding: 8px 16px;
        font-weight: 600;
        transition: all 0.3s ease;
        ${!proximoDisabled ? "cursor: pointer;" : "cursor: not-allowed;"}
      "
      ${!proximoDisabled ? "onmouseover=\"this.style.background='#3cb371'; this.style.color='white';\" onmouseout=\"this.style.background='white'; this.style.color='#3cb371';\"" : ""}
    >
      <span>Próximo</span>
      <i class="fas fa-chevron-right"></i>
    </button>
  `;

  html += "</div>";
  $("#PaginacaoAvaliacoes").html(html);
}

function renderizarBarrasEstatisticas(stats) {
  let html = "";

  for (let i = 5; i >= 1; i--) {
    const count = stats[`estrelas_${i}`] || 0;
    const percentage = stats.total > 0 ? (count / stats.total) * 100 : 0;

    html += `
      <div class="d-flex align-items-center gap-3">
        <span style="min-width: 40px; font-size: 14px; color: #1a1a1a; font-weight: 600;">
          ${i} ${gerarEstrelasHtml(i, "mini")}
        </span>
        <div class="flex-grow-1" style="height: 12px; background: #E8F5E9; border-radius: 6px; overflow: hidden;">
          <div style="height: 100%; width: ${percentage}%; background: linear-gradient(90deg, #3cb371, #2e8b57); transition: width 0.3s ease;"></div>
        </div>
        <span style="min-width: 35px; text-align: right; font-size: 14px; color: #3cb371; font-weight: 600;">
          ${count}
        </span>
      </div>
    `;
  }

  $("#barrasEstrelas").html(html);
}

function gerarEstrelasHtml(rating, size = "normal") {
  const sizeClass =
    size === "small" ? "star-small" : size === "mini" ? "star-mini" : "";
  const fontSize =
    size === "small" ? "12px" : size === "mini" ? "10px" : "20px";

  let html = "";
  const fullStars = Math.floor(rating);
  const hasHalfStar = rating % 1 >= 0.5;

  for (let i = 0; i < 5; i++) {
    if (i < fullStars) {
      html += `<i class="fas fa-star ${sizeClass}" style="color: #ffc107; font-size: ${fontSize};"></i>`;
    } else if (i === fullStars && hasHalfStar) {
      html += `<i class="fas fa-star-half-alt ${sizeClass}" style="color: #ffc107; font-size: ${fontSize};"></i>`;
    } else {
      html += `<i class="far fa-star ${sizeClass}" style="color: #ddd; font-size: ${fontSize};"></i>`;
    }
  }

  return html;
}

function escapeHtml(text) {
  const map = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#039;",
  };
  return text.replace(/[&<>"']/g, (m) => map[m]);
}

/**
 * Abrir modal de reportar avaliação
 */
function abrirModalReporte(avaliacaoId) {
  $("#reportAvaliacaoId").val(avaliacaoId);
  $("#reportMotivo").val("");
  $("#reportDescricao").val("");

  const modal = new bootstrap.Modal(
    document.getElementById("modalReportarAvaliacao"),
  );
  modal.show();
}

/**
 * Enviar reporte (preparado para backend)
 */
function enviarReporte() {
  const avaliacaoId = $("#reportAvaliacaoId").val();
  const motivo = $("#reportMotivo").val();
  const descricao = $("#reportDescricao").val();

  if (!motivo) {
    Swal.fire({
      icon: "warning",
      title: "Atenção",
      text: "Por favor, selecione um motivo para o reporte.",
      confirmButtonColor: "#3cb371",
    });
    return;
  }

  console.log("📝 Reporte preparado:");
  console.log("- Avaliação ID:", avaliacaoId);
  console.log("- Motivo:", motivo);
  console.log("- Descrição:", descricao);

  // TODO: Implementar chamada AJAX ao backend
  // const dados = new FormData();
  // dados.append('op', 'reportarAvaliacao');
  // dados.append('avaliacao_id', avaliacaoId);
  // dados.append('motivo', motivo);
  // dados.append('descricao', descricao);
  //
  // $.ajax({
  //   url: 'src/controller/controllerAvaliacoes.php',
  //   method: 'POST',
  //   data: dados,
  //   processData: false,
  //   contentType: false,
  //   dataType: 'json',
  //   cache: false
  // })
  // .done(function(response) {
  //   if (response.success) {
  //     Swal.fire({
  //       icon: 'success',
  //       title: 'Reporte Enviado!',
  //       text: response.message,
  //       confirmButtonColor: '#3cb371',
  //       timer: 3000
  //     });
  //   } else {
  //     Swal.fire({
  //       icon: 'error',
  //       title: 'Erro',
  //       text: response.message,
  //       confirmButtonColor: '#ef4444'
  //     });
  //   }
  // })
  // .fail(function() {
  //   Swal.fire({
  //     icon: 'error',
  //     title: 'Erro de Comunicação',
  //     text: 'Não foi possível enviar o reporte. Tente novamente.',
  //     confirmButtonColor: '#ef4444'
  //   });
  // });

  // Por agora, apenas fechar o modal e mostrar confirmação
  bootstrap.Modal.getInstance(
    document.getElementById("modalReportarAvaliacao"),
  ).hide();

  Swal.fire({
    icon: "success",
    title: "Reporte Enviado!",
    text: "Obrigado pelo seu reporte. A nossa equipa irá analisar esta avaliação.",
    confirmButtonColor: "#3cb371",
    timer: 3000,
  });
}
